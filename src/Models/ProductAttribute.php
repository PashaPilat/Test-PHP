<?php
namespace App\Models;

use App\Core\BaseModel;

/**
 * Class ProductAttribute
 *
 * Модель значения атрибута для товара.
 * Отражает таблицу `product_attributes`.
 */
class ProductAttribute extends BaseModel
{
    /** @var int Уникальный идентификатор записи */
    public int $id;

    /** @var int ID товара */
    public int $product_id;

    /** @var int ID атрибута */
    public int $attribute_id;

    /** @var string Значение атрибута */
    public string $value;

    /** @var string|null Описание значения */
    public ?string $description = null;

    /** @var string Статус значения */
    public string $status = 'active';
}
