<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala 1</title>
    <link rel="stylesheet" href="sala1.css">
</head>
<body>
<div id="glowny">
    <div id="blok_sali">

        <div id="top-controls">
            <div id="uzytkownik">
                <img src="uzytkownik.jpg" alt="Użytkownik">
                <div class="tekst">
                    <b><span>Użytkownik:</span></b>
                    <span id="imieNazwisko"></span>
                </div>
            </div>

            <div id="naglowek-sala"><h2>Sala Konferencyjna 1</h2></div>

            <a id="sala2" href="sala2.php">Sala konferencyjna 2 →</a>
        </div>

        <div id="main-content">
            <div id="lewa-strona">
                <div id="rezerwacje">
                    <div id="xd">
                        <h2>Zarezerwowane terminy</h2>
                    </div>
                    <div id="lista-rezerwacji">
                        <table id="tabela-rezerwacji">
                            <thead>
                            <tr>
                                <th>Data</th>
                                <th>Godzina</th>
                                <th>Rezerwacja</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Połączenie z bazą
                            $host = "localhost";
                            $user = "root";
                            $password = "";
                            $dbname = "modernforms_system";

                            $conn = new mysqli($host, $user, $password, $dbname);
                            if ($conn->connect_error) die("Błąd połączenia: " . $conn->connect_error);

                            $sql = "SELECT Data, Godzina, Rezerwacja FROM sala_konf1 WHERE Data >= CURDATE() ORDER BY Data, Godzina";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                    <td>" . htmlspecialchars($row['Data']) . "</td>
                                    <td>" . htmlspecialchars($row['Godzina']) . "</td>
                                    <td>" . htmlspecialchars($row['Rezerwacja']) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>Brak zarezerwowanych dat</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="prawa-strona">
                <h2>Rezerwacja</h2>
                <label>Data</label>
                <input type="text" id="rezerwacja-data" placeholder="RRRR-MM-DD">
                <label>Godzina</label>
                <input type="text" id="rezerwacja-godzina" placeholder="HH:MM-HH:MM">
                <label>Hasło</label>
                <input type="password" id="rezerwacja-haslo" placeholder="Wpisz hasło">
                <div id="haslo-komunikat"></div>
                <button id="przycisk-podsumowanie">Podsumowanie</button>
            </div>
        </div>
    </div>
</div>

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
    const inputHaslo = document.getElementById("rezerwacja-haslo");
    const komunikatHaslo = document.getElementById("haslo-komunikat");
    const przyciskPodsumowanie = document.getElementById("przycisk-podsumowanie");

    przyciskPodsumowanie.addEventListener("click", () => {
        fetch(`check_password.php?imie=${encodeURIComponent(imieLS)}&nazwisko=${encodeURIComponent(nazwiskoLS)}&haslo=${encodeURIComponent(inputHaslo.value)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    localStorage.setItem("rezerwacjaData", document.getElementById("rezerwacja-data").value);
                    localStorage.setItem("rezerwacjaGodzina", document.getElementById("rezerwacja-godzina").value);
                    localStorage.setItem("rezerwacjaSala", "Sala Konferencyjna 1");
                    window.location.href = "podsumowanie.html";
                } else {
                    komunikatHaslo.style.display = "block";
                    komunikatHaslo.textContent = data.error || "Błędne hasło";
                }
            })
            .catch(() => {
                komunikatHaslo.style.display = "block";
                komunikatHaslo.textContent = "Błąd połączenia z serwerem";
            });
    });
</script>
</body>
</html>