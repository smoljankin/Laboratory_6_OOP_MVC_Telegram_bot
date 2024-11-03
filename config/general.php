<?php

use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;

return [
    'templateDir' => 'Views',
    'dbFile' => __DIR__ . '/../db/mydb.sq3',
    'models' => [
        UserModel::class,
        CategoryModel::class,
        ProductModel::class,
        OrderModel::class,
    ]
];
