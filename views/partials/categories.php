<ul class="category-list">
    <?php foreach ($categories as $item): ?>
        <?php $cat = $item['category'] ?? $item; ?>
        <li>
            <a href="/catalog/<?= $cat->slug ?>"
                class="category-link"
                data-slug="<?= $cat->slug ?>">
                <?= htmlspecialchars($cat->name) ?>
                <span class="count">
                    <?= $cat->products_count ?>
                </span>
            </a>

            <?php if (!empty($item['children'])): ?>
                <?php
                $children = $item['children'];
                include BASE_PATH . '/views/catalog/partials/categories.php';
                ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>