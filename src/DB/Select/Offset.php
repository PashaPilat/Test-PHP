<?php
namespace App\DB\Select;

use App\DB;
use App\DB\Contracts\OffsetContract;
use InvalidArgumentException;

/**
 * Класс для построения SQL‑смещения OFFSET.
 */
class Offset implements OffsetContract {
    /** @var string|null SQL‑фрагмент OFFSET */
    private ?string $offset = null;

    /**
     * Устанавливает значение OFFSET.
     *
     * @param int $count Смещение (>= MIN_OFFSET)
     * @throws InvalidArgumentException Если $count < MIN_OFFSET
     */
    public function set(int $count): void {
        if ($count < self::MIN_OFFSET) {
            throw new InvalidArgumentException("Offset must be >= " . self::MIN_OFFSET);
        }
        $this->offset = "OFFSET $count";
        DB::getToSql()?->add('offset', $this->getSql());
    }

    /**
     * Возвращает SQL‑фрагмент OFFSET.
     *
     * @return string SQL‑код (например "OFFSET 20")
     */
    public function getSql(): string {
        return $this->offset ? " " . $this->offset : '';
    }
}
