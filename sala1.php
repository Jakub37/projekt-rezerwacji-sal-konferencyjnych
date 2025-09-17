<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sala 1</title>
    <link rel="stylesheet" href="sala1.css">
    <style>
        /* Modal */
        #modal {
            display: none;
            /* ukryty domyślnie */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.6);
            justify-content: center;
            align-items: center;
        }

        #modal-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px;
            min-width: 300px;
            min-height: 200px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        #modal-content h2 {
            font-size: 30px;
            /* większy napis */
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            /* wycentrowanie */
            width: 100%;
            /* potrzebne, żeby działało wycentrowanie */
            text-align: center;
        }

        #modal-content p {
            font-size: 16px;
            margin: 0;
        }
    </style>
</head>
<?php
// Połączenie z bazą - dostosuj dane do swojego serwera
$host = "localhost";
$user = "root";
$password = "";
$dbname = "modernforms_system";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Pobieramy 5 najbliższych rezerwacji - załóżmy, że jest kolumna Data i Godzina
$sql = "SELECT id,Miejsca, Data, Godzina, Status, Rezerwacja FROM sala_konf1 WHERE Data >= CURDATE() ORDER BY Data, Godzina LIMIT 5";
$result = $conn->query($sql);
?>

<body>
    <div id="glowny">
        <div id="blok_sali">

            <!-- Użytkownik -->
            <div id="uzytkownik">
                <img src="uzytkownik.jpg" alt="Użytkownik">
                <div class="tekst">
                    <b><span>Użytkownik:</span></b>
                    <span id="imieNazwisko"></span>
                </div>

            </div>

            <!-- Link do innej sali -->
            <a id="sala2" href="sala2.html">Sala konferencyjna 2 →</a>

            <!-- Nagłówek -->
            <h1>Sala Konferencyjna 1</h1>
            <div id="rezerwacje" style="float: left;">
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
                            echo "<tr><td colspan='4'>Brak dostępnych rezerwacji</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div id="rezerwacje">


    <a href="rezerwacje.php" id="wiecej-przycisk" style="text-align: center;">Więcej terminów</a>
</div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal">
        <div id="modal-content">
            <h2>Zalogowano jako:</h2>
            Imię i nazwisko<br>
            <p id="modal-user"></p><br><br>
            Numer telefonu<br>
            <p id="modal-telefon"></p><br><br>
            E-mail<br>
            <p id="modal-email"></p>
        </div>
    </div>

    <script>
        const imie = localStorage.getItem("uzytkownikImie") || "";
        const nazwisko = localStorage.getItem("uzytkownikNazwisko") || "";

        // wstaw imię i nazwisko do małego boxa
        document.getElementById("imieNazwisko").innerText = imie + " " + nazwisko;

        // modal
        const uzytkownikDiv = document.getElementById("uzytkownik");
        const modal = document.getElementById("modal");
        const modalUser = document.getElementById("modal-user");
        const modalTelefon = document.getElementById("modal-telefon");
        const modalEmail = document.getElementById("modal-email");

        uzytkownikDiv.addEventListener("click", () => {
            modal.style.display = "flex";

            // ustaw imię i nazwisko
            modalUser.textContent = imie + " " + nazwisko;

            // pobierz dodatkowe dane z PHP
            fetch(`get_user.php?imie=${encodeURIComponent(imie)}&nazwisko=${encodeURIComponent(nazwisko)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        modalTelefon.textContent = "Brak danych";
                        modalEmail.textContent = "Brak danych";
                    } else {
                        modalTelefon.textContent = data.telefon;
                        modalEmail.textContent = data.email;
                    }
                })
                .catch(err => {
                    modalTelefon.textContent = "Błąd połączenia";
                    modalEmail.textContent = "Błąd połączenia";
                    console.error(err);
                });
        });

        // zamykanie modala
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });

        fetch('get_reservations.php')
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                const tbody = document.querySelector('#tabela-rezerwacji tbody');
                tbody.innerHTML = ''; // wyczyść

                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                <td>${row.id}</td>
                <td>${row.Miejsca}</td>
                <td>${row.Data}</td>
                <td>${row.Godzina}</td>
                <td>${row.Status}</td>
                <td>${row.Rezerwacja}</td>
            `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                console.error('Błąd pobierania danych:', err);
            });

    </script>



</body>

</html>