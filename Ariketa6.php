<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Ariketa 6</title>
</head>
<body>
    <h1>Ariketa 6</h1>


    <h2>Ariketa 6.1</h2>
    <?php
    // 'function' bidez kode berrerabilgarria sortzen dugu.
    // Funtzio honek array bat hartzen du parametro moduan ($zenbakiak) eta batura itzultzen du.
    function batuArraya($zenbakiak) {
        $batura = 0; // Batuketa metatzeko aldagai bat hasieratu
        foreach ($zenbakiak as $zenbakia) {
            $batura += $zenbakia; // Elementu bakoitza baturari gehitu
        }
        return $batura; // 'return' komandoak azken balioa funtziotik kanpora bidaltzen du
    }

    // Proba egiteko zenbakien array-a sortu
    $zenbakiZerrenda = array(4, 8, 15, 16, 23, 42);
    
    // Funtzioari deitu eta emaitza aldagai batean gorde
    $emaitza = batuArraya($zenbakiZerrenda);

    // Emaitzak pantailan erakutsi
    echo "<p>Array-eko zenbakiak: " . implode(", ", $zenbakiZerrenda) . "</p>";
    echo "<p>Zenbakien batura: " . $emaitza . "</p>";
    ?>

    <h2>Ariketa 6.2</h2>
    <?php
    // Bi array hartu, batu eta HTML taula batean bistaratzen dituen funtzioa
    function konbinatuEtaBistaratu($array1, $array2) {
        // array_merge() funtzioak bi zerrendak elkartzen ditu zerrenda bakar batean
        $konbinatua = array_merge($array1, $array2);

        // HTML taula sortu
        echo "<table border='1'>";
        echo "<tr><th>Indizea</th><th>Balioa</th></tr>";
        
        // $indizea gakoa da (0, 1, 2...) eta $balioa edukia ("Sagarra", "Goiara"...)
        foreach ($konbinatua as $indizea => $balioa) {
            echo "<tr>";
            echo "<td>" . $indizea . "</td>";
            echo "<td>" . $balioa . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Bi array sortu probak egiteko
    $frutak = array("Sagarra", "Goiara", "Mendikotea");
    $Osoak = array("Platanoa", "Marrubia");

    // Funtzioari bi array-ak pasaz deitu
    konbinatuEtaBistaratu($frutak, $Osoak);
    ?>

    <h2>Ariketa 6.3</h2>
    <?php
    // Ikasleen zerrenda hartu eta taulan inprimatzen duen funtzioa
    function bistaratuIkasleak($ikasleZerrenda) {
        echo "<table border='1'>";
        echo "<tr><th>Ikaslea</th><th>Nota</th></tr>";
        
        // $lerroa bakoitza ikasle baten array elkartzailea da (izena eta nota)
        foreach ($ikasleZerrenda as $lerroa) {
            echo "<tr>";
            echo "<td>" . $lerroa["ikaslea"] . "</td>"; // Gakoaren bidez izena lortu
            echo "<td>" . $lerroa["nota"] . "</td>";    // Gakoaren bidez nota lortu
            echo "</tr>";
        }
        echo "</table>";
    }

    // Dimentsio anitzeko array-a sortu (ikasle bakoitzak bere datuak ditu)
    $ikasleak = array(
        array("ikaslea" => "Jon", "nota" => 8),
        array("ikaslea" => "Ane", "nota" => 9),
        array("ikaslea" => "Markel", "nota" => 7)
    );

    // Funtzioari deitu
    bistaratuIkasleak($ikasleak);
    ?>

    <h2>Ariketa 6.4</h2>
    <?php
    function kalkulatuFaktoriala() {
        // 1 eta 10 arteko ausazko zenbakia sortu
        $zenbakia = rand(1, 10);
        $faktoriala = 1;         // Biderkadura gordetzeko aldagaia (1etik hasita)
        $biderketak = array();   // Urrats guztiak gordetzeko array hutsa

        // 1etik sortutako zenbakira arteko biderketak egin
        for ($i = 1; $i <= $zenbakia; $i++) {
            $faktoriala *= $i; // $faktoriala = $faktoriala * $i
            
            // Urrats bakoitzaren azalpen testua array-an gorde
            $biderketak[] = "Urratsa " . $i . ": bider " . $i . " = " . $faktoriala;
        }

        // Emaitzak erakutsi
        echo "<p>Sortutako ausazko zenbakia: " . $zenbakia . "</p>";
        echo "<p>" . $zenbakia . "!(en) faktorialaren emaitza: " . $faktoriala . "</p>";
        echo "<p>Egindako biderketak:</p>";

        // Urrats guztiak HTML zerrenda batean (<ul> / <li>) bistaratu
        echo "<ul>";
        foreach ($biderketak as $biderketa) {
            echo "<li>" . $biderketa . "</li>";
        }
        echo "</ul>";
    }

    // Funtzioari deitu exekutatu dadin
    kalkulatuFaktoriala();
    ?>

</body>
</html>