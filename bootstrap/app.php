<?php

    declare(strict_types=1);

    use App\Helpers\JWT;
    use App\Helpers\Redirect;
    use App\Http\Router;
    use App\Services\ApiService;
    use App\View\View;
    use Dotenv\Dotenv;
    use Michelf\MarkdownExtra;

    $root = dirname(__DIR__);

    /**
     * Główny plik bootstrapa aplikacji.
     *
     * Inicjalizuje środowisko, wczytuje zmienne z .env, konfiguruje router,
     * widoki oraz serwisy niezbędne do działania aplikacji.
     *
     * @return array Tablica zawierająca główne instancje obiektów aplikacji
     */
    $dotenv = Dotenv::createImmutable($root);
    $dotenv->load();

    $dotenv->required([
        'ROOT_DIR',

        'DB_HOST',
        'DB_USER',
        'DB_PASS',
        'DB_NAME',
        'DB_PORT',

        'API_HOST',
        'API_PORT',

        'ENCRYPTION_KEY'
    ]);

    if (isset($_ENV['ROOT_DIR']) && $_ENV['ROOT_DIR'] == '/') {
        $_ENV['ROOT_DIR'] = '';
    }


    $router = new Router();
    require $root . '/config/routes.php';

    $view = new View($root . "/templates");
    $api = new ApiService($_ENV['API_HOST'], $_ENV['API_PORT']);
    $jwt = new JWT($_ENV['ENCRYPTION_KEY']);
    $redirect = new Redirect($_ENV['ROOT_DIR']);
    $markdown = new MarkdownExtra();

    return [
        'router' => $router,
        'view' => $view,
        'api' => $api,
        'jwt' => $jwt,
        'redirect' => $redirect,
        'markdown' => $markdown,
    ];
