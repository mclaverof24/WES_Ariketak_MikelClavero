<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Ariketa 5</title>
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

    <h2>Ariketa 5.1</h2>
    <?php
    // Dimentsio anitzeko array-a: Liburu bakoitza gako-balio (clave-valor) bikote bat da
    $liburuak = [
        ["izena" => "Harry Potter", "autorea" => "J.K. Rowling"],
        ["izena" => "Game of Thrones", "autorea" => "George R.R. Martin"],
        ["izena" => "The Hobbit", "autorea" => "J.R.R. Tolkien"]
    ];
    ?>
    <table>
        <tr>
            <th>Liburua</th>
            <th>Autorea</th>
        </tr>
        <!-- 'foreach'-ek liburu bakoitzaren datuak ($liburua) hartzen ditu banan-banan -->
        <?php foreach ($liburuak as $liburua): ?>
            <tr>
                <!-- Gakoaren izena erabiliz ("izena", "autorea") balio bakoitza inprimatzen dugu -->
                <td><?php echo $liburua["izena"]; ?></td>
                <td><?php echo $liburua["autorea"]; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Ariketa 5.2</h2>
    <?php
    $ikasleak_notak = [
        ["ikaslea" => "Jon", "nota" => 8],
        ["ikaslea" => "Ane", "nota" => 9],
        ["ikaslea" => "Markel", "nota" => 7]
    ];

    $baturak = 0; // Noten batura metatzeko aldagai bat hasieratu
    $kopuru = count($ikasleak_notak); // count() funtzioak zerrendan zenbat ikasle dauden zenbatzen du (3)

    // 'for' begizta erabiliz zerrendaren indize digitalak (0, 1, 2) arakatzen ditugu
    for ($i = 0; $i < $kopuru; $i++) {
        $baturak += $ikasleak_notak[$i]["nota"]; // Baturari ikasle bakoitzaren nota gehitu
    }

    $batazbestekoa = $baturak / $kopuru;
    ?>
    <table>
        <tr>
            <th>Ikaslea</th>
            <th>Nota</th>
        </tr>
        <!-- Berriro 'for' erabiliz taulako lerroak inprimatzen ditugu -->
        <?php for ($i = 0; $i < $kopuru; $i++): ?>
            <tr>
                <td><?php echo $ikasleak_notak[$i]["ikaslea"]; ?></td>
                <td><?php echo $ikasleak_notak[$i]["nota"]; ?></td>
            </tr>
        <?php endfor; ?>
        <tr>
            <th>Batazbestekoa</th>
            <!-- number_format(balioa, 2) funtzioak 2 hamartarretara mugatzen du erakutsitako emaitza -->
            <th><?php echo number_format($batazbestekoa, 2); ?></th>
        </tr>
    </table>

    <h2>Ariketa 5.3</h2>
    <?php
    $ebaluazioak = [
        ["ikaslea" => "Jon", "nota" => 8],
        ["ikaslea" => "Ane", "nota" => 6],
        ["ikaslea" => "Markel", "nota" => 3]
    ];
    ?>
    <table>
        <tr>
            <th>Ikaslea</th>
            <th>Nota</th>
            <th>Ebaluazioa</th>
        </tr>
        <?php foreach ($ebaluazioak as $item): ?>
            <?php
            $nota = $item["nota"];
            
            // Notaren arabera kalifikazio testua esleitu
            if ($nota < 5) {
                $egoera = "Txarra"; 
            } elseif ($nota <= 7) {
                $egoera = "Ona";  
            } else {
                $egoera = "Oso ona";   
            }
            ?>
            <tr>
                <td><?php echo $item["ikaslea"]; ?></td>
                <td><?php echo $item["nota"]; ?></td>
                <td><?php echo $egoera; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Ariketa 5.4</h2>
    <?php
    // Erregistro bakoitzak 4 eremu/gako dituen array-en zerrenda
    $ikasleak_datuak = [
        ["izena" => "Jon", "abizena" => "Garmendia", "telefonoa" => "600111222", "adina" => 20],
        ["izena" => "Ane", "abizena" => "Etxeberria", "telefonoa" => "611222333", "adina" => 22],
        ["izena" => "Markel", "abizena" => "Zubizarreta", "telefonoa" => "622333444", "adina" => 21]
    ];
    ?>
    <table>
        <tr>
            <th>Izena</th>
            <th>Abizena</th>
            <th>Telefonoa</th>
            <th>Adina</th>
        </tr>
        <?php foreach ($ikasleak_datuak as $ikaslea): ?>
            <tr>
                <td><?php echo $ikaslea["izena"]; ?></td>
                <td><?php echo $ikaslea["abizena"]; ?></td>
                <td><?php echo $ikaslea["telefonoa"]; ?></td>
                <td><?php echo $ikaslea["adina"]; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Ariketa 5.5</h2>
    <?php
    // Ikasle bakoitzak array anitz ditu bere barnean (abizenak, telefonoak eta notak)
    $ikasleak_osatua = [
        [
            "izena" => "Jon",
            "abizenak" => ["Garmendia", "Iparragirre"],
            "telefonoak" => ["600111222", "943111222"],
            "adina" => 20,
            "notak" => ["Programazioa" => 8, "Sareak" => 7, "Datubaseak" => 9]
        ],
        [
            "izena" => "Ane",
            "abizenak" => ["Etxeberria", "Larrañaga"],
            "telefonoak" => ["611222333", "943222333"],
            "adina" => 22,
            "notak" => ["Programazioa" => 9, "Sareak" => 9, "Datubaseak" => 10]
        ],
        [
            "izena" => "Markel",
            "abizenak" => ["Zubizarreta", "Goikoetxea"],
            "telefonoak" => ["622333444", "943333444"],
            "adina" => 21,
            "notak" => ["Programazioa" => 6, "Sareak" => 5, "Datubaseak" => 7]
        ]
    ];
    ?>
    <table>
        <tr>
            <th>Izena</th>
            <th>Abizenak</th>
            <th>Telefonoak</th>
            <th>Adina</th>
            <th>Notak (Moduloak)</th>
        </tr>
        <?php foreach ($ikasleak_osatua as $ikaslea): ?>
            <tr>
                <td><?php echo $ikaslea["izena"]; ?></td>
                
                <!-- implode(" ", ...): Abizen biak zuriune bat jarriz lotzen ditu -->
                <td><?php echo implode(" ", $ikaslea["abizenak"]); ?></td>
                
                <!-- implode(" / ", ...): Bi telefonoak barraz "/" bananduta elkartzen ditu -->
                <td><?php echo implode(" / ", $ikaslea["telefonoak"]); ?></td>
                
                <td><?php echo $ikaslea["adina"]; ?></td>
                <td>
                    <?php 
                    $notak_testua = [];
                    // Barruko 'foreach' hau notak kudeatzeko: $moduloa gakoa da eta $nota balioa
                    foreach ($ikaslea["notak"] as $moduloa => $nota) {
                        $notak_testua[] = "$moduloa: $nota";
                    }
                    // Prestaturiko testu guztiak komaz elkartzen ditugu
                    echo implode(", ", $notak_testua);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>