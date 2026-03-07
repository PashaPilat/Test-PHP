<?php
namespace App\DB\Tools\Enum;

    enum JoinOperator: string {
        case EQ  = '=';
        case NEQ = '<>';
        case GT  = '>';
        case LT  = '<';
        case GTE = '>=';
        case LTE = '<=';
    }