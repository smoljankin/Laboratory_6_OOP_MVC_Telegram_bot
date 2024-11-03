<?php

require_once './vendor/autoload.php';

$router = new App\Core\Router(
    __DIR__ . '/config/routes.php', 
    __DIR__ . '/config/general.php'
);
$router->handle();
