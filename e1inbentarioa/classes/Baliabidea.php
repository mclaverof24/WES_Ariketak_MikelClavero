<?php
declare(strict_types=1);

/** Tabla baliabideak: perfil o modelo de equipo (los equipos físicos cuelgan de él). */
class Baliabidea
{
    private const FIELDS = [
        'kategoriaId'  => ['type' => 'int', 'required' => true],
        'izena'        => ['type' => 'string', 'required' => true, 'max' => 150],
        'deskribapena' => ['type' => 'text'],
        'marka'        => ['type' => 'string', 'max' => 100],
        'modeloa'      => ['type' => 'string', 'max' => 100],
    ];

    private const COLUMNS = 'id, kategoriaId, izena, deskribapena, marka, modeloa';

    public function __construct(private PDO $pdo)
    {
    }

    /** READ: todos los recursos. */
    public function getAll(): array
    {
        return $this->pdo->query('SELECT ' . self::COLUMNS . ' FROM baliabideak ORDER BY id')->fetchAll();
    }

    /** READ: un recurso o null si no existe. */
    public function getById(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT ' . self::COLUMNS . ' FROM baliabideak WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /** CREATE: valida y crea; 422 si la categoría no existe (clave foránea). */
    public function create(array $in): array
    {
        [$data, $err] = validate(self::FIELDS, $in, false);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }

        $this->pdo->prepare(
            'INSERT INTO baliabideak (kategoriaId, izena, deskribapena, marka, modeloa)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([
            $data['kategoriaId'],
            $data['izena'],
            $data['deskribapena'] ?? null,
            $data['marka'] ?? null,
            $data['modeloa'] ?? null,
        ]);

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
        $this->pdo->prepare("UPDATE baliabideak SET $set WHERE id = :__id")
            ->execute($data + ['__id' => $id]);

        return $this->getById($id);
    }

    /** DELETE: falla con 409 si el recurso tiene equipos. */
    public function delete(int $id): void
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        $this->pdo->prepare('DELETE FROM baliabideak WHERE id = ?')->execute([$id]);
    }
}
