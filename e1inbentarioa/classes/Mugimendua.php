<?php
declare(strict_types=1);

/**
 * Tabla mugimenduak: historial de ubicaciones de los equipos.
 * amaieraData NULL = ubicación actual. Un equipo solo puede tener una ubicación activa.
 */
class Mugimendua
{
    private const SELECT =
        'SELECT m.id, m.gelaId, g.izena AS gela, m.gailuId, d.etiketa,
                m.erabiltzaileId, u.username, m.hasieraData, m.amaieraData
         FROM mugimenduak m
         JOIN gelak g          ON g.id = m.gelaId
         JOIN gailuak d        ON d.id = m.gailuId
         JOIN erabiltzaileak u ON u.id = m.erabiltzaileId';

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * READ: historial. Filtros opcionales (enteros ya validados por el endpoint):
     * gailuId, gelaId y active (true = solo ubicaciones actuales).
     */
    public function getAll(array $filters = []): array
    {
        $where  = [];
        $params = [];

        if (isset($filters['gailuId'])) {
            $where[]  = 'm.gailuId = ?';
            $params[] = $filters['gailuId'];
        }
        if (isset($filters['gelaId'])) {
            $where[]  = 'm.gelaId = ?';
            $params[] = $filters['gelaId'];
        }
        if (!empty($filters['active'])) {
            $where[] = 'm.amaieraData IS NULL';
        }

        $sql = self::SELECT . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' ORDER BY m.hasieraData DESC, m.id DESC';
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    /** READ: un movimiento o null si no existe. */
    public function getById(int $id): ?array
    {
        $st = $this->pdo->prepare(self::SELECT . ' WHERE m.id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    /**
     * CREATE: mueve un equipo a un aula. Cierra la ubicación activa anterior y abre la nueva
     * en una misma transacción. El usuario sale de la sesión, nunca del cliente.
     */
    public function create(array $in, int $userId): array
    {
        [$data, $err] = validate([
            'gelaId'      => ['type' => 'int', 'required' => true],
            'gailuId'     => ['type' => 'int', 'required' => true],
            'hasieraData' => ['type' => 'date', 'default' => date('Y-m-d')],
        ], $in, false);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }

        $this->pdo->beginTransaction();
        try {
            $st = $this->pdo->prepare('SELECT id, egoera FROM gailuak WHERE id = ? FOR UPDATE');
            $st->execute([$data['gailuId']]);
            $gailua = $st->fetch();
            if (!$gailua) {
                throw new ApiException('El equipo no existe', 404);
            }
            if ($gailua['egoera'] === 'Baja') {
                throw new ApiException('Un equipo dado de baja no se puede mover', 422);
            }

            $st = $this->pdo->prepare('SELECT id FROM gelak WHERE id = ?');
            $st->execute([$data['gelaId']]);
            if (!$st->fetch()) {
                throw new ApiException('El aula no existe', 422);
            }

            // Ubicación activa actual: se cierra para que el equipo solo esté en un sitio a la vez.
            $st = $this->pdo->prepare(
                'SELECT id, gelaId, hasieraData FROM mugimenduak
                 WHERE gailuId = ? AND amaieraData IS NULL FOR UPDATE'
            );
            $st->execute([$data['gailuId']]);
            $activo = $st->fetch();
            if ($activo) {
                if ((int)$activo['gelaId'] === $data['gelaId']) {
                    throw new ApiException('El equipo ya está en esa aula', 409);
                }
                if ($data['hasieraData'] < $activo['hasieraData']) {
                    throw new ApiException('La fecha no puede ser anterior al inicio de la ubicación actual', 422);
                }
                $this->pdo->prepare('UPDATE mugimenduak SET amaieraData = ? WHERE id = ?')
                    ->execute([$data['hasieraData'], $activo['id']]);
            }

            $this->pdo->prepare(
                'INSERT INTO mugimenduak (gelaId, gailuId, erabiltzaileId, hasieraData) VALUES (?, ?, ?, ?)'
            )->execute([$data['gelaId'], $data['gailuId'], $userId, $data['hasieraData']]);
            $newId = (int)$this->pdo->lastInsertId();

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }

        return $this->getById($newId);
    }

    /** UPDATE (corregir fechas): comprueba el orden de las fechas y que no haya dos ubicaciones activas. */
    public function update(int $id, array $in): array
    {
        $mov = $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        [$data, $err] = validate([
            'hasieraData' => ['type' => 'date', 'required' => true],
            'amaieraData' => ['type' => 'date'],
        ], $in, true);
        if ($err) {
            throw new ApiException('Datos no válidos', 422, $err);
        }
        if (!$data) {
            throw new ApiException('No hay campos que actualizar', 400);
        }

        $hasiera = $data['hasieraData'] ?? $mov['hasieraData'];
        $amaiera = array_key_exists('amaieraData', $data) ? $data['amaieraData'] : $mov['amaieraData'];

        if ($amaiera !== null && $amaiera < $hasiera) {
            throw new ApiException('La fecha de fin no puede ser anterior a la de inicio', 422);
        }
        if ($amaiera === null) {
            $st = $this->pdo->prepare(
                'SELECT COUNT(*) FROM mugimenduak WHERE gailuId = ? AND amaieraData IS NULL AND id <> ?'
            );
            $st->execute([$mov['gailuId'], $id]);
            if ((int)$st->fetchColumn() > 0) {
                throw new ApiException('El equipo ya tiene otra ubicación activa', 409);
            }
        }

        // Las claves de $data salen de la lista de campos de arriba (código propio), nunca del cliente.
        $set = implode(', ', array_map(fn($col) => "`$col` = :$col", array_keys($data)));
        $this->pdo->prepare("UPDATE mugimenduak SET $set WHERE id = :__id")
            ->execute($data + ['__id' => $id]);

        return $this->getById($id);
    }

    /** DELETE: borra un movimiento del historial. */
    public function delete(int $id): void
    {
        $this->getById($id) ?? throw new ApiException('No encontrado', 404);

        $this->pdo->prepare('DELETE FROM mugimenduak WHERE id = ?')->execute([$id]);
    }
}
