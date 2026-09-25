<?php
require_once __DIR__ . '/../config/conexioa.php';

/**
 * Partaideak taulari dagokion klasea.
 */
class Partaidea {
    public $id;
    public $izena;
    public $herrialdea;
    public $taldea_id;

    public function __construct($id = null, $izena = null, $herrialdea = null, $taldea_id = null) {
        $this->id = $id;
        $this->izena = $izena;
        $this->herrialdea = $herrialdea;
        $this->taldea_id = $taldea_id;
    }

    /** Talde jakin bateko partaide guztiak itzuli. */
    public static function taldekoak($taldea_id) {
        $pdo = Conexioa::lortu();
        $stmt = $pdo->prepare("SELECT * FROM Partaideak WHERE taldea_id = ? ORDER BY izena ASC");
        $stmt->execute([$taldea_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Objektu honen datuekin partaide berri bat sortu. */
    public function gehitu() {
        $pdo = Conexioa::lortu();
        $stmt = $pdo->prepare("INSERT INTO Partaideak (izena, herrialdea, taldea_id) VALUES (?, ?, ?)");
        return $stmt->execute([$this->izena, $this->herrialdea, $this->taldea_id]);
    }
}