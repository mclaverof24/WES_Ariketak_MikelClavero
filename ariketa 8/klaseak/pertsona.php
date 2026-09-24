<?php
// Klase Abstraktua (abstract class): Klase honek beste klase batzuentzat "oinarri" edo "molde" gisa balio du.
// Ezin da zuzenean instantziatu (ezin da 'new Pertsona()' egin), herentzia bidez erabiltzeko da.
abstract class Pertsona {
    // Propietate babestua (protected): Klase honetan ETA hemendik sortutako klase umeetan 
    // (Programatzailea, Diseinatzailea) bakarrik erabil daiteke, baina ez kanpotik.
    protected $izenAbizenak;

    // Eraikitzailea: Izena jasotzen du klase ume bat sortzen denean
    public function __construct($izenAbizenak) {
        $this->izenAbizenak = $izenAbizenak;
    }

    // Metodo Abstraktua: Ez du barruko koderik. Arau bat ezartzen du:
    // Pertsona klasea hedatzen duten klase ume GUZTIEK nahitaez definitu behar dute 'aurkeztu()' metodoa.
    abstract public function aurkeztu();
}

// 'Programatzailea' klaseak 'Pertsona' klaseko propietate eta metodoak oredetsitzen ditu ('extends')
class Programatzailea extends Pertsona {
    // Pertsona klaseak eskatzen duen 'aurkeztu()' metodo abstraktuari berezko logika ematen diogu
    public function aurkeztu() {
        // $this->izenAbizenak erabil dezakegu guraso klasean 'protected' gisa definituta dagoelako
        return "Kaixo " . $this->izenAbizenak . " deitzen naiz eta programatzailea naiz.";
    }
}

// 'Diseinatzailea' klaseak ere 'Pertsona' klasea hedatzen du
class Diseinatzailea extends Pertsona {
    // Diseinatzaileari dagokion aurkezpen-testua itzultzen duen metodoa
    public function aurkeztu() {
        return "Kaixo " . $this->izenAbizenak . " deitzen naiz eta diseinatzailea naiz.";
    }
}
?>