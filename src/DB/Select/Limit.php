<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Contracts\LimitContract;
use InvalidArgumentException;

/**
 * Класс для построения SQL‑ограничения LIMIT.
 */
class Limit implements LimitContract {
    /** @var string|null SQL‑фрагмент LIMIT */
    private ?string $limit = null;

    /**
     * Устанавливает значение LIMIT.
     *
     * @param int $count Количество строк (>= MIN_LIMIT)
     * @throws InvalidArgumentException Если $count < MIN_LIMIT
     */
    public function set(int $count): void {
        if ($count < self::MIN_LIMIT) {
            throw new InvalidArgumentException("Limit must be >= " . self::MIN_LIMIT);
        }
        $this->limit = "LIMIT $count";
        DB::getToSql()?->add('limit', $this->getSql());
    }

    /**
     * Возвращает SQL‑фрагмент LIMIT.
     *
     * @return string SQL‑код (например "LIMIT 10")
     */
    public function getSql(): string {
        return $this->limit ? " " . $this->limit : '';
    }
}
