<?php
namespace App\DB\Tools;

use App\DB\Contracts\CollectionContract;

class Collection implements CollectionContract {
    /** @var array Элементы коллекции */
    private array $items;

    /**
     * Создаёт коллекцию.
     *
     * @param array $items Массив элементов
     */
    public function __construct(array $items) {
        $this->items = $items;
    }

    /**
     * Возвращает все элементы коллекции.
     *
     * @return array Массив элементов
     */
    public function all(): array {
        return $this->items;
    }

    /**
     * Возвращает первый элемент коллекции.
     *
     * @return array|null Первый элемент или null, если коллекция пуста
     */
    public function first(): ?array {
        return $this->items[0] ?? null;
    }

    /**
     * Извлекает значения по ключу из всех элементов коллекции.
     *
     * @param string $key Ключ для выборки
     * @return Collection Новая коллекция значений
     */
    public function pluck(string $key): Collection {
        return new self(array_map(fn($item) => $item[$key] ?? null, $this->items));
    }

    /**
     * Применяет функцию ко всем элементам коллекции.
     *
     * @param callable $callback Функция обратного вызова
     * @return Collection Новая коллекция результатов
     */
    public function map(callable $callback): Collection {
        return new self(array_map($callback, $this->items));
    }

    /**
     * Фильтрует элементы коллекции по условию.
     *
     * @param callable $callback Функция обратного вызова, возвращающая true/false
     * @return Collection Новая коллекция отфильтрованных элементов
     */
    public function filter(callable $callback): Collection {
        return new self(array_values(array_filter($this->items, $callback)));
    }

    /**
     * Возвращает количество элементов коллекции.
     *
     * @return int Количество элементов
     */
    public function count(): int {
        return count($this->items);
    }

    /**
     * Проверяет, пуста ли коллекция.
     *
     * @return bool true если коллекция пуста, иначе false
     */
    public function isEmpty(): bool {
        return empty($this->items);
    }

    /**
     * Возвращает коллекцию в формате JSON.
     *
     * @param int $options Опции для json_encode
     * @return string JSON‑строка
     */
    public function toJson(int $options = 0): string {
        return json_encode($this->items, $options);
    }
}
