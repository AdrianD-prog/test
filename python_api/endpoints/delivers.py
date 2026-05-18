from flask import Blueprint, jsonify
from mysql.connector import Error

from config.db import get_db_connection

deliver_route = Blueprint('delivers', __name__)

DEFAULT_DELIVERS = [
    {
        'id': 'courier_standard',
        'name': 'Kurier standard',
        'description': 'Dostawa w 2-4 dni robocze',
        'price': 14.99,
        'eta': '2-4 dni',
    },
    {
        'id': 'parcel_locker',
        'name': 'Paczkomat',
        'description': 'Odbior o dowolnej porze',
        'price': 9.99,
        'eta': '1-2 dni',
    },
    {
        'id': 'courier_express',
        'name': 'Kurier express',
        'description': 'Priorytetowa realizacja zamowienia',
        'price': 24.99,
        'eta': '24h',
    },
]


@deliver_route.route('/', methods=['GET'])
def get_delivers():
    """Endpoint pobierania metod dostawy."""
    connection = None
    cursor = None

    try:
        connection = get_db_connection()
        cursor = connection.cursor(dictionary=True)
        cursor.execute(
            """
            SELECT id, name, description
            FROM delivers
            ORDER BY id
            """
        )

        delivers = cursor.fetchall() or []
        if not delivers:
            return jsonify({'success': True, 'delivers': DEFAULT_DELIVERS})

        normalized = []
        for index, deliver in enumerate(delivers):
            fallback = DEFAULT_DELIVERS[index % len(DEFAULT_DELIVERS)]
            normalized.append({
                'id': str(deliver.get('id') or fallback['id']),
                'name': deliver.get('name') or fallback['name'],
                'description': deliver.get('description') or fallback['description'],
                'price': fallback['price'],
                'eta': fallback['eta'],
            })

        return jsonify({'success': True, 'delivers': normalized})

    except Error as error:
        print(f'Blad SQL w delivers: {error}')
        return jsonify({'success': True, 'delivers': DEFAULT_DELIVERS})
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()
