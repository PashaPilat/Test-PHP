<?php
use App\DB\Migration;
use App\DB\Blueprint;
use App\DB\Schema;

class CreateAttributes extends Migration {
    public function up() {
        // Создание таблицы
        Schema::create('attributes', function (Blueprint $table) {
            $table->id(11); // PK, уникальный идентификатор атрибута
            $table->string('name', 255, false, null, 'Название атрибута');      // обязательное поле
            $table->string('slug', 255, false, null, 'Алиас/Slug атрибута');    // обязательное поле
            $table->text('description', true, null, 'Описание атрибута');
            $table->enum('type', ['string','number','boolean'], 'string', 'Тип значения атрибута');
            $table->enum('status', ['active','inactive'], 'active', 'Статус атрибута');
            $table->timestamps(); // created_at, updated_at
        });

        // Индексы
        Schema::table('attributes', function (Blueprint $table) {
            $table->index('slug', 'idx_attribute_slug');
            $table->index('status', 'idx_attribute_status');
        });

        // Триггеры для временных меток
        Schema::timestampTriggers('attributes');
    }

    public function down() {
        Schema::dropIfExists('attributes');
    }
}
