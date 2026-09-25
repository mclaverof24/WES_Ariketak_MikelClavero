<?php
require_once __DIR__ . '/config/hasiera.php';
require_once __DIR__ . '/klaseak/Taldea.php';
require_once __DIR__ . '/klaseak/Partaidea.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$taldea_id = (int) $_GET['id'];
$taldea    = Taldea::bilatu($taldea_id);

if (!$taldea) {
    header('Location: index.php');
    exit;
}

$partaideak = Partaidea::taldekoak($taldea_id);
$gogokoena  = gogokoenarenIzena();
?>
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($taldea['izena']) ?> - Partaideak</title>
    <link rel="stylesheet" href="css/estiloak.css">
</head>
<body>

<h1><?= htmlspecialchars($taldea['izena']) ?> - Partaideak</h1>

<?php erakutsiMezua(); ?>

<table>
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
<form action="prozesatu/partaidea_gehitu.php" method="post">
    <input type="hidden" name="taldea_id" value="<?= $taldea_id ?>">
    <label>Izena: <input type="text" name="izena"></label><br>
    <label>Herrialdea: <input type="text" name="herrialdea"></label><br>
    <button type="submit">Sortu</button>
</form>

<p><a href="index.php">Itzuli</a></p>

<?php if ($gogokoena): ?>
    <p class="gogokoena">Zure talde favoritoa: <strong><?= htmlspecialchars($gogokoena) ?></strong></p>
<?php endif; ?>

</body>
</html>