<?php
interface ZarataEgin {
    public function esan();
}

class Txakurra implements ZarataEgin {
    public function esan() {
        return "GUAU";
    }
}

class Katua implements ZarataEgin {
    public function esan() {
        return "MIAU";
    }
}
?>