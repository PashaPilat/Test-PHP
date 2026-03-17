<?php

use App\DB\Migration;
use App\DB\Blueprint;
use App\DB\Schema;

/**
 * Class CreateTableCounter
 *
 * Миграция для создания таблицы counters.
 * Хранит агрегированные данные для категорий и атрибутов:
 * - количество дочерних категорий
 * - количество товаров
 * - количество вариантов значений
 * - минимальная и максимальная цена товаров
 * - JSON со значениями атрибутов
 * * Поддерживает два типа строк:
 *
 * 1. category
 *    entity_type = category
 *    entity_id   = NULL
 *    category_id = id категории
 *
 * 2. attribute
 *    entity_type = attribute
 *    entity_id   = id атрибута
 *    category_id = id категории
 *
 * counters используется как быстрый индекс каталога
 * для построения фильтров и статистики.
 */
class CreateTableCounter extends Migration
{
    public function up()
    {
        // ------------------------------
        // Таблица counters
        // ------------------------------
        Schema::create('counters', function (Blueprint $table) {
            $table->enum('entity_type', ['category', 'attribute'], null, 'Тип сущности');
            $table->integer('entity_id', 11, true, true, 0, 'ID атрибута');
            $table->integer('category_id', 11, true, true, 0, 'ID категории');
            // Счётчики
            $table->integer('children_count', null, true, false, 0, 'Количество дочерних категорий (только для категорий)');
            $table->integer('products_count', null, true, false, 0, 'Количество товаров в этой категории');
            $table->integer('attributes_count', null, true, false, 0, 'для атрибута = количество id значений атрибута, для категории = это количество атрибутов в категории');

            // Диапазон цен для категории
            $table->decimal('min_price', 10, 2, true,  0, 'Минимальная цена товаров (только для категории)');
            $table->decimal('max_price', 10, 2, true,  0, 'Максимальная цена товаров (только для категории)');

            // JSON со значениями атрибутов
            $table->json('values_json', true, 'Список уникальных id значений атрибута(для категори) или id value(для атрибутов) в формате JSON');
        });

        // ------------------------------
        // Индексы
        // ------------------------------
        Schema::table('counters', function (Blueprint $table) {
            // уникальность строки
            $table->unique(['entity_type', 'entity_id', 'category_id'], 'uniq_counter');
            // быстрый поиск атрибутов категории
            $table->index(['entity_type', 'category_id'], 'idx_category');
        });

        // ------------------------------
        // Триггеры для категорий
        // ------------------------------

        // Увеличение children_count при добавлении категории
        Schema::trigger(
            'counter_category_children_inc',
            'AFTER',
            'INSERT',
            'categories',
            "IF NEW.parent_id IS NOT NULL AND NEW.parent_id != NEW.id THEN
                INSERT INTO counters (entity_type,entity_id,category_id,children_count)
                VALUES ('category',0,NEW.parent_id,1)
                ON DUPLICATE KEY UPDATE children_count = children_count + 1;
            END IF;"
        );

        // Уменьшение children_count при удалении категории
        Schema::trigger(
            'counter_category_children_dec',
            'AFTER',
            'DELETE',
            'categories',
            "IF OLD.parent_id IS NOT NULL AND OLD.parent_id != OLD.id THEN
                UPDATE counters
                SET children_count = GREATEST(children_count - 1, 0)
                WHERE entity_type='category'
                AND entity_id=0
                AND category_id = OLD.parent_id;
            END IF;"
        );

        // =================================================
        // ТРИГГЕРЫ ТОВАРОВ В КАТЕГОРИИ
        // =================================================

        // Увеличение products_count при добавлении товара в категорию
        Schema::trigger(
            'counter_category_product_inc',
            'AFTER',
            'INSERT',
            'product_category',
            "INSERT INTO counters (entity_type, entity_id, category_id, products_count, min_price, max_price)
        SELECT 'category',0,NEW.category_id,1,p.price,p.price
        FROM products p WHERE p.id = NEW.product_id
        ON DUPLICATE KEY UPDATE
            products_count = products_count + 1,
            min_price = LEAST(COALESCE(min_price,p.price),p.price),
            max_price = GREATEST(COALESCE(max_price,p.price),p.price);"
        );

        // Уменьшение products_count при удалении товара из категории
        Schema::trigger(
            'counter_category_product_dec',
            'AFTER',
            'DELETE',
            'product_category',
            "UPDATE counters
        SET products_count = GREATEST(products_count - 1, 0)
        WHERE entity_type='category'
          AND entity_id=0
          AND category_id = OLD.category_id;"
        );


        // ------------------------------
        // Триггеры для атрибутов
        // ------------------------------


        // удаление атрибута
        /*
        Schema::trigger(
            'counter_attribute_delete',
            'AFTER',
            'DELETE',
            'attributes',
            "DELETE FROM counters
                WHERE entity_type='attribute'
                AND entity_id = OLD.id
                AND category_id = OLD.category_id;

                -- обновляем категорию
                UPDATE counters
                SET
                values_json = JSON_REMOVE(values_json,CONCAT('$.\"',OLD.id,'\"')),
                attributes_count = JSON_LENGTH(
                    JSON_REMOVE(values_json,CONCAT('$.\"',OLD.id,'\"'))
                )
                WHERE entity_type='category'
                AND category_id = OLD.category_id;"
        );
*/
        
        // добавление значения атрибута
        Schema::trigger(
            'counter_attribute_value_insert',
            'AFTER',
            'INSERT',
            'product_attributes',
            "
        DECLARE done INT DEFAULT 0;
        DECLARE cat_id INT;
        DECLARE attr_exists INT DEFAULT 0;
        DECLARE val_exists INT DEFAULT 0;
        DECLARE cur CURSOR FOR
            SELECT category_id FROM product_category WHERE product_id = NEW.product_id;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cur;
        read_loop: LOOP
            FETCH cur INTO cat_id;
            IF done = 1 THEN
                LEAVE read_loop;
            END IF;

            -- 1. проверка: есть ли строка для атрибута
            SELECT COUNT(*) 
            INTO attr_exists
            FROM counters
            WHERE entity_type='attribute'
              AND entity_id = NEW.attribute_id
              AND category_id = cat_id;

            IF attr_exists = 0 THEN
                INSERT INTO counters(entity_type,entity_id,category_id,products_count,attributes_count,values_json)
                VALUES('attribute',NEW.attribute_id,cat_id,0,0,JSON_OBJECT());
            END IF;

            -- 2. проверка: есть ли value в JSON
            SELECT MAX(JSON_CONTAINS_PATH(values_json,'one',CONCAT('$.\"',NEW.id,'\"')))
            INTO val_exists
            FROM counters
            WHERE entity_type='attribute'
              AND entity_id = NEW.attribute_id
              AND category_id = cat_id;

            IF val_exists = 0 THEN
                UPDATE counters
                SET
                    products_count = products_count + 1,
                    values_json = JSON_SET(
                        COALESCE(values_json,JSON_OBJECT()),
                        CONCAT('$.\"',NEW.id,'\"'),
                        TRUE
                    ),
                    attributes_count = JSON_LENGTH(
                        JSON_SET(
                            COALESCE(values_json,JSON_OBJECT()),
                            CONCAT('$.\"',NEW.id,'\"'),
                            TRUE
                        )
                    )
                WHERE entity_type='attribute'
                  AND entity_id = NEW.attribute_id
                  AND category_id = cat_id;
            END IF;

            -- 3. проверка: есть ли сам атрибут в JSON категории
            SELECT MAX(JSON_CONTAINS_PATH(values_json,'one',CONCAT('$.\"',NEW.attribute_id,'\"')))
            INTO val_exists
            FROM counters
            WHERE entity_type='category'
              AND category_id = cat_id;

            IF val_exists = 0 THEN
                UPDATE counters
                SET
                    values_json = JSON_SET(
                        COALESCE(values_json,JSON_OBJECT()),
                        CONCAT('$.\"',NEW.attribute_id,'\"'),
                        TRUE
                    ),
                    attributes_count = JSON_LENGTH(
                        JSON_SET(
                            COALESCE(values_json,JSON_OBJECT()),
                            CONCAT('$.\"',NEW.attribute_id,'\"'),
                            TRUE
                        )
                    )
                WHERE entity_type='category'
                  AND category_id = cat_id;
            END IF;

        END LOOP;
        CLOSE cur;
    "
        );

        // удаление значения атрибута
        Schema::trigger(
            'counter_attribute_value_delete',
            'AFTER',
            'DELETE',
            'product_attributes',
            "
        DECLARE done INT DEFAULT 0;
        DECLARE cat_id INT;
        DECLARE val_exists INT DEFAULT 0;
        DECLARE cur CURSOR FOR
            SELECT category_id FROM product_category WHERE product_id = OLD.product_id;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

        OPEN cur;
        read_loop: LOOP
            FETCH cur INTO cat_id;
            IF done = 1 THEN
                LEAVE read_loop;
            END IF;

            -- 1. проверка: есть ли value в JSON атрибута
            SELECT JSON_CONTAINS_PATH(values_json,'one',CONCAT('$.\"',OLD.id,'\"'))
            INTO val_exists
            FROM counters
            WHERE entity_type='attribute'
              AND entity_id = OLD.attribute_id
              AND category_id = cat_id LIMIT 1;

            IF val_exists = 1 THEN
                UPDATE counters
                SET
                    products_count = IF(products_count > 0, products_count - 1, 0),
                    values_json = JSON_REMOVE(values_json,CONCAT('$.\"',OLD.id,'\"')),
                    attributes_count = JSON_LENGTH(
                        JSON_REMOVE(values_json,CONCAT('$.\"',OLD.id,'\"'))
                    )
                WHERE entity_type='attribute'
                  AND entity_id = OLD.attribute_id
                  AND category_id = cat_id;
            END IF;

            -- 2. проверка: если значений нет — удаляем строку атрибута
            DELETE FROM counters
            WHERE entity_type='attribute'
              AND entity_id = OLD.attribute_id
              AND category_id = cat_id
              AND (attributes_count IS NULL OR attributes_count <= 0);

            -- 3. проверка: если атрибут удалён — убираем его из JSON категории
            IF (SELECT JSON_CONTAINS_PATH(values_json,'one',CONCAT('$.\"',OLD.attribute_id,'\"'))
                FROM counters
                WHERE entity_type='category'
                  AND category_id = cat_id) = 1 THEN
                UPDATE counters
                SET
                    values_json = JSON_REMOVE(values_json,CONCAT('$.\"',OLD.attribute_id,'\"')),
                    attributes_count = JSON_LENGTH(
                        JSON_REMOVE(values_json,CONCAT('$.\"',OLD.attribute_id,'\"'))
                    )
                WHERE entity_type='category'
                  AND category_id = cat_id;
            END IF;

        END LOOP;
        CLOSE cur;
    "
        );

        // ------------------------------
        // Представления
        // ------------------------------

        // Категории с подсчётами
        Schema::view(
            'categories_with_count',
            "SELECT
        c.id,
        c.name,
        c.slug,
        c.path,
        c.parent_id,
        c.description,
        c.icon,
        c.status,
        COALESCE(SUM(cnt.children_count),0) AS children_count,
        COALESCE(SUM(cnt.products_count),0) AS products_count,
        COALESCE(SUM(cnt.attributes_count),0) AS attributes_count,
        MIN(cnt.min_price) AS min_price,
        MAX(cnt.max_price) AS max_price
    FROM categories c
    LEFT JOIN counters cnt
        ON cnt.entity_type='category'
        AND cnt.category_id=c.id
    GROUP BY c.id, c.name, c.slug, c.path, c.parent_id, c.description, c.icon, c.status"
        );

        // Атрибуты с подсчётами
        Schema::view(
            'attributes_with_count',
            "SELECT
        a.id,
        a.name,
        a.slug,
        a.description,
        a.type,
        a.status,
        cnt.category_id,
        SUM(cnt.products_count) AS products_count,
        SUM(cnt.attributes_count) AS attributes_count,
        JSON_ARRAYAGG(cnt.values_json) AS values_json
    FROM attributes a
    JOIN counters cnt
        ON cnt.entity_type='attribute'
        AND cnt.entity_id=a.id
    GROUP BY a.id, a.name, a.slug, a.description, a.type, cnt.category_id"
        );
    }

    public function down()
    {
        // Удаление триггеров
        Schema::dropTrigger('increment_products_count');
        Schema::dropTrigger('decrement_products_count');
        Schema::dropTrigger('increment_children_count');
        Schema::dropTrigger('decrement_children_count');
        Schema::dropTrigger('increment_attribute_products_count');
        Schema::dropTrigger('decrement_attribute_products_count');
        Schema::dropTrigger('increment_attribute_variants_count');
        Schema::dropTrigger('decrement_attribute_variants_count');

        // Удаление представлений
        Schema::dropView('categories_with_count');
        Schema::dropView('attributes_with_count');

        // Удаление таблицы
        Schema::dropIfExists('counters');
    }
}
