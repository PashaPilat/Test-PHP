<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\ProductService;
use App\Services\CategoryService;

/**
 * Class CatalogController
 *
 * Контроллер каталога товаров.
 */
class CatalogController extends BaseController
{
    /**
     * Отображает страницу каталога.
     *
     * @param array $params Параметры запроса
     * @return void
     */
    public function index(): void
    {
        $categoryService = new CategoryService();
        $productService = new ProductService();

        $menuService = new \App\Services\MenuService();
        $topMenu = $menuService->getTopMenuWithChildren();

        $categories = $categoryService->getTree();
        $data = $productService->getProducts(null);
        $this->render('catalog/index', [
            'categories' => $categories,
            'products' => $data['products'],
            'topMenu' => $topMenu,
            'title' => $data['title'],
            'currentCategory' => null
        ]);
    }

    /**
     * Страница категории
     * @param array $params Параметры запроса
     * @return void
     */
    public function show(array $params): void
    {
        $slug = $params['slug'];
        if (!$slug) {
            http_response_code(404);
            echo "Категория не найдена";
            return;
        }
        $categoryService = new CategoryService();
        $category = $categoryService->getBySlug($slug);
        if (!$category) {
            http_response_code(404);
            echo "Категория не найдена";
            return;
        }

        $productService = new ProductService();
        $data = $productService->getProducts($slug, $params['query']['sort'] ?? null, $params['query']['page'] ?? null);

        $menuService = new \App\Services\MenuService();
        $topMenu = $menuService->getTopMenuWithChildren();

        $categories = $categoryService->getTree();
        
        $this->render('catalog/index', [
            'categories' => $categories,
            'products' => $data['products'],
            'topMenu' => $topMenu,
            'title' => $category->name,
            'currentCategory' => $category,
        ]);
    }
}
