import $ from 'jquery'
import CatalogState from './CatalogState'

/**
 * Class Catalog
 *
 * Управляет каталогом товаров:
 * - загрузка товаров
 * - работа с состоянием каталога
 * - обработка кнопок покупки
 */
export default class Catalog {

    constructor(core) {
        this.core = core
        this.state = new CatalogState()
    }

    /**
     * Инициализация каталога
     */
    init() {
        const activeCategory = $('#categoryList .category-item.active').data('slug')
        if (activeCategory) {
            this.state.setCategory(activeCategory)
        }
        console.log(activeCategory);
        this.bindPagination()
        this.bindHeaderCategories()
        this.bindSort()
    }

    /**
     * Загружает стр каталога по слуг
     */
    loadCategory(categorySlug = null) {
        if (categorySlug) {
            this.state.setCategory(categorySlug)
        }
        const params = this.state.getParams()

        // проверка корректности параметров
        if (!params || typeof params !== 'object') {
            console.error('Некорректные параметры для загрузки каталога', params)
            return
        }

        // если выбрана категория → грузим категорию
        if (this.state.state.category) {
            this.core.api.getCategories(params).done((response) => {
                if (!response || !response.success) {
                    alert('Ошибка загрузки товаров')
                    return
                }
                document.title = response.title || 'Каталог'
                $('#sidebar').html(response.sidebar_html || '')
                $('#product-list').html(response.products_html || '')
                this.core.history.push(this.state.state)
                this.core.filters.initPriceSlider()
            })
        } else {
            // главная страница → грузим все товары
            this.core.api.getProducts(params).done((response) => {
                if (!response || !response.success) {
                    alert('Ошибка загрузки товаров')
                    return
                }
                document.title = response.title || 'Каталог'
                $('#product-list').html(response.products_html || '')
                this.core.history.push(this.state.state)
            })
        }
    }

    /**
     * Восстанавливает состояние (назад/вперёд)
     */
    restoreState(state) {
        this.state.setState(state)
        const params = this.state.getParams()
        this.core.api.getProducts(params).done((response) => {
            $('#product-list').html(response.products_html)
            $('.catalog-title').text(response.title)
        })
    }

    /**
     * Устанавливает состояние каталога полностью.
     *
     * Используется при восстановлении истории (History API),
     * когда пользователь нажимает "назад/вперёд".
     *
     * @param {Object} state
     */
    setState(state) {
        this.state.category = state.category ?? null
        this.state.page = state.page ?? 1
        this.state.sort = state.sort ?? 'default'
        this.state.filters = state.filters ?? {}
    }

    /**
     * Устанавливает страницу
     */
    setPage(page) {
        this.state.setPage(page)
        this.loadCategory()
    }

    /**
     * Устанавливает сортировку
     */
    setSort(sort) {
        this.state.setSort(sort)
        this.loadCategory()
    }

    bindSort() {
        const core = this.core
        $(document).on('change', '#catalog-sort', function () {
            const sort = $(this).val()
            core.catalog.setSort(sort)
        })
    }

    /**
     * Добавляет фильтр
     */
    setFilter(name, value) {
        this.state.setFilter(name, value)
        this.loadCategory()
    }
    bindHeaderCategories() {
        const core = this.core
        $(document).on('click', '.add_nav a', function (e) {
            const url = $(this).attr('href')
            if (!url.startsWith('/catalog')) return
            e.preventDefault()
            const slug = url.split('/').pop()
            core.catalog.loadCategory(slug)
        })
    }

    /*
    * пагинация
    */
    bindPagination() {
        const core = this.core
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault()
            const page = $(this).data('page')
            if (!page) return
            core.catalog.setPage(page)
        })
    }

}