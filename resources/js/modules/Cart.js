import $ from 'jquery'
import { Modal } from 'bootstrap'
import { notify } from '../components/notifier'

export default class Cart {
    constructor(core) {
        this.core = core
        this.cart = JSON.parse(localStorage.getItem('cart') || '[]')
        const modalEl = document.getElementById('cartModal')
        if (modalEl) {
            this.modal = new Modal(modalEl)
        } else {
            console.log("no modalEl!!!")
        }
    }
    init() {
        this.bindOpen()
        this.bindBuy()
        this.bindQtyChange()
        this.bindRemove()
        this.bindClear()
        this.refreshCart()
    }
    refreshCart(callback) {
        this.core.api.updateProductCart({ cart: this.cart }).done((response) => {
            if (response.success) {
                $('#cart-content').html(response.cart_html)
                $('#divShoppingCard').html(response.cart_header_html)

                if (typeof callback === 'function') {
                    callback(response)
                }
            }
        })
    }

    bindOpen() {
        const self = this
        $(document).on('click', '.nav-link-cart', function () {
            self.refreshCart(() => {
                self.modal.show()
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
            // обновляем сервер
            self.refreshCart(() => {
                notify(`Товар "${name}" с ценой ${price} добавлен в корзину`, 'success')
                self.modal.show()
            })
        })
    }
    bindQtyChange() {
        const self = this
        $(document).on('click', '.qty-plus', function () {
            let id = $(this).data('id')
            self.cart[id] = (self.cart[id] || 0) + 1
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.refreshCart()
        })
        $(document).on('click', '.qty-minus', function () {
            let id = $(this).data('id')
            if (!id || !self.cart[id]) return
            self.cart[id] = Math.max(1, self.cart[id] - 1)
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.refreshCart()
        })
    }
    bindRemove() {
        const self = this
        $(document).on('click', '.remove-btn', function () {
            let id = $(this).data('id')
            if (!id) return

            delete self.cart[id]
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.refreshCart()
        })
    }
    bindClear() {
        const self = this
        $(document).on('click', '.clear-btn', function () {
            // полностью очищаем объект
            self.cart = {}
            localStorage.setItem('cart', JSON.stringify(self.cart))
            self.refreshCart()
        })
    }
}