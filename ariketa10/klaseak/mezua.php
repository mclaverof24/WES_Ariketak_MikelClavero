<?php
/**
 * Mensajes de error / confirmación entre redirecciones (flash en sesión).
 */
class Mezua
{
    public static function ezarri(string $testua, bool $errorea = false): void
    {
        $_SESSION['mezua'] = ['testua' => $testua, 'errorea' => $errorea];
    }

    /** Muestra el mensaje una sola vez */
    public static function erakutsi(): void
    {
        if (!isset($_SESSION['mezua'])) {
            return;
        }
        $m = $_SESSION['mezua'];
        $kolorea = $m['errorea'] ? 'red' : 'green';
        echo '<p style="color:' . $kolorea . '">' . htmlspecialchars($m['testua']) . '</p>';
        unset($_SESSION['mezua']);
    }
}