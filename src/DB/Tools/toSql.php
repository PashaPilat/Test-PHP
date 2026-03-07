<?php
namespace App\DB\Tools;

use App\DB\Contracts\ToSqlContract;
use InvalidArgumentException;
use RuntimeException;

class ToSql implements ToSqlContract {
    /** @var array Части SQL‑запроса (ключ => [sql, params]) */
    private array $parts = [];

    /** @var array Параметры для подготовленного запроса */
    private array $params = [];

    /** @var string SQL‑строка с плейсхолдерами */
    protected string $sql = '';

    /** @var string SQL‑строка с подставленными параметрами */
    protected string $sqlString = '';

    /**
     * Добавляет часть SQL‑запроса.
     *
     * @param string $key   Ключ (например select, from, where)
     * @param string $sql   SQL‑фрагмент
     * @param array  $params Параметры для подготовленного запроса
     * @throws InvalidArgumentException Если ключ или параметры некорректны
     */
    public function add(string $key, string $sql, array $params = []): void {
        if (!in_array($key, self::ALLOWED_PARTS, true)) {
            throw new InvalidArgumentException("Invalid SQL part key: $key");
        }
        foreach ($params as $param) {
            if (!is_string($param) && !is_int($param) && !is_float($param) && $param !== null) {
                throw new InvalidArgumentException("Invalid parameter type");
            }
        }
        $this->parts[$key] = ['sql' => $sql, 'params' => $params];
        $this->params = array_merge($this->params, $params);
    }

    /**
     * Собирает SQL‑запрос из частей.
     *
     * @throws RuntimeException Если отсутствует основная часть (select/insert/update/delete)
     */
    public function build(): void {
        $sqlParts = [];
        $sqlPartsWithParams = [];

        foreach (self::ALLOWED_PARTS as $key) {
            if (isset($this->parts[$key])) {
                $chunk = $this->parts[$key]['sql'];
                $sqlParts[] = $chunk;

                $chunkWithParams = $chunk;
                foreach ($this->parts[$key]['params'] as $param) {
                    $chunkWithParams = preg_replace('/\?/', $this->sanitizeParam($param), $chunkWithParams, 1);
                }
                $sqlPartsWithParams[] = $chunkWithParams;
            }
        }

        $this->sql = implode(' ', $sqlParts);
        $this->sqlString = implode(' ', $sqlPartsWithParams);

        $this->validate();
    }

    /**
     * Экранирует параметр для отладочной строки.
     *
     * @param mixed $param Параметр
     * @return string Экранированное значение
     */
    private function sanitizeParam($param): string {
        if ($param === null) return 'NULL';
        if (is_numeric($param)) return (string)$param;
        return "'" . addslashes((string)$param) . "'";
    }

    public function getSql(): string {
        if (empty($this->sql)) $this->build();
        return $this->sql;
    }

    public function getSqlStr(): string {
        if (empty($this->sqlString)) $this->build();
        return $this->sqlString;
    }

    public function getParams(): array {
        return $this->params;
    }

    public function reset(): void {
        $this->parts = [];
        $this->params = [];
        $this->sql = '';
        $this->sqlString = '';
    }

    public function has(string $key): bool {
        return isset($this->parts[$key]);
    }

    public function remove(string $key): void {
        unset($this->parts[$key]);
    }

    public function debug(): array {
        return $this->parts;
    }

    public function merge(ToSqlContract $other): void {
        $this->parts = array_merge($this->parts, $other->debug());
        $this->params = array_merge($this->params, $other->getParams());
    }

    /**
     * Проверяет корректность собранного запроса.
     *
     * @throws RuntimeException Если отсутствует основная часть
     */
    public function validate(): void {
        $hasMain = $this->has('select') || $this->has('insert') || $this->has('update') || $this->has('delete');
        if (!$hasMain) {
            throw new RuntimeException("Invalid SQL: missing main clause (select/insert/update/delete)");
        }
    }
}
