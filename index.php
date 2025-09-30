<?php
session_start();
require_once __DIR__ . '/config.php';
$komunikat = "";

$conn = db_connect();

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
        $sql = "SELECT Imie, Nazwisko, Haslo FROM uzytkownicy WHERE id_uzytkownika = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_uzytkownika);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($imie, $nazwisko, $hash_z_bazy);
            $stmt->fetch();

            if (password_verify($haslo, $hash_z_bazy)) {
                $_SESSION['id_uzytkownika'] = $id_uzytkownika;
                $_SESSION['ImieNazwisko'] = $imie . ' ' . $nazwisko;
                header('Location: main.php');
                exit;
            } else {
                $komunikat = "Błędne hasło!";
            }
        } else {
            $komunikat = "Nie znaleziono użytkownika!";
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
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="glowny">
        <h1 id="naglowek">LOGOWANIE</h1>
        <div id="blok_logowania">
            <h2>Wybierz użytkownika</h2>
            <form method="post" action="index.php">
                <select name="id_uzytkownika" id="id_uzytkownika" required>
                    <option value="">-- wybierz --</option>
                    <?php foreach ($uzytkownicy as $u): ?>
                        <option value="<?php echo $u['id_uzytkownika']; ?>">
                            <?php echo htmlspecialchars($u['Imie'] . " " . $u['Nazwisko']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
                <input type="password" name="haslo" placeholder="Hasło..." id="haslo" required><br>
                <button type="submit" id="przycisk">Zaloguj</button>
            </form>

            <?php if ($komunikat): ?>
                <p id="komunikat" style="color: red;"><?php echo htmlspecialchars($komunikat); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


