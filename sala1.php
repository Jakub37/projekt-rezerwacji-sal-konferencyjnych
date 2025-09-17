<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala 1</title>
    <link rel="stylesheet" href="sala1.css">
    <style>
        #blok_sali {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            padding: 30px;
            border-radius: 20px;
            background-color: white;
            width: 90%;
            min-height: 80vh;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.3);
        }

        #naglowek-sala {
            font-size: 40px;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        #top-controls {
            position: absolute;
            top: 20px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        #uzytkownik {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        #uzytkownik img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }

        #uzytkownik .tekst {
            display: flex;
            flex-direction: column;
            font-size: 12px;
            color: #333;
            line-height: 1.2;
        }

        #uzytkownik:hover {
            background-color: rgba(0,0,0,0.1);
        }

        #sala2 {
            text-decoration: none;
            color: #333;
            background-color: rgba(0,0,0,0.1);
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        #sala2:hover {
            background-color: rgba(0,0,0,0.2);
        }

        /* Sekcja główna: tabela + formularz */
        #main-content {
            display: flex;
            width: 100%;
            gap: 30px;
            margin-top: 20px;
            justify-content: center;
            align-items: flex-start;
        }

        #lewa-strona {
            flex: 1;
        }

        #prawa-strona {
            flex: 0 0 300px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 8px 25px rgba(0,0,0,0.3);
            background-color: rgba(255,255,255,0.95);
            align-self: center; /* wyśrodkowanie bloku względem tabeli */
        }

        #prawa-strona h2 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        #prawa-strona input {
            padding: 8px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 100%;
        }

        #prawa-strona button {
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            margin-top: 10px;
        }

        #prawa-strona button:hover {
            background-color: #45a049;
        }

        #haslo-komunikat {
            color: red;
            font-weight: bold;
            margin-top: 5px;
            display: none;
        }

        #tabela-rezerwacji {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        #tabela-rezerwacji th, #tabela-rezerwacji td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
        }

        #tabela-rezerwacji thead {
            background-color: #4CAF50;
            color: white;
        }

        #tabela-rezerwacji tbody tr:hover {
            background-color: #f1f1f1;
        }

        #wiecej-przycisk {
            display: block;
            margin: 15px auto 0 auto;
            padding: 10px 20px;
            background-color: #4CAF50; 
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            text-align: center;
        }

        #wiecej-przycisk:hover {
            background-color: #45a049;
        }
    </style>
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
$sql = "SELECT id,Miejsca, Data, Godzina, Status, Rezerwacja FROM sala_konf1 WHERE Data >= CURDATE() ORDER BY Data, Godzina LIMIT 5";
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

            <a id="sala2" href="sala2.php">Sala konferencyjna 2 →</a>
            <script>
                        document.getElementById("przycisk").addEventListener("click"), function() {
                            window.location.href = "wybor_sali.html";
                        }
                    </script>

            <a id="sala2" href="sala2.html">Sala konferencyjna 2 →</a>

        </div>

        <!-- Napis sala -->
        <div id="naglowek-sala"><b>Sala Konferencyjna 1</b></div>

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
