<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// Solo el administrador puede listar, crear (alta), editar o borrar usuarios.
$me     = require_role('admin');
$pdo    = db();
$method = $_SERVER['REQUEST_METHOD'];
$id     = id_from_query();

$fields = [
    'username' => ['type' => 'string', 'required' => true, 'max' => 50],
    'rol'      => ['type' => 'string', 'required' => true, 'enum' => ['admin', 'user'], 'default' => 'user'],
];

/** Contraseña: 8-72 caracteres (límite de bcrypt). Acumula errores en $err. */
function password_from(array $in, bool $required, array &$err): ?string
{
    $p = $in['pasahitza'] ?? null;
    if ($p === null || $p === '') {
        if ($required) {
            $err['pasahitza'] = 'Campo obligatorio';
        }
        return null;
    }
    if (!is_string($p) || strlen($p) < 8 || strlen($p) > 72) {
        $err['pasahitza'] = 'Debe tener entre 8 y 72 caracteres';
        return null;
    }
    return $p;
}

function check_username(array $data, array &$err): void
{
    if (isset($data['username']) && !preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $data['username'])) {
        $err['username'] = 'Entre 3 y 50 caracteres: letras, números, _ . -';
    }
}

try {
    if ($method === 'GET') {
        if ($id !== null) {
            $st = $pdo->prepare('SELECT id, username, rol FROM erabiltzaileak WHERE id = ?');
            $st->execute([$id]);
            $row = $st->fetch();
            $row ? json_out($row) : json_error('No encontrado', 404);
        }
        json_out($pdo->query('SELECT id, username, rol FROM erabiltzaileak ORDER BY id')->fetchAll());
    }

    if ($method === 'POST') {
        $in = body();
        [$data, $err] = validate($fields, $in, false);
        check_username($data, $err);
        $pass = password_from($in, true, $err);
        if ($err) {
            json_error('Datos no válidos', 422, $err);
        }
        $pdo->prepare('INSERT INTO erabiltzaileak (username, pasahitza, rol) VALUES (?, ?, ?)')
            ->execute([$data['username'], password_hash($pass, PASSWORD_BCRYPT), $data['rol']]);
        $newId = (int)$pdo->lastInsertId();
        json_out(['id' => $newId, 'username' => $data['username'], 'rol' => $data['rol']], 201);
    }

    if ($method === 'PUT' || $method === 'DELETE') {
        if ($id === null) {
            json_error('Falta el parámetro id', 400);
        }
        $st = $pdo->prepare('SELECT id, username, rol FROM erabiltzaileak WHERE id = ?');
        $st->execute([$id]);
        if (!$st->fetch()) {
            json_error('No encontrado', 404);
        }

        if ($method === 'DELETE') {
            if ($id === $me['id']) {
                json_error('No puedes borrar tu propio usuario', 400);
            }
            $pdo->prepare('DELETE FROM erabiltzaileak WHERE id = ?')->execute([$id]);
            json_out(['ok' => true]);
        }

        $in = body();
        [$data, $err] = validate($fields, $in, true);
        check_username($data, $err);
        $pass = password_from($in, false, $err);
        if ($err) {
            json_error('Datos no válidos', 422, $err);
        }
        if (isset($data['rol']) && $id === $me['id'] && $data['rol'] !== 'admin') {
            json_error('No puedes quitarte a ti mismo el rol admin', 400);
        }
        if ($pass !== null) {
            $data['pasahitza'] = password_hash($pass, PASSWORD_BCRYPT);
        }
        if (!$data) {
            json_error('No hay campos que actualizar', 400);
        }
        $set = implode(', ', array_map(fn($c) => "`$c` = :$c", array_keys($data)));
        $pdo->prepare("UPDATE erabiltzaileak SET $set WHERE id = :__id")->execute($data + ['__id' => $id]);
        json_out(['ok' => true]);
    }

    json_error('Método no permitido', 405);
} catch (PDOException $e) {
    db_error_response($e);
}