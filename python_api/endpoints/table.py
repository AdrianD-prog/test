from flask import Blueprint, request, jsonify
from config.db import get_db_connection
from mysql.connector import Error
from endpoints.authz import require_auth, require_roles

table_route = Blueprint('admin', __name__)

# List of tables that are allowed to be managed via generic endpoints
ALLOWED_TABLES = [
    'cart', 'deliveries', 'delivers', 'delivery_items', 'email_verification',
    'favourites', 'orders', 'order_items', 'products', 'product_categories',
    'product_ratings', 'roles', 'stock_movements', 'users', 'user_cart',
    'user_data', 'user_ratings', 'vehicles', 'warehouses', 'warehouse_products',
    'warehouse_transfers', 'warehouse_transfer_items'
]

@table_route.route('/', methods=['GET'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def list_tables(current_user):
    """List all available tables for management"""
    return jsonify(ALLOWED_TABLES)

@table_route.route('/<table_name>', methods=['GET'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def get_table_data(table_name, current_user):
    """Get data from a specific table"""
    if table_name not in ALLOWED_TABLES:
        return jsonify({'error': 'Table not allowed'}), 403

    connection = get_db_connection()
    if not connection:
        return jsonify({'error': 'Database connection error'}), 500

    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        # We use string formatting because table names cannot be parameterized in MySQL, 
        # but we validate against ALLOWED_TABLES whitelist.
        cursor.execute(f"SELECT * FROM `{table_name}` LIMIT 1000")
        rows = cursor.fetchall()

        # Convert datetime objects to string
        for row in rows:
            for key, value in row.items():
                if hasattr(value, 'isoformat'):
                    row[key] = value.isoformat()

        return jsonify(rows)
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@table_route.route('/<table_name>/schema', methods=['GET'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def get_table_schema(table_name, current_user):
    """Get columns of a specific table"""
    if table_name not in ALLOWED_TABLES:
        return jsonify({'error': 'Table not allowed'}), 403

    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute(f"DESCRIBE `{table_name}`")
        columns = cursor.fetchall()
        return jsonify(columns)
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@table_route.route('/<table_name>', methods=['POST'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def add_row(table_name, current_user):
    """Add a row to a table"""
    if table_name not in ALLOWED_TABLES:
        return jsonify({'error': 'Table not allowed'}), 403

    data = request.json
    if not data:
        return jsonify({'error': 'No data provided'}), 400

    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor()
        columns = ", ".join([f"`{k}`" for k in data.keys()])
        placeholders = ", ".join(["%s"] * len(data))
        values = list(data.values())

        query = f"INSERT INTO `{table_name}` ({columns}) VALUES ({placeholders})"
        cursor.execute(query, values)
        connection.commit()
        return jsonify({'success': True, 'id': cursor.lastrowid})
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@table_route.route('/<table_name>/<int:row_id>', methods=['PUT'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def update_row(table_name, row_id, current_user):
    """Update a row in a table (assumes 'id' column)"""
    if table_name not in ALLOWED_TABLES:
        return jsonify({'error': 'Table not allowed'}), 403

    # Safety check for 'users' table - protect owner
    if table_name == 'users':
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT r.name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = %s", (row_id,))
        user_role = cursor.fetchone()
        if user_role and user_role['name'] == 'owner':
            return jsonify({'error': 'Cannot edit owner'}), 403
        cursor.close()
        connection.close()

    data = request.json
    if not data:
        return jsonify({'error': 'No data provided'}), 400

    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor()
        set_clause = ", ".join([f"`{k}` = %s" for k in data.keys()])
        values = list(data.values())
        values.append(row_id)

        query = f"UPDATE `{table_name}` SET {set_clause} WHERE id = %s"
        cursor.execute(query, values)
        connection.commit()
        return jsonify({'success': True})
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()

@table_route.route('/<table_name>/<int:row_id>', methods=['DELETE'])
@require_auth
@require_roles('owner', 'admin', 'manager')
def delete_row(table_name, row_id, current_user):
    """Delete a row from a table (assumes 'id' column)"""
    if table_name not in ALLOWED_TABLES:
        return jsonify({'error': 'Table not allowed'}), 403

    # Safety check for 'users' table - protect owner
    if table_name == 'users':
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT r.name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = %s", (row_id,))
        user_role = cursor.fetchone()
        if user_role and user_role['name'] == 'owner':
            return jsonify({'error': 'Cannot delete owner'}), 403
        cursor.close()
        connection.close()

    connection = get_db_connection()
    cursor = None
    try:
        cursor = connection.cursor()
        query = f"DELETE FROM `{table_name}` WHERE id = %s"
        cursor.execute(query, (row_id,))
        connection.commit()
        return jsonify({'success': True})
    except Error as e:
        return jsonify({'error': str(e)}), 500
    finally:
        if cursor: cursor.close()
        if connection: connection.close()
