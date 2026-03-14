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
class ProductController extends BaseController
{
    /**
     * Отображает страницу товара.
     *
     * @param array $params Параметры запроса
     * @return void
     */
    public function index(array $params): void
    {
        $slug = $params['slug'] ?? null;
        if (!$slug) {
            http_response_code(404);
            echo "Товар '{$slug}' не найден!";
            return;
        }
        $productService = new ProductService();

        $menuService = new \App\Services\MenuService();
        $topMenu = $menuService->getTopMenuWithChildren();

        $product = $productService->getBySlug($slug);
        
        $this->render('product/show', [
            'product' => $product,
            'topMenu' => $topMenu,
            'title' => $product->name,
            'currentCategory' => null
        ],'ProductLayout');
    }

}
