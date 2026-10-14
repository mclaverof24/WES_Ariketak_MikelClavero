<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// GET [?id=|gailuId=|gelaId=|active=1]   historial   (cualquier usuario autenticado)
// POST {gelaId, gailuId, hasieraData?}   mueve un equipo (admin o user)
// PUT ?id=                               corrige fechas (solo admin)
// DELETE ?id=                            borra un movimiento (solo admin)

$user       = require_login();
$mugimendua = new Mugimendua(db());
$id         = id_from_query();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if ($id !== null) {
            $row = $mugimendua->getById($id);
            $row ? json_out($row) : json_error('No encontrado', 404);
        }
        json_out($mugimendua->getAll([
            'gailuId' => int_from_query('gailuId'),
            'gelaId'  => int_from_query('gelaId'),
            'active'  => ($_GET['active'] ?? '') === '1',
        ]));

    case 'POST':
        json_out($mugimendua->create(body(), $user['id']), 201);

    case 'PUT':
        require_role('admin');
        json_out($mugimendua->update(need_id($id), body()));

    case 'DELETE':
        require_role('admin');
        $mugimendua->delete(need_id($id));
        json_out(['ok' => true]);

    default:
        json_error('Método no permitido', 405);
}
