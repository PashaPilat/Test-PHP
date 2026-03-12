<header>
    <!-- Верхняя белая полоса -->
    <nav class="navbar navbar-light bg-light py-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Логотип -->
            <a class="navbar-brand fw-bold" href="/"><img class="logo" src="/assets/images/logo full.png" /></a>

            <!-- Поиск -->
            <form class="d-flex mx-auto" action="/search" method="get">
                <input class="form-control me-2" type="search" name="q" placeholder="Поиск" aria-label="Search">
                <button class="btn btn-dark" type="submit">Найти</button>
            </form>

            <!-- Корзина -->
            <span class="nav-link text-dark nav-link-cart" role="button">
                <i class="bi bi-cart"></i> Корзина
            </span>
        </div>
    </nav>

    <!-- Нижняя чёрная полоса с категориями -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav add_nav">
                    <?php foreach ($topMenu as $cat): ?>
                        <li class="show-sub_ul">
                            <a href="/catalog/<?= $cat->slug ?>" class="sf-with-ul">
                                <?= htmlspecialchars($cat->name) ?>
                                <?php if (!empty($cat->children)): ?>
                                    <span class="sf-sub-indicator"> »</span>
                                <?php endif; ?>
                            </a>
                            <?php if (!empty($cat->children)): ?>
                                <ul class="sub_ul">
                                    <?php foreach ($cat->children as $child): ?>
                                        <li class="wrapper">
                                            <div class="sub">
                                                <a href="/catalog/<?= $child->slug ?>">
                                                    <div class="image-wrap">
                                                        <div class="img-container">
                                                            <img src="<?= $child->icon ?: '/assets/images/no_category.png' ?>"
                                                                alt="<?= htmlspecialchars($child->name) ?>">
                                                        </div>
                                                    </div>
                                                    <?= htmlspecialchars($child->name) ?>
                                                </a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>