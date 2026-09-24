<?php
// require_once: Kanpoko PHP fitxategiak (klaseak/objektuak) fitxategi honetara kargatzeko.
// 'once' atalak fitxategi bera behin bakarrik kargatzea ziurtatzen du (erroreak ekiditeko).
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

    <h2>Ariketa 8.1</h2>
    <?php
    // 'new' hitz erreserbatua erabiliz 'Ikaslea' klaseko objektu berri bat sortzen da ($ikaslea)
    $ikaslea = new Ikaslea("Nora");

    // Objektuaren notaGehitu() metodoari deitzen diogu irakasgaiak eta notak pasaz
    $ikaslea->notaGehitu("WES", 7);
    $ikaslea->notaGehitu("DIW", 8);
    $ikaslea->notaGehitu("DAW", 5);
    $ikaslea->notaGehitu("WEC", 7);
    $ikaslea->notaGehitu("EIE", 9);

    // Ikaslearen nota guztiak pantailaratzen dituen metodoari deitu
    $ikaslea->erakutsiNotak();
    ?>

    <br>

    <h2>Ariketa 8.2</h2>
    <?php
    // Produktua klaseko objektu bat sortu: izena ("Sagarra") eta prezioa (7€) jarriz
    $produktua = new Produktua("Sagarra", 7);

    // aukeratu() metodoak erositako unitate kopurua jaso eta prezio osoa kalkulatzen du
    $produktua->aukeratu(3); // 3 * 7 = 21
    ?>

    <br>

    <h2>Ariketa 8.3</h2>
    <?php
    // Liburua klaseko 3 objektu desberdin sortu
    $liburu1 = new Liburua("Harry Potter", "J.K. Rowling");
    $liburu2 = new Liburua("The Hobbit", "J.R.R. Tolkien");
    $liburu3 = new Liburua("To Kill a Mockingbird", "Harper Lee");

    // Katalogoa kudeatzeko objektu nagusia sortu
    $katalogoa = new LiburuKatalogoa();

    // Sortutako liburuak katalogoaren barruan sartu/gorde
    $katalogoa->liburuaGehitu($liburu1);
    $katalogoa->liburuaGehitu($liburu2);
    $katalogoa->liburuaGehitu($liburu3);

    // Katalogo osoa bistaratu
    $katalogoa->katalogoaBistaratu();
    ?>

    <br>

    <h2>Ariketa 8.4</h2>
    <!-- htmlspecialchars($_SERVER['PHP_SELF']) erabiliz orrialde berera bidaltzen dira datuak segurtasunez -->
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

        <!-- Botoi honek 'bidali_pertsona' izeneko POST eskaera bat sortzen du -->
        <input type="submit" name="bidali_pertsona" value="Bidali">
    </form>

    <?php
    // Formularioa POST bidez bidali bada eta botoi zehatz hau sakatu bada bakarrik exekutatu
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bidali_pertsona'])) {
        // htmlspecialchars() erabiliz erabiltzaileak idatzitako testua garbitzen da (XSS erasorik ez izateko)
        $izenAbizenak = htmlspecialchars($_POST['izena']) . " " . htmlspecialchars($_POST['abizenak']);
        $zeregia = $_POST['zeregia'];

        // Aukeratutako zereginaren arabera, klase desberdin baten objektua sortzen dugu
        if ($zeregia === 'programatzailea') {
            $pertsona = new Programatzailea($izenAbizenak);
        } else {
            $pertsona = new Diseinatzailea($izenAbizenak);
        }

        // Pertsona horren aurkezpen-mezua erakutsi
        echo "<p>" . $pertsona->aurkeztu() . "</p>";
    }
    ?>

    <br>

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
    // Animalien formularioa bidali den egiaztatu
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bidali_animalia'])) {
        $animaliaAukera = $_POST['animalia'];

        // Aukeraketaren arabera Txakurra edo Katua objektua instantziatu
        if ($animaliaAukera === 'txakurra') {
            $animalia = new Txakurra();
        } else {
            $animalia = new Katua();
        }

        // Animalia bakoitzak bere esan() metodo propioa erabiliko du (adib. "Oau oau" edo "Miau")
        echo "<p>" . $animalia->esan() . "</p>";
    }
    ?>

</body>
</html>