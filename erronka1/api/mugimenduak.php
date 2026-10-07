<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// GET    ?gailuId=&gelaId=&active=1   historial (cualquier usuario autenticado)
// POST   {gelaId, gailuId, hasieraData?}  mueve un equipo (admin o user)
// PUT    ?id=  corrige fechas (solo admin)
// DELETE ?id=  borra un movimiento (solo admin)

$user   = require_login();
$pdo    = db();
$method = $_SERVER['REQUEST_METHOD'];
$id     = id_from_query();

try {
    if ($method === 'GET') {
        $sql = 'SELECT m.id, m.gelaId, g.izena AS gela, m.gailuId, d.etiketa,
                       m.erabiltzaileId, u.username, m.hasieraData, m.amaieraData
                FROM mugimenduak m
                JOIN gelak g          ON g.id = m.gelaId
                JOIN gailuak d        ON d.id = m.gailuId
                JOIN erabiltzaileak u ON u.id = m.erabiltzaileId';
        $where  = [];
        $params = [];
        foreach (['gailuId' => 'm.gailuId', 'gelaId' => 'm.gelaId', 'id' => 'm.id'] as $q => $col) {
            $val = $q === 'id' ? $id : (isset($_GET[$q]) ? filter_var($_GET[$q], FILTER_VALIDATE_INT) : null);
            if ($val === false) {
                json_error("$q no válido", 400);
            }
            if ($val !== null) {
                $where[]  = "$col = ?";
                $params[] = $val;
            }
        }
        if (($_GET['active'] ?? '') === '1') {
            $where[] = 'm.amaieraData IS NULL';
        }
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY m.hasieraData DESC, m.id DESC';
        $st = $pdo->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll();
        if ($id !== null) {
            $rows ? json_out($rows[0]) : json_error('No encontrado', 404);
        }
        json_out($rows);
    }

    if ($method === 'POST') {
        [$data, $err] = validate([
            'gelaId'      => ['type' => 'int', 'required' => true],
            'gailuId'     => ['type' => 'int', 'required' => true],
            'hasieraData' => ['type' => 'date', 'required' => true, 'default' => date('Y-m-d')],
        ], body(), false);
        if ($err) {
            json_error('Datos no válidos', 422, $err);
        }

        $pdo->beginTransaction();

        $st = $pdo->prepare('SELECT id, egoera FROM gailuak WHERE id = ? FOR UPDATE');
        $st->execute([$data['gailuId']]);
        $gailua = $st->fetch();
        if (!$gailua) {
            abort_tx($pdo, 'El equipo no existe', 404);
        }
        if ($gailua['egoera'] === 'Baja') {
            abort_tx($pdo, 'Un equipo dado de baja no se puede mover', 422);
        }

        $st = $pdo->prepare('SELECT id FROM gelak WHERE id = ?');
        $st->execute([$data['gelaId']]);
        if (!$st->fetch()) {
            abort_tx($pdo, 'El aula no existe', 422);
        }

        // Ubicación activa actual: se cierra para que el equipo solo esté en un sitio a la vez.
        $st = $pdo->prepare(
            'SELECT id, gelaId, hasieraData FROM mugimenduak
             WHERE gailuId = ? AND amaieraData IS NULL FOR UPDATE'
        );
        $st->execute([$data['gailuId']]);
        $activo = $st->fetch();
        if ($activo) {
            if ((int)$activo['gelaId'] === $data['gelaId']) {
                abort_tx($pdo, 'El equipo ya está en esa aula', 409);
            }
            if ($data['hasieraData'] < $activo['hasieraData']) {
                abort_tx($pdo, 'La fecha no puede ser anterior al inicio de la ubicación actual', 422);
            }
            $pdo->prepare('UPDATE mugimenduak SET amaieraData = ? WHERE id = ?')
                ->execute([$data['hasieraData'], $activo['id']]);
        }

        // El usuario sale siempre de la sesión, nunca del cliente.
        $pdo->prepare(
            'INSERT INTO mugimenduak (gelaId, gailuId, erabiltzaileId, hasieraData) VALUES (?, ?, ?, ?)'
        )->execute([$data['gelaId'], $data['gailuId'], $user['id'], $data['hasieraData']]);
        $newId = (int)$pdo->lastInsertId();
        $pdo->commit();
        json_out(['id' => $newId] + $data + ['erabiltzaileId' => $user['id']], 201);
    }

    if ($method === 'PUT' || $method === 'DELETE') {
        if ($user['rol'] !== 'admin') {
            json_error('No tienes permisos para esta operación', 403);
        }
        if ($id === null) {
            json_error('Falta el parámetro id', 400);
        }
        $st = $pdo->prepare('SELECT * FROM mugimenduak WHERE id = ?');
        $st->execute([$id]);
        $mov = $st->fetch();
        if (!$mov) {
            json_error('No encontrado', 404);
        }

        if ($method === 'DELETE') {
            $pdo->prepare('DELETE FROM mugimenduak WHERE id = ?')->execute([$id]);
            json_out(['ok' => true]);
        }

        [$data, $err] = validate([
            'hasieraData' => ['type' => 'date', 'required' => true],
            'amaieraData' => ['type' => 'date'],
        ], body(), true);
        if ($err) {
            json_error('Datos no válidos', 422, $err);
        }
        if (!$data) {
            json_error('No hay campos que actualizar', 400);
        }

        $hasiera = $data['hasieraData'] ?? $mov['hasieraData'];
        $amaiera = array_key_exists('amaieraData', $data) ? $data['amaieraData'] : $mov['amaieraData'];
        if ($amaiera !== null && $amaiera < $hasiera) {
            json_error('La fecha de fin no puede ser anterior a la de inicio', 422);
        }
        if ($amaiera === null) {
            $st = $pdo->prepare(
                'SELECT COUNT(*) FROM mugimenduak WHERE gailuId = ? AND amaieraData IS NULL AND id <> ?'
            );
            $st->execute([$mov['gailuId'], $id]);
            if ((int)$st->fetchColumn() > 0) {
                json_error('El equipo ya tiene otra ubicación activa', 409);
            }
        }

        $set = implode(', ', array_map(fn($c) => "`$c` = :$c", array_keys($data)));
        $pdo->prepare("UPDATE mugimenduak SET $set WHERE id = :__id")->execute($data + ['__id' => $id]);
        json_out(['ok' => true]);
    }

    json_error('Método no permitido', 405);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    db_error_response($e);
}