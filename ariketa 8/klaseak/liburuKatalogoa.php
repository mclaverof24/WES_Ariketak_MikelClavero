<?php
class LiburuKatalogoa {
    private $liburuak = array();

    public function liburuaGehitu(Liburua $liburua) {
        $this->liburuak[] = $liburua;
    }

    public function katalogoaBistaratu() {
        echo "<ul>";
        foreach ($this->liburuak as $liburua) {
            echo "<li>" . $liburua->izena . " (Egilea: " . $liburua->egilea . ")</li>";
        }
        echo "</ul>";
    }
}
?>