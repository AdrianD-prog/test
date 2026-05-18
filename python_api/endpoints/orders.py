from datetime import datetime

from flask import Blueprint, jsonify, request
from mysql.connector import Error

from config.db import get_db_connection
from endpoints.authz import require_auth

orders_route = Blueprint('orders', __name__)


@orders_route.route('/<int:user_id>', methods=['GET'])
@require_auth
def get_orders(user_id, current_user):
    """Zwraca liste zamowien danego uzytkownika."""
    if int(user_id) != int(current_user.get('id', 0)):
        return jsonify({'success': False, 'error': 'Brak uprawnień'}), 403
    connection = None
    cursor = None

    try:
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        cursor.execute(
            """
            SELECT
                o.id,
                o.order_date,
                o.status,
                o.total_price,
                COALESCE(SUM(oi.quantity), 0) AS items_count
            FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.user_id = %s
            GROUP BY o.id, o.order_date, o.status, o.total_price
            ORDER BY o.order_date DESC, o.id DESC
            """,
            (user_id,)
        )
        orders = cursor.fetchall() or []

        for order in orders:
            order_date = order.get('order_date')
            if hasattr(order_date, 'isoformat'):
                order['order_date'] = order_date.isoformat()

            total_price = order.get('total_price')
            if total_price is not None:
                order['total_price'] = float(total_price)

            items_count = order.get('items_count')
            if items_count is not None:
                order['items_count'] = int(items_count)

        return jsonify({'success': True, 'orders': orders})

    except Error as error:
        print(f'Blad SQL w orders GET: {error}')
        return jsonify({'success': False, 'error': str(error)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()


@orders_route.route('/', methods=['POST'])
@require_auth
def checkout(current_user):
    """
    Tworzy zamowienie z zawartosci koszyka danego uzytkownika.
    Oczekuje JSON: {"user_id": int}
    Zwraca: {"order_id": int, "total_price": float}
    """
    data = request.get_json() or {}
    user_id = data.get('user_id')
    if not user_id:
        return jsonify({'success': False, 'error': 'Brak user_id'}), 400

    if int(user_id) != int(current_user.get('id', 0)):
        return jsonify({'success': False, 'error': 'Nieprawidłowy użytkownik'}), 403

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    cursor.execute(
        """
        SELECT c.product_id, c.quantity, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = %s
        """,
        (user_id,)
    )
    cart_items = cursor.fetchall()

    if not cart_items:
        return jsonify({'success': False, 'error': 'Koszyk jest pusty'}), 400

    total_price = sum(item['quantity'] * item['price'] for item in cart_items)

    cursor.execute(
        """
        INSERT INTO orders (user_id, order_date, status, total_price)
        VALUES (%s, %s, %s, %s)
        """,
        (user_id, datetime.now(), 'paid', total_price)
    )
    order_id = cursor.lastrowid

    for item in cart_items:
        cursor.execute(
            """
            INSERT INTO order_items (order_id, product_id, quantity, price_at_time)
            VALUES (%s, %s, %s, %s)
            """,
            (order_id, item['product_id'], item['quantity'], item['price'])
        )

    cursor.execute(
        """
        SELECT u.id
        FROM users u
        JOIN roles r ON r.id = u.role_id
        WHERE LOWER(r.name) = 'driver'
        ORDER BY RAND()
        LIMIT 1
        """)
    driver = cursor.fetchone()

    if not driver:
        return jsonify({'success': False, 'error': 'Brak kierowcow'}), 409


    cursor.execute(
        """
        INSERT INTO deliveries (order_id, driver_id, warehouse_id, status, delivery_date)
        VALUES (%s, %s, 1, 'in_transit', NULL)
        """,
        (order_id, driver['id'])
    )

    cursor.execute("DELETE FROM cart WHERE user_id = %s", (user_id,))

    conn.commit()
    cursor.close()
    conn.close()

    return jsonify({'success': True, 'order_id': order_id, 'total_price': float(total_price)}), 201
