<?php
declare(strict_types=1);

// ---------- Respuestas JSON ----------

function json_out(mixed $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_error(string $message, int $code = 400, array $details = []): never
{
    $out = ['error' => $message];
    if ($details) {
        $out['details'] = $details;
    }
    json_out($out, $code);
}

/** Lee el cuerpo JSON. Exigir application/json dificulta ataques CSRF con formularios. */
function body(): array
{
    $type = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($type, 'application/json') === false) {
        json_error('Content-Type debe ser application/json', 415);
    }
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw === false ? '' : $raw, true);
    if (!is_array($data)) {
        json_error('El cuerpo debe ser un objeto JSON válido', 400);
    }
    return $data;
}

function id_from_query(): ?int
{
    if (!isset($_GET['id'])) {
        return null;
    }
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id < 1) {
        json_error('id no válido', 400);
    }
    return $id;
}

/** Parámetro entero opcional de la query (?gailuId=3). null si no viene; 400 si no es válido. */
function int_from_query(string $name): ?int
{
    if (!isset($_GET[$name])) {
        return null;
    }
    $value = filter_var($_GET[$name], FILTER_VALIDATE_INT);
    if ($value === false || $value < 1) {
        json_error("$name no válido", 400);
    }
    return $value;
}

/** Exige que venga ?id= (para PUT y DELETE). */
function need_id(?int $id): int
{
    if ($id === null) {
        json_error('Falta el parámetro id', 400);
    }
    return $id;
}

/** Traduce errores de MySQL (claves foráneas, valores únicos) a respuestas HTTP comprensibles. */
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

// ---------- Sesión y roles ----------

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function current_user(): ?array
{
    start_session();
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $user = current_user();
    if ($user === null) {
        json_error('Debes iniciar sesión', 401);
    }
    return $user;
}

function require_role(string $role): array
{
    $user = require_login();
    if ($user['rol'] !== $role) {
        json_error('No tienes permisos para esta operación', 403);
    }
    return $user;
}

// ---------- Validación ----------

/**
 * Valida $in según $fields. Cada regla admite:
 *  type: int | string | text | date, required, max, enum, default
 * Con $partial = true (PUT) solo se validan los campos presentes.
 * Devuelve [datosLimpios, errores].
 */
function validate(array $fields, array $in, bool $partial): array
{
    $clean  = [];
    $errors = [];

    foreach ($fields as $name => $rule) {
        if (!array_key_exists($name, $in)) {
            if (array_key_exists('default', $rule) && !$partial) {
                $clean[$name] = $rule['default'];
            } elseif (!empty($rule['required']) && !$partial) {
                $errors[$name] = 'Campo obligatorio';
            }
            continue;
        }

        $v = $in[$name];
        if (is_string($v)) {
            $v = trim($v);
        }

        if ($v === null || $v === '') {
            if (!empty($rule['required'])) {
                $errors[$name] = 'Campo obligatorio';
            } elseif ($partial) {
                $clean[$name] = null;
            } elseif (array_key_exists('default', $rule)) {
                $clean[$name] = $rule['default'];
            }
            continue;
        }

        if (!is_scalar($v) || is_bool($v)) {
            $errors[$name] = 'Valor no válido';
            continue;
        }

        switch ($rule['type']) {
            case 'int':
                $i = filter_var($v, FILTER_VALIDATE_INT);
                if ($i === false || $i < 1) {
                    $errors[$name] = 'Debe ser un entero positivo';
                    continue 2;
                }
                $v = $i;
                break;

            case 'date':
                $d = DateTime::createFromFormat('!Y-m-d', (string)$v);
                if (!$d || $d->format('Y-m-d') !== (string)$v) {
                    $errors[$name] = 'Fecha no válida (formato AAAA-MM-DD)';
                    continue 2;
                }
                $v = (string)$v;
                break;

            default: // string | text
                $v   = (string)$v;
                $max = $rule['max'] ?? ($rule['type'] === 'text' ? 65535 : 255);
                if (mb_strlen($v) > $max) {
                    $errors[$name] = "Máximo $max caracteres";
                    continue 2;
                }
        }

        if (isset($rule['enum']) && !in_array($v, $rule['enum'], true)) {
            $errors[$name] = 'Valores permitidos: ' . implode(', ', $rule['enum']);
            continue;
        }

        $clean[$name] = $v;
    }

    return [$clean, $errors];
}
