<?php
return [
    'name' => 'MyCatalog',
    'debug' => true, // режим отладки
    'timezone' => 'Europe/Kiev',
    'log_days' => 10, // хранить логи 10 дней, 0 = не удалять
    'log_time_execution' => true, // логировать время выполнения запросов
    'port_http' => 3000 // false если без порта, или 3000 если нужно
];
