<?php
require __DIR__ . '/../vendor/autoload.php';

use App\DB;
use App\DB\Tools\Enum\WhereOperator;
use App\DB\Tools\Enum\Boolean;
use App\DB\Tools\Enum\OrderDirection;

// Конфиг подключения
$config = require __DIR__ . '/../config/db.php';
DB::connect($config);

// Пример использования

// Простой запрос
$categories = DB::select()
    ->from('categories')
    ->where('status', 'active'); // Получить SQL строку
/*
// Сложный запрос
$cat = DB::select('id')
    ->from('categories')
    ->where('status', 'active')
    ->group(Boolean::AND)
        ->where('type', 'premium', WhereOperator::EQ)
        ->where('type', 'vip', WhereOperator::EQ, Boolean::OR)
    ->groupEnd()
    ->innerJoin(['products','p'], 'categories.id', 'p.category_id')
    ->leftJoin('orders', 'categories.id', 'orders.category_id')
    ->rightJoin('suppliers', 'categories.supplier_id', 'suppliers.id')
    ->groupBy('status')
    ->having('COUNT(p.id) > ?', [5])
    ->orderBy('id', OrderDirection::DESC)
    ->paginate(3, 10); // страница 3, по 10 строк
*/
// Пример UPDATE
// DB::update('categories')
//     ->set(['status' => 'inactive'])
//     ->where('id', 1)
//     ->whereOr('parent', 10, WhereOperator::GT)
//     ->exec();
var_dump($categories->paginate(1,10));
var_dump($categories->toSql());
// Вывод
//var_dump($categories->all(), $categories->toSql(), $cat, $cat->toSql());

