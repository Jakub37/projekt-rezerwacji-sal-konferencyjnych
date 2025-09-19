<?php
session_start();
$komunikat = "";

// Połączenie z bazą i pobranie użytkowników
$host = "localhost";
$user = "root";
$password = "";
$dbname = "modernforms_system";
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia z bazą!");
}
$uzytkownicy = [];
$sql = "SELECT id_uzytkownika, Imie, Nazwisko FROM uzytkownicy";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $uzytkownicy[] = $row;
    }
}

// Obsługa logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_uzytkownika = $_POST['id_uzytkownika'] ?? '';
    $haslo = $_POST['haslo'] ?? '';

    if ($id_uzytkownika === "" || $haslo === "") {
        $komunikat = "Wybierz użytkownika i wpisz hasło!";
    } else {
        $sql = "SELECT Imie, Nazwisko FROM uzytkownicy WHERE id_uzytkownika = ? AND Haslo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_uzytkownika, $haslo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($imie, $nazwisko);
            $stmt->fetch();
            echo "<script>
                localStorage.setItem('uzytkownikImie', '".htmlspecialchars($imie)."');
                localStorage.setItem('uzytkownikNazwisko', '".htmlspecialchars($nazwisko)."');
                localStorage.setItem('uzytkownikId', '".htmlspecialchars($id_uzytkownika)."');
                window.location.href = 'main.php';
            </script>";
            exit;
        } else {
            $komunikat = "Błędne hasło lub użytkownik!";
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Logowanie</title>
</head>
<body>
    <div id="glowny">
        <h1 id="naglowek">LOGOWANIE</h1>
        <div id="blok_logowania">
            <h2>Wybierz użytkownika</h2>
            <form method="post" action="logowanie.php">
                <select name="id_uzytkownika" id="id_uzytkownika">
                    <option value="">-- wybierz --</option>
                    <?php foreach ($uzytkownicy as $u): ?>
                        <option value="<?php echo $u['id_uzytkownika']; ?>">
                            <?php echo htmlspecialchars($u['Imie'] . " " . $u['Nazwisko']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
                <input type="password" placeholder="Hasło..." name="haslo" id="haslo"><br>
                <button type="submit" id="przycisk">Zaloguj</button>
            </form>
            <?php if ($komunikat): ?>
                <p id="komunikat" style="color:red; font-weight:bold; margin-top:10px;"><?php echo $komunikat; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>