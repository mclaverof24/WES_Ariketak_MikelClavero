<?php
require_once __DIR__ . '/taldea.php';

/**
 * Gestiona el equipo favorito: cookie + $_SESSION.
 */
class Gogokoena
{
    public static function hasieratu(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['gogokoena']) && isset($_COOKIE['gogokoena'])) {
            $_SESSION['gogokoena'] = (int) $_COOKIE['gogokoena'];
        }
    }

    public static function ezarri(int $id): void
    {
        setcookie('gogokoena', (string) $id, time() + 30 * 24 * 3600, '/');
        $_SESSION['gogokoena'] = $id;
    }

    public static function erakutsi(): void
    {
        if (!isset($_SESSION['gogokoena'])) {
            return;
        }
        $talde = Taldea::bilatu((int) $_SESSION['gogokoena']);
        if ($talde) {
            echo '<p>Zure talde favoritoa: ' . htmlspecialchars($talde['izena']) . '</p>';
        }
    }
}