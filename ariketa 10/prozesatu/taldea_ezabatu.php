<?php
require_once __DIR__ . '/../config/hasiera.php';
require_once __DIR__ . '/../klaseak/Taldea.php';

$id = $_POST['id'] ?? null;

if ($id !== null) {
    Taldea::ezabatu((int) $id);
    $_SESSION['mezua'] = ['mota' => 'ondo', 'testua' => 'Taldea eta bere partaideak ezabatu dira.'];

    // Ezabatutako taldea gogokoena bazen, garbitu cookiea eta sesioa
    if (isset($_SESSION['talde_gogokoena']) && $_SESSION['talde_gogokoena'] == $id) {
        unset($_SESSION['talde_gogokoena']);
        setcookie('talde_gogokoena', '', time() - 3600, '/');
    }
} else {
    $_SESSION['mezua'] = ['mota' => 'errorea', 'testua' => 'Ez da taldea aurkitu.'];
}

header('Location: ../index.php');
exit;