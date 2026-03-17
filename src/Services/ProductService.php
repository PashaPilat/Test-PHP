<?php

namespace App\Services;

use App\DB;
use App\Models\Product;
use App\DB\Tools\Enum\OrderDirection;
use App\DB\Tools\Enum\WhereOperator;

/**
 * Class ProductService
 *
 * Сервис работы с товарами каталога.
 *
 * Отвечает за:
 * - получение списка товаров
 * - сортировку
 * - пагинацию
 * - подготовку данных для вывода
 * - формирование фильтров для категории
 */
class ProductService
{
    /**
     * Количество товаров на страницу.
     *
     * @var int
     */
    private int $perPage = 12;

    // ------------------------------------------------------------
    // Методы получения товаров
    // ------------------------------------------------------------

    /**
     * Получает товар по ID.
     *
     * @param int $id
     * @return Product|null
     */
    public function getById(int $id): ?Product
    {
        if ($id <= 0) {
            return null;
        }

        $row = DB::select('*')
            ->from(['products', 'p'])
            ->where('p.id', $id, WhereOperator::EQ)
            ->where('p.status', 'active', WhereOperator::EQ)
            ->first();

        if (!$row) {
            return null;
        }

        $product = new Product();
        $product->fill($row);

        return $product;
    }

    /**
     * Получает товар по slug.
     *
     * @param string $slug
     * @return Product|null
     */
    public function getBySlug(string $slug): ?Product
    {
        if ($slug <= 0) {
            return null;
        }

        $row = DB::select('*')
            ->from(['products', 'p'])
            ->where('p.slug', $slug, WhereOperator::EQ)
            ->where('p.status', 'active', WhereOperator::EQ)
            ->first();

        if (!$row) {
            return null;
        }

        $product = new Product();
        $product->fill($row);

        // атрибуты товара
        $attrRows = DB::select([
            'pa.id',
            'pa.product_id',
            'pa.attribute_id',
            'pa.value',
            'pa.description AS value_description',
            'a.name',
            'a.slug',
            'a.description AS attribute_description',
            'a.type'
        ])
            ->from(['product_attributes', 'pa'])
            ->join(['attributes', 'a'], 'a.id', 'pa.attribute_id')
            ->where('pa.product_id', $product->id, WhereOperator::EQ)
            ->where('pa.status', 'active', WhereOperator::EQ)
            ->all();

        $attributes = [];
        $colors = [];

        foreach ($attrRows as $row) {
            if ($row['slug'] === 'color') {
                $colors[] = [
                    'id'    => $row['attribute_id'],
                    'name'  => $row['name'],
                    'value' => $row['value']
                ];
            } else {
                $attributes[] = [
                    'id'          => $row['attribute_id'],
                    'name'        => $row['name'],
                    'slug'        => $row['slug'],
                    'description' => $row['attribute_description'],
                    'type'        => $row['type'],
                    'value'       => $row['value'],
                    'value_description' => $row['value_description']
                ];
            }
        }

        $product->attributes = $attributes;
        $product->colors = $colors;

        return $product;
    }

    /**
     * Получает список товаров каталога.
     *
     * @param string|null $categorySlug Слаг категории
     * @param string|null $sort Тип сортировки
     * @param int|null $page Номер страницы
     *
     * @return array
     */
    public function getProducts(?string $categorySlug, ?string $sort = null, ?int $page = null): array
    {
        // базовые поля продукта
        $fields = [
            'p.id',
            'p.name',
            'p.slug',
            'p.description',
            'p.image',
            'p.price',
            'p.old_price',
            'p.status',
            'p.views',
            'p.purchases',
            'p.likes',
            'p.favorites',
            'p.rating',
            'p.rating_count',
            'p.created_at',
            'p.updated_at'
        ];

        // если указана категория — добавляем поля категории
        if ($categorySlug) {
            $fields = array_merge($fields, [
                'c.id AS category_id',
                'c.name AS category_name',
                'c.slug AS category_slug',
                'c.path AS category_path',
                'c.parent_id AS category_parent_id',
                'c.description AS category_description'
            ]);
        }

        $query = DB::select($fields)
            ->from(['products', 'p'])
            ->where('p.status', 'active', WhereOperator::EQ);

        if ($categorySlug) {
            $query->join(['product_category', 'cp'], 'cp.product_id', 'p.id');
            $query->join(['categories', 'c'], 'c.id', 'cp.category_id');
            $query->where('c.slug', $categorySlug, WhereOperator::EQ);
        }

        // сортировка
        if ($sort !== null) {
            $this->applySorting($query, $sort);
        }

        // пагинация
        if ($page !== null) {
            $offset = ($page - 1) * $this->perPage;
            $query->limit($this->perPage)->offset($offset);
        }

        $rows = $query->all();
        $products = $this->mapRowsToProducts($rows);

        return [
            'products' => $products,
            'pagination' => [
                'page' => $page ?? 1
            ]
        ];
    }

    // ------------------------------------------------------------
    // Фильтры
    // ------------------------------------------------------------

    /**
     * Формирует фильтры для категории.
     *
     * Использует таблицу counters и представления:
     * - categories_with_count (для min/max цены)
     * - attributes_with_count (для списка атрибутов и их значений)
     *
     * @param string $categorySlug Слаг категории
     * @return array Структура фильтра
     */
    public function getFiltersForCategory(string $categorySlug): array
    {
        // Получаем данные по категории (min/max цены)
        $categoryRow = DB::select(['c.id', 'c.min_price', 'c.max_price'])
            ->from(['categories_with_count', 'c'])
            ->where('c.slug', $categorySlug, WhereOperator::EQ)
            ->first();

        $filters = [
            'price' => [
                'min' => $categoryRow['min_price'] ?? 0,
                'max' => $categoryRow['max_price'] ?? 0,
            ],
            'attributes' => []
        ];

        // Получаем список атрибутов с products_count > 0
        $attrRows = DB::select(['a.id', 'a.name', 'a.slug', 'a.description', 'a.type', 'a.values_json'])
            ->from(['attributes_with_count', 'a'])
            ->where('a.products_count', 0, WhereOperator::GT)
            ->where('a.category_id', $categoryRow['id'], WhereOperator::EQ)
            ->where('a.status', 'active', WhereOperator::EQ)
            ->orderBy('a.name', OrderDirection::ASC) // сортировка атрибутов по имени
            ->all();

        foreach ($attrRows as $attr) {
            $values = [];
            if (!empty($attr['values_json'])) {
                $decoded = json_decode($attr['values_json'], true);
                $valueIds = isset($decoded[0]) && is_array($decoded[0])
                    ? array_keys($decoded[0])
                    : array_keys($decoded);

                if (!empty($valueIds)) {
                    $rows = DB::select(['id', 'attribute_id', 'value', 'description'])
                        ->from('product_attributes')
                        ->where('id', $valueIds, WhereOperator::IN)
                        ->where('status', 'active', WhereOperator::EQ)
                        ->orderBy('value', OrderDirection::ASC)
                        ->all();

                    // группируем по value
                    $grouped = [];
                    foreach ($rows as $row) {
                        $val = $row['value'];
                        if (!isset($grouped[$val])) {
                            $grouped[$val] = [
                                'title' => $val,
                                'description' => $row['description'],
                                'value' => []
                            ];
                        }
                        $grouped[$val]['value'][] = $row['id'];
                    }
                    $values = array_values($grouped);
                }
            }

            $filters['attributes'][] = [
                'id' => $attr['id'],
                'name' => $attr['name'],
                'slug' => $attr['slug'],
                'description' => $attr['description'],
                'type' => $attr['type'],
                'values' => $values
            ];
        }

        return $filters;
    }


    // ------------------------------------------------------------
    // Вспомогательные методы
    // ------------------------------------------------------------

    /**
     * Применяет сортировку к запросу.
     *
     * @param mixed  $query
     * @param string $sort
     *
     * @return void
     */
    private function applySorting($query, string $sort): void
    {
        switch ($sort) {
            case 'price_desc':
                $query->orderBy('p.price', OrderDirection::DESC);
                break;
            case 'price_asc':
                $query->orderBy('p.price', OrderDirection::ASC);
                break;
            case 'create_asc':
                $query->orderBy('p.created_at', OrderDirection::DESC);
                break;
            case 'name_asc':
                $query->orderBy('p.name', OrderDirection::DESC);
                break;
            case 'name_desc':
                $query->orderBy('p.name', OrderDirection::DESC);
                break;
            case 'views_asc':
                $query->orderBy('p.view', OrderDirection::DESC);
                break;     
            default:
                $query->orderBy('p.price', OrderDirection::ASC);
        }
    }

    /**
     * Преобразует строки БД в объекты Product.
     *
     * @param array $rows
     * @return Product[]
     */
    private function mapRowsToProducts(array $rows): array
    {
        $products = [];
        foreach ($rows as $row) {
            $product = new Product();
            $product->fill($row);
            $products[] = $product;
        }

        return $products;
    }
}
