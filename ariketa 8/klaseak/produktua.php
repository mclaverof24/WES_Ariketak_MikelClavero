<?php
class Produktua {
    private $izenburua;
    private $prezioa;

    public function __construct($izenburua, $prezioa) {
        $this->izenburua = $izenburua;
        $this->prezioa = $prezioa;
    }

    public function aukeratu($kopurua) {
        $prezioFinala = $this->prezioa * $kopurua;
        $this->pantailaratu($prezioFinala);
    }

    public function pantailaratu($prezioFinala) {
        echo "<p>Produktua: " . $this->izenburua . " Prezioa: " . $prezioFinala . "</p>";
    }
}
?>