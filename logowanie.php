<?php
session_start();
$komunikat = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie'] ?? '');
    $nazwisko = trim($_POST['nazwisko'] ?? '');

    if ($imie === "" || $nazwisko === "") {
        $komunikat = "Wypełnij dane!";
    } else {
        $host = "localhost";
        $user = "root";
        $password = "";
        $dbname = "modernforms_system";

        $conn = new mysqli($host, $user, $password, $dbname);
        if ($conn->connect_error) {
            $komunikat = "Błąd połączenia z bazą!";
        } else {
            $sql = "SELECT id_uzytkownika FROM uzytkownicy WHERE Imie = ? AND Nazwisko = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $imie, $nazwisko);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "<script>
                    localStorage.setItem('uzytkownikImie', '".htmlspecialchars($imie)."');
                    localStorage.setItem('uzytkownikNazwisko', '".htmlspecialchars($nazwisko)."');
                    window.location.href = 'wybor_sali.html';
                </script>";
                exit;
            } else {
                $komunikat = "Nie znaleziono użytkownika!";
            }
            $stmt->close();
            $conn->close();
        }
    }
}
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
            <h2>Dane użytkownika</h2>
            <form method="post" action="logowanie.php">
                <input type="text" placeholder="Imię..." name="imie" id="dane_imie"><br>
                <input type="text" placeholder="Nazwisko..." name="nazwisko" id="dane_nazwisko"><br>
                <button type="submit" id="przycisk">Zaloguj</button>
            </form>
            <?php if ($komunikat): ?>
                <p id="komunikat" style="color:red; font-weight:bold; margin-top:10px;"><?php echo $komunikat; ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
        <div id="blok_logowania">
            <h2>Dane użytkownika</h2>
            <input type="text" placeholder="Imię..." id="dane_imie"><br>
            <input type="text" placeholder="Nazwisko..." id="dane_nazwisko"><br>
            <button id="przycisk">Zaloguj</button>
            <p id="komunikat" style="color:red; font-weight:bold; display:none; margin-top:10px;"></p>
        </div>
    </div>

    <script>
        document.getElementById("przycisk").addEventListener("click", function() {
            const imie = document.getElementById("dane_imie").value.trim();
            const nazwisko = document.getElementById("dane_nazwisko").value.trim();
            const komunikat = document.getElementById("komunikat");

            if (imie === "" || nazwisko === "") {
                // jeśli którekolwiek pole jest puste
                komunikat.textContent = "Wypełnij dane!";
                komunikat.style.display = "block";
            } else {
                // zapis do localStorage
                localStorage.setItem("uzytkownikImie", imie);
                localStorage.setItem("uzytkownikNazwisko", nazwisko);

                // przejście dalej
                window.location.href = "wybor_sali.html";
            }
        });
    </script>
</body>
</html>
