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

/** Deshace la transacción abierta (si la hay) y responde con error. */
function abort_tx(PDO $pdo, string $message, int $code = 400, array $details = []): never
{
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error($message, $code, $details);
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

// ---------- Sesión y roles ----------

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
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