<?php
require_once 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $izena = trim($_POST['izena'] ?? '');
    $abizenak = trim($_POST['abizenak'] ?? '');
    $liburua = trim($_POST['liburua'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $alokairuData = $_POST['alokairu_data'] ?? '';
    $nan = trim($_POST['nan'] ?? '');

    $errorak = [];

    // 1. Emaila balidatu
    $emailBalidazioa = balidatuEmaila($email);
    if ($emailBalidazioa !== true) {
        $errorak[] = $emailBalidazioa;
    }

    // 2. Alokairu data balidatu
    if (empty($alokairuData)) {
        $errorak[] = 'Alokairu-data eremua osorik egon behar da.';
    }

    // 3. NANa balidatu
    $nanBalidazioa = balidatuNAN($nan);
    if (!$nanBalidazioa['baliozkoa']) {
        $errorak[] = $nanBalidazioa['mezua'];
    }

    // Emaitzak erakutsi
    echo "<!DOCTYPE html><html lang='eu'><head><meta charset='UTF-8'><title>Alokairuaren Emaitza</title></head><body>";

    if (count($errorak) === 0) {
        // Datu guztiak OSO ZUZENAK dira
        $itzulketaData = kalkulatuItzulketaData($alokairuData);

        echo "<h1>Alokairua Egoki Erregistratu Da!</h1>";
        echo "<p><strong>Erabiltzailea:</strong> " . htmlspecialchars($izena) . " " . htmlspecialchars($abizenak) . "</p>";
        echo "<p><strong>Liburua:</strong> " . htmlspecialchars($liburua) . "</p>";
        echo "<p><strong>Mail zuzena:</strong> " . htmlspecialchars($email) . "</p>";
        echo "<p><strong>NAN zuzena:</strong> " . htmlspecialchars(strtoupper($nan)) . "</p>";
        echo "<p><strong>Itzulketa-data (10 egun beranduago):</strong> " . $itzulketaData . "</p>";
    } else {
        // Erroren bat dago
        echo "<h1>Errorea Datuak Prozesatzean</h1>";
        echo "<p>Ondorengo eremuetan akatsak aurkitu dira:</p>";
        echo "<ul>";
        foreach ($errorak as $errorea) {
            echo "<li>" . $errorea . "</li>";
        }
        echo "</ul>";
        echo "<br><a href='index.php'>Atzera egin eta datuak berriro sartu</a>";
    }

    echo "</body></html>";
} else {
    // Formularioa bide arruntetik bidali ez bada, index.php-ra bideratu
    header('Location: index.php');
    exit;
}
?>