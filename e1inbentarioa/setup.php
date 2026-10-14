<?php
declare(strict_types=1);

// Uso (solo línea de comandos, una vez, tras importar el .sql):
//   php setup.php <usuario_admin> <contraseña>
// Inserta el primer administrador. Bórralo después.

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Solo se puede ejecutar desde la línea de comandos.');
}

require __DIR__ . '/config/db.php';

[$script, $username, $password] = $argv + [null, null, null];
if (!$username || !$password) {
    exit("Uso: php setup.php <usuario_admin> <contraseña>\n");
}
if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
    exit("Usuario no válido (3-50 caracteres: letras, números, _ . -)\n");
}
if (strlen($password) < 8 || strlen($password) > 72) {
    exit("La contraseña debe tener entre 8 y 72 caracteres\n");
}

$pdo = db();
$pdo->beginTransaction();

$existeUser = $pdo->prepare('SELECT COUNT(*) FROM erabiltzaileak WHERE username = ?');
$existeUser->execute([$username]);

if ((int)$existeUser->fetchColumn() === 0) {
    $pdo->prepare('INSERT INTO erabiltzaileak (username, pasahitza, rol) VALUES (?, ?, ?)')
        ->execute([$username, password_hash($password, PASSWORD_BCRYPT), 'admin']);
    echo "Administrador '$username' creado.\n";
} else {
    echo "El usuario '$username' ya existe; no se ha modificado.\n";
}

$pdo->commit();
echo "Proceso finalizado. Ya puedes borrar setup.php.\n";