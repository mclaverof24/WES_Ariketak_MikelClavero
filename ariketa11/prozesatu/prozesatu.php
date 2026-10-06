<?php
/**
 * Procesa TODOS los formularios de la aplicación.
 * Cada formulario envía un campo oculto "ekintza" con la acción a realizar.
 * Al terminar, redirige a la página correspondiente con un mensaje.
 */
require_once __DIR__ . '/../klaseak/taldea.php';
require_once __DIR__ . '/../klaseak/partaidea.php';
require_once __DIR__ . '/../klaseak/gogokoena.php';
require_once __DIR__ . '/../klaseak/mezua.php';

Gogokoena::hasieratu();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$ekintza = $_POST['ekintza'] ?? '';
$helburua = '../index.php'; // página a la que volver

switch ($ekintza) {

    case 'taldea_sortu':
        $izena   = trim($_POST['izena'] ?? '');
        $puntuak = trim($_POST['puntuak'] ?? '');

        if ($izena === '' || $puntuak === '') {
            Mezua::ezarri('Eremu guztiak bete behar dira.', true);
        } elseif (!ctype_digit($puntuak)) {
            Mezua::ezarri('Puntuak zenbaki oso positibo bat izan behar da.', true);
        } else {
            Taldea::sortu($izena, (int) $puntuak);
            Mezua::ezarri('Taldea ondo sortu da.');
        }
        break;

    case 'puntuak_aldatu':
        $puntuak = trim($_POST['puntuak'] ?? '');
        if ($puntuak === '' || !is_numeric($puntuak)) {
            Mezua::ezarri('Puntuak balio zuzena izan behar du.', true);
        } else {
            Taldea::puntuakAldatu((int) ($_POST['id'] ?? 0), (int) $puntuak);
            Mezua::ezarri('Puntuak ondo aldatu dira.');
        }
        break;

    case 'taldea_ezabatu':
        Taldea::ezabatu((int) ($_POST['id'] ?? 0));
        Mezua::ezarri('Taldea eta bere partaideak ezabatu dira.');
        break;

    case 'gogokoena':
        $id = (int) ($_POST['id'] ?? 0);
        if (Taldea::bilatu($id)) {
            Gogokoena::ezarri($id); // cookie + sesión
            Mezua::ezarri('Talde gogokoena gorde da.');
        }
        break;

    case 'partaidea_sortu':
        $taldeaId   = (int) ($_POST['taldea_id'] ?? 0);
        $izena      = trim($_POST['izena'] ?? '');
        $herrialdea = trim($_POST['herrialdea'] ?? '');
        $helburua   = '../partaideak.php?id=' . $taldeaId;

        if ($izena === '' || $herrialdea === '') {
            Mezua::ezarri('Eremu guztiak bete behar dira.', true);
        } else {
            Partaidea::sortu($izena, $herrialdea, $taldeaId);
            Mezua::ezarri('Partaidea ondo sortu da.');
        }
        break;
}

header('Location: ' . $helburua);
exit;