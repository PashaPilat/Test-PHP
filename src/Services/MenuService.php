<?php

namespace App\Services;

use App\DB;
use App\DB\Tools\Enum\OrderDirection;
use App\DB\Tools\Enum\WhereOperator;
use App\Models\Category;

/**
 * Сервис для формирования меню (хедер, сайдбар).
 */
class MenuService
{
    /**
     * Возвращает категории верхнего уровня для хедера.
     *
     * @return Category[]
     */
    public function getTopMenu(): array
    {
        $rows = DB::select('*')
            ->from('categories_with_count')
            ->where('parent_id', 1, WhereOperator::EQ)
            ->where('id', 1, WhereOperator::GT)
            ->whereIsActive(true)
            ->orderBy('name', OrderDirection::ASC)
            ->all();
        return $this->mapRowsToCategories($rows);
    }

    /**
     * Возвращает дерево категорий для сайдбара.
     *
     * @return Category[] дерево категорий
     */
    public function getSidebarTree(): array
    {
        $rows = DB::select('*')
            ->from('categories_with_count')
            ->where('id', 1, WhereOperator::GT) // исключаем корень
            ->whereIsActive(true)
            ->orderBy('name', OrderDirection::ASC)
            ->all();

        $categories = $this->mapRowsToCategories($rows);

        // индексируем по id
        $byId = [];
        foreach ($categories as $cat) {
            $byId[$cat->id] = [
                'category' => $cat,
                'children' => []
            ];
        }

        $tree = [];
        foreach ($categories as $cat) {
            if (isset($byId[$cat->parent_id])) {
                $byId[$cat->parent_id]['children'][] = &$byId[$cat->id];
            } else {
                $tree[] = &$byId[$cat->id];
            }
        }

        return $tree;
    }

    /**
     * Преобразует массив строк в массив объектов Category.
     *
     * @param array $rows
     * @param bool $onlyTopLevel если true — возвращаем только верхний уровень
     * @return Category[]
     */
    private function mapRowsToCategories(array $rows, bool $onlyTopLevel = false): array
    {
        $categories = [];
        foreach ($rows as $row) {
            $cat = new Category();
            $cat->fill($row);

            if ($onlyTopLevel && $cat->parent_id == 1) {
                continue;
            }

            $categories[] = $cat;
        }
        return $categories;
    }

    public function getTopMenuWithChildren(): array
    {
        $rows = DB::select('*')
            ->from('categories_with_count')
            ->where('parent_id', 1, WhereOperator::EQ)
            ->where('id', 1, WhereOperator::GT)
            ->whereIsActive(true)
            ->orderBy('name', OrderDirection::ASC)
            ->all();

        $topCategories = $this->mapRowsToCategories($rows);

        foreach ($topCategories as $cat) {
            $childrenRows = DB::select('*')
                ->from('categories_with_count')
                ->where('parent_id', $cat->id, WhereOperator::EQ)
                ->whereIsActive(true)
                ->orderBy('name', OrderDirection::ASC)
                ->all();

            $cat->children = $this->mapRowsToCategories($childrenRows);
        }

        return $topCategories;
    }
}
