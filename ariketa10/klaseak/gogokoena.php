<?php
require_once __DIR__ . '/taldea.php';

/**
 * Gestiona el equipo favorito: cookie + $_SESSION.
 */
class Gogokoena
{
    /** Hay que llamarlo al principio de cada página */
    public static function hasieratu(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Si hay cookie pero la sesión está vacía (nueva sesión), la recuperamos
        if (!isset($_SESSION['gogokoena']) && isset($_COOKIE['gogokoena'])) {
            $_SESSION['gogokoena'] = (int) $_COOKIE['gogokoena'];
        }
    }

    /** Guarda el favorito en cookie (30 días) y en sesión. Va antes de cualquier salida HTML */
    public static function ezarri(int $id): void
    {
        setcookie('gogokoena', (string) $id, time() + 30 * 24 * 3600, '/');
        $_SESSION['gogokoena'] = $id;
    }

    /** Muestra el equipo favorito (si existe) */
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