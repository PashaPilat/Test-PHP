<!-- SIDEBAR -->
<aside class="catalog-sidebar">
    <?php if (!empty($parentCat)) { ?>
        <div class="list-group">
            <?php foreach ($parentCat as $cat): ?>
                <a class="list-group-item category-link d-flex justify-content-between align-items-center"
                    data-id="<?= $cat->id ?>" href="#" data-slug="<?= $cat->slug ?>">
                    <?= htmlspecialchars($cat->name) ?>
                    <span class="badge bg-secondary"><?= $cat->products_count ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php } ?>
</aside>