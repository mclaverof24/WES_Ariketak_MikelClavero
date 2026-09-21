<?php
class Ikaslea {
    private $izena;
    private $notak = array();

    public function __construct($izena) {
        $this->izena = $izena;
    }

    public function notaGehitu($ikasgaia, $nota) {
        $this->notak[$ikasgaia] = $nota;
    }

    public function batazBestekoa() {
        if (empty($this->notak)) return 0;
        return array_sum($this->notak) / count($this->notak);
    }

    public function erakutsiNotak() {
        echo "<p>Ikasle: " . $this->izena . "</p>";
        echo "<ul>";
        foreach ($this->notak as $ikasgaia => $nota) {
            echo "<li>" . $ikasgaia . ": " . $nota . ".</li>";
        }
        echo "</ul>";
        echo "<p>Bataz bestekoa: " . number_format($this->batazBestekoa(), 1) . "</p>";
    }
}
?>