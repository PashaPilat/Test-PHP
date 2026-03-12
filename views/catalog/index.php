<div class="catalog-page row">
    <?php include BASE_PATH . '/views/catalog/partials/category.php'; ?>
    <!-- PRODUCTS -->
    <section class="catalog-products">
        <h1 class="catalog-title">Каталог товаров: <?= htmlspecialchars($title) ?></h1>
        <div class="catalog-sort mb-3">
            <select id="catalog-sort" class="form-select w-auto">
                <option value="default">По умолчанию</option>
                <option value="price_asc">Цена ↑</option>
                <option value="price_desc">Цена ↓</option>
                <option value="name_asc">Название A-Z</option>
                <option value="name_desc">Название Z-A</option>
                <option value="create_asc">Сначала новые</option>
                <option value="view_asc">Сначала популярные</option>
            </select>
        </div>
        <div class="product-grid" id="product-list">
            <?php include BASE_PATH . '/views/catalog/partials/products.php'; ?>
        </div>
    </section>

</div>