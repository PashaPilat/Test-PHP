<?php
use App\DB\Migration;
use App\DB\Blueprint;
use App\DB\Schema;

class CreateProductCategory extends Migration {
    public function up() {
        // Создание таблицы
        Schema::create('product_category', function (Blueprint $table) {
            $table->integer('product_id', 11, true, false, null, 'Товар');
            $table->integer('category_id', 11, true, false, null, 'Категория');
        });

        // Индексы и связи
        Schema::table('product_category', function (Blueprint $table) {
            $table->foreign('product_id', 'products', 'id', Blueprint::CASCADE, Blueprint::CASCADE, 'fk_product_category_product');
            $table->foreign('category_id', 'categories', 'id', Blueprint::CASCADE, Blueprint::CASCADE, 'fk_product_category_category');
            $table->unique(['product_id','category_id'], 'uniq_product_category');
        });
    }

    public function down() {
        Schema::dropIfExists('product_category');
    }
}
