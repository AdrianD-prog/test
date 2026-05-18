<?php

    declare(strict_types=1);

    namespace App\Http;

    use App\Helpers\JWT;
    use App\Helpers\Mail;
    use App\Helpers\Redirect;
    use App\Services\ApiService;
    use App\View\View;
    use Michelf\MarkdownExtra;

    /**
     * Klasa Router
     *
     * Zarządza kierowaniem żądań HTTP do przypisanych kontrolerów i metod.
     * Umożliwia definiowanie tras z metodami HTTP, dopasowywaniem ścieżek i grupowaniem tras
     * ze wspólnym prefiksem. Obsługuje wysyłanie żądań i wywoływanie odpowiedniej
     * metody kontrolera na podstawie metody HTTP i ścieżki.
     */
class Router
{
    /** @var array Tablica zarejestrowanych tras */
    private array $routes = [];
    /** @var string Ścieżka bazowa dla grupy tras */
    private string $basePath;

    /**
     * Inicjalizuje router, ustawiając ścieżkę bazową z zmiennej środowiskowej.
     */
    public function __construct()
    {
        $this->basePath = $_ENV['ROOT_DIR'];
    }

    /**
     * Konwertuje ścieżkę do małych liter i usuwa wiodące oraz końcowe ukośniki.
     * Zapewnia spójny format ścieżek używanych w systemie routingu.
     *
     * @param string $path Ścieżka do normalizacji
     * @return string Znormalizowana ścieżka
     */
    private function normalizePath(string $path): string
    {
        return strtolower(trim($path, '/'));
    }

    /**
     * Łączy prefiks ze ścieżką, dbając o poprawne ukośniki.
     *
     * @param string $prefix Prefiks ścieżki
     * @param string $path Ścieżka podrzędna
     * @return string Połączona ścieżka
     */
    private function joinPaths(string $prefix, string $path): string
    {
        $prefix = $this->normalizePath($prefix);
        $path   = $this->normalizePath($path);

        if ($prefix === '') {
            return $path;
        }

        if ($path === '') {
            return $prefix;
        }

        return $prefix . '/' . $path;
    }

    /**
     * Grupuje trasy pod wspólnym prefiksem.
     *
     * @param string $prefix Prefiks dla grupy tras
     * @param callable $callback Funkcja zwrotna rejestrująca trasy w grupie
     * @return void
     */
    public function group(string $prefix, callable $callback): void
    {
        $previousPath = $this->basePath;
        $this->basePath = $this->joinPaths($previousPath, $prefix);

        try {
            $callback($this);
        } finally {
            $this->basePath = $previousPath;
        }
    }

    /**
     * Rejestruje nową trasę w systemie routingu, łącząc podaną ścieżkę z aktualną
     * ścieżką bazową i normalizując wynik. Trasa jest powiązana z kontrolerem,
     * metodą HTTP oraz tytułem strony.
     *
     * @param string $method Metoda HTTP (GET, POST, PUT, DELETE)
     * @param string $path Ścieżka trasy, która zostanie połączona z aktualną ścieżką bazową
     * @param array $controller Tablica zawierająca klasę kontrolera i nazwę metody [Class::class, 'methodName']
     * @return void
     */
    public function add(string $method, string $path, array $controller): void
    {
        $fullPath = $this->joinPaths($this->basePath, $path);
        $fullPath = $this->normalizePath($fullPath);
        $fullPath = preg_replace_callback('#{(?<param>[a-zA-Z0-9_]+)(?::(?<type>[^}]+))?}#', function (array $matches): string {
            if (isset($matches['type'])) {
                if ($matches['type'] == 'int') {
                    return '(?<' . $matches[1] . '>\d+)';
                }
            }
            return '(?<' . $matches[1] . '>[^/]+)';
        }, $fullPath);

        $this->routes[] = [
            'path' => $fullPath,
            'method' => strtoupper($method),
            'controller' => $controller,
            'middlewares' => []
        ];
    }

    /**
     * Dopasowuje żądaną ścieżkę i metodę HTTP do zdefiniowanych tras.
     * Jeśli znaleziono trasę, tworzy instancję kontrolera i wywołuje
     * przypisaną metodę. W przypadku braku dopasowania zwraca odpowiedź 404.
     *
     * @param string $path Ścieżka żądania HTTP do przetworzenia
     * @param View $view Obiekt widoku
     * @param ApiService $api Serwis API
     * @param JWT $jwt Pomocnik JWT
     * @param Redirect $redirect Pomocnik przekierowań
     * @param MarkdownExtra $markdown
     * @return void
     */
    public function dispatch(string $path, View $view, ApiService $api, JWT $jwt, Redirect $redirect, MarkdownExtra $markdown): void
    {
        $path = $this->normalizePath($path);
        $method = strtoupper($_SERVER['REQUEST_METHOD']);


        foreach ($this->routes as $route) {
            if (
                !preg_match("#^{$route['path']}$#", $path, $matches) ||
                $route['method'] !== $method
            ) {
                continue;
            }

            [$class, $function] = $route['controller'];

            $controller = new $class($view, $api, $jwt, $redirect, $markdown);
            $controller->{$function}($matches);

            return;
        }

        http_response_code(404);
        require dirname(__DIR__, 2) . '/templates/views/404.php';
    }
}
