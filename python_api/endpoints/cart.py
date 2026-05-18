from flask import request, jsonify, Blueprint
from mysql.connector import Error

from config.db import get_db_connection
from endpoints.authz import require_auth

cart_route = Blueprint('cart', __name__)


@cart_route.route('/', methods=['POST'])
@require_auth
def add_to_cart(current_user):
    data = request.get_json(silent=True) or {}
    user_id = current_user.get('id')
    product_id = data.get('product_id')
    quantity = data.get('quantity', 1)

    if not product_id:
        return jsonify({'error': 'Brak wymaganych danych'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute(
            "SELECT id, quantity FROM cart WHERE user_id = %s AND product_id = %s",
            (user_id, product_id),
        )
        existing = cursor.fetchone()

        if existing:
            cursor.execute(
                "UPDATE cart SET quantity = quantity + %s WHERE user_id = %s AND product_id = %s",
                (quantity, user_id, product_id),
            )
        else:
            cursor.execute(
                "INSERT INTO cart (user_id, product_id, quantity) VALUES (%s, %s, %s)",
                (user_id, product_id, quantity),
            )

        connection.commit()
        return jsonify({'success': True, 'message': 'Produkt dodany do koszyka'}), 201
    except Error as e:
        connection.rollback()
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@cart_route.route('/<int:user_id>', methods=['GET'])
@require_auth
def get_cart(user_id, current_user):
    if int(current_user.get('id')) != int(user_id):
        return jsonify({'error': 'Brak uprawnień'}), 403

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT * FROM user_cart WHERE user_id = %s", (user_id,))
        items = cursor.fetchall()
        total = sum(item['price'] * item['quantity'] for item in items)
        return jsonify({'items': items, 'total': total, 'count': len(items)}), 200
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@cart_route.route('/<int:item_id>', methods=['PUT'])
@require_auth
def update_cart(item_id, current_user):
    data = request.get_json(silent=True) or {}
    quantity = data.get('quantity')
    user_id = current_user.get('id')

    if not quantity or quantity < 1:
        return jsonify({'error': 'Nieprawidłowa ilość'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute(
            "UPDATE cart SET quantity = %s WHERE user_id = %s AND id = %s",
            (quantity, user_id, item_id),
        )
        connection.commit()

        if cursor.rowcount > 0:
            return jsonify({'success': True, 'message': 'Zaktualizowano koszyk'}), 200
        return jsonify({'error': 'Element nie znaleziony'}), 404
    except Error as e:
        connection.rollback()
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@cart_route.route('/<int:item_id>', methods=['DELETE'])
@require_auth
def remove_from_cart(item_id, current_user):
    user_id = current_user.get('id')

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute("DELETE FROM cart WHERE user_id = %s AND id = %s", (user_id, item_id))
        connection.commit()

        if cursor.rowcount > 0:
            return jsonify({'success': True, 'message': 'Produkt usunięty z koszyka'}), 200
        return jsonify({'error': 'Element nie znaleziony'}), 404
    except Error as e:
        connection.rollback()
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()
