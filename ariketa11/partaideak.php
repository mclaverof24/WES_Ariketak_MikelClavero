<?php
require_once __DIR__ . '/klaseak/taldea.php';
require_once __DIR__ . '/klaseak/partaidea.php';
require_once __DIR__ . '/klaseak/gogokoena.php';
require_once __DIR__ . '/klaseak/mezua.php';

Gogokoena::hasieratu();

// Comprobamos que el equipo existe
$id = (int) ($_GET['id'] ?? 0);
$talde = Taldea::bilatu($id);
if (!$talde) {
    header('Location: index.php');
    exit;
}
$partaideak = Partaidea::taldekoak($id);
?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($talde['izena']) ?> - Partaideak</title>
</head>
<body>
    <h1><?= htmlspecialchars($talde['izena']) ?> - Partaideak</h1>

    <?php Mezua::erakutsi(); ?>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Izena</th>
            <th>Herrialdea</th>
        </tr>
        <?php foreach ($partaideak as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['izena']) ?></td>
            <td><?= htmlspecialchars($p['herrialdea']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Gehitu partaidea</h2>
    <form action="prozesatu/prozesatu.php" method="post">
        <input type="hidden" name="ekintza" value="partaidea_sortu">
        <input type="hidden" name="taldea_id" value="<?= $id ?>">
        <p>Izena: <input type="text" name="izena"></p>
        <p>Herrialdea: <input type="text" name="herrialdea"></p>
        <button type="submit">Sortu</button>
    </form>

    <?php Gogokoena::erakutsi(); ?>

    <p><a href="index.php">Itzuli</a></p>
</body>
</html>