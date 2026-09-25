<?php
require_once __DIR__ . '/../config/hasiera.php';
require_once __DIR__ . '/../klaseak/Taldea.php';

$izena   = trim($_POST['izena'] ?? '');
$puntuak = trim($_POST['puntuak'] ?? '');

if ($izena === '' || $puntuak === '') {
    $_SESSION['mezua'] = ['mota' => 'errorea', 'testua' => 'Eremu guztiak bete behar dira.'];
} else {
    $taldea = new Taldea(null, $izena, (int) $puntuak);
    if ($taldea->gehitu()) {
        $_SESSION['mezua'] = ['mota' => 'ondo', 'testua' => 'Taldea ondo sortu da.'];
    } else {
        $_SESSION['mezua'] = ['mota' => 'errorea', 'testua' => 'Errore bat gertatu da taldea sortzean.'];
    }
}

header('Location: ../index.php');
exit;