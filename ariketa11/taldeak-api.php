<?php
/**
 * API REST de la tabla "taldeak".
 *
 * GET              -> todos los equipos (sin miembros)
 * GET ?id=X        -> un equipo con la lista de sus miembros
 * POST             -> crea un equipo   {"izena": "...", "puntuak": 10}
 * PUT ?id=X        -> actualiza puntos {"puntuak": 20}
 * DELETE ?id=X     -> borra el equipo y sus miembros
 *
 * Todas las respuestas son JSON.
 */
require_once __DIR__ . '/klaseak/konexioa.php';
require_once __DIR__ . '/klaseak/taldea.php';
require_once __DIR__ . '/klaseak/partaidea.php';

header('Content-Type: application/json; charset=utf-8');

/**
 * Envía una respuesta JSON con el código HTTP indicado y termina la ejecución.
 *
 * @param int   $kodea Código de estado HTTP
 * @param mixed $datuak Datos a codificar en JSON
 */
function erantzun(int $kodea, $datuak): void
{
    http_response_code($kodea);
    echo json_encode($datuak, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Envía una respuesta de error en formato JSON.
 */
function errorea(int $kodea, string $mezua): void
{
    erantzun($kodea, ['errorea' => $mezua]);
}

/**
 * Lee y decodifica el cuerpo JSON de la petición.
 * Si el JSON no es válido o no es un objeto, responde con 400.
 *
 * @return array Datos del cuerpo
 */
function gorputzaIrakurri(): array
{
    $datuak = json_decode(file_get_contents('php://input'), true);
    if (!is_array($datuak)) {
        errorea(400, 'Gorputza JSON baliodun bat izan behar da.');
    }
    return $datuak;
}

/**
 * Valida que el valor sea un entero >= 0 (puntos).
 */
function puntuakZuzenak($balioa): bool
{
    return filter_var($balioa, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) !== false
        && !is_bool($balioa);
}

/**
 * Obtiene el id del equipo desde la URL (?id=X) y comprueba que existe.
 *
 * @return array Fila del equipo
 */
function taldeaLortuEdoErroreak(): array
{
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id === false || $id === null) {
        errorea(400, '"id" parametroa beharrezkoa da eta zenbaki osoa izan behar da.');
    }
    $talde = Taldea::bilatu($id);
    if (!$talde) {
        errorea(404, 'Ez da taldea aurkitu.');
    }
    return $talde;
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {

        case 'GET':
            if (isset($_GET['id'])) {
                // Un equipo con sus miembros
                $talde = taldeaLortuEdoErroreak();
                $talde['partaideak'] = Partaidea::taldekoak((int) $talde['id']);
                erantzun(200, $talde);
            }
            // Todos los equipos, sin miembros
            erantzun(200, Taldea::guztiak());
            break;

        case 'POST':
            $datuak  = gorputzaIrakurri();
            $izena   = is_string($datuak['izena'] ?? null) ? trim($datuak['izena']) : '';
            $puntuak = $datuak['puntuak'] ?? null;

            if ($izena === '' || $puntuak === null) {
                errorea(400, '"izena" eta "puntuak" eremuak beharrezkoak dira.');
            }
            if (!puntuakZuzenak($puntuak)) {
                errorea(400, '"puntuak" zenbaki oso positibo bat izan behar da.');
            }

            Taldea::sortu($izena, (int) $puntuak);
            $id = (int) Konexioa::lortu()->lastInsertId();
            erantzun(201, Taldea::bilatu($id));
            break;

        case 'PUT':
            $talde  = taldeaLortuEdoErroreak();
            $datuak = gorputzaIrakurri();

            if (!array_key_exists('puntuak', $datuak) || !puntuakZuzenak($datuak['puntuak'])) {
                errorea(400, '"puntuak" zenbaki oso positibo bat izan behar da.');
            }

            Taldea::puntuakAldatu((int) $talde['id'], (int) $datuak['puntuak']);
            erantzun(200, Taldea::bilatu((int) $talde['id']));
            break;

        case 'DELETE':
            $talde = taldeaLortuEdoErroreak();
            Taldea::ezabatu((int) $talde['id']);
            erantzun(200, ['mezua' => 'Taldea eta bere partaideak ezabatu dira.']);
            break;

        default:
            header('Allow: GET, POST, PUT, DELETE');
            errorea(405, 'Metodoa ez da onartzen.');
    }
} catch (PDOException $e) {
    // No se expone el detalle del error de la BD al cliente
    errorea(500, 'Zerbitzariaren errorea.');
}