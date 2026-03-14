<?php foreach ($products as $product): ?>
    <div class="product-card">
        <a href="/product/<?= $product->slug ?>"><img src="<?= $product->image ?? '/assets/images/no_category.png'  ?>" alt="<?= htmlspecialchars($product->name) ?>"></a>
        <div class="fw-bold"><a href="/product/<?= $product->slug ?>"><?= htmlspecialchars($product->name) ?></a></div>
        <div class="product-price text-success">$<?= $product->price ?></div>
        <button class="btn btn-warning buy-btn"
            data-name="<?= htmlspecialchars($product->name) ?>"
            data-price="<?= $product->price ?>"
            data-id="<?= $product->id ?>">
            Купить
        </button>
        <div class="product-attrs">
            <?php foreach ($product->attributes as $attr): ?>
                <div><?= htmlspecialchars($attr->name) ?>: <?= htmlspecialchars($attr->value) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>