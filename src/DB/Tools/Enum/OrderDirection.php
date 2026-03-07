<?php
namespace App\DB\Tools\Enum;
    /**
     * Допустимые направления сортировки ORDER BY.
     */
    enum OrderDirection: string {
        case ASC  = 'ASC';
        case DESC = 'DESC';
    }