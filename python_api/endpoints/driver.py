from flask import Blueprint, jsonify, request
from mysql.connector import Error

from config.db import get_db_connection
from endpoints.authz import require_auth, require_roles

driver_route = Blueprint('driver', __name__)


@driver_route.route('/deliveries/<int:driver_id>', methods=['GET'])
@require_auth
@require_roles('driver', 'owner', 'admin', 'manager')
def get_driver_deliveries(driver_id, current_user):
    current_role = str(current_user.get('role_name', '')).lower()
    current_user_id = int(current_user.get('id'))
    if current_role == 'driver' and current_user_id != int(driver_id):
        return jsonify({'success': False, 'error': 'Brak uprawnień'}), 403

    connection = None
    cursor = None

    try:
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        cursor.execute(
            """
            SELECT * FROM delivery_details WHERE driver_id = %s
            """,
            (driver_id,)
        )
        deliveries = cursor.fetchall() or []

        for delivery in deliveries:
            for field_name in ('delivery_date', 'order_date'):
                field_value = delivery.get(field_name)
                if hasattr(field_value, 'isoformat'):
                    delivery[field_name] = field_value.isoformat()
            if delivery.get('total_price') is not None:
                delivery['total_price'] = float(delivery['total_price'])

        return jsonify({'success': True, 'deliveries': deliveries})
    except Error as error:
        print(error)
        return jsonify({'success': False, 'error': str(error)}), 500
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()


@driver_route.route('/deliveries/<int:delivery_id>/delivered', methods=['POST'])
@require_auth
@require_roles('driver', 'owner', 'admin', 'manager')
def mark_delivery_as_delivered(delivery_id, current_user):
    current_role = str(current_user.get('role_name', '')).lower()
    driver_id = int(current_user.get('id'))
    payload = request.get_json(silent=True) or {}
    if current_role in ('owner', 'admin', 'manager') and payload.get('driver_id'):
        driver_id = int(payload.get('driver_id'))

    connection = None
    read_cursor = None
    write_cursor = None

    try:
        connection = get_db_connection()
        read_cursor = connection.cursor(dictionary=True)
        read_cursor.execute(
            "SELECT id, status FROM deliveries WHERE id = %s AND driver_id = %s LIMIT 1",
            (delivery_id, driver_id)
        )
        delivery = read_cursor.fetchone()

        if not delivery:
            return jsonify({'success': False, 'error': 'Nie znaleziono dostawy'}), 404

        if str(delivery.get('status', '')).lower() == 'delivered':
            return jsonify({'success': True})

        write_cursor = connection.cursor()
        write_cursor.execute(
            """
            UPDATE deliveries
            SET status = %s, delivery_date = NOW()
            WHERE id = %s AND driver_id = %s
            """,
            ('delivered', delivery_id, driver_id)
        )
        connection.commit()
        return jsonify({'success': True})
    except Error as error:
        return jsonify({'success': False, 'error': str(error)}), 500
    finally:
        if read_cursor is not None:
            read_cursor.close()
        if write_cursor is not None:
            write_cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()
