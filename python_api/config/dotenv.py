import os

from dotenv import load_dotenv

# Lista zmiennych środowiskowych wymaganych przez aplikację.
required = [
    'ROOT_DIR',

    'DB_HOST',
    'DB_USER',
    'DB_PASS',
    'DB_NAME',
    'DB_PORT',

    'API_HOST',
    'API_PORT',

    'ENCRYPTION_KEY'
]

def initialize_dotenv():
    # Tworzy ścieżkę do pliku .env w katalogu głównym projektu.
    dotenv_path = os.path.join(os.path.dirname(__file__), '../../', '.env')
    if not os.path.exists(dotenv_path):
        raise ValueError(f'Missing .env file at {dotenv_path}')
    # Wczytuje zmienne środowiskowe z pliku .env.
    load_dotenv(dotenv_path)

    # Sprawdza, czy wszystkie wymagane zmienne zostały ustawione.
    for var in required:
        if os.getenv(var) is None:
            raise ValueError(f'Missing environment variable: {var}')