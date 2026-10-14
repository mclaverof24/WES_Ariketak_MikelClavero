<?php
declare(strict_types=1);

/**
 * Tabla erabiltzaileak: usuarios del sistema (roles admin y user).
 * Las contraseñas se guardan con bcrypt y nunca se devuelven.
 */
class Erabiltzailea
{
    private const FIELDS = [
        'username' => ['type' => 'string', 'required' => true, 'max' => 50],
        'rol'      => ['type' => 'string', 'required' => true, 'default' => 'user', 'enum' => ['admin', 'user']],
    ];

    public function __construct(private PDO $pdo)
    {
    }

    /** READ: todos los usuarios (sin contraseña). */
    public function getAll(): array
    {
        return $this->pdo->query('SELECT id, username, rol FROM erabiltzaileak ORDER BY id')->fetchAll();
    }

    /** READ: un usuario (sin contraseña) o null si no existe. */
    public function getById(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT id, username, rol FROM erabiltzaileak WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /** LOGIN: devuelve los datos de sesión si las credenciales son correctas; si no, null. */
    public function authenticate(string $username, string $password): ?array
    {
        $st = $this->pdo->prepare('SELECT id, username, pasahitza, rol FROM erabiltzaileak WHERE username = ?');
        $st->execute([$username]);
        $user = $st->fetch();

        if (!$user || !password_verify($password, $user['pasahitza'])) {
            usleep(300000); // frena un poco la fuerza bruta
            return null;
        }

        return ['id' => (int)$user['id'], 'username' => $user['username'], 'rol' => $user['rol']];
    }

    /** CREATE (alta de usuario): el endpoint ya exige rol admin. */
    public function create(array $in): array
    {
        [$data, $err] = validate(self::FIELDS, $in, false);
        $this->checkUsername($data, $err);
        $password = $this->passwordFrom($in, true, $err);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }

        $this->pdo->prepare('INSERT INTO erabiltzaileak (username, pasahitza, rol) VALUES (?, ?, ?)')
            ->execute([$data['username'], password_hash($password, PASSWORD_BCRYPT), $data['rol']]);

        return $this->getById((int)$this->pdo->lastInsertId());
    }

    /**
     * UPDATE: cambia solo los campos enviados (la contraseña, si viene, se vuelve a cifrar).
     * Un admin no puede quitarse a sí mismo el rol admin.
     */
    public function update(int $id, array $in, int $currentUserId): array
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        [$data, $err] = validate(self::FIELDS, $in, true);
        $this->checkUsername($data, $err);
        $password = $this->passwordFrom($in, false, $err);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }
        if (isset($data['rol']) && $id === $currentUserId && $data['rol'] !== 'admin') {
            throw new ApiException('No puedes quitarte a ti mismo el rol admin', 400);
        }
        if ($password !== null) {
            $data['pasahitza'] = password_hash($password, PASSWORD_BCRYPT);
        }
        if (!$data) {
            throw new ApiException('No hay campos que actualizar', 400);
        }

        // Las claves de $data salen de FIELDS o son 'pasahitza' (código propio), nunca del cliente.
        $set = implode(', ', array_map(fn($col) => "`$col` = :$col", array_keys($data)));
        $this->pdo->prepare("UPDATE erabiltzaileak SET $set WHERE id = :__id")
            ->execute($data + ['__id' => $id]);

        return $this->getById($id);
    }

    /** DELETE: no se puede borrar el propio usuario; 409 si tiene movimientos. */
    public function delete(int $id, int $currentUserId): void
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        if ($id === $currentUserId) {
            throw new ApiException('No puedes borrar tu propio usuario', 400);
        }

        $this->pdo->prepare('DELETE FROM erabiltzaileak WHERE id = ?')->execute([$id]);
    }

    /** Nombre de usuario: 3-50 caracteres (letras, números, _ . -). Acumula el error en $err. */
    private function checkUsername(array $data, array &$err): void
    {
        if (isset($data['username']) && !preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $data['username'])) {
            $err['username'] = 'Entre 3 y 50 caracteres: letras, números, _ . -';
        }
    }

    /** Contraseña: 8-72 caracteres (límite de bcrypt). Acumula el error en $err. */
    private function passwordFrom(array $in, bool $required, array &$err): ?string
    {
        $password = $in['pasahitza'] ?? null;
        if ($password === null || $password === '') {
            if ($required) {
                $err['pasahitza'] = 'Campo obligatorio';
            }
            return null;
        }
        if (!is_string($password) || strlen($password) < 8 || strlen($password) > 72) {
            $err['pasahitza'] = 'Debe tener entre 8 y 72 caracteres';
            return null;
        }
        return $password;
    }
}
