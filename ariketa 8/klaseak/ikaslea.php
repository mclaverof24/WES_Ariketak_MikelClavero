<?php
// Ikasle baten datuak eta notak kudeatzeko klasea
class Ikaslea {
    // Propietate pribatuak (private): Klase honen barruan soilik erabil daitezke
    private $izena;            // Ikaslearen izena gordetzeko
    private $notak = array();  // Notak gordetzeko array elkartzaile hutsa

    // Eraikitzailea (__construct): 'new Ikaslea("Nora")' egiterakoan automatikoki exekutatzen da
    public function __construct($izena) {
        // $this hitzak klase honetako uneko objektuari erreferentzia egiten dio
        $this->izena = $izena;
    }

    // Ikasgaia eta nota zerrendan gorde/gehitzeko metodoa
    public function notaGehitu($ikasgaia, $nota) {
        // $ikasgaia gako bezala erabiliz, nota balioa esleitzen zaio
        $this->notak[$ikasgaia] = $nota;
    }

    // Ikaslearen bataz besteko nota kalkulatzen duen metodoa
    public function batazBestekoa() {
        // Notarik ez badago, 0 itzuli erroredun zatiketak (zati 0) ekiditeko
        if (empty($this->notak)) return 0;
        
        // Noten batura zati ikasgai kopurua (count)
        return array_sum($this->notak) / count($this->notak);
    }

    // Ikaslearen informazio guztia HTML bidez inprimatzen duen metodoa
    public function erakutsiNotak() {
        echo "<p>Ikasle: " . $this->izena . "</p>";
        echo "<ul>";
        // Notak zerrenda batean (<ul> / <li>) bistaratu
        foreach ($this->notak as $ikasgaia => $nota) {
            echo "<li>" . $ikasgaia . ": " . $nota . ".</li>";
        }
        echo "</ul>";
        // Bataz besteko nota hamartar bakarrekin (1) inprimatu
        echo "<p>Bataz bestekoa: " . number_format($this->batazBestekoa(), 1) . "</p>";
    }
}
?>