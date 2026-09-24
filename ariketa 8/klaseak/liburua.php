<?php
// Liburu baten datuak (izena eta egilea) ordezkatzen dituen klase sinplea
class Liburua {
    // Propietate publikoak (public): Klase honetatik nahiz kanpotik zuzenean ikusi eta alda daitezke
    public $izena;
    public $egilea;

    // Eraikitzailea (__construct): 'new Liburua("Harry Potter", "J.K. Rowling")'
    // egiterakoan automatikoki exekutatzen da
    public function __construct($izena, $egilea) {
        // Kanpotik jasotako balioak ($izena eta $egilea) 
        // objektu honen propietateetan ($this->izena, $this->egilea) gordetzen dira
        $this->izena = $izena;
        $this->egilea = $egilea;
    }
}
?>