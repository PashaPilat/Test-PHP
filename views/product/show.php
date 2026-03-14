<!-- CONTENT -->
<div class="product-info_product-name">
    <h1 class="category_heading"><?= htmlspecialchars($product->name) ?></h1>

    <!--P_COMPARE and P_WISHLIST-->
    <div id="compare_wishlist" class="compare compare_wishlist">
        <!--P_COMPARE-->
        <span data-id="486" class="compare_button">
            <label for="compare_486" data-toggle="tooltip" data-placement="auto top" title="" data-original-title="Сравнить"><span>Сравнить</span></label>
        </span>
        <!--P_WISHLIST-->
        <span data-id="486" class="wishlisht_button">
            <label for="wishlist_486" data-toggle="tooltip" data-placement="auto top" title="" data-original-title="Желания">Желания</label>
        </span>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-9">
        <div class="row prod_main_info">
            <div class="col-sm-6 col-xs-12">
                <!-- PRODUCT INFO SLIDER -->
                <div class="slider_product_card">
                    <div class="additional_images2">
                        <div id="sync1" class="owl-carousel owl-theme owl-loaded" style="max-height:320px">
                            <img height="320" width="320" style="max-height:320px" src="<?= $product->image ?? '/assets/images/no_category.png'  ?>" alt="<?= htmlspecialchars($product->name) ?>">
                        </div>
                    </div>
                </div>
                <!-- END PRODUCT INFO SLIDER -->
            </div>

            <!-- PRODUCT INFO DESCRIPTION -->
            <div class="col-sm-6 col-xs-12">
                <div class="description_card_product row">
                    <!--P_MODEL-->
                    <div class="col-sm-4 first_row">
                        <span class="art_card_product"><?= $product->id ?></span>
                    </div>
                    <!--TEXT_FREE_SHIPPING-->

                    <!--P_ATTRIBUTES-->
                    <div class="prod_attributes col-sm-12">
                        <div class="prod_attributes_div">
                            <div class="attr_select">
                                <table class="prod_options" style="width:100%;border-spacing:0;padding:0;">
                                    <tbody>
                                        <tr>
                                            <td class="left_td">Цвет:</td>
                                            <td>
                                                <div id="info" class="color_attributes attributes_list_type">
                                                    <?php foreach ($product->colors as $index => $color): ?>
                                                        <label class="attributes_list color_attributes-item <?= $index === 0 ? 'active' : '' ?>">
                                                            <input type="radio"
                                                                name="color"
                                                                value="<?= $color['id'] ?>"
                                                                <?= $index === 0 ? 'checked' : '' ?>>
                                                            <div class="attribute-description">
                                                                <div><?= \App\Core\View::escape($color['value']); ?></div>
                                                            </div>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <div class="product-buy-price col-sm-12">
                        <div class="prod_price">
                            <span id="summ_price"><span class="new_price_card_product">$<?= $product->price ?></span></span>
                        </div>

                        <div class="prod_buy_btns">
                            <div class="product-quantity-selector">
                                <span class="quantity-selector-mask">
                                    <input type="number" id="" class="input-text qty text" max="999" name="cart_quantity" value="1" size="4" pattern="[0-9]*" inputmode="numeric" aria-labelledby="" min="1" step="1">
                                </span>
                            </div>
                            <div id="r_buy_intovar" class="pre_orders" data-id="486">
                                <button class="btn btn-warning buy-btn"
                                    data-name="<?= htmlspecialchars($product->name) ?>"
                                    data-price="<?= $product->price ?>"
                                    data-id="<?= $product->id ?>">
                                    Купить
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--P_ATTRIBUTES TEXT-->
                    <div class="col-sm-12 col-xs-12">
                        <div class="prod_attributes p_attr_text">
                            <div class="prod_attributes_div">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PRODUCT INFO DESCRIPTION -->


                <!-- P_RATING  -->
                <div class="container_rating_likes">
                    <!-- RATING -->
                    <div class="rating_product">
                        <div>
                            <div class="rating_wrapper">
                                <div class="sp_rating">
                                    <div class="base">
                                        <div class="average" style="width: 0%;"></div>
                                    </div>
                                    <div class="status">
                                        <div class="score review_score">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="score<?= $i ?>"
                                                    data-val="<?= $i ?>"
                                                    class="<?= ($product->rating >= $i ? 'score-active' : '') ?>">
                                                </span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="quantity_rating">
                                <span><?= $product->views ?></span>&nbsp;просмотров</span>
                        </div>
                    </div>
                    <!-- END RATING -->
                </div>

                <!-- P_SHORT_DESCRIPTION  -->
                <div class="short-description">
                    <div class="h3">Краткое описание</div>
                    <p><?= htmlspecialchars($product->description) ?> </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABS -->

<div class="tab-content">
    <!-- P_TAB_DESCRIPTION -->
    <div id="tab-description" class="tab-pane tab-content-item active">
        <h2>Характеристики</h2>
        <table class="table table-striped table-bordered" width="100%">
            <tbody>
                <?php foreach ($product->attributes as $attr): ?>
                    <tr>
                        <td class="attr-name">
                            <?= \App\Core\View::escape($attr['description'] ?? $attr['name']); ?>
                        </td>
                        <td class="attr-value">
                            <?= \App\Core\View::escape($attr['value']); ?>
                            <?php if ($attr['type'] === 'number'): ?>
                                <!-- можно добавить единицы измерения, если они есть -->
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>