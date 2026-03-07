<?php
namespace App\DB\Contracts;

interface CollectionContract {
    /**
     * Возвращает все элементы коллекции.
     *
     * @return array Массив элементов
     */
    public function all(): array;

    /**
     * Возвращает первый элемент коллекции.
     *
     * @return array|null Первый элемент или null, если коллекция пуста
     */
    public function first(): ?array;

    /**
     * Извлекает значения по ключу из всех элементов коллекции.
     *
     * @param string $key Ключ для выборки
     * @return CollectionContract Новая коллекция значений
     */
    public function pluck(string $key): CollectionContract;

    /**
     * Применяет функцию ко всем элементам коллекции.
     *
     * @param callable $callback Функция обратного вызова
     * @return CollectionContract Новая коллекция результатов
     */
    public function map(callable $callback): CollectionContract;

    /**
     * Фильтрует элементы коллекции по условию.
     *
     * @param callable $callback Функция обратного вызова, возвращающая true/false
     * @return CollectionContract Новая коллекция отфильтрованных элементов
     */
    public function filter(callable $callback): CollectionContract;

    /**
     * Возвращает количество элементов коллекции.
     *
     * @return int Количество элементов
     */
    public function count(): int;

    /**
     * Проверяет, пуста ли коллекция.
     *
     * @return bool true если коллекция пуста, иначе false
     */
    public function isEmpty(): bool;

    /**
     * Возвращает коллекцию в формате JSON.
     *
     * @param int $options Опции для json_encode
     * @return string JSON‑строка
     */
    public function toJson(int $options = 0): string;
}
