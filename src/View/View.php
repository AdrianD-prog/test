<?php

    declare(strict_types=1);

    namespace App\View;

    /**
     * Klasa widoku odpowiedzialna za renderowanie szablonów.
     *
     * Zarządza ścieżkami do plików szablonów i obsługuje ich dołączanie do odpowiedzi HTTP.
     */
final class View
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
    }

    /**
     * Renderuje pojedynczy szablon.
     *
     * @param string $template Ścieżka względna do szablonu (bez rozszerzenia .php)
     * @param array $params
     * @return void
     */
    public function render(string $template, array $params = []): void
    {
        $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';
        $root = "$protocol://$_SERVER[SERVER_NAME]$_ENV[ROOT_DIR]";

        extract(array_merge($params, ['root' => $root]));

        $file = $this->basePath . '/' . trim($template, '/') . '.php';

        if (!file_exists($file) && !is_file($file)) {
            http_response_code(500);
            echo 'Template not found. File: ' . $file;
            return;
        }

        require $file;
    }

    /**
     * Renderuje szablon i zwraca HTML jako string (przydatne do layoutów).
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderToString(string $template, array $params = []): string
    {
        ob_start();
        $this->render($template, array_merge($params, ['view' => $this]));
        return (string)ob_get_clean();
    }

    /**
     * Renderuje kompletną stronę z layoutem (nagłówek, treść, stopka).
     *
     * @param string $page Nazwa strony do wyrenderowania
     * @param array $params Tablica parametrów dostępnych w szablonie
     * @return void
     */
    public function renderPage(string $page, array $params = []): void
    {
        $content = $this->renderToString('views/' . trim($page, '/'), $params);

        $this->render('layout/app', [
            'view' => $this,
            'title' => $params['title'],
            'content' => $content,

            'scripts' => $params['scripts'] ?? [],
            'styles'  => $params['styles'] ?? [],
        ]);
    }
}
