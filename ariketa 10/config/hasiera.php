<?php
/**
 * Sesioa hasi eta orri guztietan behar diren funtzio lagungarriak.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gogokoena-ren cookiea badago baina sesioan ez, sinkronizatu
if (isset($_COOKIE['talde_gogokoena']) && !isset($_SESSION['talde_gogokoena'])) {
    $_SESSION['talde_gogokoena'] = $_COOKIE['talde_gogokoena'];
}

/**
 * Sesioan gordetako mezua (errorea/ondo) pantailaratu eta ezabatu.
 */
function erakutsiMezua() {
    if (isset($_SESSION['mezua'])) {
        $mota   = $_SESSION['mezua']['mota'];   // 'errorea' edo 'ondo'
        $testua = htmlspecialchars($_SESSION['mezua']['testua']);
        echo "<div class=\"mezua $mota\">$testua</div>";
        unset($_SESSION['mezua']);
    }
}

/**
 * Gogokoen bezala markatutako taldearen izena itzultzen du (edo null).
 */
function gogokoenarenIzena() {
    if (isset($_SESSION['talde_gogokoena'])) {
        require_once __DIR__ . '/../klaseak/Taldea.php';
        $taldea = Taldea::bilatu($_SESSION['talde_gogokoena']);
        if ($taldea) {
            return $taldea['izena'];
        }
    }
    return null;
}