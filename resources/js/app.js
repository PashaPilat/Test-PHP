import * as bootstrap from 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.js'
import { Modal } from 'bootstrap'
import noUiSlider from 'nouislider'
import 'nouislider/dist/nouislider.css'

import $ from 'jquery'

window.$ = window.jQuery = $
window.bootstrap = bootstrap;

import PlatPCore from './core/PlatPCore'
$(document).ready(function () {
    const core = new PlatPCore()
    core.init()
})

