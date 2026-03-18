/**
 * Class HistoryManager
 *
 * Управляет URL каталога через History API.
 * Синхронизирует состояние каталога и адресную строку.
 */
export default class HistoryManager {

    constructor(core) {
        this.core = core
    }

    init() {
        window.addEventListener('popstate', (event) => {
            if (!event.state) return
            this.core.catalog.restoreState(event.state)
        })
    }

    /**
     * Обновляет URL в браузере
     */
    push(state) {
        const url = this.buildUrl(state)
        window.history.pushState(state, '', url)
    }

    /**
     * Строит URL из состояния каталога
     */
    buildUrl(state) {
        // если категория не выбрана → главная страница
        let url = state.category ? `/catalog/${state.category}` : '/'

        const params = new URLSearchParams()
        if (state.page > 1) params.set('page', state.page)
        if (state.sort !== 'default') params.set('sort', state.sort)

        // сериализация фильтров
        if (state.filters) {
            for (const [key, val] of Object.entries(state.filters)) {
                if (typeof val === 'object') {
                    for (const [subKey, subVal] of Object.entries(val)) {
                        params.set(`filters[${key}][${subKey}]`, subVal)
                    }
                } else {
                    params.set(`filters[${key}]`, val)
                }
            }
        }

        const query = params.toString()
        return query ? `${url}?${query}` : url
    }


}