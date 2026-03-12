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

    getProducts(slug) {
        return this.request('/api/products/' + slug)
    }

    getCategories(params) {
        return this.request('/api/categories', params)
    }

    updateProductCart(params) {
        return this.request('/api/cart', params)
    }

}