<?php
namespace App\DB\Tools\Enum;


    enum WhereOperator: string {
        // Базовые сравнения
        case EQ   = '=';
        case NEQ  = '<>';
        case GT   = '>';
        case LT   = '<';
        case GTE  = '>=';
        case LTE  = '<=';

        // Расширенные для WHERE/HAVING
        case LIKE        = 'LIKE';
        case NOT_LIKE    = 'NOT LIKE';
        case IN          = 'IN';
        case NOT_IN      = 'NOT IN';
        case BETWEEN     = 'BETWEEN';
        case NOT_BETWEEN = 'NOT BETWEEN';
        case IS_NULL     = 'IS NULL';
        case IS_NOT_NULL = 'IS NOT NULL';
    }