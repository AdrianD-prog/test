from flask import Blueprint, jsonify
from config.db import get_db_connection

careers_route = Blueprint('careers', __name__)

@careers_route.route('/', methods=['GET'])
def get_careers():
    """oferty pracy"""
    try:
        connection = get_db_connection()
        cursor = connection.cursor()
        
        
        cursor.execute("SELECT id, name, salary FROM roles where name != 'customer' and name != 'owner' ORDER BY id")
        rows = cursor.fetchall()
        
        offers = []
        for row in rows:
            offers.append({
                'id': row[0],
                'nazwa': row[1],
                'minPensja': row[2]
            })
        
        cursor.close()
        connection.close()
        
        return jsonify(offers), 200
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@careers_route.route('/<int:id>', methods=['GET'])
def get_career_by_id(id):
    """wybierz sobie po id"""
    try:
        connection = get_db_connection()
        cursor = connection.cursor()
        
        cursor.execute("SELECT id, name, salary FROM roles WHERE id = %s", (id,))
        row = cursor.fetchone()
        
        cursor.close()
        connection.close()
        
        if row:
            offer = {
                'id': row[0],
                'nazwa': row[1],
                'minPensja': row[2]
            }
            return jsonify(offer), 200
        else:
            return jsonify({'error': 'Oferta nie znaleziona'}), 404
            
    except Exception as e:
        return jsonify({'error': str(e)}), 500