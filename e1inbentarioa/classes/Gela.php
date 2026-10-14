<?php
declare(strict_types=1);

/** Tabla gelak: aulas y espacios físicos (Almacén, Departamento, aulas de grupo...). */
class Gela
{
    private const FIELDS = [
        'izena'  => ['type' => 'string', 'required' => true, 'max' => 100],
        'taldea' => ['type' => 'string', 'max' => 100],
    ];

    public function __construct(private PDO $pdo)
    {
    }

    /** READ: todas las aulas. */
    public function getAll(): array
    {
        return $this->pdo->query('SELECT id, izena, taldea FROM gelak ORDER BY id')->fetchAll();
    }

    /** READ: un aula o null si no existe. */
    public function getById(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT id, izena, taldea FROM gelak WHERE id = ?');
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

        $this->pdo->prepare('INSERT INTO gelak (izena, taldea) VALUES (?, ?)')
            ->execute([$data['izena'], $data['taldea'] ?? null]);

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

        // Las claves de $data salen de FIELDS (código propio), nunca del cliente.
        $set = implode(', ', array_map(fn($col) => "`$col` = :$col", array_keys($data)));
        $this->pdo->prepare("UPDATE gelak SET $set WHERE id = :__id")
            ->execute($data + ['__id' => $id]);

        return $this->getById($id);
    }

    /** DELETE: falla con 409 si el aula tiene movimientos. */
    public function delete(int $id): void
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        $this->pdo->prepare('DELETE FROM gelak WHERE id = ?')->execute([$id]);
    }
}
