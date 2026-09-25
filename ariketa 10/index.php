<?php
require_once __DIR__ . '/config/hasiera.php';
require_once __DIR__ . '/klaseak/Taldea.php';

$taldeak   = Taldea::guztiak();
$gogokoena = gogokoenarenIzena();
?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Hackaton - Sailkapena</title>
    <link rel="stylesheet" href="css/estiloak.css">
</head>
<body>

<h1>Sailkapena</h1>

<?php erakutsiMezua(); ?>

<table>
    <tr>
        <th>ID</th>
        <th>Izena</th>
        <th>Puntuak</th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach ($taldeak as $t): ?>
    <tr>
        <td><?= $t['id'] ?></td>
        <td><a href="partaideak.php?id=<?= $t['id'] ?>"><?= htmlspecialchars($t['izena']) ?></a></td>
        <td>
            <form action="prozesatu/taldea_aldatu.php" method="post" class="linea-forma">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <input type="number" name="puntuak" value="<?= $t['puntuak'] ?>" required>
                <button type="submit">Aldatu</button>
            </form>
        </td>
        <td>
            <form action="prozesatu/taldea_ezabatu.php" method="post"
                  onsubmit="return confirm('Ziur zaude \'<?= htmlspecialchars($t['izena']) ?>\' taldea ezabatu nahi duzula?');">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button type="submit">Ezabatu</button>
            </form>
        </td>
        <td>
            <form action="prozesatu/gogokoena.php" method="post">
                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                <button type="submit">Gogokoena</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Gehitu taldea</h2>
<form action="prozesatu/taldea_gehitu.php" method="post">
    <label>Izena: <input type="text" name="izena"></label><br>
    <label>Puntuak: <input type="number" name="puntuak"></label><br>
    <button type="submit">Sortu</button>
</form>

<?php if ($gogokoena): ?>
    <p class="gogokoena">Zure talde favoritoa: <strong><?= htmlspecialchars($gogokoena) ?></strong></p>
<?php endif; ?>

</body>
</html>