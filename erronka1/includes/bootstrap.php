<?php
declare(strict_types=1);

ini_set('display_errors', '0');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/crud.php';

set_exception_handler(function (Throwable $e): void {
    error_log($e->getMessage());
    if (!headers_sent()) {
        json_error('Error interno del servidor', 500);
    }
});