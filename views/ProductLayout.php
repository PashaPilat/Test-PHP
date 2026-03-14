<!DOCTYPE html>
<html lang="ru">
<?php include BASE_PATH . '/views/partials/head.php'; ?>

<body class="bg-light">
    <?php include BASE_PATH . '/views/partials/header.php'; ?>
    <main>
        <div class="container">
            <div class="row"> <!-- CENTER CONTENT -->
                <div class="col-xs-12 padd-0 right_content">
                    <?php //include BASE_PATH . '/views/partials/breadcrumb.php'; ?>
                    <?php include $contentFile; ?>
                </div>
                <!-- END CENTER CONTENT -->
                <!-- COLUMN LEFT -->
                <!-- END COLUMN LEFT -->
            </div>
        </div>
    </main>
    <!-- footer -->
    <?php include BASE_PATH . '/views/partials/footer.php'; ?>
    <!-- /footer -->
    <!-- cart_modal -->
    <?php include BASE_PATH . '/views/cart/cart_modal.php'; ?>
    <!-- /cart_modal -->
    <div id="notifier" class="notifier"></div>
</body>

</html>