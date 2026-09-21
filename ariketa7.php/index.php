<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Liburutegia - Alokairu Formularioa</title>
</head>
<body>

    <h1>Liburutegiko Alokairu Formularioa</h1>

    <form action="process.php" method="POST">
        <p>
            <label for="izena">Erabiltzailearen izena:</label><br>
            <input type="text" id="izena" name="izena" required>
        </p>

        <p>
            <label for="abizenak">Erabiltzailearen abizenak:</label><br>
            <input type="text" id="abizenak" name="abizenak" required>
        </p>

        <p>
            <label for="liburua">Alokatutako liburua:</label><br>
            <input type="text" id="liburua" name="liburua" required>
        </p>

        <p>
            <label for="email">Emaila:</label><br>
            <input type="email" id="email" name="email" required>
        </p>

        <p>
            <label for="alokairu_data">Alokairu data:</label><br>
            <input type="date" id="alokairu_data" name="alokairu_data" required>
        </p>

        <p>
            <label for="nan">Erabiltzailearen NAN zenbakia (8 zenbaki + letra):</label><br>
            <input type="text" id="nan" name="nan" placeholder="12345678Z" required>
        </p>

        <button type="submit">Bidali Alokairua</button>
    </form>

</body>
</html>