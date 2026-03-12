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
        let url = '/catalog'
        if (state.category) {
            url += '/' + state.category
        }
        const params = new URLSearchParams()
        if (state.page > 1) params.set('page', state.page)
        if (state.sort !== 'default') params.set('sort', state.sort)
        const query = params.toString()
        return query ? `${url}?${query}` : url
    }
}