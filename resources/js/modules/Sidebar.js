import $ from 'jquery'

export default class Sidebar {
    constructor(core) {
        this.core = core
    }
    init() {
        this.bindToggle()
        this.bindCategoryClick()
    }
    bindToggle() {
        $('.catalog-sidebar').on('click', '.toggle-arrow', function (e) {
            e.stopPropagation()
            const $li = $(this).closest('.category-item')
            $li.toggleClass('open')
            $li.children('.sub-categories').slideToggle(200)
        })
    }
    bindCategoryClick() {
        const core = this.core
        $('.catalog-sidebar').on('click', '.category-name', function () {
            const slug = $(this).closest('.category-item').data('slug')
            if (!slug) return
            core.catalog.loadCategory(slug)
        })
    }
}