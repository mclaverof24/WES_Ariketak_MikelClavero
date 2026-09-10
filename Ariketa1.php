<!DOCTYPE html>
<html>
<body>

<h1>Ariketa 1</h1>

<h2>Ariketa 1.1</h2>
<?php
// '$zenbaki' izeneko aldagaia sortu eta 7 balioa ezarri
$zenbaki = 7;

// 'echo' komandoak testua pantailan erakusten du.
// Puntuak (.) testua eta aldagai baten balioa elkartzeko (konkatenatzeko) balio du.
echo "Zenbakia: " . $zenbaki;
?>


<h2>Ariketa 1.2</h2>
<?php
// '$balioa' aldagaiari 5 balioa eman
$balioa = 5;

// Egiaztatu '$balioa' 10 baino handiagoa den
if ($balioa > 10) {
    // Baldintza EGIA bada, mezu hau erakutsiko du:
    echo "Balioa handia da";
} else {
    // Baldintza GEZURRA bada (5 ez da 10 baino handiagoa), hemen sartuko da:
    echo "Balioa txikia da";
}
?>


<h2>Ariketa 1.3</h2>
<?php
// '$erosketa' aldagaiari 15 balioa ezarri
$erosketa = 15;

// Balioa 10 baino handiagoa den aztertu
if ($erosketa > 10) {
    // 15 > 10 EGIA denez, barruan sartu eta mezua inprimatuko du.
    // 10 edo gutxiago balitz, ez luke ezer egingo.
    echo "Erosketa kopurua handia da";
}
?>


<h2>Ariketa 1.4</h2>
<?php
// Sisteman gordetako benetako PIN zenbakia
$benetakoPIN = 1234;

// Erabiltzaileak sartutako PIN zenbakia
$erabiltzailePIN = 1234;

// === eragileak bi balioak ZORROTZ berdinak diren begiratzen du (balioa eta datu mota bera)
if ($erabiltzailePIN === $benetakoPIN) {
    // Biak 1234 direnez, baimen mezua erakusten du
    echo "PIN zuzena da.";
} else {
    // Berdinak ez balira, mezu hau erakutsiko luke
    echo "PIN okerra da.";
}
?>


<h2>Ariketa 1.5</h2>
<?php
// Erabiltzailearen adina gorde (20 urte)
$adina = 20;

// Testu-aldagai huts bat hasieratu emaitza gordetzeko
$baimendutako_mezua = ""; 

// Adina 18 edo gehiago den egiaztatu (adinez nagusia den)
if ($adina >= 18) {
    // 20 >= 18 EGIA denez, testu hau aldagai barruan gordetzen dugu
    $baimendutako_mezua = "Gure lokalera sartu zaitezke";
} else {
    // 18 urte baino gutxiago balitu, beste testu hau gordeko luke
    $baimendutako_mezua = "Ezin zara sartu";
}

// Amaitzeko, aldagaiak gordeta duen testua pantailan inprimatzen dugu
echo $baimendutako_mezua;
?>

</body>
</html>