<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// GET [?id=]   lista o un recurso   (cualquier usuario autenticado)
// POST         crea                 (admin)
// PUT ?id=     edita                (admin)
// DELETE ?id=  borra                (admin)

require_login();
$baliabidea = new Baliabidea(db());
$id         = id_from_query();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($id === null) {
            json_out($baliabidea->getAll());
        }
        $row = $baliabidea->getById($id);
        $row ? json_out($row) : json_error('No encontrado', 404);

    case 'POST':
        require_role('admin');
        json_out($baliabidea->create(body()), 201);

    case 'PUT':
        require_role('admin');
        json_out($baliabidea->update(need_id($id), body()));

    case 'DELETE':
        require_role('admin');
        $baliabidea->delete(need_id($id));
        json_out(['ok' => true]);

    default:
        json_error('Método no permitido', 405);
}
