# Importuje funkcję odpowiedzialną za wczytanie zmiennych środowiskowych z pliku .env.
from config.dotenv import initialize_dotenv

# Uruchamia wczytywanie zmiennych środowiskowych przed użyciem konfiguracji aplikacji.
initialize_dotenv()

# Importuje moduł os, który pozwala odczytywać zmienne środowiskowe przez os.getenv().
import os

# Importuje scheduler działający w tle, używany do cyklicznego wykonywania zadań.
from apscheduler.schedulers.background import BackgroundScheduler

# Importuje funkcję tworzącą połączenie z bazą danych.
from config.db import get_db_connection

# Importuje główne elementy Flask używane do aplikacji, requestów, odpowiedzi i blueprintów.
from flask import Flask, request, Response, jsonify, Blueprint

# Importuje obsługę CORS, żeby frontend mógł wykonywać zapytania do API z innej domeny/portu.
from flask_cors import CORS

# Importuje moduły endpointów, które później zostaną podpięte do głównego API jako blueprinty.
from endpoints import products, auth, cart, account, careers, orders, delivers, favorites, table, driver

# Tworzy instancję aplikacji Flask; __name__ mówi Flaskowi, gdzie znajduje się główny moduł aplikacji.
app = Flask(__name__)


# Definiuje funkcję cyklicznie usuwającą przeterminowane tokeny weryfikacji e-mail.
def cleanup_expired_tokens():
    # Wchodzi w kontekst aplikacji, żeby operacje zależne od Flask działały również poza zwykłym requestem HTTP.
    with app.app_context():
        # Otwiera połączenie z bazą danych za pomocą lokalnej funkcji pomocniczej.
        connection = get_db_connection()
        # Tworzy kursor, czyli obiekt wykonujący zapytania SQL na aktywnym połączeniu.
        cursor = connection.cursor()
        # Usuwa z tabeli email_verification wszystkie rekordy, których data wygaśnięcia jest starsza niż aktualny czas bazy.
        cursor.execute("DELETE FROM email_verification WHERE expires_at < NOW()")
        # Zatwierdza wykonaną operację DELETE, żeby zmiana została zapisana w bazie danych.
        cursor.commit()


# Tworzy scheduler działający w tle razem z procesem aplikacji.
scheduler = BackgroundScheduler()

# Dodaje do schedulera zadanie odpowiedzialne za regularne czyszczenie przeterminowanych tokenów.
scheduler.add_job(
    # Wskazuje funkcję, którą scheduler ma wywoływać.
    func=cleanup_expired_tokens,
    # Ustawia typ wyzwalacza na interwał, czyli wykonywanie zadania co określony czas.
    trigger="interval",
    # Określa, że zadanie ma uruchamiać się co 5 godzin.
    hours=5
)

# Startuje scheduler, dzięki czemu zadanie zaczyna działać w tle.
scheduler.start()

# Wczytuje konfigurację Flask z mapy wartości zbudowanej w kodzie.
app.config.from_mapping(
    # Ustawia nazwę serwera z hosta i portu pobranych ze zmiennych środowiskowych API_HOST oraz API_PORT.
    SERVER_NAME=f"{os.getenv('API_HOST')}:{os.getenv('API_PORT')}",
)

# Wyłącza ścisłe rozróżnianie adresów URL z końcowym slashem i bez niego.
app.url_map.strict_slashes = False

# Włącza CORS dla wszystkich tras i wszystkich originów, bez przesyłania ciasteczek/credentials.
CORS(app, resources={r"/*": {"origins": "*"}}, supports_credentials=False)


# Rejestruje funkcję wykonywaną przed każdym requestem przychodzącym do aplikacji.
@app.before_request
# Definiuje obsługę requestów preflight wysyłanych przez przeglądarkę dla CORS.
def handle_preflight():
    # Sprawdza, czy aktualny request używa metody OPTIONS, czyli jest requestem preflight.
    if request.method == "OPTIONS":
        # Tworzy pustą odpowiedź HTTP dla requestu OPTIONS.
        res = Response()
        # Dodaje nagłówek odpowiedzi; tutaj wartość '*' oznacza bardzo luźne ustawienie tej kontroli.
        res.headers['X-Content-Type-Options'] = '*'
        # Zwraca odpowiedź i kończy dalsze przetwarzanie requestu OPTIONS.
        return res


# Rejestruje trasę główną aplikacji pod adresem '/'.
@app.route('/')
# Definiuje funkcję obsługującą wejście na główny adres API.
def home():
    # Zwraca prostą odpowiedź JSON informującą, że API działa.
    return jsonify({
        # Pole message zawiera tekst statusowy widoczny dla klienta API.
        'message': 'API Amazonix działa!'
    })


# Tworzy główny blueprint, który grupuje wszystkie trasy API przed podpięciem ich pod /api.
api = Blueprint('api', __name__)

# Rejestruje trasy produktów bez dodatkowego prefiksu, więc będą dostępne bezpośrednio pod /api/...
api.register_blueprint(products.products_route)

# Rejestruje trasy autoryzacji pod prefiksem /api/auth.
api.register_blueprint(auth.auth_route, url_prefix='/auth')

# Rejestruje trasy koszyka pod prefiksem /api/cart.
api.register_blueprint(cart.cart_route, url_prefix='/cart')

# Rejestruje trasy ofert pracy/karier pod prefiksem /api/careers.
api.register_blueprint(careers.careers_route, url_prefix='/careers')

# Rejestruje trasy konta użytkownika pod prefiksem /api/account.
api.register_blueprint(account.account_route, url_prefix='/account')

# Rejestruje trasy zamówień pod prefiksem /api/orders.
api.register_blueprint(orders.orders_route, url_prefix='/orders')

# Rejestruje trasy dostaw pod prefiksem /api/delivers.
api.register_blueprint(delivers.deliver_route, url_prefix='/delivers')

# Rejestruje trasy ulubionych produktów pod prefiksem /api/favorites.
api.register_blueprint(favorites.favorites_route, url_prefix='/favorites')

# Rejestruje trasy związane z tabelami administracyjnymi pod prefiksem /api/tables.
api.register_blueprint(table.table_route, url_prefix='/tables')

# Rejestruje trasy kierowcy pod prefiksem /api/driver.
api.register_blueprint(driver.driver_route, url_prefix='/driver')

# Podpina główny blueprint api do aplikacji Flask pod wspólnym prefiksem /api.
app.register_blueprint(api, url_prefix='/api')

# Sprawdza, czy plik uruchomiono bezpośrednio jako program, a nie zaimportowano jako moduł.
if __name__ == '__main__':
    # Wypisuje pustą linię i poziomą ramkę w konsoli dla czytelnego startu aplikacji.
    print("\n" + "=" * 50)
    # Wypisuje nazwę uruchamianego API.
    print("🚀 API AMAZONIX")
    # Wypisuje kolejną linię ramki w konsoli.
    print("=" * 50)
    # Wypisuje adres serwera zbudowany z konfiguracji SERVER_NAME.
    print(f"📡 Serwer działa na: http://{app.config['SERVER_NAME']}")
    # Wypisuje końcową linię ramki i pustą linię po komunikacie startowym.
    print("=" * 50 + "\n")
    # Uruchamia serwer deweloperski Flask w trybie debugowania.
    app.run(debug=True)
