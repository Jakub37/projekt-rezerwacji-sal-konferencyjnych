<?php
$conn = new mysqli("localhost", "root", "", "modernforms_system");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$id             = $_POST['id'] ?? '';
$nr_sali        = $_POST['nr_sali'] ?? '';
$data           = $_POST['data'] ?? '';
$od_godziny     = $_POST['od_godziny'] ?? '';
$do_godziny     = $_POST['do_godziny'] ?? '';
$id_uzytkownika = $_POST['id_uzytkownika'] ?? '';

// Walidacja nr_sali: tylko 1 lub 2
if ($nr_sali !== '' && !in_array((int)$nr_sali, [1, 2], true)) {
    echo "Nieprawidłowy numer sali (dozwolone: 1 lub 2)";
    $conn->close();
    exit;
}

if ($id && $nr_sali && $data && $od_godziny && $do_godziny && $id_uzytkownika) {
    // Sprawdzenie kolizji z innymi rezerwacjami (wyklucz bieżący rekord)
    $checkSql = "SELECT 1 FROM sale
                 WHERE nr_sali = ?
                   AND data = ?
                   AND ? < do_godziny
                   AND ? > od_godziny
                   AND id <> ?";
    $check = $conn->prepare($checkSql);
    $check->bind_param("isssi", $nr_sali, $data, $od_godziny, $do_godziny, $id);
    $check->execute();
    $checkResult = $check->get_result();
    if ($checkResult && $checkResult->num_rows > 0) {
        echo "Termin zajęty";
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    $sql = "UPDATE sale 
            SET nr_sali=?, data=?, od_godziny=?, do_godziny=? 
            WHERE id=? AND id_uzytkownika=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssii", $nr_sali, $data, $od_godziny, $do_godziny, $id, $id_uzytkownika);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Rezerwacja zaktualizowana";
        } else {
            echo "Brak zmian";
        }
    } else {
        echo "Błąd podczas aktualizacji: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Brak wymaganych danych – nie można zaktualizować rezerwacji";
}

$conn->close();
?>
