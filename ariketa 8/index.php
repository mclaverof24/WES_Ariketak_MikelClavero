<?php
require_once 'klaseak/ikaslea.php';
require_once 'klaseak/produktua.php';
require_once 'klaseak/liburua.php';
require_once 'klaseak/liburuKatalogoa.php';
require_once 'klaseak/pertsona.php';
require_once 'klaseak/animaliak.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ariketa 8</title>
</head>
<body>

    <h1>Ariketa 8</h1>

    <!-- Ariketa 8.1 -->
    <h2>Ariketa 8.1</h2>
    <?php
    $ikaslea = new Ikaslea("Nora");
    $ikaslea->notaGehitu("WES", 7);
    $ikaslea->notaGehitu("DIW", 8);
    $ikaslea->notaGehitu("DAW", 5);
    $ikaslea->notaGehitu("WEC", 7);
    $ikaslea->notaGehitu("EIE", 9);

    $ikaslea->erakutsiNotak();
    ?>

    <br>

    <!-- Ariketa 8.2 -->
    <h2>Ariketa 8.2</h2>
    <?php
    $produktua = new Produktua("Sagarra", 7);
    $produktua->aukeratu(3); // 3 * 7 = 21
    ?>

    <br>

    <!-- Ariketa 8.3 -->
    <h2>Ariketa 8.3</h2>
    <?php
    $liburu1 = new Liburua("Harry Potter", "J.K. Rowling");
    $liburu2 = new Liburua("The Hobbit", "J.R.R. Tolkien");
    $liburu3 = new Liburua("To Kill a Mockingbird", "Harper Lee");

    $katalogoa = new LiburuKatalogoa();
    $katalogoa->liburuaGehitu($liburu1);
    $katalogoa->liburuaGehitu($liburu2);
    $katalogoa->liburuaGehitu($liburu3);

    $katalogoa->katalogoaBistaratu();
    ?>

    <br>

    <!-- Ariketa 8.4 -->
    <h2>Ariketa 8.4</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <p>
            Izena: <input type="text" name="izena" required>
        </p>
        <p>
            Abizenak: <input type="text" name="abizenak" required>
        </p>
        <p>
            Zeregina: 
            <select name="zeregia">
                <option value="programatzailea">Programatzailea</option>
                <option value="diseinatzailea">Diseinatzailea</option>
            </select>
        </p>

        <input type="submit" name="bidali_pertsona" value="Bidali">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bidali_pertsona'])) {
        $izenAbizenak = htmlspecialchars($_POST['izena']) . " " . htmlspecialchars($_POST['abizenak']);
        $zeregia = $_POST['zeregia'];

        if ($zeregia === 'programatzailea') {
            $pertsona = new Programatzailea($izenAbizenak);
        } else {
            $pertsona = new Diseinatzailea($izenAbizenak);
        }

        echo "<p>" . $pertsona->aurkeztu() . "</p>";
    }
    ?>

    <br>

    <!-- Ariketa 8.5 -->
    <h2>Ariketa 8.5</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <p>
            Animalia aukeratu: 
            <select name="animalia">
                <option value="txakurra">Txakurra</option>
                <option value="katua">Katua</option>
            </select>
            <input type="submit" name="bidali_animalia" value="Aukeratu">
        </p>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bidali_animalia'])) {
        $animaliaAukera = $_POST['animalia'];

        if ($animaliaAukera === 'txakurra') {
            $animalia = new Txakurra();
        } else {
            $animalia = new Katua();
        }

        echo "<p>" . $animalia->esan() . "</p>";
    }
    ?>

</body>
</html>