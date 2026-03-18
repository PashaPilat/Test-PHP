<?php if (!empty($filters['price']) || !empty($filters['attributes'])): ?>
    <div id="filters_box" class="box filter_box">
        <div class="filter_box_in">

            <!-- Сброс -->
            <?php if (!empty($filters['price']) || !empty($filters['attributes'])): ?>
                <a rel="nofollow" class="filter_heading filter_reset_link"
                    href="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                    Сбросить фильтры
                </a>
            <?php endif; ?>

            <!-- Цена -->
            <?php if (!empty($filters['price']) && !empty($filters['price']['category_min']) && !empty($filters['price']['category_max'])): ?>
                <div class="dipcen mt-3">
                    <div><span class="filter_heading">Цена</span></div>
                    <div id="slider-range"></div>
                    <span class="left slider-from">
                        <input type="number"
                            min="<?= htmlspecialchars($filters['price']['category_min']) ?>"
                            max="<?= htmlspecialchars($filters['price']['category_max']) ?>"
                            class="input-type-number-custom"
                            name="rmin"
                            id="range1"
                            value="<?= htmlspecialchars($filters['price']['min']) ?>">
                    </span>
                    <span class="left slider-to">
                        <input type="number"
                            min="<?= htmlspecialchars($filters['price']['category_min']) ?>"
                            max="<?= htmlspecialchars($filters['price']['category_max']) ?>"
                            class="input-type-number-custom"
                            name="rmax"
                            id="range2"
                            value="<?= htmlspecialchars($filters['price']['max']) ?>">
                    </span>
                    &nbsp;&nbsp;<span class="price_fltr">$</span>
                </div>
                <div class="clear"></div>
            <?php endif; ?>

            <!-- Атрибуты -->
            <?php if (!empty($filters['attributes'])): ?>
                <div class="attrib_divs attrib_divs_mobil ajax" id="attribs">
                    <?php foreach ($filters['attributes'] as $attribute): ?>
                        <?php if (!empty($attribute['values'])): ?>
                            <div class="block">
                                <div class="filter_heading toggle-filter d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($attribute['name']) ?>
                                    <span class="filter-arrow">▸</span>
                                </div>
                                <div class="inner-scroll filter-content">
                                    <!-- чекбокс "все" -->
                                    <div class="item">
                                        <input class="filter_all" type="checkbox"
                                            id="filter_all_<?= $attribute['id'] ?>"
                                            name="<?= $attribute['id'] ?>" value="not"
                                            <?= empty(array_filter($attribute['values'], fn($v) => !empty($v['selected']))) ? 'checked' : '' ?>>
                                        <label for="filter_all_<?= $attribute['id'] ?>">все</label>
                                    </div>

                                    <!-- значения -->
                                    <?php foreach ($attribute['values'] as $val): ?>
                                        <div class="item">
                                            <input type="checkbox"
                                                id="attr<?= $attribute['id'] ?>_<?= htmlspecialchars($val['title']) ?>"
                                                name="<?= $attribute['id'] ?>"
                                                value="<?= implode(',', $val['value']) ?>"
                                                <?= !empty($val['selected']) ? 'checked' : '' ?>>
                                            <label for="attr<?= $attribute['id'] ?>_<?= htmlspecialchars($val['title']) ?>">
                                                <?= htmlspecialchars($val['title']) ?>
                                                <?php if (!empty($val['qty'])): ?>
                                                    <span class="qty"><?= $val['qty'] ?></span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
<?php endif; ?>