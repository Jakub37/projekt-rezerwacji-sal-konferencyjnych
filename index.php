<?php
session_start();
require_once __DIR__ . '/config.php';

$komunikat = "";

$conn = db_connect();

// Pobierz użytkowników (aktywnych)
$uzytkownicy = [];
$sql = "SELECT id_uzytkownika, Imie, Nazwisko FROM uzytkownicy WHERE aktywny = 1 ORDER BY Nazwisko ASC, Imie ASC";
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

// Pobierz rezerwacje informacyjne (najbliższe)
$rezerwacje = [];
$sqlRezerwacje = "
    SELECT s.nr_sali, s.data, s.od_godziny, s.do_godziny, u.Imie, u.Nazwisko
    FROM sale s
    JOIN uzytkownicy u ON s.id_uzytkownika = u.id_uzytkownika
    WHERE s.data >= CURDATE()
    ORDER BY s.data ASC, s.od_godziny ASC";
$res = $conn->query($sqlRezerwacje);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $rezerwacje[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="glowny">
    <h1 id="naglowek">LOGOWANIE</h1>

    <div id="blok_logowania">
        <h2>Wybierz użytkownika</h2>
        <form method="post" action="index.php">
            <select name="id_uzytkownika" required>
                <option value="">-- wybierz --</option>
                <?php foreach ($uzytkownicy as $u): ?>
                    <option value="<?= $u['id_uzytkownika'] ?>">
                        <?= htmlspecialchars($u['Nazwisko'] . " " . $u['Imie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>
            <input type="password" name="haslo" placeholder="Hasło..." required><br>
            <button type="submit" id="przycisk">Zaloguj</button>
        </form>

        <?php if ($komunikat): ?>
            <p id="komunikat"><?= htmlspecialchars($komunikat) ?></p>
        <?php endif; ?>
    </div>

    <div id="podglad-rezerwacji">
        <h2>Aktualne rezerwacje</h2>
        <table>
            <thead>
            <tr>
                <th>Sala</th>
                <th>Data</th>
                <th>Dzień</th>
                <th>Od godziny</th>
                <th>Do godziny</th>
                <th>Użytkownik</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $dniTygodnia = [1 => 'Poniedziałek', 2 => 'Wtorek', 3 => 'Środa',
                            4 => 'Czwartek', 5 => 'Piątek', 6 => 'Sobota', 7 => 'Niedziela'];
            if (!empty($rezerwacje)) {
                $lastDate = null;
                foreach ($rezerwacje as $r) {
                    $sala = ($r['nr_sali'] == 1) ? 'Administracyjna' :
                            (($r['nr_sali'] == 2) ? 'Handlowy' : 'Sala ' . $r['nr_sali']);
                    $dzien = $dniTygodnia[(int)date('N', strtotime($r['data']))];

                    if ($lastDate !== null && $lastDate !== $r['data']) {
                        echo "<tr class='separator-row'><td colspan='6'></td></tr>";
                    }

                    echo "<tr>
                            <td>" . htmlspecialchars($sala) . "</td>
                            <td>" . htmlspecialchars($r['data']) . "</td>
                            <td>" . htmlspecialchars($dzien) . "</td>
                            <td>" . htmlspecialchars($r['od_godziny']) . "</td>
                            <td>" . htmlspecialchars($r['do_godziny']) . "</td>
                            <td>" . htmlspecialchars($r['Nazwisko'] . ' ' . $r['Imie']) . "</td>
                        </tr>";

                    $lastDate = $r['data'];
                }
            } else {
                echo "<tr><td colspan='6'>Brak rezerwacji</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
