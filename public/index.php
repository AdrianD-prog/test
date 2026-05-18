<?php

    declare(strict_types=1);

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    require dirname(__DIR__) . '/vendor/autoload.php';

    $app = require dirname(__DIR__) . '/bootstrap/app.php';

    $router = $app['router'];
    $view = $app['view'];
    $api = $app['api'];
    $jwt = $app['jwt'];
    $redirect = $app['redirect'];
    $markdown = $app['markdown'];

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $router->dispatch($path, $view, $api, $jwt, $redirect, $markdown);