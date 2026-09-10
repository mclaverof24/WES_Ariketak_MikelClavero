<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ariketa 2</title>
</head>
<body>
    <h1>Ariketa 2</h1>


    <div>
        <h2>1. Asteko eguna</h2>
        <?php
        // date("N") funtzioak gaurko asteko eguna zenbaki bidez itzultzen du:
        // 1 (Astelehena) datutik 7 (Igandea) datera.
        $eguna = date("N");

        // Zenbakiaren arabera, egunei dagokien testu-izena esleitzen diegu
        if ($eguna == 1) {
            $egunIzena = "Astelehena";
        } elseif ($eguna == 2) {
            $egunIzena = "Asteartea";
        } elseif ($eguna == 3) {
            $egunIzena = "Asteazkena";
        } elseif ($eguna == 4) {
            $egunIzena = "Osteguna";
        } elseif ($eguna == 5) {
            $egunIzena = "Ostirala";
        } elseif ($eguna == 6) {
            $egunIzena = "Larunbata";
        } else {
            // Eguna 1 eta 6 artekoa ez bada, nekez 7 izango da (Igandea)
            $egunIzena = "Igandea";
        }
        ?>
        <!-- HTML barruan PHPko aldagaiak inprimatzen ditugu -->
        <p>Asteko eguna: <?php echo $eguna; ?></p>
        <p>Egunaren izena: <?php echo $egunIzena; ?></p>
    </div>


    <div>
        <h2>2. Ingelesezko notak</h2>
        <?php
        // Notaren letra aldagai batean ezartzen dugu
        $nota = "B";

        // 'switch' egiturak balio bera hainbat kasurekin (case) alderatzeko balio du
        switch ($nota) {
            case "F":
                $mezua = "oso gutxi";
                break; // 'break' aginduak 'switch' egituratik ateratzen gaitu behin katsua aurkituta
            case "D":
                $mezua = "gutxi";
                break;
            case "C":
                $mezua = "nahiko";
                break;
            case "B":
                $mezua = "ondo";
                break;
            case "A":
                $mezua = "oso ondo";
                break;
            default:
                // Letra goiko kasuetako bat ere ez bada, 'default' atala exekutatzen da
                $mezua = "nota ez da ezaguna";
                break;
        }
        ?>
        <p>Nota: <?php echo $nota; ?></p>
        <p>Emaitza: <?php echo $mezua; ?></p>
    </div>


    <div>
        <h2>3. Gutxieneko eta gehienezko kopurua</h2>
        <?php
        // Gutxieneko eta gehienezko muga-aldagaiak definitu
        $gutxieneko = 0;
        $gehienezko = 30;

        // rand(min, max) funtzioak 0 eta 30 arteko ausazko zenbaki bat sortzen du
        $zenbakia = rand(0, 30);

        // Bi baldintza aldi berean egiaztatzeko and (&&) eragilea erabiltzen da
        if ($zenbakia >= 0 and $zenbakia <= 10) {
            $kategoria = "0 eta 10 artean dago";
        } elseif ($zenbakia > 10 and $zenbakia <= 20) {
            $kategoria = "10 eta 20 artean dago";
        } elseif ($zenbakia > 20 and $zenbakia <= 30) {
            $kategoria = "20 eta 30 artean dago";
        } else {
            $kategoria = "Zenbakia ez da sailkatuta";
        }
        ?>
        <p>Ausazko zenbakia: <?php echo $zenbakia; ?></p>
        <p>Gutxieneko kopurua: <?php echo $gutxieneko; ?></p>
        <p>Gehienezko kopurua: <?php echo $gehienezko; ?></p>
        <p>Sailkapena: <?php echo $kategoria; ?></p>
    </div>
</body>
</html>