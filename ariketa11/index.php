<?php
require_once __DIR__ . '/klaseak/taldea.php';
require_once __DIR__ . '/klaseak/gogokoena.php';
require_once __DIR__ . '/klaseak/mezua.php';

Gogokoena::hasieratu();
$taldeak = Taldea::guztiak();
?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Sailkapena</title>
</head>
<body>
    <h1>Sailkapena</h1>

    <?php Mezua::erakutsi(); ?>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Izena</th>
            <th>Puntuak</th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($taldeak as $t): ?>
        <tr>
            <td><?= $t['id'] ?></td>
            <td><a href="partaideak.php?id=<?= $t['id'] ?>"><?= htmlspecialchars($t['izena']) ?></a></td>

            <!-- Cambiar puntos -->
            <td>
                <form action="prozesatu/prozesatu.php" method="post">
                    <input type="hidden" name="ekintza" value="puntuak_aldatu">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <input type="number" name="puntuak" value="<?= $t['puntuak'] ?>">
                    <button type="submit">Aldatu</button>
                </form>
            </td>

            <!-- Borrar equipo -->
            <td>
                <form action="prozesatu/prozesatu.php" method="post">
                    <input type="hidden" name="ekintza" value="taldea_ezabatu">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button type="submit">Ezabatu</button>
                </form>
            </td>

            <!-- Marcar favorito -->
            <td>
                <form action="prozesatu/prozesatu.php" method="post">
                    <input type="hidden" name="ekintza" value="gogokoena">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button type="submit">Gogokoena</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Gehitu taldea</h2>
    <form action="prozesatu/prozesatu.php" method="post">
        <input type="hidden" name="ekintza" value="taldea_sortu">
        <p>Izena: <input type="text" name="izena"></p>
        <p>Puntuak: <input type="text" name="puntuak"></p>
        <button type="submit">Sortu</button>
    </form>

    <?php Gogokoena::erakutsi(); ?>
</body>
</html>