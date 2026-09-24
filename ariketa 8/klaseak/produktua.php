<?php
// Produktu baten datuak (izenburua eta unitate bakoitzaren prezioa) kudeatzeko klasea
class Produktua {
    // Propietate pribatuak (private): Klase honen barruan soilik sar gaitezke balio hauetara
    private $izenburua;
    private $prezioa;

    // Eraikitzailea (__construct): Produktu berri bat sortzen denean 
    // ($produktua = new Produktua("Sagarra", 7)) izenburua eta prezioa esleitzeko
    public function __construct($izenburua, $prezioa) {
        $this->izenburua = $izenburua;
        $this->prezioa = $prezioa;
    }

    // Erositako unitate kopurua jaso eta prezio osoa kalkulatzen duen metodo publikoa
    public function aukeratu($kopurua) {
        // Unitateko prezioa bider jasotako kopurua eginez prezio totala kalkulatu
        $prezioFinala = $this->prezioa * $kopurua;
        
        // Jarraian, emaitza pantailaratzen duen barne-metodoari deitu
        $this->pantailaratu($prezioFinala);
    }

    // Kalkulatutako azken prezioa HTML formatuan inprimatzen duen metodoa
    public function pantailaratu($prezioFinala) {
        echo "<p>Produktua: " . $this->izenburua . " Prezioa: " . $prezioFinala . "</p>";
    }
}
?>