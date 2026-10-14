<?php
declare(strict_types=1);

/** Tabla kategoriak: categorías de equipamiento. */
class Kategoria
{
    private const FIELDS = [
        'izena' => ['type' => 'string', 'required' => true, 'max' => 100],
    ];

    public function __construct(private PDO $pdo)
    {
    }

    /** READ: todas las categorías. */
    public function getAll(): array
    {
        return $this->pdo->query('SELECT id, izena FROM kategoriak ORDER BY id')->fetchAll();
    }

    /** READ: una categoría o null si no existe. */
    public function getById(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT id, izena FROM kategoriak WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /** CREATE: valida y crea; devuelve la fila creada. */
    public function create(array $in): array
    {
        [$data, $err] = validate(self::FIELDS, $in, false);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }

        $this->pdo->prepare('INSERT INTO kategoriak (izena) VALUES (?)')
            ->execute([$data['izena']]);

        return $this->getById((int)$this->pdo->lastInsertId());
    }

    /** UPDATE: solo cambia los campos enviados; devuelve la fila actualizada. */
    public function update(int $id, array $in): array
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        [$data, $err] = validate(self::FIELDS, $in, true);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }
        if (!$data) {
            throw new ApiException('No hay campos que actualizar', 400);
        }

        $this->pdo->prepare('UPDATE kategoriak SET izena = ? WHERE id = ?')
            ->execute([$data['izena'], $id]);

        return $this->getById($id);
    }

    /** DELETE: falla con 409 (clave foránea) si la categoría tiene recursos. */
    public function delete(int $id): void
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        $this->pdo->prepare('DELETE FROM kategoriak WHERE id = ?')->execute([$id]);
    }
}
