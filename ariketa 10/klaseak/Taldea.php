<?php
require_once __DIR__ . '/../config/conexioa.php';

/**
 * Taldea taulari dagokion klasea.
 */
class Taldea {
    public $id;
    public $izena;
    public $puntuak;

    public function __construct($id = null, $izena = null, $puntuak = null) {
        $this->id = $id;
        $this->izena = $izena;
        $this->puntuak = $puntuak;
    }

    /** Talde guztiak itzuli, puntuen arabera ordenatuta. */
    public static function guztiak() {
        $pdo = Conexioa::lortu();
        $stmt = $pdo->query("SELECT * FROM Taldea ORDER BY puntuak DESC, izena ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ID baten araberako taldea bilatu. */
    public static function bilatu($id) {
        $pdo = Conexioa::lortu();
        $stmt = $pdo->prepare("SELECT * FROM Taldea WHERE id = ?");
        $stmt->execute([$id]);
        $emaitza = $stmt->fetch(PDO::FETCH_ASSOC);
        return $emaitza ?: null;
    }

    /** Objektu honen datuekin talde berri bat sortu. */
    public function gehitu() {
        $pdo = Conexioa::lortu();
        $stmt = $pdo->prepare("INSERT INTO Taldea (izena, puntuak) VALUES (?, ?)");
        return $stmt->execute([$this->izena, $this->puntuak]);
    }

    /** Talde baten puntuazioa eguneratu. */
    public static function aldatuPuntuak($id, $puntuak) {
        $pdo = Conexioa::lortu();
        $stmt = $pdo->prepare("UPDATE Taldea SET puntuak = ? WHERE id = ?");
        return $stmt->execute([$puntuak, $id]);
    }

    /** Taldea ezabatu (Partaideak taulan ON DELETE CASCADE dagoenez, partaideak ere ezabatuko dira). */
    public static function ezabatu($id) {
        $pdo = Conexioa::lortu();
        $stmt = $pdo->prepare("DELETE FROM Taldea WHERE id = ?");
        return $stmt->execute([$id]);
    }
}