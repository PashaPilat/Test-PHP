<?php

declare(strict_types=1);

use App\DB;
use App\Services\CategoryService;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Core/Helpers.php';

define('BASE_PATH', dirname(__DIR__));

DB::connect();

$start = microtime(true);

$service = new CategoryService();
$categories = $service->getAll();

$tree = [];

foreach ($categories as $cat) {
    $tree[$cat->id] = [];
}

foreach ($categories as $cat) {
    if ($cat->parent_id && isset($tree[$cat->parent_id])) {
        $tree[$cat->parent_id][$cat->id] = $cat->id;
    }
}

foreach ($tree as $id => &$children) {
    if (empty($children)) {
        $children = $id;
    }
}

$executionTime = microtime(true) - $start;

echo "<pre>";
echo "Время выполнения: " . number_format($executionTime, 3) . " сек.\n";
print_r($tree);
echo "</pre>";
