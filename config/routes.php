<?php

return [
    [
        'uri'   => '/',
        'handler' => App\Controllers\HomeController::class,
    ],
    [
        'uri'   => '/login',
        'handler' => App\Controllers\LoginController::class,
    ],
    [
        'uri'   => '/logout',
        'handler' => App\Controllers\LogoutController::class,
    ],
    [
        'uri'   => '/category',
        'handler' => App\Controllers\CategoryController::class,
    ],
    [
        'uri'   => '/new/category',
        'handler' => App\Controllers\NewCategoryController::class,
    ],
    [
        'uri' => '/products',
        'handler' => App\Controllers\ProductsController::class,
    ],
    [
        'uri' => '/new/product',
        'handler' => App\Controllers\NewProductController::class,
    ],
    [
        'uri' => '/warehouse',
        'handler' => App\Controllers\WarehouseController::class,
    ],
    [
        'uri' => '/orders',
        'handler' => App\Controllers\OrdersController::class,
    ],
];