<?php
use App\DB\Migration;
use App\DB\Blueprint;
use App\DB\Schema;

class CreateProductAttributes extends Migration {
    public function up() {
        // Создание таблицы
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id(11); // PK, уникальный идентификатор записи
            $table->integer('product_id', 11, true, false, null, 'ID товара');     // FK → products.id
            $table->integer('attribute_id', 11, true, false, null, 'ID атрибута'); // FK → attributes.id
            $table->string('value', 512, false, null, 'Значение атрибута');
            $table->text('description', true, null, 'Описание значения атрибута');
            $table->enum('status', ['active','inactive'], 'active', 'Статус значения атрибута');
        });

        // Внешние ключи и индексы
        Schema::table('product_attributes', function (Blueprint $table) {
            $table->foreign('product_id', 'products', 'id', Blueprint::CASCADE, Blueprint::CASCADE, 'fk_product_attr_product');
            $table->foreign('attribute_id', 'attributes', 'id', Blueprint::CASCADE, Blueprint::CASCADE, 'fk_product_attr_attribute');

            $table->index('product_id', 'idx_product_attr_product');
            $table->index('attribute_id', 'idx_product_attr_attribute');
            $table->index('status', 'idx_product_attr_status');
        });
    }

    public function down() {
        Schema::dropIfExists('product_attributes');
    }
}
