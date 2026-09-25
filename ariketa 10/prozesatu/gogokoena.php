<?php
require_once __DIR__ . '/../config/hasiera.php';

$id = $_POST['id'] ?? null;

if ($id !== null) {
    setcookie('talde_gogokoena', $id, time() + (86400 * 30), '/'); // 30 egunez iraungo du
    $_SESSION['talde_gogokoena'] = $id;
}

// Erabiltzailea zegoen orrira itzuli (index.php edo partaideak.php)
$itzulera = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header('Location: ' . $itzulera);
exit;