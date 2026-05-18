import os
from functools import wraps

import jwt
from flask import jsonify, request


def get_current_user():
    auth_header = request.headers.get('Authorization', '')
    print(auth_header)
    if not auth_header.startswith('Bearer '):
        return None

    token = auth_header.split(' ', 1)[1].strip()
    if not token:
        return None
    try:
        return jwt.decode(token, os.getenv('ENCRYPTION_KEY'), algorithms=['HS256'])
    except Exception:
        return None


def require_auth(fn):
    @wraps(fn)
    def wrapper(*args, **kwargs):
        user = get_current_user()
        if not user:
            return jsonify({'success': False, 'error': 'Wymagane logowanie'}), 401
        return fn(*args, current_user=user, **kwargs)

    return wrapper


def require_roles(*allowed_roles):
    normalized = {str(r).lower() for r in allowed_roles}

    def decorator(fn):
        @wraps(fn)
        def wrapper(*args, current_user=None, **kwargs):
            user = current_user or get_current_user()
            if not user:
                return jsonify({'success': False, 'error': 'Wymagane logowanie'}), 401

            role = str(user.get('role_name', '')).lower()
            if role not in normalized:
                return jsonify({'success': False, 'error': 'Brak uprawnień'}), 403

            return fn(*args, current_user=user, **kwargs)

        return wrapper

    return decorator
