import mysql.connector
from mysql.connector import Error
import os

def get_db_connection():
    """Funkcja do połączenia z bazą danych"""
    try:
        connection = mysql.connector.connect(
            host=os.getenv('DB_HOST'),
            database=os.getenv('DB_NAME'),
            user=os.getenv('DB_USER'),
            password=os.getenv('DB_PASS'),
            port=os.getenv('DB_PORT', '3306'),
            charset='utf8mb4',
        )

        # Zwracamy obiekt połączenia do dalszych zapytań SQL.
        return connection
    except Error as e:
        # Jeżeli nie uda się połączyć, wypisz błąd i zwróć None.
        print(f"Błąd połączenia z bazą: {e}")
        return None