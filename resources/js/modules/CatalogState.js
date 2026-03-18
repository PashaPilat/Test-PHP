/**
 * Class CatalogState
 *
 * Хранит текущее состояние каталога:
 * категория, фильтры, страница, сортировка.
 * Используется Catalog модулем для построения запросов.
 */
export default class CatalogState {

    constructor() {
        this.state = {
            category: null,
            page: 1,
            sort: 'default',
            filters: {}
        }
    }

    /**
     * Устанавливает категорию
     */
    setCategory(category) {
        this.state.category = category
        this.state.page = 1
    }

    /**
     * Устанавливает страницу
     */
    setPage(page) {
        this.state.page = page
    }

    /**
     * Устанавливает сортировку
     */
    setSort(sort) {
        this.state.sort = sort
    }

    /**
     * Добавляет фильтр
     */
    setFilter(name, value) {
        this.state.filters[name] = value
        this.state.page = 1
    }

    /**
     * Удаляет фильтр
     */
    removeFilter(name) {
        delete this.state.filters[name]
    }

    /**
     * Возвращает объект параметров для API
     */
    getParams() {
        return {
            category: this.state.category,
            page: this.state.page,
            sort: this.state.sort,
            filters: this.state.filters
        }
    }
    /**
     * Восстановить состояние из объекта
     */
    setState(state) {
        this.state = {
            category: state.category ?? null,
            page: state.page ?? 1,
            sort: state.sort ?? 'default',
            filters: state.filters ?? {}
        }
    }
}