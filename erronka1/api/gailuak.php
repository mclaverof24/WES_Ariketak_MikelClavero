<?php
declare(strict_types=1);
require __DIR__ . '/../includes/bootstrap.php';

// Equipos físicos. Etiqueta obligatoria y única; solo admin escribe.
crud([
    'table'  => 'gailuak',
    'fields' => [
        'baliabideId' => ['type' => 'int', 'required' => true],
        'etiketa'     => ['type' => 'string', 'required' => true, 'max' => 50],
        'erosketaData' => ['type' => 'date'],
        'egoera'      => [
            'type'     => 'string',
            'required' => true,
            'default'  => 'Operatiboa',
            'enum'     => ['Operatiboa', 'Matxuratua', 'Baja'],
        ],
    ],

    // Al dar de baja un equipo se cierra su ubicación activa: ya no cuenta en el stock.
    'before' => function (string $method, array &$data, ?array $existing, PDO $pdo): void {
        if ($method === 'PUT' && $existing !== null && ($data['egoera'] ?? null) === 'Baja') {
            $pdo->prepare(
                'UPDATE mugimenduak SET amaieraData = GREATEST(hasieraData, CURDATE())
                 WHERE gailuId = ? AND amaieraData IS NULL'
            )->execute([$existing['id']]);
        }
    },

    // Todo equipo nuevo entra en el aula "Almacén" (si existe) para que siempre tenga ubicación.
    'after' => function (string $method, int $id, array $data, PDO $pdo, array $user): void {
        if ($method !== 'POST' || ($data['egoera'] ?? '') === 'Baja') {
            return;
        }
        $st = $pdo->prepare('SELECT id FROM gelak WHERE izena = ? LIMIT 1');
        $st->execute([GELA_ALMAZENA]);
        $gelaId = $st->fetchColumn();
        if ($gelaId === false) {
            return;
        }
        $pdo->prepare(
            'INSERT INTO mugimenduak (gelaId, gailuId, erabiltzaileId, hasieraData)
             VALUES (?, ?, ?, CURDATE())'
        )->execute([$gelaId, $id, $user['id']]);
    },
]);