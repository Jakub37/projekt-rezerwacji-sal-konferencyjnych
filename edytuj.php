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

if ($id && $nr_sali && $data && $od_godziny && $do_godziny && $id_uzytkownika) {
    $sql = "UPDATE sale 
            SET nr_sali=?, data=?, od_godziny=?, do_godziny=? 
            WHERE id=? AND id_uzytkownika=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssii", $nr_sali, $data, $od_godziny, $do_godziny, $id, $id_uzytkownika);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Rezerwacja zaktualizowana";
        } else {
            echo "Brak zmian lub nie znaleziono rezerwacji";
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
