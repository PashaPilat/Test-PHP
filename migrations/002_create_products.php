<?php
use App\DB\Migration;
use App\DB\Blueprint;
use App\DB\Schema;
use App\DB;

class CreateProducts extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // PK, уникальный идентификатор товара
            $table->string('name', 255, false, null, 'Название товара');    // обязательное поле
            $table->string('slug', 255, false, null, 'Алиас/Slug для URL'); // обязательное поле
            $table->text('description', true, null, 'Описание товара');
            $table->string('image', 255, true, null, 'Путь к картинке товара');

            $table->decimal('price', 10, 2, false, '0.00', 'Цена товара');   // обязательное поле
            $table->decimal('old_price', 10, 2, true, null, 'Старая цена товара'); // может быть NULL
            $table->enum('status', ['active','inactive'], 'active', 'Статус товара');

            // Дополнительные стандартные поля со встроенными комментариями
            $table->integer('views', 10, true, false, 0, 'Количество просмотров');
            $table->integer('purchases', 10, true, false, 0, 'Количество покупок');
            $table->integer('likes', 10, true, false, 0, 'Количество лайков');
            $table->integer('favorites', 10, true, false, 0, 'Количество добавлений в избранное');
            $table->decimal('rating', 3, 2, true, '0.00', 'Рейтинг товара'); // необязательное, дефолт 0
            $table->integer('rating_count', 10, true, false, 0, 'Количество оценок товара'); // необязательное, дефолт 0

            $table->timestamps(); // created_at, updated_at
        });
         // Индексы
        Schema::table('products', function (Blueprint $table) {
            $table->index('slug', 'idx_product_slug');
            $table->index('status', 'idx_product_status');
            $table->index('price', 'idx_product_price');
            $table->index('old_price', 'idx_product_old_price');
            $table->index('created_at', 'idx_product_created');
            $table->index('views', 'idx_product_views');
            $table->index('purchases', 'idx_product_purchases');
            $table->index('likes', 'idx_product_likes');
            $table->index('favorites', 'idx_product_favorites');
            $table->index('rating', 'idx_product_rating');
            $table->index('rating_count', 'idx_product_rating_count');
        });
        // Триггеры для временных меток
        Schema::timestampTriggers('products'); 
    }

    public function down() {
        Schema::dropIfExists('products');
    }
}
