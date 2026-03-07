<?php
namespace App\DB\Contracts;

interface BlueprintContract {
    // Константы для внешних ключей
    public const CASCADE   = 'CASCADE';
    public const SET_NULL  = 'SET NULL';
    public const RESTRICT  = 'RESTRICT';
    public const NO_ACTION = 'NO ACTION';

    // Константы для дефолтов
    public const DEFAULT_STRING_LENGTH = 255;
    public const DEFAULT_DECIMAL_PRECISION = 10;
    public const DEFAULT_DECIMAL_SCALE = 2;

    // Константы для стандартных полей
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Контракт для основных методов
    public function id(int $length = null, bool $unsigned = true, bool $autoIncrement = true, bool $primary = true, string $comment = 'Идентификатор'): void;
    public function string(string $name, int $length = self::DEFAULT_STRING_LENGTH, bool $nullable = false, ?string $default = null, string $comment = '', bool $unique = false): void;
    public function integer(string $name, int $length = null, bool $unsigned = true, bool $nullable = false, $default = null, string $comment = ''): void;
    public function foreign(string $column, string $refTable, string $refColumn = 'id', string $onDelete = self::CASCADE, string $onUpdate = self::CASCADE, string $name = null): void;
    public function text(string $name, bool $nullable = true, ?string $default = null, string $comment = ''): void;
    public function longText(string $name, bool $nullable = true, ?string $default = null, string $comment = ''): void;
    public function json(string $name, bool $nullable = true, string $comment = ''): void;
    public function decimal(string $name, int $precision = self::DEFAULT_DECIMAL_PRECISION, int $scale = self::DEFAULT_DECIMAL_SCALE, bool $unsigned = false, ?string $default = null, string $comment = ''): void;
    public function boolean(string $name, string $default = '0', string $comment = ''): void;
    public function enum(string $name, array $values, ?string $default = null, string $comment = ''): void;
    public function datetime(string $name, bool $nullable = true, ?string $default = null, string $comment = ''): void;

    public function timestamps(): void;

    public function index(string|array $columns, string $name = null): void;
    public function unique(string|array $columns, string $name = null): void;

}
