<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// POST /api/auth.php?action=login   {"username": "...", "pasahitza": "..."}
// POST /api/auth.php?action=logout
// GET  /api/auth.php?action=me

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'login':
        if ($method !== 'POST') {
            json_error('Método no permitido', 405);
        }
        $in       = body();
        $username = is_string($in['username'] ?? null) ? trim($in['username']) : '';
        $password = is_string($in['pasahitza'] ?? null) ? $in['pasahitza'] : '';
        if ($username === '' || $password === '') {
            json_error('Usuario y contraseña obligatorios', 422);
        }

        $user = (new Erabiltzailea(db()))->authenticate($username, $password);
        if ($user === null) {
            json_error('Credenciales incorrectas', 401);
        }

        start_session();
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        json_out($user);

    case 'logout':
        if ($method !== 'POST') {
            json_error('Método no permitido', 405);
        }
        start_session();
        $_SESSION = [];
        session_destroy();
        json_out(['ok' => true]);

    case 'me':
        if ($method !== 'GET') {
            json_error('Método no permitido', 405);
        }
        json_out(require_login());

    default:
        json_error('Acción no válida (login, logout, me)', 400);
}
