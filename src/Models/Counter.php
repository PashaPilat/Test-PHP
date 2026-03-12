<?php
namespace App\Models;

use App\Core\BaseModel;

/**
 * Class Counter
 *
 * Модель счётчиков для категорий и атрибутов.
 * Отражает таблицу `counters`.
 */
class Counter extends BaseModel
{
    /** @var string Тип сущности (category|attribute) */
    public string $entity_type;

    /** @var int ID сущности */
    public int $entity_id;

    /** @var int Количество дочерних категорий (только для категорий) */
    public int $children_count = 0;

    /** @var int Количество товаров (для категорий и атрибутов) */
    public int $products_count = 0;

    /** @var int Количество вариантов значений атрибута */
    public int $variants_count = 0;
}
