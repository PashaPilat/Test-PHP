<?php
function renderCategories(array $categories, $currentCategory = null): void
{
    echo '<ul class="category-list">';
    foreach ($categories as $item) {
        $cat = $item['category'];
        $children = $item['children'];
        $hasChildren = !empty($children);
        // проверка на активную категорию
        $isActive = $currentCategory && $currentCategory->slug === $cat->slug;
        
        echo '<li class="category-item ' 
            . ($hasChildren ? 'has-children ' : '') 
            . ($isActive ? 'active' : '') 
            . '" data-id="' . $cat->id . '" data-slug="' . $cat->slug . '">';

        echo '<div class="category-row d-flex justify-content-between align-items-end">';
        echo '<span class="category-name">' . htmlspecialchars($cat->name) . '</span>';

        // бейджи: товары и подкатегории
        echo '<span class="badge bg-primary" title="Товаров">' . $cat->products_count . '</span>';
        if ($cat->children_count > 0) {
            echo '<span class="badge bg-secondary"  title="Подкатегорий">' . $cat->children_count . '</span>';
        }

        // стрелка
        if ($hasChildren) {
            echo '<span class="toggle-arrow">▸</span>';
        }

        echo '</div>';

        // рекурсивно рендерим детей
        if ($hasChildren) {
            echo '<div class="sub-categories" style="display:none;">';
            renderCategories($children, $currentCategory ?? null);
            echo '</div>';
        }

        echo '</li>';
    }
    echo '</ul>';
}
?>
<div id="categoryList" class="catalog-block">
    <h5>Каталог</h5>
    <?php if (!empty($categories)) {
        renderCategories($categories, $currentCategory ?? null);
    } ?>
</div>