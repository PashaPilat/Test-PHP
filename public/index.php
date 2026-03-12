<?php
define('BASE_PATH', dirname(__DIR__)); // корень проекта
require __DIR__ . '/../src/Core/Helpers.php';
require __DIR__ . '/../vendor/autoload.php';
use App\App;

(new App())->run();
