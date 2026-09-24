<?php
// Interfaze bat sortzen dugu. Interfazeak klase batzuek nahitaez
// ezarri/inplementatu behar dituzten metodoen "kontratua" edo moldea definitzen du.
interface ZarataEgin {
    // Interfazearen barruan metodoen izenak soilik definitzen dira (edukirik gabe).
    // Interfaze hau erabiltzen duen klase orok 'esan()' metodoa eduki BHARKODU.
    public function esan();
}

// 'Txakurra' klaseak 'ZarataEgin' interfazea inplementatzen du ('implements')
class Txakurra implements ZarataEgin {
    // Interfazeak eskatutako 'esan()' metodoari edukia eta logika ematen diogu
    public function esan() {
        return "GUAU";
    }
}

// 'Katua' klaseak ere 'ZarataEgin' interfaze bera inplementatzen du
class Katua implements ZarataEgin {
    // Katuaren kasuan, 'esan()' metodoak berezko testua itzuliko du
    public function esan() {
        return "MIAU";
    }
}
?>