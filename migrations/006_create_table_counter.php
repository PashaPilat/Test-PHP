<?php

use App\DB\Migration;
use App\DB\Blueprint;
use App\DB\Schema;

class CreateTableCounter extends Migration
{
    public function up()
    {
        // Таблица counters
        Schema::create('counters', function (Blueprint $table) {
            $table->enum('entity_type', ['category', 'attribute'], null, 'Тип сущности');
            $table->integer('entity_id', 11, true, false, null, 'ID сущности');
            $table->integer('children_count', null, true, false, 0, 'Количество дочерних категорий (только для категорий)');
            $table->integer('products_count', null, true, false, 0, 'Количество товаров с данным атрибутом');
            $table->integer('variants_count', null, true, false, 0, 'Количество вариантов значений атрибута');
        });

        // Индексы
        Schema::table('counters', function (Blueprint $table) {
            $table->unique(['entity_type', 'entity_id'], 'uniq_entity');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
        });

        // Триггеры для категорий
        Schema::trigger(
            'increment_children_count',
            'AFTER',
            'INSERT',
            'categories',
            "IF NEW.parent_id IS NOT NULL AND NEW.parent_id != NEW.id THEN
                INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
                VALUES ('category', NEW.parent_id, 1, 0, 0)
                ON DUPLICATE KEY UPDATE children_count = children_count + 1;
            END IF;"
        );

        Schema::trigger(
            'decrement_children_count',
            'AFTER',
            'DELETE',
            'categories',
            "IF OLD.parent_id IS NOT NULL AND OLD.parent_id != OLD.id THEN
                INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
                VALUES ('category', OLD.parent_id, -1, 0, 0)
                ON DUPLICATE KEY UPDATE children_count = children_count - 1;
            END IF;"
        );

        Schema::trigger(
            'increment_products_count',
            'AFTER',
            'INSERT',
            'product_category',
            "INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
             VALUES ('category', NEW.category_id, 0, 1, 0)
             ON DUPLICATE KEY UPDATE products_count = products_count + 1;"
        );

        Schema::trigger(
            'decrement_products_count',
            'AFTER',
            'DELETE',
            'product_category',
            "INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
             VALUES ('category', OLD.category_id, 0, -1, 0)
             ON DUPLICATE KEY UPDATE products_count = products_count - 1;"
        );

        // Триггеры для атрибутов
        Schema::trigger(
            'increment_attribute_products_count',
            'AFTER',
            'INSERT',
            'product_attributes',
            "INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
             VALUES ('attribute', NEW.attribute_id, 0, 1, 0)
             ON DUPLICATE KEY UPDATE products_count = products_count + 1;"
        );

        Schema::trigger(
            'decrement_attribute_products_count',
            'AFTER',
            'DELETE',
            'product_attributes',
            "INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
             VALUES ('attribute', OLD.attribute_id, 0, -1, 0)
             ON DUPLICATE KEY UPDATE products_count = products_count - 1;"
        );

        Schema::trigger(
            'increment_attribute_variants_count',
            'AFTER',
            'INSERT',
            'product_attributes',
            "INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
             VALUES ('attribute', NEW.attribute_id, 0, 0, 1)
             ON DUPLICATE KEY UPDATE variants_count = variants_count + 1;"
        );

        Schema::trigger(
            'decrement_attribute_variants_count',
            'AFTER',
            'DELETE',
            'product_attributes',
            "INSERT INTO counters (entity_type, entity_id, children_count, products_count, variants_count)
             VALUES ('attribute', OLD.attribute_id, 0, 0, -1)
             ON DUPLICATE KEY UPDATE variants_count = variants_count - 1;"
        );

        // Представления
        Schema::view(
            'categories_with_count',
            "SELECT c.id, c.name, c.slug, c.path, c.parent_id, c.description, c.icon, c.status,
                COALESCE(cnt.children_count,0) children_count,
                COALESCE(cnt.products_count,0) products_count
            FROM categories c
            LEFT JOIN counters cnt ON cnt.entity_type='category' AND cnt.entity_id=c.id"
        );

        Schema::view(
            'attributes_with_count',
            "SELECT a.*,
                COALESCE(cnt.products_count,0) products_count,
                COALESCE(cnt.variants_count,0) variants_count
            FROM attributes a
            LEFT JOIN counters cnt ON cnt.entity_type='attribute' AND cnt.entity_id=a.id"
        );
    }

    public function down()
    {
        Schema::dropTrigger('increment_products_count');
        Schema::dropTrigger('decrement_products_count');
        Schema::dropTrigger('increment_children_count');
        Schema::dropTrigger('decrement_children_count');
        Schema::dropTrigger('increment_attribute_products_count');
        Schema::dropTrigger('decrement_attribute_products_count');
        Schema::dropTrigger('increment_attribute_variants_count');
        Schema::dropTrigger('decrement_attribute_variants_count');

        Schema::dropView('categories_with_count');
        Schema::dropView('attributes_with_count');

        Schema::dropIfExists('counters');
    }
}
