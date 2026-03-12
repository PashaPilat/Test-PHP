<?php foreach ($products as $product): ?>
    <div class="product-card">
        <img src="<?= $product->image ?? '/assets/images/no_category.png'  ?>" alt="<?= htmlspecialchars($product->name) ?>">
        <div class="fw-bold"><?= htmlspecialchars($product->name) ?></div>
        <div class="product-price text-success">$<?= $product->price ?></div>
        <button class="btn btn-warning buy-btn"
            data-name="<?= htmlspecialchars($product->name) ?>"
            data-price="<?= $product->price ?>"
            data-id="<?= $product->id ?>"
            data-bs-toggle="modal"
            data-bs-target="#buyModal">
            Купить
        </button>
        <div class="product-attrs">
            <?php foreach ($product->attributes as $attr): ?>
                <div><?= htmlspecialchars($attr->name) ?>: <?= htmlspecialchars($attr->value) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>