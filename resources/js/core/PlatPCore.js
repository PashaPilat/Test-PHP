import Api from './Api'
import Catalog from '../modules/Catalog'
import Sidebar from '../modules/Sidebar'
import HistoryManager from './HistoryManager'
import Cart from '../modules/Cart'

export default class PlatPCore {

    constructor() {
        this.api = new Api()
        this.history = new HistoryManager(this)

        this.catalog = new Catalog(this)
        this.sidebar = new Sidebar(this)
        this.cart = new Cart(this)
    }

    init() {
        this.catalog.init()
        this.sidebar.init()
        this.history.init()
        this.cart.init()
    }
}