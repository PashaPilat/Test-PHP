<?php

use App\Core\View;

/**
 * Экранирует HTML
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
