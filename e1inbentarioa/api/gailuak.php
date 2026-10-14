<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// Equipos físicos. Etiqueta obligatoria y única; solo admin escribe.
// GET [?id=]   lista o un equipo   (cualquier usuario autenticado)
// POST         crea y ubica en el Almacén   (admin)
// PUT ?id=     edita               (admin)
// DELETE ?id=  borra               (admin)

$user   = require_login();
$gailua = new Gailua(db());
$id     = id_from_query();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($id === null) {
            json_out($gailua->getAll());
        }
        $row = $gailua->getById($id);
        $row ? json_out($row) : json_error('No encontrado', 404);

    case 'POST':
        require_role('admin');
        json_out($gailua->create(body(), $user['id']), 201);

    case 'PUT':
        require_role('admin');
        json_out($gailua->update(need_id($id), body()));

    case 'DELETE':
        require_role('admin');
        $gailua->delete(need_id($id));
        json_out(['ok' => true]);

    default:
        json_error('Método no permitido', 405);
}
