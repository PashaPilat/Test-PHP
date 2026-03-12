<?php
namespace App\Models;

use App\Core\BaseModel;

/**
 * Class Category
 *
 * Модель категории товаров.
 * Отражает таблицу `categories`.
 */
class Category extends BaseModel
{
    /** @var int Уникальный идентификатор категории */
    public int $id;

    /** @var string Название категории */
    public string $name;

    /** @var string Слаг категории */
    public string $slug;

    /** @var string Путь от корня */
    public string $path;

    /** @var int|null Родительская категория */
    public ?int $parent_id = null;

    /** @var string|null Описание категории */
    public ?string $description = null;

    /** @var string|null Путь к картинке/иконке */
    public ?string $icon = null;

    /** @var string Статус категории (active/inactive) */
    public string $status = 'active';

    /** @var string Дата создания */
    public string $created_at;

    /** @var string Дата обновления */
    public string $updated_at;
    
    /** @var int Количество дочерних категорий */
    public int $children_count = 0;

    /** @var int Количество товаров в категории */
    public int $products_count = 0;
}
