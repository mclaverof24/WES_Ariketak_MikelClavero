<?php
declare(strict_types=1);

function find_row(PDO $pdo, string $table, int $id): ?array
{
    $st = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
    $st->execute([$id]);
    $row = $st->fetch();
    return $row === false ? null : $row;
}

/** Traduce errores de MySQL a respuestas HTTP comprensibles. */
function db_error_response(PDOException $e): never
{
    $code = $e->errorInfo[1] ?? 0;
    error_log($e->getMessage());
    match ($code) {
        1062    => json_error('Ya existe un registro con ese valor único (p. ej. etiqueta o nombre de usuario)', 409),
        1451    => json_error('No se puede borrar: tiene registros relacionados', 409),
        1452    => json_error('Referencia inexistente (clave foránea no válida)', 422),
        default => json_error('Error de base de datos', 500),
    };
}

/**
 * CRUD genérico para una tabla.
 *
 * $cfg:
 *  table  nombre de la tabla (constante del código, nunca del cliente)
 *  fields reglas de validación (ver validate())
 *  roles  ['POST' => [...], 'PUT' => [...], 'DELETE' => [...]]  (por defecto solo admin)
 *  before function(string $method, array &$data, ?array $existing, PDO $pdo): void
 *  after  function(string $method, int $id, array $data, PDO $pdo, array $user): void
 * GET lo puede hacer cualquier usuario autenticado.
 */
function crud(array $cfg): void
{
    $pdo    = db();
    $user   = require_login();
    $table  = $cfg['table'];
    $fields = $cfg['fields'];
    $method = $_SERVER['REQUEST_METHOD'];
    $id     = id_from_query();

    if ($method === 'GET') {
        if ($id !== null) {
            $row = find_row($pdo, $table, $id);
            if (!$row) {
                json_error('No encontrado', 404);
            }
            json_out($row);
        }
        json_out($pdo->query("SELECT * FROM `$table` ORDER BY id")->fetchAll());
    }

    if (!in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
        json_error('Método no permitido', 405);
    }
    $allowed = $cfg['roles'][$method] ?? ['admin'];
    if (!in_array($user['rol'], $allowed, true)) {
        json_error('No tienes permisos para esta operación', 403);
    }
    if ($method !== 'POST' && $id === null) {
        json_error('Falta el parámetro id', 400);
    }

    try {
        if ($method === 'POST') {
            [$data, $err] = validate($fields, body(), false);
            if ($err) {
                json_error('Datos no válidos', 422, $err);
            }
            $pdo->beginTransaction();
            if (isset($cfg['before'])) {
                ($cfg['before'])('POST', $data, null, $pdo);
            }
            $cols = array_keys($data);
            $sql  = sprintf(
                'INSERT INTO `%s` (%s) VALUES (%s)',
                $table,
                implode(', ', array_map(fn($c) => "`$c`", $cols)),
                implode(', ', array_map(fn($c) => ":$c", $cols))
            );
            $pdo->prepare($sql)->execute($data);
            $newId = (int)$pdo->lastInsertId();
            if (isset($cfg['after'])) {
                ($cfg['after'])('POST', $newId, $data, $pdo, $user);
            }
            $pdo->commit();
            json_out(find_row($pdo, $table, $newId), 201);
        }

        $existing = find_row($pdo, $table, $id);
        if (!$existing) {
            json_error('No encontrado', 404);
        }

        if ($method === 'PUT') {
            [$data, $err] = validate($fields, body(), true);
            if ($err) {
                json_error('Datos no válidos', 422, $err);
            }
            if (!$data) {
                json_error('No hay campos que actualizar', 400);
            }
            $pdo->beginTransaction();
            if (isset($cfg['before'])) {
                ($cfg['before'])('PUT', $data, $existing, $pdo);
            }
            $set = implode(', ', array_map(fn($c) => "`$c` = :$c", array_keys($data)));
            $pdo->prepare("UPDATE `$table` SET $set WHERE id = :__id")->execute($data + ['__id' => $id]);
            if (isset($cfg['after'])) {
                ($cfg['after'])('PUT', $id, $data, $pdo, $user);
            }
            $pdo->commit();
            json_out(find_row($pdo, $table, $id));
        }

        // DELETE
        $pdo->beginTransaction();
        if (isset($cfg['before'])) {
            $dummy = [];
            ($cfg['before'])('DELETE', $dummy, $existing, $pdo);
        }
        $pdo->prepare("DELETE FROM `$table` WHERE id = ?")->execute([$id]);
        $pdo->commit();
        json_out(['ok' => true]);
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        db_error_response($e);
    }
}