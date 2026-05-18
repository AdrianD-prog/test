import random
import string

from flask import jsonify, Blueprint, request
from mysql.connector import Error

from config.db import get_db_connection
from endpoints.authz import require_auth, require_roles

products_route = Blueprint('produkt', __name__)


@products_route.route('/produkty', methods=['GET'])
def get_products():
    args = request.args
    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        if len(args) == 0:
            cursor.execute(
                """
                SELECT ap.*,
                       (SELECT IFNULL(SUM(quantity), 0) FROM stock_movements WHERE product_id = ap.id) as stock_quantity
                FROM avalible_products ap
                WHERE ap.active = 1
                """
            )
        else:
            cursor.execute(
                f"""
                    SELECT ap.*,
                           (SELECT IFNULL(SUM(quantity), 0) FROM stock_movements WHERE product_id = ap.id) as stock_quantity
                    FROM avalible_products ap
                    WHERE
                        ap.name LIKE %s
                        AND ap.active >= %s
                        AND (ap.price BETWEEN %s AND %s)
                        AND ap.category_id LIKE %s
                        AND (ap.rating >= %s OR {args.get('rating', 0, type=int) == 0 and "ap.rating IS NULL" or "ap.rating IS NOT NULL"})
                    ORDER BY RAND();
                """,
                (
                    f"%{args.get('search', '')}%",
                    (args.get('inStock') == 'on' and 1 or 0),
                    args.get('priceMin', 0, type=int),
                    args.get('priceMax', 1000000, type=int) >= 20001 and 1000000 or args.get('priceMax'),
                    args.get('category', -1, type=int) == -1 and "%" or args.get('category', type=int),
                    args.get('rating', 0, type=int),
                ),
            )
        return jsonify(cursor.fetchall()), 200
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@products_route.route('/produkty/<int:product_id>', methods=['GET'])
def get_product(product_id):
    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute(
            """
            SELECT ap.*,
                   (SELECT IFNULL(SUM(quantity), 0) FROM stock_movements WHERE product_id = ap.id) as stock_quantity
            FROM avalible_products ap
            WHERE ap.id = %s
            """,
            [product_id],
        )
        return jsonify(cursor.fetchall()), 200
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@products_route.route('/kategorie', methods=['GET'])
def get_categories():
    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500
    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT pc.* FROM product_categories pc")
        return jsonify(cursor.fetchall()), 200
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@products_route.route('/opinie/<int:product_id>', methods=['GET'])
def get_reviews(product_id):
    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500
    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT * from user_ratings WHERE product_id = %s", [product_id])
        return jsonify(cursor.fetchall()), 200
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@products_route.route('/opinie', methods=['POST'])
@require_auth
def add_review(current_user):
    data = request.get_json(silent=True) or {}
    product_id = data.get('product_id')
    user_id = current_user.get('id')
    rating = data.get('rating')
    comment = data.get('comment')

    if not all([product_id, rating]):
        return jsonify({'error': 'Brak wymaganych danych'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute("SELECT id FROM product_ratings WHERE product_id = %s AND user_id = %s", (product_id, user_id))
        if cursor.fetchone():
            return jsonify({'error': 'Użytkownik już wystawił opinię dla tego produktu'}), 400
        cursor.execute(
            "INSERT INTO product_ratings (product_id, user_id, rating, comment) VALUES (%s, %s, %s, %s)",
            (product_id, user_id, rating, comment),
        )
        connection.commit()
        return jsonify({'success': True, 'message': 'Opinia została dodana'}), 201
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@products_route.route('/opinie/<int:review_id>', methods=['PUT'])
@require_auth
def update_review(review_id, current_user):
    data = request.get_json(silent=True) or {}
    rating = data.get('rating')
    comment = data.get('comment')
    user_id = current_user.get('id')

    if not all([rating]):
        return jsonify({'error': 'Brak wymaganych danych'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute("SELECT user_id FROM product_ratings WHERE id = %s", (review_id,))
        review = cursor.fetchone()
        if not review:
            return jsonify({'error': 'Opinia nie istnieje'}), 404
        if int(review[0]) != int(user_id):
            return jsonify({'error': 'Brak uprawnień do edycji tej opinii'}), 403
        cursor.execute(
            "UPDATE product_ratings SET rating = %s, comment = %s WHERE id = %s",
            (rating, comment, review_id),
        )
        connection.commit()
        return jsonify({'success': True, 'message': 'Opinia została zaktualizowana'}), 200
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


def generate_sku(length=8):
    return ''.join(random.choices(string.ascii_uppercase + string.digits, k=length))


@products_route.route('/produkty', methods=['POST'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def add_product(current_user):
    data = request.get_json(silent=True) or {}
    name = data.get('name')
    description = data.get('description')
    price = data.get('price')
    category_id = data.get('category_id')
    image_url = data.get('image_url')
    stock_quantity = data.get('stock_quantity', 0)

    if not all([name, price, category_id]):
        return jsonify({'error': 'Brak wymaganych danych (nazwa, cena, kategoria)'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500
    cursor = None
    try:
        cursor = connection.cursor()
        sku = generate_sku()
        while True:
            cursor.execute("SELECT id FROM products WHERE sku = %s", (sku,))
            if not cursor.fetchone():
                break
            sku = generate_sku()

        cursor.execute(
            "INSERT INTO products (name, sku, description, price, category_id, image) VALUES (%s, %s, %s, %s, %s, %s)",
            (name, sku, description, price, category_id, image_url),
        )
        product_id = cursor.lastrowid
        if stock_quantity > 0:
            cursor.execute(
                "INSERT INTO stock_movements (product_id, warehouse_id, movement_type, quantity) VALUES (%s, %s, %s, %s)",
                (product_id, 1, 'in', stock_quantity),
            )
        connection.commit()
        return jsonify({'success': True, 'message': 'Produkt został dodany', 'id': product_id}), 201
    except Error as e:
        connection.rollback()
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@products_route.route('/produkty/<int:product_id>', methods=['PUT'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def update_product(product_id, current_user):
    data = request.get_json(silent=True) or {}
    name = data.get('name')
    description = data.get('description')
    price = data.get('price')
    category_id = data.get('category_id')
    image_url = data.get('image_url')
    stock_quantity = data.get('stock_quantity')

    if not all([name, price, category_id]):
        return jsonify({'error': 'Brak wymaganych danych'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500
    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute(
            "UPDATE products SET name = %s, description = %s, price = %s, category_id = %s, image = %s WHERE id = %s",
            (name, description, price, category_id, image_url, product_id),
        )

        if stock_quantity is not None:
            cursor.execute("SELECT SUM(quantity) FROM stock_movements WHERE product_id = %s", (product_id,))
            current_stock = cursor.fetchone()[0] or 0
            diff = int(stock_quantity) - current_stock
            if diff != 0:
                movement_type = 'in' if diff > 0 else 'out'
                cursor.execute(
                    "INSERT INTO stock_movements (product_id, warehouse_id, movement_type, quantity) VALUES (%s, %s, %s, %s)",
                    (product_id, 1, movement_type, diff),
                )
        connection.commit()
        return jsonify({'success': True, 'message': 'Produkt został zaktualizowany'}), 200
    except Error as e:
        connection.rollback()
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()


@products_route.route('/produkty/<int:product_id>', methods=['DELETE'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def delete_product(product_id, current_user):
    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500
    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute("UPDATE products SET active = 0 WHERE id = %s", (product_id,))
        connection.commit()
        return jsonify({'success': True, 'message': 'Produkt został oznaczony jako nieaktywny'}), 200
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()
