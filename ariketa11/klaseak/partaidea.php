<?php
require_once __DIR__ . '/konexioa.php';

/**
 * Operaciones sobre la tabla "partaideak".
 */
class Partaidea
{
    public static function taldekoak(int $taldeaId): array
    {
        $st = Konexioa::lortu()->prepare('SELECT * FROM partaideak WHERE taldea_id = ? ORDER BY id');
        $st->execute([$taldeaId]);
        return $st->fetchAll();
    }

    public static function sortu(string $izena, string $herrialdea, int $taldeaId): void
    {
        $st = Konexioa::lortu()->prepare(
            'INSERT INTO partaideak (izena, herrialdea, taldea_id) VALUES (?, ?, ?)'
        );
        $st->execute([$izena, $herrialdea, $taldeaId]);
    }
}