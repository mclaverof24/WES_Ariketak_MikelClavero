<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// Solo el administrador puede listar, crear (alta), editar o borrar usuarios.
// GET [?id=]   lista o un usuario
// POST         alta de usuario
// PUT ?id=     edita
// DELETE ?id=  borra

$me            = require_role('admin');
$erabiltzailea = new Erabiltzailea(db());
$id            = id_from_query();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($id === null) {
            json_out($erabiltzailea->getAll());
        }
        $row = $erabiltzailea->getById($id);
        $row ? json_out($row) : json_error('No encontrado', 404);

    case 'POST':
        json_out($erabiltzailea->create(body()), 201);

    case 'PUT':
        json_out($erabiltzailea->update(need_id($id), body(), $me['id']));

    case 'DELETE':
        $erabiltzailea->delete(need_id($id), $me['id']);
        json_out(['ok' => true]);

    default:
        json_error('Método no permitido', 405);
}
