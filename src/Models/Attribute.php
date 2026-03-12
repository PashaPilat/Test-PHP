<?php
namespace App\Models;

use App\Core\BaseModel;

/**
 * Class Attribute
 *
 * Модель атрибута товара.
 * Отражает таблицу `attributes`.
 */
class Attribute extends BaseModel
{
    /** @var int Уникальный идентификатор атрибута */
    public int $id;

    /** @var string Название атрибута */
    public string $name;

    /** @var string Слаг атрибута */
    public string $slug;

    /** @var string|null Описание атрибута */
    public ?string $description = null;

    /** @var string Тип значения (string, number, boolean) */
    public string $type = 'string';

    /** @var string Статус атрибута */
    public string $status = 'active';

    /** @var string Дата создания */
    public string $created_at;

    /** @var string Дата обновления */
    public string $updated_at;


    /** @var int Количество товаров с данным атрибутом */
    public int $products_count = 0;

    /** @var int Количество вариантов значений атрибута */
    public int $variants_count = 0;
}
