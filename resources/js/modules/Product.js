import $ from 'jquery'
import CatalogState from './CatalogState'

/**
 * Class Catalog
 *
 * Управляет страницей товара:
 * - загрузка страницы
 * - работа с состоянием каталога
 * - обработка кнопок покупки
 */
export default class Catalog {

    constructor(core) {
        this.core = core
    }

    /**
     * Инициализация товара
     */
    init() {
        this.bindHeaderCategories()
    }

    /**
     * Загружает товар по slug
     */
    loadProducts(productSlug = null) {
        this.core.api.getProducts(productSlug).done((response) => {
            if (!response.success) {
                alert('Ошибка загрузки товаров')
                return
            }
            console.log(response);
            //$('#product-list').html(response.products_html)
            //$('.catalog-title').text(response.title)
        })
    }
    
    bindHeaderCategories() {
        const core = this.core
        $(document).on('click', '.add_nav a', function (e) {
            const url = $(this).attr('href')
            if (!url.startsWith('/catalog')) return
            e.preventDefault()
            const slug = url.split('/').pop()
            core.catalog.loadProducts(slug)
        })
    }


}