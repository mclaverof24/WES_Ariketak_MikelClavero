<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ariketa 3</title>
    <style>
        /* Zerrenden puntu beltzak kentzeko CSS estiloa */
        ul {
            list-style-type: none;
        }
    </style>
</head>
<body>

    <h1>Ariketa 3</h1>
    <h2>Ariketa 3.1</h2>
    <?php
    $i = 0;        // Begiztaren kontagailua
    $batura = 0;   // Zenbakien batura gordetzeko aldagai bat hasieratu

    // Begizta hau 10 aldiz exekutatuko da ($i-k 0tik 9ra arteko balioak hartuko ditu)
    while ($i < 10) {
        $zenbakia = rand(1, 10); // 1 eta 10 arteko ausazko zenbaki bat sortu
        $batura += $zenbakia;    // Batuketa pilatu ($batura = $batura + $zenbakia)
        $i++;                    // Kontagailua unitate batean handitu
    }

    echo "10 zenbakien batura guztira: " . $batura;
    ?>

    <h2>Ariketa 3.2</h2>
    <?php
    $biderkadura = 1; // Biderkadurak 1etik hasi behar du (0tik hasiz gero emaitza beti 0 litzateke)

    // $j aldagaiak 1etik 5era arteko balioak hartuko ditu, banaka handituz ($j++)
    for ($j = 1; $j <= 5; $j++) {
        $biderkadura *= $j; // $biderkadura = $biderkadura * $j (5! edo 1x2x3x4x5)
    }

    echo "Bost zenbakien biderkadura: ".$biderkadura;
    ?>

    <h2>Ariketa 3.3</h2>
    <?php
    $zenbakia = 3;

    // 'do' zatia gutxienez behin exekutatzen da, baldintza egiaztatu aurretik
    do {
        echo $zenbakia ." ";
        $zenbakia += 3;
    } while ($zenbakia <= 30);
    ?>

    <h2>Ariketa 3.4</h2>
    <?php
    // Herrialdeen izenekin osatutako zerrenda (array) bat sortu
    $herrialdeak = array("EH", "Frantzia", "Alemania", "Italia");

    echo "<ul>";
    // 'foreach'-ek zerrendako elementu bakoitza hartu eta $herrialdea aldagaian jartzen du banan-banan
    foreach ($herrialdeak as $herrialdea) {
        echo "<li>" . $herrialdea . "</li>"; // Zerrendako elementu moduan inprimatu
    }
    echo "</ul>";
    ?>


    <h2>Ariketa 3.5</h2>
    <?php
    $kontagailua = 0;   // Aurkitutako zenbaki lehenen kopurua gorde
    $lehenak = array(); // Zenbaki lehen guztiak gordetzeko zerrenda hutsa

    // 1etik 100era arteko zenbaki guztiak aztertu
    for ($n = 1; $n <= 100; $n++) {
        if ($n <= 1) {
            continue;
        }

        $isPrimo = true;

        // $n-ren zatigarritasuna egiaztatu 2tik bere erro karratura arte
        for ($k = 2; $k * $k <= $n; $k++) {
            if ($n % $k == 0) { // Zatiketaren hondarra 0 bada (zatigarria da)
                $isPrimo = false;
                break;            // Begiztatik irten (ez da gehiago egiaztatu behar)
            }
        }

        // $isPrimo EGIA izaten jarraitzen badu, zerrendan gorde
        if ($isPrimo) {
            $lehenak[] = $n; // Zerrendaren amaieran zenbakia gehitu
            $kontagailua++;  // Kontagailua inkrementatu
        }
    }

    // implode() funtzioak zerrendako elementuak komaz berdin lotzen ditu testu bakarrean
    echo implode(", ", $lehenak) . "</p>";
    echo "<p>Kopurua: " . $kontagailua;
    ?>

</body>
</html>