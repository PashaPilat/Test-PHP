<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\MenuService;

/**
 * Class CatalogController
 *
 * Контроллер каталога товаров.
 */
class CatalogController extends BaseController
{
    /**
     * Отображает страницу каталога (без конкретной категории).
     *
     * @param array $params Параметры запроса
     * @return void
     */
    public function index(array $params = []): void
    {
        $categoryService = new CategoryService();
        $productService  = new ProductService();
        $menuService     = new MenuService();

        $topMenu    = $menuService->getTopMenuWithChildren();
        $categories = $categoryService->getTree();

        // фильтры из запроса
        $filters = $params['query']['filters'] ?? [];

        // выборка товаров
        $data = $productService->getProducts(
            null,
            $params['query']['sort'] ?? null,
            $params['query']['page'] ?? null,
            $filters
        );

        // фильтры для всего каталога (без категории можно отдать пустой массив)
        $filterData = [];

        $this->render('catalog/index', [
            'categories'      => $categories,
            'products'        => $data['products'],
            'topMenu'         => $topMenu,
            'title'           => $data['title'] ?? 'Каталог товаров',
            'currentCategory' => null,
            'filters'         => $filterData
        ]);
    }

    /**
     * Страница категории
     *
     * @param array $params Параметры запроса
     * @return void
     */
    public function show(array $params): void
    {
        $slug = $params['slug'] ?? null;
        if (!$slug) {
            http_response_code(404);
            echo "Категория не найдена";
            return;
        }

        $categoryService = new CategoryService();
        $category        = $categoryService->getBySlug($slug);
        if (!$category) {
            http_response_code(404);
            echo "Категория не найдена";
            return;
        }

        $productService = new ProductService();
        $menuService    = new MenuService();

        $topMenu    = $menuService->getTopMenuWithChildren();
        $categories = $categoryService->getTree();

        // фильтры из запроса
        $filters = $params['query']['filters'] ?? [];

        // выборка товаров с учётом фильтров
        $data = $productService->getProducts(
            $slug,
            $params['query']['sort'] ?? null,
            $params['query']['page'] ?? null,
            $filters
        );

        // формируем фильтры для категории с учётом выбранных значений
        $filterData = $productService->getFiltersForCategory($slug, $filters);

        $this->render('catalog/index', [
            'categories'      => $categories,
            'products'        => $data['products'],
            'topMenu'         => $topMenu,
            'title'           => $category->name,
            'currentCategory' => $category,
            'filters'         => $filterData
        ]);
    }
}
