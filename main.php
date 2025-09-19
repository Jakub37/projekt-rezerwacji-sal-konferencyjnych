<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sala 1</title>
    <link rel="stylesheet" href="main.css" />
</head>
<body>
<div id="glowny">
    <div id="blok_sali">

        <div id="top-controls">
            <div id="uzytkownik">
                <img src="uzytkownik.jpg" alt="Użytkownik" />
                <div class="tekst">
                    <b><span>Użytkownik:</span></b>
                    <span id="imieNazwisko"></span>
                </div>
            </div>

            <div id="naglowek-sala"><h1>Rezerwacja Sal</h1></div>
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
                                <th>Nr sali</th>
                                <th>Data</th>
                                <th>Od godziny</th>
                                <th>Do godziny</th>
                                <th>Rezerwacja</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $conn = new mysqli("localhost", "root", "", "modernforms_system");
                            if ($conn->connect_error) die("Błąd połączenia: " . $conn->connect_error);

                            $sql = "SELECT nr_sali, data, od_godziny, do_godziny, rezerwacja FROM sale WHERE data >= CURDATE() ORDER BY data, od_godziny";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                    <td>" . htmlspecialchars($row['nr_sali']) . "</td>
                                    <td>" . htmlspecialchars($row['data']) . "</td>
                                    <td>" . htmlspecialchars($row['od_godziny']) . "</td>
                                    <td>" . htmlspecialchars($row['do_godziny']) . "</td>
                                    <td>" . htmlspecialchars($row['rezerwacja']) . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Brak zarezerwowanych terminów</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="prawa-strona">
                <h2>Rezerwacja</h2>
                <label>Nr sali</label>
                <input type="number" id="rezerwacja-nr_sali" placeholder="Nr sali" min="1" max="2" />
                <label>Data</label>
                <input type="date" id="rezerwacja-data" />
                <label>Od godziny</label>
                <input type="time" id="rezerwacja-od_godziny" />
                <label>Do godziny</label>
                <input type="time" id="rezerwacja-do_godziny" />
                <button id="przycisk-podsumowanie">Podsumowanie</button>
            </div>
        </div>
    </div>
</div>

<div id="modal">
    <div id="modal-content">
        <h2>Zalogowano jako:</h2>
        <p id="modal-user"></p><br /><br />
        Numer telefonu<br />
        <p id="modal-telefon"></p><br /><br />
        E-mail<br />
        <p id="modal-email"></p>
    </div>
</div>

<script>
    const imieLS = localStorage.getItem("uzytkownikImie") || "";
    const nazwiskoLS = localStorage.getItem("uzytkownikNazwisko") || "";
    const uzytkownikId = localStorage.getItem("uzytkownikId") || "";

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

        if (!uzytkownikId) {
            modalTelefon.textContent = "Brak danych";
            modalEmail.textContent = "Brak danych";
            return;
        }

        fetch(`get_user.php?id=${encodeURIComponent(uzytkownikId)}`)
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
        if(e.target === modal) modal.style.display = "none";
    });

    // Rezerwacja bez hasła
    const przyciskPodsumowanie = document.getElementById("przycisk-podsumowanie");

    przyciskPodsumowanie.addEventListener("click", () => {
        const nr_sali = document.getElementById("rezerwacja-nr_sali").value.trim();
        const data = document.getElementById("rezerwacja-data").value.trim();
        const od_godziny = document.getElementById("rezerwacja-od_godziny").value.trim();
        const do_godziny = document.getElementById("rezerwacja-do_godziny").value.trim();

        if (!nr_sali || !data || !od_godziny || !do_godziny) {
            alert("Wszystkie pola są wymagane!");
            return;
        }

        fetch("zarezerwuj.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `nr_sali=${encodeURIComponent(nr_sali)}&data=${encodeURIComponent(data)}&od_godziny=${encodeURIComponent(od_godziny)}&do_godziny=${encodeURIComponent(do_godziny)}&rezerwacja=${encodeURIComponent(imieLS + " " + nazwiskoLS)}`
        })
        .then(res => res.text())
        .then(msg => {
            alert("Termin dodany");
            setTimeout(() => window.location.reload(), 1000);
        })
        .catch(() => alert("Błąd połączenia z serwerem"));
    });
</script>
</body>
</html>
