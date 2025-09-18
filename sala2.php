<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala 1</title>
    <link rel="stylesheet" href="sala2.css">
</head>

<?php
// Połączenie z bazą
$host = "localhost";
$user = "root";
$password = "";
$dbname = "modernforms_system";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die("Błąd połączenia: " . $conn->connect_error);

// Pobranie rezerwacji
$sql = "SELECT id,Miejsca, Data, Godzina, Status, Rezerwacja FROM sala_konf2 WHERE Data >= CURDATE() ORDER BY Data, Godzina LIMIT 5";
$result = $conn->query($sql);

// Pobranie hasła aktualnego użytkownika
$imie = isset($_GET['imie']) ? $_GET['imie'] : '';
$nazwisko = isset($_GET['nazwisko']) ? $_GET['nazwisko'] : '';
$haslo = '';
if($imie && $nazwisko){
    $stmt = $conn->prepare("SELECT haslo FROM uzytkownicy WHERE imie=? AND nazwisko=?");
    $stmt->bind_param("ss", $imie, $nazwisko);
    $stmt->execute();
    $stmt->bind_result($haslo);
    $stmt->fetch();
    $stmt->close();
}
?>

<body>
<div id="glowny">
    <div id="blok_sali">

        <!-- Top controls: użytkownik + link sala2 -->
        <div id="top-controls">
            <div id="uzytkownik">
                <img src="uzytkownik.jpg" alt="Użytkownik">
                <div class="tekst">
                    <b><span>Użytkownik:</span></b>
                    <span id="imieNazwisko"></span>
                </div>
            </div>
            <a id="sala2" href="sala1.php">Sala konferencyjna 1 →</a>
        </div>

        <!-- Napis sala -->
        <div id="naglowek-sala"><b>Sala Konferencyjna 2</b></div>

        <!-- Główna sekcja: tabela + formularz -->
        <div id="main-content">
            <div id="lewa-strona">
                <div id="rezerwacje">
                    <div id="xd">
                        <h2>Najbliższe rezerwacje</h2>
                    </div>
                    <table id="tabela-rezerwacji">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Miejsca</th>
                            <th>Data</th>
                            <th>Godzina</th>
                            <th>Status</th>
                            <th>Rezerwacja</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                <td>" . htmlspecialchars($row['id']) . "</td>
                                <td>" . htmlspecialchars($row['Miejsca']) . "</td>
                                <td>" . htmlspecialchars($row['Data']) . "</td>
                                <td>" . htmlspecialchars($row['Godzina']) . "</td>
                                <td>" . htmlspecialchars($row['Status']) . "</td>
                                <td>" . htmlspecialchars($row['Rezerwacja']) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Brak dostępnych rezerwacji</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>

                    <!-- Przycisk pod tabelą -->
                    <a href="rezerwacje.php" id="wiecej-przycisk">Więcej terminów</a>

                    
                </div>
            </div>

            <div id="prawa-strona">
                <h2>Rezerwacja</h2>
                <label>Data</label>
                <input type="text" id="rezerwacja-data" placeholder="DD-MM-RRRR">
                <label>Godzina</label>
                <input type="text" id="rezerwacja-godzina" placeholder="HH:MM">
                <label>Hasło</label>
                <input type="password" id="rezerwacja-haslo" placeholder="Wpisz hasło">
                <div id="haslo-komunikat"></div>
                <button id="przycisk-podsumowanie">Podsumowanie</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal">
    <div id="modal-content">
        <h2>Zalogowano jako:</h2>
        <p id="modal-user"></p><br><br>
        Numer telefonu<br>
        <p id="modal-telefon"></p><br><br>
        E-mail<br>
        <p id="modal-email"></p>
    </div>
</div>

<script>
    const imieLS = localStorage.getItem("uzytkownikImie") || "";
    const nazwiskoLS = localStorage.getItem("uzytkownikNazwisko") || "";
    document.getElementById("imieNazwisko").innerText = imieLS + " " + nazwiskoLS;

    // Modal
    const uzytkownikDiv = document.getElementById("uzytkownik");
    const modal = document.getElementById("modal");
    const modalUser = document.getElementById("modal-user");
    const modalTelefon = document.getElementById("modal-telefon");
    const modalEmail = document.getElementById("modal-email");

    uzytkownikDiv.addEventListener("click", () => {
        modal.style.display = "flex";
        modalUser.textContent = imieLS + " " + nazwiskoLS;
        fetch(`get_user.php?imie=${encodeURIComponent(imieLS)}&nazwisko=${encodeURIComponent(nazwiskoLS)}`)
            .then(res => res.json())
            .then(data => {
                modalTelefon.textContent = data.telefon || "Brak danych";
                modalEmail.textContent = data.email || "Brak danych";
            }).catch(() => {
                modalTelefon.textContent = "Błąd połączenia";
                modalEmail.textContent = "Błąd połączenia";
            });
    });

    modal.addEventListener("click", e => {
        if(e.target === modal) modal.style.display = "none";
    });

    // Sprawdzanie hasła
    const prawdziweHaslo = "<?php echo $haslo; ?>";
    const inputHaslo = document.getElementById("rezerwacja-haslo");
    const komunikatHaslo = document.getElementById("haslo-komunikat");
    const przyciskPodsumowanie = document.getElementById("przycisk-podsumowanie");

    przyciskPodsumowanie.addEventListener("click", () => {
        if(inputHaslo.value === prawdziweHaslo && prawdziweHaslo !== ""){
            window.location.href = "podsumowanie.html";
        } else {
            komunikatHaslo.style.display = "block";
            komunikatHaslo.textContent = "Błędne hasło";
        }
    });
</script>

</body>
</html>
