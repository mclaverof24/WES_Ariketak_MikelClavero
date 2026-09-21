<?php
abstract class Pertsona {
    protected $izenAbizenak;

    public function __construct($izenAbizenak) {
        $this->izenAbizenak = $izenAbizenak;
    }

    abstract public function aurkeztu();
}

class Programatzailea extends Pertsona {
    public function aurkeztu() {
        return "Kaixo " . $this->izenAbizenak . " deitzen naiz eta programatzailea naiz.";
    }
}

class Diseinatzailea extends Pertsona {
    public function aurkeztu() {
        return "Kaixo " . $this->izenAbizenak . " deitzen naiz eta diseinatzailea naiz.";
    }
}
?>