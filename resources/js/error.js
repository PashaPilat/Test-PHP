import $ from 'jquery';

window.$ = window.jQuery = $;


/*
|--------------------------------------------------------------------------
| Tabs
|--------------------------------------------------------------------------
|
| Плавное переключение вкладок
|
*/

function showTab(id) {
    $('.tab-panel').hide();

    $('#' + id)
        .fadeIn(200);
}

window.showTab = showTab;


/*
|--------------------------------------------------------------------------
| Accordion
|--------------------------------------------------------------------------
|
| Плавное раскрытие блоков
|
*/

function toggleAcc(el) {
    const block = el.parentElement;
    const pre = el.nextElementSibling;

    if (block.classList.contains('open')) {
        $(pre).slideUp(200);
        block.classList.remove('open');
    }
    else {
        $(pre).slideDown(200);
        block.classList.add('open');
    }
}

window.toggleAcc = toggleAcc;


/*
|--------------------------------------------------------------------------
| Default tab
|--------------------------------------------------------------------------
*/

$(function () {

    showTab('request');

});