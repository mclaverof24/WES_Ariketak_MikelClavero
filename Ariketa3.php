<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ariketa 3</title>

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

    // 'foreach'-ek zerrendako elementu bakoitza hartu eta $herrialdea aldagaian jartzen du banan-banan
    foreach ($herrialdeak as $herrialdea) {
        echo "$herrialdea <br>" ; // Zerrendako elementu moduan inprimatu
    }
    ?>


    <h2>Ariketa 3.5</h2>
    <?php
    // Aurkitutako zenbaki lehenen kopurua zenbatzeko aldagai bat hasieratu
    $hainbat = 0;

    // Begizta (bucle) nagusia: 2tik 100era arteko zenbaki guztiak banan-banan aztertuko ditu
    for ($zenb = 2; $zenb <= 100; $zenb++) {
        
        // Hasieran suposatzen dugu aztertzen ari garen zenbakia ($zenb) LEHENA dela
        $lehen = true;

        // Bigarren begizta: $zenb hori ea beste zenbakiren batekin zatigarria den egiaztatuko du
        // (2tik hasi eta aztertzen ari garen zenbakia baino bat gutxiagora arte)
        for ($i = 2; $i < $zenb; $i++) {
            
            // Onarpen-baldintza: hondarra (%) 0 bada, zatiketa zehatza da (zatigarria da)
            if ($zenb % $i == 0) {
                // Ez denez lehena, 'false' jarri eta barruko begiztatik irten (ez du gehiago bilatu behar)
                $lehen = false;
                break;
            }
        }

        // '$lehen' aldagaiak 'true' izaten jarraitzen badu, zenbakia lehena dela esan nahi du
        if ($lehen) {
            echo $zenb . " "; // Zenbakia eta zuriune bat inprimatu
            $hainbat++;       // Aurkitutako zenbaki lehenen kontagailuari 1 gehitu
        }
    }

    echo "<br>";
    echo "Kopurua: " . $hainbat;
    ?>

</body>
</html>