<!DOCTYPE html>
<html lang="ru">
<?php include BASE_PATH . '/views/partials/head.php'; ?>

<body class="bg-light">
    <?php include BASE_PATH . '/views/partials/header.php'; ?>
    <div class="container-fluid py-4">
        <div class="row">
            <aside id="sidebar" class="catalog-sidebar col-3">
                <?php include BASE_PATH . '/views/partials/sidebar.php'; ?>
            </aside>

            <main id="contentPage" class="col-9">
                <?php include $contentFile; ?>
            </main>
        </div>
    </div>
    <!-- footer -->
    <?php include BASE_PATH . '/views/partials/footer.php'; ?>
    <!-- /footer -->
    <!-- cart_modal -->
    <?php include BASE_PATH . '/views/cart/cart_modal.php'; ?>
    <!-- /cart_modal -->
    <div id="notifier" class="notifier"></div>
</body>

</html>