
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sala 2</title>
    <link rel="stylesheet" href="sala2.css" />
</head>

<body>
    <div id="glowny">
        <div id="blok_sali">

            <!-- Górny pasek -->
            <div id="top-controls">
                <a id="sala2" href="sala1.php">← Sala konferencyjna 1</a>
                <div id="naglowek-sala">
                    <h2>Sala Konferencyjna 2</h2>
                </div>
                <div id="uzytkownik">
                    <img src="uzytkownik.jpg" alt="Użytkownik" />
                    <div class="tekst">
                        <b><span>Użytkownik:</span></b>
                        <span id="imieNazwisko"></span>
                    </div>
                </div>
            </div>

            <!-- Główna treść -->
            <div id="main-content">
                <div id="lewa-strona">
                    <div id="rezerwacje">
                        <div id="xd">
                            <h2>Zarezerwowane terminy</h2>
                        </div>

                        <!-- 🔄 Przewijana tabela -->
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
                                    $conn = new mysqli("localhost", "root", "", "modernforms_system");
                                    if ($conn->connect_error)
                                        die("Błąd połączenia: " . $conn->connect_error);

                                    $sql = "SELECT Data, Godzina, Rezerwacja FROM sala_konf2 WHERE Data >= CURDATE() ORDER BY Data, Godzina LIMIT 20";
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
                                        echo "<tr><td colspan='3'>Brak zarezerwowanych terminów</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Prawa strona -->
                <div id="prawa-strona">
                    <h2>Rezerwacja</h2>
                    <label>Data</label>
                    <input type="text" id="rezerwacja-data" placeholder="RRRR-MM-DD" />
                    <label>Godzina</label>
                    <input type="text" id="rezerwacja-godzina" placeholder="HH:MM-HH:MM" />
                    <label>Hasło</label>
                    <input type="password" id="rezerwacja-haslo" placeholder="Wpisz hasło" />
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
            <p id="modal-user"></p><br>
            Numer telefonu<br>
            <p id="modal-telefon"></p><br>
            E-mail<br>
            <p id="modal-email"></p>
        </div>
    </div>

    <script>
        const imieLS = localStorage.getItem("uzytkownikImie") || "";
        const nazwiskoLS = localStorage.getItem("uzytkownikNazwisko") || "";
        document.getElementById("imieNazwisko").innerText = imieLS + " " + nazwiskoLS;

        const modal = document.getElementById("modal");
        const modalUser = document.getElementById("modal-user");
        const modalTelefon = document.getElementById("modal-telefon");
        const modalEmail = document.getElementById("modal-email");

        document.getElementById("uzytkownik").addEventListener("click", () => {
            modal.style.display = "flex";
            modalUser.textContent = imieLS + " " + nazwiskoLS;
            fetch(`get_user.php?imie=${encodeURIComponent(imieLS)}&nazwisko=${encodeURIComponent(nazwiskoLS)}`)
                .then(res => res.json())
                .then(data => {
                    modalTelefon.textContent = data.telefon || "Brak danych";
                    modalEmail.textContent = data.email || "Brak danych";
                })
                .catch(() => {
                    modalTelefon.textContent = "Błąd połączenia";
                    modalEmail.textContent = "Błąd połączenia";
                });
        });

        modal.addEventListener("click", e => {
            if (e.target === modal) modal.style.display = "none";
        });

        const komunikatHaslo = document.getElementById("haslo-komunikat");
        document.getElementById("przycisk-podsumowanie").addEventListener("click", () => {
            const data = document.getElementById("rezerwacja-data").value.trim();
            const godzina = document.getElementById("rezerwacja-godzina").value.trim();
            const haslo = document.getElementById("rezerwacja-haslo").value.trim();

            if (!data || !godzina || !haslo) {
                komunikatHaslo.style.display = "block";
                komunikatHaslo.textContent = "Wszystkie pola są wymagane.";
                return;
            }

            fetch(`check_password.php?imie=${encodeURIComponent(imieLS)}&nazwisko=${encodeURIComponent(nazwiskoLS)}&haslo=${encodeURIComponent(haslo)}`)
                .then(res => res.json())
                .then(dataResp => {
                    if (dataResp.success) {
                        localStorage.setItem("rezerwacjaData", data);
                        localStorage.setItem("rezerwacjaGodzina", godzina);
                        localStorage.setItem("rezerwacjaSala", "Sala Konferencyjna 2");
                        window.location.href = "podsumowanie.html";
                    } else {
                        komunikatHaslo.style.display = "block";
                        komunikatHaslo.textContent = dataResp.error || "Błędne hasło";
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