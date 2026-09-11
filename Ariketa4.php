<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Ariketa 4</title>
    <style>
        table, th, td {
            border: 1px solid black;  
        }
        th {
            font-weight: bold; 
        }
    </style>
</head>
<body>

    <h1>Ariketa 4</h1>
    <h2>Ariketa 4.1</h2>
    <?php
    $zenbakiak = array(); // Array (zerrenda) huts bat sortu

    // 5 aldiz exekutatu 1 eta 100 arteko ausazko zenbakiak zerrendan gordeaz
    for ($i = 0; $i < 5; $i++) {
        $zenbakiak[] = rand(1, 100); // [] erabiliz elementua zerrendaren amaieran gehitzen da
    }

    // array_sum() funtzioak zerrendako zenbaki guztien batura kalkulatzen du
    $batura = array_sum($zenbakiak);
    ?>

    <table>
        <tr>
            <th>1. zenbakia</th>
            <th>2. zenbakia</th>
            <th>3. zenbakia</th>
            <th>4. zenbakia</th>
            <th>5. zenbakia</th>
        </tr>
        <tr>
            <!-- Sintaxi alternatiboa: 'foreach (...):' eta 'endforeach;' HTML garbiago idazteko -->
            <?php foreach ($zenbakiak as $zenbakia): ?>
                <td><?php echo $zenbakia; ?></td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <!-- colspan="5" atributuak 5 zutabe elkartzen ditu lerro bakarrean -->
            <td colspan="5">Batura: <?php echo $batura; ?></td>
        </tr>
    </table>

    <h2>Ariketa 4.2</h2>
    <?php
    $herrialdeak = array("EH", "Frantzia", "Alemania", "Italia");

    // sort() funtzioak array-ko testuak alfabetikoki ordenatzen ditu (A-Z)
    sort($herrialdeak);
    ?>

    <table>
        <tr>
            <th>1. herrialdea</th>
            <th>2. herrialdea</th>
            <th>3. herrialdea</th>
            <th>4. herrialdea</th>
        </tr>
        <tr>
            <!-- Ordenatutako herrialdeak taulako gelaxketan inprimatu -->
            <?php foreach ($herrialdeak as $herrialdea): ?>
                <td><?php echo $herrialdea; ?></td>
            <?php endforeach; ?>
        </tr>
    </table>

    <h2>Ariketa 4.3</h2>
    <?php
    $ausazkoak = array();

    // 6 ausazko zenbaki sortu eta zerrendan gorde
    for ($i = 0; $i < 6; $i++) {
        $ausazkoak[] = rand(1, 100);
    }
    ?>

    <table>
        <tr>
            <th>Zenbakia</th>
            <th>Bikoitia</th>
        </tr>
        <!-- Zenbaki bakoitzeko taulako lerro bat (<tr>) sortuko da -->
        <?php foreach ($ausazkoak as $z): ?>
            <tr>
                <td><?php echo $z; ?></td>
                <td>
                    <?php 
                    // % (moduloa) eragileak zatiketaren hondarra ematen du.
                    // Zenbakia 2rekin zatitzean hondarra 0 bada, BIKOITIA da.
                    if ($z % 2 == 0) {
                        echo "BAI";
                    } else {
                        echo "EZ";
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>