<?php
declare(strict_types=1);

/**
 * Tabla gailuak: unidades físicas inventariadas.
 * Reglas: etiqueta obligatoria y única; todo equipo nuevo entra en el aula Almacén;
 * al darlo de baja se cierra su ubicación activa.
 */
class Gailua
{
    private const EGOERAK = ['Operatiboa', 'Matxuratua', 'Baja'];

    private const FIELDS = [
        'baliabideId'  => ['type' => 'int', 'required' => true],
        'etiketa'      => ['type' => 'string', 'required' => true, 'max' => 50],
        'erosketaData' => ['type' => 'date'],
        'egoera'       => ['type' => 'string', 'required' => true, 'default' => 'Operatiboa', 'enum' => self::EGOERAK],
    ];

    private const COLUMNS = 'id, baliabideId, etiketa, erosketaData, egoera';

    public function __construct(private PDO $pdo)
    {
    }

    /** READ: todos los equipos. */
    public function getAll(): array
    {
        return $this->pdo->query('SELECT ' . self::COLUMNS . ' FROM gailuak ORDER BY id')->fetchAll();
    }

    /** READ: un equipo o null si no existe. */
    public function getById(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT ' . self::COLUMNS . ' FROM gailuak WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /**
     * CREATE: valida (etiqueta obligatoria), comprueba que la etiqueta es única,
     * inserta el equipo y lo ubica en el Almacén, todo en una transacción.
     */
    public function create(array $in, int $userId): array
    {
        [$data, $err] = validate(self::FIELDS, $in, false);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }
        $this->assertEtiketaLibre($data['etiketa']);

        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare(
                'INSERT INTO gailuak (baliabideId, etiketa, erosketaData, egoera) VALUES (?, ?, ?, ?)'
            )->execute([
                $data['baliabideId'],
                $data['etiketa'],
                $data['erosketaData'] ?? null,
                $data['egoera'],
            ]);
            $id = (int)$this->pdo->lastInsertId();

            if ($data['egoera'] !== 'Baja') {
                $this->ubicarEnAlmacen($id, $userId);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        return $this->getById($id);
    }

    /** UPDATE: solo cambia los campos enviados. Pasar a "Baja" cierra su ubicación activa. */
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
        if (isset($data['etiketa'])) {
            $this->assertEtiketaLibre($data['etiketa'], $id);
        }

        $this->pdo->beginTransaction();
        try {
            if (($data['egoera'] ?? null) === 'Baja') {
                $this->pdo->prepare(
                    'UPDATE mugimenduak SET amaieraData = GREATEST(hasieraData, CURDATE())
                     WHERE gailuId = ? AND amaieraData IS NULL'
                )->execute([$id]);
            }

            // Las claves de $data salen de FIELDS (código propio), nunca del cliente.
            $set = implode(', ', array_map(fn($col) => "`$col` = :$col", array_keys($data)));
            $this->pdo->prepare("UPDATE gailuak SET $set WHERE id = :__id")
                ->execute($data + ['__id' => $id]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        return $this->getById($id);
    }

    /** DELETE: falla con 409 si el equipo tiene movimientos (hay que borrarlos antes). */
    public function delete(int $id): void
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        $this->pdo->prepare('DELETE FROM gailuak WHERE id = ?')->execute([$id]);
    }

    /** 409 si ya existe otro equipo con esa etiqueta. */
    private function assertEtiketaLibre(string $etiketa, ?int $exceptId = null): void
    {
        $sql    = 'SELECT id FROM gailuak WHERE etiketa = ?';
        $params = [$etiketa];
        if ($exceptId !== null) {
            $sql     .= ' AND id <> ?';
            $params[] = $exceptId;
        }
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        if ($st->fetch()) {
            throw new ApiException('Ya existe un equipo con esa etiqueta', 409, ['etiketa' => 'Etiqueta repetida']);
        }
    }

    /** Registra el movimiento inicial al aula Almacén (si existe) para que el equipo tenga ubicación. */
    private function ubicarEnAlmacen(int $gailuId, int $userId): void
    {
        $st = $this->pdo->prepare('SELECT id FROM gelak WHERE izena = ? LIMIT 1');
        $st->execute([GELA_ALMAZENA]);
        $gelaId = $st->fetchColumn();
        if ($gelaId === false) {
            return;
        }

        $this->pdo->prepare(
            'INSERT INTO mugimenduak (gelaId, gailuId, erabiltzaileId, hasieraData)
             VALUES (?, ?, ?, CURDATE())'
        )->execute([$gelaId, $gailuId, $userId]);
    }
}
