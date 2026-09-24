<?php
// Liburu ezberdinen bilduma edo katalogoa kudeatzen duen klasea
class LiburuKatalogoa {
    // Propietate pribatua: Liburua objektuak gordetzeko array/zerrenda hutsa
    private $liburuak = array();

    // Liburu berri bat katalogoan sartzeko metodoa.
    // 'Liburua $liburua': Datu-mota zehaztea da (Type Hinting).
    // Horrek ziurtatzen du jaso behar den parametroak nahitaez 'Liburua' klaseko objektu bat izan behar duela.
    public function liburuaGehitu(Liburua $liburua) {
        $this->liburuak[] = $liburua; // Objektua array-aren amaieran gehitu
    }

    // Katalogoan dauden liburu guztiak HTML zerrenda batean (<ul>/<li>) inprimatzen dituen metodoa
    public function katalogoaBistaratu() {
        echo "<ul>";
        // Katalogoko Liburua objektu bakoitza ($liburua) banan-banan arakatu
        foreach ($this->liburuak as $liburua) {
            // Liburua objektuaren jabego publikoetara ($liburua->izena eta $liburua->egilea) sartu eta erakutsi
            echo "<li>" . $liburua->izena . " (Egilea: " . $liburua->egilea . ")</li>";
        }
        echo "</ul>";
    }
}
?>