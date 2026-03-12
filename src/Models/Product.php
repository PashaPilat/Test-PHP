<?php
namespace App\Models;

use App\Core\BaseModel;

/**
 * Class Product
 *
 * Модель товара.
 * Отражает таблицу `products`.
 */
class Product extends BaseModel
{
    /** @var int Уникальный идентификатор товара */
    public int $id;

    /** @var string Название товара */
    public string $name;

    /** @var string Слаг товара */
    public string $slug;

    /** @var string|null Описание товара */
    public ?string $description = null;

    /** @var string|null Путь к картинке */
    public ?string $image = null;

    /** @var float Цена товара */
    public float $price;

    /** @var float|null Старая цена */
    public ?float $old_price = null;

    /** @var string Статус товара (active/inactive) */
    public string $status = 'active';

    /** @var int Количество просмотров */
    public int $views = 0;

    /** @var int Количество покупок */
    public int $purchases = 0;

    /** @var int Количество лайков */
    public int $likes = 0;

    /** @var int Количество добавлений в избранное */
    public int $favorites = 0;

    /** @var float Рейтинг товара */
    public float $rating = 0.0;

    /** @var int Количество оценок */
    public int $rating_count = 0;

    /** @var string Дата создания */
    public string $created_at;

    /** @var string Дата обновления */
    public string $updated_at;
}
