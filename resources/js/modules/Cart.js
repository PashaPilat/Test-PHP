import $ from 'jquery'
import { Modal } from 'bootstrap'
import { notify } from '../components/notifier'

export default class Cart {
    constructor(core) {
        this.core = core
        this.cart = JSON.parse(localStorage.getItem('cart') || '[]')
    }
    init() {
        this.bindOpen()
        this.bindBuy()
        this.bindQtyChange()
        this.bindRemove()
        this.bindClear()
       

    }
    bindOpen() {
        const self = this
        $(document).on('click', '.nav-link-cart', function () {
            self.core.api.updateProductCart({ cart: self.cart }).done((response) => {
                if (response.success) {
                   $('#cart-content').html(response.cart_html)
                    const modalEl = document.getElementById('cartModal')
                    if (modalEl) {
                        let modal = new Modal(modalEl)
                        modal.show()
                    }else{
                        console.log("no modalEl!!!")
                    }
                }
            })
        })
    }
    bindBuy() {
        const self = this
        $(document).on('click', '.buy-btn', function () {
            let id = $(this).data('id')
            let name = $(this).data('name')
            let price = $(this).data('price')
            if (!id) return
            // инкрементируем qty
            self.cart[id] = (self.cart[id] || 0) + 1
            localStorage.setItem('cart', JSON.stringify(self.cart))
            console.log(self.cart)
            // обновляем сервер
            self.core.api.updateProductCart({ cart: self.cart }).done((response) => {
                if (response.success) {
                    $('#cart-content').html(response.cart_html)
                    notify(`Товар "${name}" с ценой ${price} добавлен в корзину`, 'success')
                }
            })
        })
    }
    bindQtyChange() {
        const self = this
        $(document).on('click', '.qty-plus', function () {
            let id = $(this).data('id')
            self.cart[id] = (self.cart[id] || 0) + 1
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.core.api.updateProductCart({ cart: self.cart }).done((response) => {
                if (response.success) {
                    $('#cart-content').html(response.cart_html)
                }
            })
        })
        $(document).on('click', '.qty-minus', function () {
            let id = $(this).data('id')
            if (!id || !self.cart[id]) return
            self.cart[id] = Math.max(1, self.cart[id] - 1)
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.core.api.updateProductCart({ cart: self.cart }).done((response) => {
                if (response.success) {
                    $('#cart-content').html(response.cart_html)
                }
            })
        })
    }
    bindRemove() {
        const self = this
        $(document).on('click', '.remove-btn', function () {
            let id = $(this).data('id')
            if (!id) return

            delete self.cart[id]
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.core.api.updateProductCart({ cart: self.cart }).done((response) => {
                if (response.success) {
                    $('#cart-content').html(response.cart_html)
                }
            })
        })
    }
    bindClear() {
        const self = this
        $(document).on('click', '.clear-btn', function () {
            // полностью очищаем объект
            self.cart = {}
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.core.api.updateProductCart({ cart: self.cart }).done((response) => {
                if (response.success) {
                    $('#cart-content').html(response.cart_html)
                }
            })
        })
    }
}