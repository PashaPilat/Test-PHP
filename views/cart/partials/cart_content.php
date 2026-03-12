<?php if (empty($products)): ?>
    <p>Ваша корзина пуста!</p>
<?php else: ?>
    <div id="cartContent-page" class="container">
        <div class="row cartContent_title hidden-xs">
            <div class="col-sm-2">Изображение</div>
            <div class="col-md-5 col-sm-4 name">Название</div>
            <div class="col-md-1 col-sm-2 numeric numeric_price">Цена</div>
            <div class="col-md-2 col-sm-1 numeric numeric_qua">Кол-во</div>
            <div class="col-md-2 col-sm-3 numeric numeric_total">Сумма</div>
        </div>

        <?php foreach ($products as $product): ?>
            <div class="row cartContent_body">
                <div class="col-sm-2 col-xs-12 product_image">
                    <img src="<?= htmlspecialchars($product->image ?? '/assets/images/no_product.png') ?>"
                        class="img-responsive" alt="<?= htmlspecialchars($product->name) ?>">
                </div>
                <div class="col-md-5 col-sm-4 col-xs-12 product_name">
                    <a href="/product/<?= htmlspecialchars($product->slug) ?>"><?= htmlspecialchars($product->name) ?></a>
                </div>

                <div class="col-md-5 col-sm-6 col-xs-12 cartContent_body-numeric">
                    <div class="row">
                        <div class="col-xs-3 product_price">$<?= number_format($product->price, 2) ?> </div>
                        <div class="col-xs-4 product_qty">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default qty-minus" data-id="<?= $product->id ?>">-</button>
                                </span>
                                <input type="text" value="<?= $product->qty ?>"
                                    class="form-control inputnumber" step="1" min="1" max="100000"
                                    data-id="<?= $product->id ?>"
                                    style="text-align: center;" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default qty-plus" data-id="<?= $product->id ?>">+</button>
                                </span>
                            </div>
                            <div style="clear:both"></div>
                        </div>
                        <div class="col-xs-3 product_total">$<?= number_format($product->price * $product->qty, 2) ?></div>
                        <div class="col-xs-2 product_delete">

                            <button class="remove-btn btn btn-sm btn-danger" title="Удалить из корзины" data-id="<?= $product->id ?>" type="button">
                                <svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="row total-block">
            <div class="col-sm-12 text-right">
                <div id="cart_order_total">
                    Итого: <b>
                        $<?= number_format(array_sum(array_map(fn($p) => $p->price * $p->qty, $products)), 2) ?>
                    </b>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>