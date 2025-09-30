<?php
session_start();
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['id_uzytkownika'])) {
    header('Location: index.php');
    exit;
}
$SESSION_USER_ID = (string)$_SESSION['id_uzytkownika'];
$SESSION_IMIE_NAZWISKO = isset($_SESSION['ImieNazwisko']) ? (string)$_SESSION['ImieNazwisko'] : '';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sala 1</title>
    <link rel="stylesheet" href="main.css" />
    <link rel="icon" href="uzytkownik.jpg" type="image/x-icon" />
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
            <div>
                <a href="wyloguj.php" class="btn-back">Wyloguj</a>
            </div>
        </div>

        <div id="main-content">
            <div id="lewa-strona">
                <div id="rezerwacje">
                    <div id="xd"><h2>Zarezerwowane terminy</h2></div>
                    <div id="lista-rezerwacji">
                        <table id="tabela-rezerwacji">
                            <thead>
                            <tr>
                                <th>Sala</th>
                                <th>Data</th>
                                <th>Dzień</th>
                                <th>Od godziny</th>
                                <th>Do godziny</th>
                                <th>Rezerwacja</th>
                                <th>Edycja</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $conn = db_connect();

                            $sql = "SELECT id, nr_sali, data, od_godziny, do_godziny, rezerwacja, id_uzytkownika 
                                    FROM sale 
                                    WHERE data >= CURDATE() 
                                    ORDER BY data, od_godziny";
                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                $lastDate = null;
                                $dniTygodnia = [1 => 'Poniedziałek', 2 => 'Wtorek', 3 => 'Środa', 4 => 'Czwartek', 5 => 'Piątek', 6 => 'Sobota', 7 => 'Niedziela'];
                                while ($row = $result->fetch_assoc()) {
                                    $id = htmlspecialchars($row['id']);
                                    $ownerId = htmlspecialchars($row['id_uzytkownika']);
                                    $dataStr = htmlspecialchars($row['data']);
                                    $nrSalaVal = (int)$row['nr_sali'];
                                    $salaNazwa = $nrSalaVal === 1 ? 'Administracyjna' : ($nrSalaVal === 2 ? 'Handlowy' : ('Sala ' . htmlspecialchars($row['nr_sali'])));
                                    $ts = strtotime($row['data']);
                                    $dzien = $dniTygodnia[(int)date('N', $ts)] ?? '';

                                    if ($lastDate !== null && $lastDate !== $row['data']) {
                                        echo "<tr class='separator-row'><td colspan='7'></td></tr>";
                                    }

                                    echo "<tr data-id='{$id}' data-owner='{$ownerId}' data-room='{$nrSalaVal}'>
                                        <td>" . htmlspecialchars($salaNazwa) . "</td>
                                        <td>" . $dataStr . "</td>
                                        <td>" . htmlspecialchars($dzien) . "</td>
                                        <td>" . htmlspecialchars($row['od_godziny']) . "</td>
                                        <td>" . htmlspecialchars($row['do_godziny']) . "</td>
                                        <td>" . htmlspecialchars($row['rezerwacja']) . "</td>
                                        <td>
                                            <button class='edytuj-btn'>✏</button>
                                            <button class='zatwierdz-btn' style='display:none;'>✔</button>
                                            <button class='usun-btn'>✖</button>
                                        </td>
                                    </tr>";

                                    $lastDate = $row['data'];
                                }
                            } else {
                                echo "<tr><td colspan='7'>Brak zarezerwowanych terminów</td></tr>";
                            }
                            $conn->close();
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="prawa-strona">
                <h2>Rezerwacja</h2>
                <label>Sala</label>
                <select id="rezerwacja-nr_sali">
                    <option value="1">Administracyjna</option>
                    <option value="2">Handlowy</option>
                </select>
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
// dane użytkownika z sesji (zawsze aktualne po zalogowaniu)
const uzytkownikId = "<?php echo htmlspecialchars($SESSION_USER_ID); ?>";
const pelneImie = "<?php echo htmlspecialchars($SESSION_IMIE_NAZWISKO); ?>";
document.getElementById("imieNazwisko").innerText = pelneImie;

// lista wyboru sali (1=Administracyjna, 2=Handlowy)
const salaInput = document.getElementById("rezerwacja-nr_sali");

// modal
const uzytkownikDiv = document.getElementById("uzytkownik");
const modal = document.getElementById("modal");
const modalUser = document.getElementById("modal-user");
const modalTelefon = document.getElementById("modal-telefon");
const modalEmail = document.getElementById("modal-email");

uzytkownikDiv.addEventListener("click", () => {
    modal.style.display = "flex";
    modalUser.textContent = pelneImie;

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
    if (e.target === modal) modal.style.display = "none";
});

// dodanie rezerwacji
document.getElementById("przycisk-podsumowanie").addEventListener("click", () => {
    const nr_sali = document.getElementById("rezerwacja-nr_sali").value.trim();
    const data = document.getElementById("rezerwacja-data").value.trim();
    const od_godziny = document.getElementById("rezerwacja-od_godziny").value.trim();
    const do_godziny = document.getElementById("rezerwacja-do_godziny").value.trim();

    if (!nr_sali || !data || !od_godziny || !do_godziny) {
        alert("Wszystkie pola są wymagane!");
        return;
    }

    const nr = parseInt(nr_sali, 10);
    if (![1, 2].includes(nr)) {
        alert("Dostępne są tylko sale: 1 lub 2.");
        return;
    }

    // walidacja zakresu czasu: od_godziny < do_godziny (brak równości i cofania)
    if (od_godziny >= do_godziny) {
        alert("Nieprawidłowe dane godzin: 'Od' musi być wcześniejsze niż 'Do'.");
        return;
    }

    fetch("zarezerwuj.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `nr_sali=${encodeURIComponent(nr_sali)}&data=${encodeURIComponent(data)}&od_godziny=${encodeURIComponent(od_godziny)}&do_godziny=${encodeURIComponent(do_godziny)}&rezerwacja=${encodeURIComponent(pelneImie)}&id_uzytkownika=${encodeURIComponent(uzytkownikId)}`
    })
    .then(res => res.text())
    .then(msg => {
        alert(msg);
        if (msg === "Termin dodany") location.reload();
    })
    .catch(() => alert("Błąd połączenia z serwerem"));
});

// edycja i usuwanie
document.addEventListener("DOMContentLoaded", () => {
    const currentUserId = uzytkownikId;

    document.querySelectorAll("#tabela-rezerwacji tbody tr").forEach(row => {
        const ownerId = row.dataset.owner;
        const recordId = row.dataset.id;

        const edytujBtn = row.querySelector(".edytuj-btn");
        const zatwierdzBtn = row.querySelector(".zatwierdz-btn");
        const usunBtn = row.querySelector(".usun-btn");

        // Pomiń wiersze bez przycisków (np. separatory między dniami)
        if (!edytujBtn || !zatwierdzBtn || !usunBtn) {
            return;
        }

        if (ownerId !== currentUserId) {
            edytujBtn.disabled = true;
            usunBtn.disabled = true;
            return;
        }

        edytujBtn.addEventListener("click", () => {
            const cells = row.querySelectorAll("td");
            // 0: Sala (select), 1: Data, 2: Dzień (pomijamy), 3: Od, 4: Do
            const select = document.createElement("select");
            select.innerHTML = "<option value=\"1\">Administracyjna</option><option value=\"2\">Handlowy</option>";
            select.value = row.dataset.room || "1";
            cells[0].innerHTML = "";
            cells[0].appendChild(select);

            const inputData = document.createElement("input");
            inputData.type = "date";
            inputData.value = cells[1].textContent;
            cells[1].innerHTML = "";
            cells[1].appendChild(inputData);

            const inputOd = document.createElement("input");
            inputOd.type = "time";
            inputOd.value = cells[3].textContent;
            cells[3].innerHTML = "";
            cells[3].appendChild(inputOd);

            const inputDo = document.createElement("input");
            inputDo.type = "time";
            inputDo.value = cells[4].textContent;
            cells[4].innerHTML = "";
            cells[4].appendChild(inputDo);

            edytujBtn.style.display = "none";
            zatwierdzBtn.style.display = "inline-block";
        });

        zatwierdzBtn.addEventListener("click", () => {
            const cells = row.querySelectorAll("td");
            const nr_sali = (cells[0].querySelector("select")?.value || cells[0].querySelector("input")?.value || "");
            const data = cells[1].querySelector("input")?.value || "";
            let od_godziny = cells[3].querySelector("input")?.value || "";
            let do_godziny = cells[4].querySelector("input")?.value || "";

            // Normalizacja czasu do HH:MM
            const toHHMM = (t) => {
                if (!t) return t;
                const parts = String(t).split(":");
                if (parts.length >= 2) {
                    const hh = parts[0].padStart(2, "0");
                    const mm = parts[1].padStart(2, "0");
                    return `${hh}:${mm}`;
                }
                return t;
            };
            od_godziny = toHHMM(od_godziny);
            do_godziny = toHHMM(do_godziny);

            // Możliwa częściowa edycja: wymagamy tylko istnienia wartości po uzupełnieniu z inputów
            if (!nr_sali || !data || !od_godziny || !do_godziny) {
                alert("Wszystkie pola muszą być wypełnione.");
                return;
            }

            const nr = parseInt(nr_sali, 10);
            if (![1, 2].includes(nr)) {
                alert("Dostępne są tylko sale: 1 lub 2.");
                return;
            }

            if (od_godziny >= do_godziny) {
                alert("Nieprawidłowe dane godzin: 'Od' musi być wcześniejsze niż 'Do'.");
                return;
            }

            fetch("edytuj.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${encodeURIComponent(recordId)}&nr_sali=${encodeURIComponent(nr_sali)}&data=${encodeURIComponent(data)}&od_godziny=${encodeURIComponent(od_godziny)}&do_godziny=${encodeURIComponent(do_godziny)}&id_uzytkownika=${encodeURIComponent(currentUserId)}&rezerwacja=${encodeURIComponent(pelneImie)}`
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
            })
            .catch(() => alert("Błąd połączenia z serwerem"))
            .finally(() => {
                location.reload();
            });
        });

        usunBtn.addEventListener("click", () => {
            if (!confirm("Czy na pewno chcesz usunąć tę rezerwację?")) return;

            fetch("usun.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${encodeURIComponent(recordId)}&id_uzytkownika=${encodeURIComponent(currentUserId)}`
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg || "Usunięto rezerwację");
                location.reload();
            })
            .catch(() => alert("Błąd połączenia z serwerem"));
        });
    });
});
</script>
</body>
</html>
