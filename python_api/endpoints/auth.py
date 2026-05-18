import hashlib
import hmac
import os
import secrets
from datetime import datetime, timedelta
from time import time

import jwt
from config.db import get_db_connection
from flask import jsonify, request, Blueprint
from mysql.connector import Error

# Tworzy blueprint o nazwie 'auth' dla tras logowania i rejestracji.
# Dzięki temu wszystkie funkcje z tej grupy mogą być rejestrowane razem.
# __name__ przekazuje Flaskowi nazwę modułu, co pomaga Flaskowi poprawnie zidentyfikować
# źródło blueprintu i obsłużyć ścieżki do zasobów lub szablonów.
auth_route = Blueprint('auth', __name__)

@auth_route.route('/login', methods=['POST'])
def login():
    """Endpoint logowania"""
    # Pobierz dane JSON z ciała żądania.
    data = request.json
    email = data.get('email')
    password = data.get('password')
    
    # Wyświetl informacje o próbie logowania.
    print(f"Próba logowania: {email}")
    
    if not email or not password:
        return jsonify({
            'success': False,
            'error': 'Email i hasło są wymagane'
        }), 400
    
    connection = get_db_connection()
    if not connection:
        return jsonify({'success': False, 'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        
        # Sprawdź użytkownika w tabeli users.
        cursor.execute("""
            SELECT 
            u.*, r.name AS role_name
        
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
                       
            WHERE u.email = %s AND u.password = %s
        """, (email, password))
        
        user = cursor.fetchone()
        
        if user:
            print(f"Zalogowano: {email}")

            created_at_value = user['created_at']
            if hasattr(created_at_value, 'isoformat'):
                created_at_value = created_at_value.isoformat()
            
            data = {
                'id': user['id'],
                'email': user['email'],
                'first_name': user['first_name'],
                'last_name': user['last_name'],

                'phone': user['phone'],
                'country': user['country'],
                'city': user['city'],
                'address': user['address'],

                'role_name': user['role_name'],
                'created_at': created_at_value,
                'verified': user['verified'],

                'exp': int(time()) + 3600
            }

            jwt_token = jwt.encode(data, os.getenv("ENCRYPTION_KEY"))

            return jsonify({'success': True, 'jwt_token': jwt_token})
        else:
            print(f"Nieudane logowanie: {email}")
            return jsonify({
                'success': False,
                'error': 'Nieprawidłowy email lub hasło'
            }), 401
            
    except Error as e:
        print(f"Błąd SQL: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()

# ======================================================================================================================================
# register endpoint
# ======================================================================================================================================
@auth_route.route('/register', methods=['POST'])
def register():
    """Endpoint rejestracji"""
    data = request.json

    required = ['email', 'password', 'first_name', 'last_name']
    for field in required:
        if not data.get(field):
            return jsonify({
                'success': False,
                'error': f'Pole {field} jest wymagane'
            }), 400
    
    connection = get_db_connection()
    if not connection:
        return jsonify({'success': False, 'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)

        cursor.execute("SELECT id FROM users WHERE email = %s", (data['email'],))
        if cursor.fetchone():
            return jsonify({
                'success': False,
                'error': 'Email już istnieje w bazie'
            }), 400

        cursor.execute("""
            INSERT INTO users (email, password, first_name, last_name, phone, country, city, address, role_id, created_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 1, NOW())
        """, (
            data['email'],
            data['password'],
            data['first_name'],
            data['last_name'],
            data.get('phone', ''),
            data.get('country', ''),
            data.get('city', ''),
            data.get('address', '')
        ))
        
        user_id = cursor.lastrowid

        token = secrets.token_hex(32)

        cursor.execute("INSERT INTO email_verification (user_id, random_key, expires_at) VALUES (%s, %s, %s)", (user_id, token, datetime.now() + timedelta(hours=24)))

        connection.commit()
        
        print(f"Zarejestrowano: {data['email']}")
        return jsonify({
            'success': True,
            'message': 'Rejestracja udana',
            'user_id': user_id,
            'token': token
        }), 201
        
    except Error as e:
        connection.rollback()
        print(f"Błąd rejestracji: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()

@auth_route.route('/forgot-password', methods=['POST'])
def forgot_password():
    """Endpoint żądania resetowania hasła"""
    data = request.json
    email = data.get('email')

    if not email:
        return jsonify({'success': False, 'error': 'Email jest wymagany'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'success': False, 'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT id FROM users WHERE email = %s", (email,))
        user = cursor.fetchone()

        if user:
            token = secrets.token_hex(32)
            expires_at = datetime.now() + timedelta(hours=1)
            
            # Usuń stare tokeny
            cursor.execute("DELETE FROM email_verification WHERE user_id = %s", (user['id'],))
            
            # Wstaw nowy token
            cursor.execute(
                "INSERT INTO email_verification (user_id, random_key, expires_at) VALUES (%s, %s, %s)",
                (user['id'], token, expires_at)
            )
            connection.commit()
            
            # W prawdziwej aplikacji wysłalibyśmy maila. Tutaj zwracamy token dla celów testowych/uproszczenia.
            print(f"Token resetowania hasła dla {email}: {token}")
            return jsonify({
                'success': True, 
                'message': 'Jeśli email istnieje w naszej bazie, wysłaliśmy instrukcje resetowania hasła.',
                'token': token # Zwracamy token, aby Junie/użytkownik mógł dokończyć proces bez maila
            })
        else:
            # Ze względów bezpieczeństwa zwracamy ten sam komunikat
            return jsonify({
                'success': True, 
                'message': 'Jeśli email istnieje w naszej bazie, wysłaliśmy instrukcje resetowania hasła.'
            })

    except Error as e:
        print(f"Błąd forgot-password: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()

@auth_route.route('/reset-password', methods=['POST'])
def reset_password():
    """Endpoint faktycznego resetowania hasła"""
    data = request.json
    token = data.get('token')
    new_password = data.get('password')

    if not token or not new_password:
        return jsonify({'success': False, 'error': 'Token i nowe hasło są wymagane'}), 400

    connection = get_db_connection()
    if not connection:
        return jsonify({'success': False, 'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute(
            "SELECT user_id FROM email_verification WHERE random_key = %s AND expires_at > NOW()",
            (token,)
        )
        result = cursor.fetchone()

        if not result:
            return jsonify({'success': False, 'error': 'Nieprawidłowy lub wygasły token'}), 400

        user_id = result['user_id']

        # Aktualizacja hasła
        cursor.execute("UPDATE users SET password = %s WHERE id = %s", (new_password, user_id))
        
        # Usuń zużyty token
        cursor.execute("DELETE FROM email_verification WHERE user_id = %s", (user_id,))
        
        connection.commit()
        return jsonify({'success': True, 'message': 'Hasło zostało pomyślnie zmienione'})

    except Error as e:
        connection.rollback()
        print(f"Błąd reset-password: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()

@auth_route.route('/verify/<int:user_id>/<string:token>', methods=['POST'])
def verify_email(token, user_id):
    connection = get_db_connection()
    if not connection:
        return jsonify({'success': False, 'error': 'Błąd połączenia z bazą'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT IF(random_key = %s, 1, 0) as `verified` FROM email_verification WHERE user_id = %s AND expires_at > NOW()", (token, user_id))

        result = cursor.fetchone()

        if result and result['verified'] == 0:
            return jsonify({"success": True, "verified": False}), 200

        cursor.execute("DELETE FROM email_verification WHERE user_id = %s AND random_key = %s", (user_id, token))
        cursor.execute("UPDATE users SET verified = 1 WHERE id = %s", (user_id,))

        connection.commit()
        return jsonify({"success": True, "verified": True}), 200
    except Error as e:
        connection.rollback()
        print(f"Błąd weryfikacji: {e}")
        return jsonify({'success': False, 'error': str(e)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection.is_connected():
            connection.close()