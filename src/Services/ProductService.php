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
 */
class ProductService
{
    /**
     * Количество товаров на страницу.
     *
     * @var int
     */
    private int $perPage = 12;

    /**
     * Получает товар по ID.
     *
     * @param int $id
     * @return Product|null
     */
    public function getById(int $id): ?Product
    {
        if ($id <= 0) {  return null; }
        $row = DB::select('*')
            ->from(['products', 'p'])
            ->where('p.id', $id, WhereOperator::EQ)
            ->where('p.status', 'active', WhereOperator::EQ)
            ->first();
        if (!$row) { return null; }
        $product = new Product();
        $product->fill($row);

        return $product;
    }

    /**
     * Получает список товаров каталога.
     *
     * @param string|null $categorySlug Слаг категории
     * @param string|null      $sort         Тип сортировки
     * @param int         $page         Номер страницы
     *
     * @return array
     */
    public function getProducts(?string $categorySlug, ?string $sort = null, ?int $page = null): array
    {
        $query = DB::select('*')
            ->from(['products', 'p'])
            ->where('p.status', 'active', WhereOperator::EQ);

        if ($categorySlug) {
            $query->join(
                ['product_category', 'cp'],
                'cp.product_id',
                'p.id'
            );
            $query->join(
                ['categories', 'c'],
                'c.id',
                'cp.category_id'
            );
            $query->where('c.slug', $categorySlug, WhereOperator::EQ);
        }

        // сортировка только если указана
        if ($sort !== null) {
            $this->applySorting($query, $sort);
        }

        // пагинация только если указана
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
                $query->orderBy('price', OrderDirection::DESC);
                break;
            case 'price_asc':
                $query->orderBy('price', OrderDirection::ASC);
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
                $query->orderBy('price', OrderDirection::ASC);
        }
    }

    /**
     * Преобразует строки БД в объекты Product.
     *
     * @param array $rows
     *
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
