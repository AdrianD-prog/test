<?php

    namespace App\Helpers;

/**
 * Klasa pomocnicza do obsługi przekierowań HTTP.
 */
class Redirect
{
    /** @var string Ścieżka bazowa aplikacji */
    private string $root;

    /**
     * @param string $root Ścieżka bazowa
     */
    public function __construct(string $root)
    {
        $this->root = $root;
    }

    /**
     * Łączy ścieżkę bazową z podanym URI.
     *
     * @param string $uri URI do połączenia
     * @return string Pełna ścieżka
     */
    private function joinPath(string $uri): string
    {
        $uri = trim($uri, '/');
        $path = trim($this->root, '/') . '/';
        return trim("$path$uri", '/');
    }

    /**
     * Wykonuje przekierowanie pod wskazany URI.
     *
     * @param string $uri URI docelowy
     * @return never
     */
    public function to(string $uri): never
    {
        $uri = $this->joinPath($uri);
        $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http';

        header("Location: $protocol://$_SERVER[SERVER_NAME]/$uri");
        exit();
    }

    public function back(): never
    {
        header("Location: $_SERVER[HTTP_REFERER]");
        exit();
    }
}