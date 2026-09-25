<?php
require_once __DIR__ . '/../config/hasiera.php';
require_once __DIR__ . '/../klaseak/Partaidea.php';

$izena      = trim($_POST['izena'] ?? '');
$herrialdea = trim($_POST['herrialdea'] ?? '');
$taldea_id  = $_POST['taldea_id'] ?? null;

if ($izena === '' || $herrialdea === '' || $taldea_id === null) {
    $_SESSION['mezua'] = ['mota' => 'errorea', 'testua' => 'Eremu guztiak bete behar dira.'];
} else {
    $partaidea = new Partaidea(null, $izena, $herrialdea, (int) $taldea_id);
    if ($partaidea->gehitu()) {
        $_SESSION['mezua'] = ['mota' => 'ondo', 'testua' => 'Partaidea ondo sortu da.'];
    } else {
        $_SESSION['mezua'] = ['mota' => 'errorea', 'testua' => 'Errore bat gertatu da partaidea sortzean.'];
    }
}

header('Location: ../partaideak.php?id=' . urlencode($taldea_id));
exit;