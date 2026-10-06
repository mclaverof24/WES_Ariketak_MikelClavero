<?php
require_once __DIR__ . '/konexioa.php';

/**
 * Operaciones sobre la tabla "taldeak".
 */
class Taldea
{
    public static function guztiak(): array
    {
        return Konexioa::lortu()->query('SELECT * FROM taldeak ORDER BY id')->fetchAll();
    }

    public static function bilatu(int $id): ?array
    {
        $st = Konexioa::lortu()->prepare('SELECT * FROM taldeak WHERE id = ?');
        $st->execute([$id]);
        $talde = $st->fetch();
        return $talde ?: null;
    }

    public static function sortu(string $izena, int $puntuak): void
    {
        $st = Konexioa::lortu()->prepare('INSERT INTO taldeak (izena, puntuak) VALUES (?, ?)');
        $st->execute([$izena, $puntuak]);
    }

    public static function puntuakAldatu(int $id, int $puntuak): void
    {
        $st = Konexioa::lortu()->prepare('UPDATE taldeak SET puntuak = ? WHERE id = ?');
        $st->execute([$puntuak, $id]);
    }

    /** Borra el equipo junto con sus miembros */
    public static function ezabatu(int $id): void
    {
        $pdo = Konexioa::lortu();
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM partaideak WHERE taldea_id = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM taldeak WHERE id = ?')->execute([$id]);
        $pdo->commit();
    }
}