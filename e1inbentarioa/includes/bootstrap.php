<?php
declare(strict_types=1);

ini_set('display_errors', '0');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';

// Carga automática de las clases de classes/ (Kategoria, Gailua, Mugimendua...).
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (preg_match('/^[A-Za-z]+$/', $class) && is_file($file)) {
        require $file;
    }
});

// ---------- CORS ----------
// Solo se autorizan los orígenes de ALLOWED_ORIGINS (nunca "*", porque usamos cookies).
$allowedOrigins = defined('ALLOWED_ORIGINS') ? ALLOWED_ORIGINS : [];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 600');
}
header('Vary: Origin');

// Preflight: el navegador pregunta antes de enviar JSON entre orígenes distintos.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Manejo central de errores: las clases lanzan excepciones y aquí se convierten en JSON.
set_exception_handler(function (Throwable $e): void {
    if ($e instanceof ApiException) {
        json_error($e->getMessage(), $e->getCode(), $e->details);
    }
    if ($e instanceof PDOException) {
        db_error_response($e);
    }
    error_log($e->getMessage());
    json_error('Error interno del servidor', 500);
});
