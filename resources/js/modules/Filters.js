import $ from 'jquery'
import noUiSlider from 'nouislider'

export default class Filters {
    constructor(core) {
        this.core = core
    }

    init() {
        this.initPriceSlider()
        this.bindCheckboxFilters()
        this.bindToggleSections()
    }

    initPriceSlider() {
        const core = this.core
        const slider = document.getElementById('slider-range')
        if (!slider) {
            return
        }
        const minLimit = parseFloat($('#range1').attr('min')) || 0
        const maxLimit = parseFloat($('#range2').attr('max')) || 0
        const min = parseFloat($('#range1').val())
        const max = parseFloat($('#range2').val())

        noUiSlider.create(slider, {
            start: [min, max],
            connect: true,
            range: {
                min: minLimit,
                max: maxLimit
            },
            step: 1
        })


        // обновляем инпуты при движении ползунков
        slider.noUiSlider.on('update', function (values) {
            $('#range1').val(Math.round(values[0]))
            $('#range2').val(Math.round(values[1]))
        })

        // при отпускании — обновляем фильтр
        slider.noUiSlider.on('change', function (values) {
            core.catalog.setFilter('price', { min: values[0], max: values[1] })
        })

        // если вводят руками
        $('#range1, #range2').on('change', function () {
            const minVal = $('#range1').val()
            const maxVal = $('#range2').val()
            slider.noUiSlider.set([minVal, maxVal])
            core.catalog.setFilter('price', { min: minVal, max: maxVal })
        })
    }

    bindCheckboxFilters() {
        const core = this.core
        $(document).on('change', '.attrib_divs input[type=checkbox]', function () {
            const $el = $(this)
            const name = $el.attr('name')
            const value = $el.val()
            core.catalog.setFilter(name, value)
        })
    }

    bindToggleSections() {
        $(document).on('click', '.filter_heading.toggle-filter', function () {
            const $heading = $(this)
            const $content = $heading.next('.filter-content, .inner-scroll')
            $content.slideToggle(200)
            $heading.find('.filter-arrow').text(
                $content.is(':visible') ? '▾' : '▸'
            )
        })
    }
}
