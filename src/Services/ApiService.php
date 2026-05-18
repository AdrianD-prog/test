<?php

// Deklaruje przestrzeń nazw, w której znajduje się klasa ApiService.
namespace App\Services;

// Importuje enum opisujący typy requestów HTTP obsługiwane przez serwis.
use App\Enums\RequestType;

/**
 * Serwis do obsługi komunikacji z zewnętrznym API.
 *
 * Klasa centralizuje budowanie adresów URL, dodawanie nagłówków autoryzacji
 * oraz wykonywanie żądań HTTP przez cURL.
 */
class ApiService
{
    /**
     * Przechowuje bazowy adres API, na przykład http://localhost:5000/api.
     *
     * @var string Podstawowy adres URL API
     */
    private string $apiUrl;

    /**
     * Konstruktor przygotowuje bazowy adres API na podstawie hosta i portu.
     *
     * @param string $host Host API, domyślnie localhost.
     * @param string $port Port API, domyślnie 5000.
     */
    public function __construct(string $host = 'localhost', string $port = "5000")
    {
        // Składa bazowy adres API i zapisuje go w polu klasy do późniejszego użycia.
        $this->apiUrl = "http://$host:$port/api";
    }

    /**
     * Zwraca pełny adres URL dla podanego endpointu.
     *
     * @param string $endpoint Nazwa endpointu, na przykład /products albo auth/login.
     * @return string Pełny adres URL prowadzący do konkretnego endpointu API.
     */
    private function getApiUrl(string $endpoint): string
    {
        // Usuwa początkowe i końcowe slashe, żeby uniknąć podwójnych znaków "/" w finalnym adresie.
        $uri = trim($endpoint, '/');

        // Łączy bazowy adres API z oczyszczonym endpointem.
        return "$this->apiUrl/$uri";
    }

    /**
     * Buduje nagłówki HTTP używane przy komunikacji z API.
     *
     * @param bool $json Określa, czy dodać nagłówek Content-Type: application/json.
     * @return array Lista nagłówków przekazywanych do cURL.
     */
    private function authHeaders(bool $json = false): array
    {
        // Tworzy podstawową listę nagłówków i deklaruje, że klient oczekuje odpowiedzi JSON.
        $headers = ['Accept: application/json'];

        // Sprawdza, czy request będzie wysyłał ciało JSON.
        if ($json) {
            // Dodaje nagłówek informujący API, że ciało requestu ma format JSON.
            $headers[] = 'Content-Type: application/json';
        }

        // Sprawdza, czy sesja PHP nie została jeszcze uruchomiona.
        if (session_status() === PHP_SESSION_NONE) {
            // Uruchamia sesję, aby można było odczytać token JWT zapisany w $_SESSION.
            session_start();
        }

        // Pobiera token JWT z sesji; jeśli go nie ma, używa pustego stringa.
        $token = $_SESSION['JWT_TOKEN'] ?? '';

        // Sprawdza, czy token istnieje i jest niepustym tekstem.
        if (is_string($token) && $token !== '') {
            // Dodaje nagłówek Authorization w formacie Bearer token.
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        // Zwraca kompletną listę nagłówków dla requestu.
        return $headers;
    }

    /**
     * Wykonuje żądanie HTTP do API.
     *
     * @param RequestType $type Typ żądania (GET, POST, PUT, DELETE).
     * @param string $endpoint Endpoint API, który zostanie dołączony do bazowego URL.
     * @param array $data Dane do wysłania w ciele requestu dla POST, PUT i DELETE.
     * @return array|null Zdekodowana odpowiedź JSON albo tablica z informacją o błędzie.
     */
    public function request(RequestType $type, string $endpoint, array $data = []): ?array
    {
        // Buduje pełny URL API dla wskazanego endpointu.
        $url = $this->getApiUrl($endpoint);

        // Inicjalizuje uchwyt cURL, który będzie reprezentował pojedyncze żądanie HTTP.
        $ch = curl_init();

        // Ustawia adres URL, pod który cURL ma wysłać request.
        curl_setopt($ch, CURLOPT_URL, $url);

        // Nakazuje cURL zwrócić odpowiedź jako string zamiast wypisywać ją bezpośrednio.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Wybiera konfigurację cURL zależnie od typu requestu przekazanego jako enum RequestType.
        switch ($type) {
            // Obsługuje request GET.
            case RequestType::GET:
                // Dodaje nagłówki autoryzacji bez Content-Type JSON, bo GET zwykle nie wysyła ciała requestu.
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authHeaders());
                // Kończy konfigurację wariantu GET.
                break;

            // Obsługuje request POST.
            case RequestType::POST:
                // Informuje cURL, że żądanie ma użyć metody POST.
                curl_setopt($ch, CURLOPT_POST, true);
                // Koduje dane wejściowe do JSON i ustawia je jako ciało requestu.
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                // Dodaje nagłówki autoryzacji oraz Content-Type: application/json.
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authHeaders(true));
                // Kończy konfigurację wariantu POST.
                break;

            // Obsługuje request PUT.
            case RequestType::PUT:
                // Ustawia niestandardową metodę HTTP na PUT.
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                // Koduje dane wejściowe do JSON i ustawia je jako ciało requestu.
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                // Dodaje nagłówki autoryzacji oraz Content-Type: application/json.
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authHeaders(true));
                // Kończy konfigurację wariantu PUT.
                break;

            // Obsługuje request DELETE.
            case RequestType::DELETE:
                // Ustawia niestandardową metodę HTTP na DELETE.
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                // Koduje dane wejściowe do JSON i ustawia je jako ciało requestu, jeśli API wymaga danych przy DELETE.
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                // Dodaje nagłówki autoryzacji oraz Content-Type: application/json.
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->authHeaders(true));
                // Kończy konfigurację wariantu DELETE.
                break;
        }

        // Wysyła request HTTP i zapisuje surową odpowiedź API albo false przy błędzie połączenia.
        $response = curl_exec($ch);

        // Pobiera kod statusu HTTP zwrócony przez API, na przykład 200, 401 albo 500.
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Sprawdza, czy cURL zgłosił błąd techniczny i nie otrzymał odpowiedzi.
        if ($response === false) {
            // Zwraca ujednoliconą odpowiedź błędu połączenia z API.
            return ['success' => false, 'error' => 'Błąd połączenia z API'];
        }

        // Próbuje zdekodować odpowiedź API jako tablicę asocjacyjną JSON.
        $decoded = json_decode($response, true);

        // Sprawdza, czy dekodowanie JSON zakończyło się bez błędu.
        if (json_last_error() === JSON_ERROR_NONE) {
            // Jeżeli odpowiedź JSON jest tablicą, zwraca ją bez zmian; w przeciwnym razie opakowuje wartość jako błąd.
            return is_array($decoded)
                // Zwraca tablicę otrzymaną z API.
                ? $decoded
                // Zwraca ujednolicony błąd, gdy poprawny JSON nie był tablicą.
                : ['success' => false, 'error' => (string)$decoded];
        }

        // Sprawdza, czy API zwróciło status sukcesu mimo tego, że odpowiedź nie była poprawnym JSON-em.
        if ($httpCode >= 200 && $httpCode < 300) {
            // Zwraca surową odpowiedź jako dane, zachowując informację o sukcesie.
            return ['success' => true, 'data' => $response];
        }

        // Sprawdza, czy API zwróciło błąd autoryzacji i czy w sesji istnieje token JWT.
        if ($httpCode === 401 && isset($_SESSION["JWT_TOKEN"])) {
            // Przekierowuje użytkownika na logout, aby wyczyścić sesję po nieautoryzowanej odpowiedzi API.
            (new Redirect($_ENV['ROOT_DIR']))->to('/logout');
        }

        // Zwraca ogólny błąd API dla przypadków, których nie obsłużono wcześniej.
        return ['success' => false, 'error' => 'Błąd API'];
    }
}
