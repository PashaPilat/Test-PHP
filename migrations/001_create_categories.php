<?php
use App\DB\Migration;
use App\DB\Blueprint;
use App\DB\Schema;
use App\DB;

class CreateCategories extends Migration {
    public function up() {
        // Создание таблицы с parent_id nullable
        Schema::create('categories', function (Blueprint $table) {
            $table->id(11); // PK, уникальный идентификатор категории
            $table->string('name', 255, false, null, 'Название категории');
            $table->string('slug', 255, true, null, 'Слаг категории',true);
            $table->string('path', 255, true, null, 'путь от корня',true);
            $table->integer('parent_id', 11, true, true, 1, 'Родительская категория'); // FK → categories.id, временно допускаем NULL
            $table->text('description', true, null, 'Описание категории');
            $table->string('icon', 255, true, null, 'Путь к картинке/иконке');
            $table->enum('status', ['active','inactive'], 'active'); // Статус категории
            $table->timestamps(); // created_at, updated_at
        });
        // Индексы и связи
        Schema::table('categories', function (Blueprint $table) {
            $table->index('status', 'idx_category_status');
            $table->index('slug', 'idx_category_slug');
            $table->index('parent_id', 'idx_category_parent');
        });

        // Триггеры для временных меток
        Schema::timestampTriggers('categories');
        
        // Сидируем корневую категорию
        DB::insert(['id' => 1, 'name' => 'Root', 'slug' => 'root', 'path' => '1', 'parent_id' => 1, 'description' => 'Корневая категория'])->into('categories')->exec();
        // Теперь делаем parent_id обязательным и добавляем FK
        Schema::table('categories', function (Blueprint $table) {
            $table->integer('parent_id', 11, true, false, 1, 'Родительская категория');
            $table->foreign('parent_id', 'categories', 'id', Blueprint::CASCADE, Blueprint::CASCADE, 'fk_categories_parent');
        });
    }

    public function down() {
        Schema::dropIfExists('categories');
    }
}
