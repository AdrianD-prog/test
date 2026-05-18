from flask import Blueprint, jsonify, request
from config.db import get_db_connection
from endpoints.authz import require_auth

favorites_route = Blueprint('ulubione', __name__)

@favorites_route.route('/', methods=['GET'])
@require_auth
def get_favorites(current_user):
    user_id = request.args.get('user_id', type=int)
    if not user_id:
        user_id = int(current_user.get('id', 0))

    if int(user_id) != int(current_user.get('id', 0)):
        return jsonify({'success': False, 'error': 'Brak uprawnień'}), 403

    connection = get_db_connection()
    cursor = connection.cursor(dictionary=True)

    cursor.execute("""
        SELECT p.* FROM avalible_products p JOIN  favourites uf ON
        uf.product_id = p.id WHERE uf.user_id = %s """, (user_id,)
    )

    favorites = cursor.fetchall()
    cursor.close()
    connection.close()

    return jsonify(favorites), 200


@favorites_route.route('/', methods=["POST"])
@require_auth
def toggle_favorite(current_user):
    data = request.get_json()
    user_id = data.get('user_id')
    product_id = data.get('product_id')

    if not user_id or not product_id:
        return jsonify({'success': False, 'error': 'Brak danych'}), 400
    if int(user_id) != int(current_user.get('id', 0)):
        return jsonify({'success': False, 'error': 'Brak uprawnień'}), 403

    connection = get_db_connection()
    cursor = connection.cursor()
    cursor.execute("SELECT id FROM favourites WHERE user_id = %s AND product_id = %s", (user_id, product_id))
    exists = cursor.fetchone()
    if exists:
        cursor.execute("DELETE FROM favourites WHERE user_id = %s AND product_id = %s", (user_id, product_id))
        is_favorite = False
    else:
        cursor.execute("INSERT INTO favourites (user_id, product_id) VALUES (%s, %s)", (user_id, product_id))
        is_favorite = True

    connection.commit()
    cursor.close()
    connection.close()

    return jsonify({'success': True, 'isFavorite': is_favorite}), 200
