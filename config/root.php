<?php
return [
  'GET' => [
    '/'                   => [App\Controllers\CatalogController::class, 'index'], // выводим все категории
    '/catalog'            => [App\Controllers\CatalogController::class, 'index'],
    '/catalog/{slug}'     => [App\Controllers\CatalogController::class, 'show'], //выводим категорию
    '/product/{slug}'     => [App\Controllers\ProductController::class, 'index'], // выводим страницу товара

    '/api/products'       => [App\Controllers\ApiController::class, 'getProduct'], //получим от апи страницу товара, передаем параметры в виде джейсон, результат тоже в виде джейсон
    '/api/categories'     => [App\Controllers\ApiController::class, 'getCatalog'], //получим от апи страницу категории, передаем параметры в виде джейсон, результат тоже в виде джейсон
    '/api/cart'           => [App\Controllers\ApiController::class, 'getCart'], //получение данных для корзины
  ],

  'POST' => [
    //  '/api/category/{action}' => [App\Controllers\ApiController::class, 'handleCategory'], //метод добавления новой категории, входные в виде, джейсон, ответ джейсон
    //  '/api/product/{action}'  => [App\Controllers\ApiController::class, 'handleProduct'], //метод добавления нового товара, входные в виде, джейсон, ответ джейсон
  ],
];
