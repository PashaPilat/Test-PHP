<?php
namespace App\Core;

/**
 * Class BaseModel
 *
 * Базовый класс для моделей.
 * Может содержать общие методы для работы с данными.
 */
abstract class BaseModel
{
    /**
     * Заполняет модель данными из массива.
     *
     * @param array $attributes Ассоциативный массив данных
     * @return void
     */
    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
