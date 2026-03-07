<?php
namespace App\DB\Tools\Enum;

enum JoinType: string {
    case INNER = 'INNER';
    case LEFT  = 'LEFT';
    case RIGHT = 'RIGHT';
    case FULL  = 'FULL';
}
