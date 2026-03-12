<?php

namespace App\Services;

use App\DB;
use App\Models\Category;
use App\DB\Tools\Enum\OrderDirection;
use App\DB\Tools\Enum\WhereOperator;

/**
 * Class CategoryService
 *
 * Сервис работы с категориями каталога.
 *
 * Отвечает за:
 * - получение категории по slug
 * - получение списка подкатегорий
 * - получение дерева категорий
 * - преобразование строк БД в объекты Category
 */
class CategoryService
{
    /**
     * Получает категорию по slug.
     *
     * @param string $slug Слаг категории
     *
     * @return Category|null
     */
    public function getBySlug(string $slug): ?Category
    {
        if ($slug === null || trim($slug) === '')  return null;
        $row = DB::select('*')
            ->from('categories_with_count')
            ->where('slug', $slug, WhereOperator::EQ)
            ->whereIsActive(true)
            ->first();
        if (!$row) {
            return null;
        }

        $category = new Category();
        $category->fill($row);

        return $category;
    }

    /**
     * Получает список подкатегорий категории.
     *
     * @param int $parentId ID родительской категории
     *
     * @return Category[]
     */
    public function getChildren(int $parentId): array
    {
        if ($parentId === null || $parentId === 0)  
            return null;

        $rows = DB::select('*')
            ->from('categories_with_count')
            ->where('parent_id', $parentId, WhereOperator::EQ)
            ->whereIsActive(true)
            ->orderBy('name', OrderDirection::ASC)
            ->all();

        return $this->mapRowsToCategories($rows);
    }

    /**
     * Получает все категории каталога.
     *
     * Используется для построения дерева категорий.
     *
     * @return Category[]
     */
    public function getAll(): array
    {
        $rows = DB::select('*')
            ->from('categories_with_count')
            ->where('id',1, WhereOperator::GT)
            ->whereIsActive(true)
            ->orderBy('name', OrderDirection::ASC)
            ->all();

        return $this->mapRowsToCategories($rows);
    }

    /**
     * Строит дерево категорий.
     *
     * @return array
     */
    public function getTree(): array
    {
        $categories = $this->getAll();
        
        $byId = [];

        foreach ($categories as $cat) {
            $byId[$cat->id] = [
                'category' => $cat,
                'children' => []
            ];
        }

        $tree = [];

        foreach ($categories as $cat) {
            if ($cat->parent_id == 1) {
                // это корневая категория
                $tree[] = &$byId[$cat->id];
            } elseif (isset($byId[$cat->parent_id])) {
                // это подкатегория
                $byId[$cat->parent_id]['children'][] = &$byId[$cat->id];
            }
        }
        
        return $tree;
    }

    /**
     * Преобразует строки БД в объекты Category.
     *
     * @param array $rows
     *
     * @return Category[]
     */
    private function mapRowsToCategories(array $rows): array
    {
        $categories = [];

        foreach ($rows as $row) {
            $category = new Category();
            $category->fill($row);
            $categories[] = $category;
        }

        return $categories;
    }
}
