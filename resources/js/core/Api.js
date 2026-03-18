import $ from 'jquery'

export default class Api {

    request(url, data = {}) {
        return $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: 'json'
        })
    }

    // если slug есть → грузим конкретную категорию товаров
    // если slug == null → грузим все товары (главная)
    getProducts(params) {
        return this.request('/api/products', params)
    }

    getCategories(params) {
        return this.request('/api/categories', params)
    }

    updateProductCart(params) {
        return this.request('/api/cart', params)
    }
}
