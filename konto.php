<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "modernforms_system";

if (!isset($_SESSION['id_uzytkownika'])) {
    header('Location: index.php');
    exit;
}

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych!");
}

$id = (int)$_SESSION['id_uzytkownika'];
$sql = "SELECT Imie, Nazwisko, Telefon, Email FROM uzytkownicy WHERE id_uzytkownika = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($imie, $nazwisko, $telefon, $email);
$stmt->fetch();
$stmt->close();
$conn->close();

$imieNazwisko = isset($_SESSION['ImieNazwisko']) ? $_SESSION['ImieNazwisko'] : trim(($imie ?? '') . ' ' . ($nazwisko ?? ''));
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konto użytkownika</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="glowny">
        <h1 id="naglowek">KONTO</h1>
        <div id="blok_logowania">
            <h2>Zalogowano jako</h2>
            <p><strong><?php echo htmlspecialchars($imieNazwisko ?: ($imie . ' ' . $nazwisko)); ?></strong></p>
            <p>Telefon: <?php echo htmlspecialchars($telefon ?? '—'); ?></p>
            <p>E‑mail: <?php echo htmlspecialchars($email ?? '—'); ?></p>

            <div class="buttons-row">
                <form method="post" action="wyloguj.php">
                    <button type="submit" id="przycisk">Wyloguj się</button>
                </form>
                <form method="get" action="main.php">
                    <button type="submit" id="przycisk">Kontynuuj</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>


