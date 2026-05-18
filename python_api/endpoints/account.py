import os
from time import time

import jwt
from flask import request, jsonify, Blueprint
from config.db import get_db_connection
from mysql.connector import Error
from endpoints.authz import require_auth, require_roles

# Blueprint dla tras konta użytkownika.
account_route = Blueprint('account', __name__)

@account_route.route('/update', methods=['POST'])
@require_auth
def update_account(current_user):
    """Aktualizuj dane konta użytkownika"""
    data = request.json
    user_id = current_user.get('id')

    email = data.get('email', '').strip()
    first_name = data.get('first_name', '').strip()
    last_name = data.get('last_name', '').strip()
    phone = data.get('phone', '').strip()
    country = data.get('country', '').strip()
    city = data.get('city', '').strip()
    address = data.get('address', '').strip()

    if not email or not first_name or not last_name:
        return jsonify({'success': False, 'error': 'Email, imię i nazwisko są wymagane'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'success': False, 'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)

        cursor.execute("SELECT id FROM users WHERE email = %s AND id != %s", (email, user_id))
        if cursor.fetchone():
            return jsonify({'success': False, 'error': 'Email jest już używany przez innego użytkownika'}), 400

        cursor.execute(
            "UPDATE users SET email = %s, first_name = %s, last_name = %s, phone = %s, country = %s, city = %s, address = %s WHERE id = %s",
            (email, first_name, last_name, phone, country, city, address, user_id)
        )

        connection.commit()

        cursor.execute("SELECT * FROM user_data WHERE user_id = %s", (user_id,))
        user = cursor.fetchone()

        if not user:
            return jsonify({'success': False, 'error': 'Nie znaleziono użytkownika po aktualizacji'}), 404

        created_at_value = user['created_at']
        if hasattr(created_at_value, 'isoformat'):
            created_at_value = created_at_value.isoformat()

        token_data = {
            'id': user['user_id'],
            'email': user['email'],
            'created_at': created_at_value,
            'first_name': user['first_name'],
            'last_name': user['last_name'],
            'phone': user['phone'],
            'country': user['country'],
            'city': user['city'],
            'address': user['address'],
            'role_name': user['role_name'],
            'exp': int(time()) + 3600
        }
        jwt_token = jwt.encode(token_data, os.getenv('ENCRYPTION_KEY'), algorithm='HS256')

        return jsonify({'success': True, 'jwt_token': jwt_token})

    except Error as e:
        connection.rollback()
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()

@account_route.route('/users', methods=['GET'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def get_users(current_user):
    """Pobierz listę wszystkich użytkowników"""
    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500
    
    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("""
            SELECT u.id, u.email, u.first_name, u.last_name, u.phone, 
                   u.country, u.city, u.address, u.role_id, r.name as role_name, 
                   u.verified, u.created_at
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.id DESC
        """)
        users = cursor.fetchall()
        
        for user in users:
            if user['created_at'] and hasattr(user['created_at'], 'isoformat'):
                user['created_at'] = user['created_at'].isoformat()
                
        return jsonify(users)
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@account_route.route('/users/<int:user_id>', methods=['GET'])
@require_auth
def get_user(user_id, current_user):
    """Pobierz dane konkretnego użytkownika"""
    if int(current_user.get('id', 0)) != int(user_id):
        return jsonify({'success': False, 'error': 'Brak uprawnień'}), 403

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Błąd połączenia z bazą'}), 500
    
    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("""
            SELECT u.id, u.email, u.first_name, u.last_name, u.phone, 
                   u.country, u.city, u.address, u.role_id, r.name as role_name, 
                   u.verified, u.created_at
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = %s
        """, (user_id,))
        user = cursor.fetchone()
        
        if not user:
            return jsonify({'error': 'Użytkownik nie znaleziony'}), 404
            
        if user['created_at'] and hasattr(user['created_at'], 'isoformat'):
            user['created_at'] = user['created_at'].isoformat()
            
        return jsonify(user)
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@account_route.route('/users', methods=['POST'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def add_user(current_user):
    """Dodaj nowego użytkownika (przez menedżera)"""
    data = request.json
    email = data.get('email')
    password = data.get('password')
    first_name = data.get('first_name')
    last_name = data.get('last_name')
    role_id = data.get('role_id', 1) # Domyślnie customer
    
    if not all([email, password, first_name, last_name]):
        return jsonify({'success': False, 'error': 'Brakujące wymagane dane'}), 400
        
    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute("SELECT id FROM users WHERE email = %s", (email,))
        if cursor.fetchone():
            return jsonify({'success': False, 'error': 'Email jest już zajęty'}), 400
            
        cursor.execute("""
            INSERT INTO users (email, password, first_name, last_name, role_id, verified)
            VALUES (%s, %s, %s, %s, %s, 1)
        """, (email, password, first_name, last_name, role_id))
        connection.commit()
        return jsonify({'success': True, 'id': cursor.lastrowid})
    except Error as e:
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@account_route.route('/users/<int:user_id>', methods=['PUT'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def edit_user(user_id, current_user):
    """Edytuj użytkownika"""
    data = request.json
    email = data.get('email')
    first_name = data.get('first_name')
    last_name = data.get('last_name')
    role_id = data.get('role_id')
    
    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor()
        # Sprawdź unikalność emaila
        cursor.execute("SELECT id FROM users WHERE email = %s AND id != %s", (email, user_id))
        if cursor.fetchone():
            return jsonify({'success': False, 'error': 'Email jest już używany'}), 400
            
        cursor.execute("""
            UPDATE users 
            SET email = %s, first_name = %s, last_name = %s, role_id = %s,
                phone = %s, country = %s, city = %s, address = %s
            WHERE id = %s
        """, (email, first_name, last_name, role_id, 
              data.get('phone'), data.get('country'), data.get('city'), data.get('address'),
              user_id))
        connection.commit()
        return jsonify({'success': True})
    except Error as e:
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@account_route.route('/users/<int:user_id>', methods=['DELETE'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def delete_user(user_id, current_user):
    """Usuń użytkownika"""
    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor()
        cursor.execute("DELETE FROM users WHERE id = %s", (user_id,))
        connection.commit()
        return jsonify({'success': True})
    except Error as e:
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@account_route.route('/roles', methods=['GET'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def get_roles(current_user):
    """Pobierz listę ról"""
    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT id, name FROM roles")
        roles = cursor.fetchall()
        return jsonify(roles)
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()
