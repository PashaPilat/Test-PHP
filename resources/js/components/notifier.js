import $ from 'jquery';

export function notify(message, type = 'success') {
    const $notifier = $('#notifier');
    $notifier.removeClass('error success').addClass(type).text(message).addClass('show');

    setTimeout(() => {
        $notifier.removeClass('show');
    }, 3000); // скрыть через 3 секунды
}
