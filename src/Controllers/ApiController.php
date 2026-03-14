<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\View;
use App\Services\ProductService;
use App\Services\CategoryService;

/**
 * Class ApiController
 *
 * Контроллер API каталога.
 *
 * Отвечает за AJAX взаимодействие фронтенда с сервером.
 */
class ApiController
{
    /**
     * Возвращает страницу товара.
     *
     * @param array $params Параметры маршрута
     *
     * @return void
     */
    public function getProduct(array $params): void
    {
        $query = $params['query'];

        $data = (new ProductService())->getProducts(
            $query['category'] ?? null,
            $query['sort'] ?? 'price_asc',
            (int)($query['page'] ?? 1)
        );

        Response::json([
            'success' => true,
            'products_html' => View::renderPartial(
                'catalog/partials/products',
                ['products' => $data['products']]
            ),
            'title' => $data['title'],
            'pagination' => $data['pagination']
        ]);
    }

    /**
     * Возвращает данные для страницы категории.
     * Используется для обновления сайдбара через AJAX.
     *
     * @param array $params
     * @return void
     */
    public function getCatalog(array $params): void
    {
        $slug = $params['query']['category'] ?? null;
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
        $categories = $categoryService->getTree();

        Response::json([
            'success' => true,
            'currentCategory' => $category,
            'sidebar_html' => View::renderPartial('partials/sidebar', [
                'categories' => $categories,
                'currentCategory' => $category
            ]),
            'products_html' => View::renderPartial('catalog/partials/products', [
                'products' => $data['products']
            ]),
            'title' => $category->name,
        ]);
    }

    /**
     * Возвращает данные для страницы категории.
     * Используется для обновления сайдбара через AJAX.
     *
     * @param array $params
     * @return void
     */
    public function getCart(array $params): void
    {

        $cart = $params['query']['cart'] ?? [];
        $_SESSION['cart'] = $cart;
        $data = ['qty' => 0, 'price' => 0];
        $products = [];
        if (!empty($cart)) {
            $productService = new ProductService();
            foreach ($cart as $id => $qty) {
                $product = $productService->getById((int)$id);
                if ($product) {
                    $product->qty = (int)$qty;
                    $products[] = $product;
                    $data['qty'] += $product->qty;
                    $data['price'] += $product->price * $product->qty;
                }
            }
        }

        Response::json([
            'success' => true,
            'cart_html' => View::renderPartial('cart/partials/cart_content', [
                'products' => $products
            ]),
            'cart_header_html' => View::renderPartial('partials/header_cart', ['cart' => $data])
        ]);
    }
}
