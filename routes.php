<?php

$url = $_GET['url'] ?? '/';

$routes = [

    '/' => 'DashboardController',
    'products' => 'ProductController',
    'boxes' => 'BoxController',
    'alerts' => 'AlertController',
    'login' => 'AuthController',

];

if (array_key_exists($url, $routes)) {

    require __DIR__ . "/app/controllers/{$routes[$url]}.php";
} else {
    http_response_code(404);
    echo "404 - Página no encontrada";
}
