<?php
require_once __DIR__ . '/../config/hasiera.php';
require_once __DIR__ . '/../klaseak/Taldea.php';

$id      = $_POST['id'] ?? null;
$puntuak = $_POST['puntuak'] ?? null;

if ($id === null || $puntuak === null || $puntuak === '') {
    $_SESSION['mezua'] = ['mota' => 'errorea', 'testua' => 'Eremu guztiak bete behar dira.'];
} else {
    Taldea::aldatuPuntuak((int) $id, (int) $puntuak);
    $_SESSION['mezua'] = ['mota' => 'ondo', 'testua' => 'Puntuazioa eguneratu da.'];
}

header('Location: ../index.php');
exit;