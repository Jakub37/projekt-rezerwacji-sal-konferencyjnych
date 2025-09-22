<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "modernforms_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$nr_sali = $_POST['nr_sali'] ?? '';
$data = $_POST['data'] ?? '';
$od_godziny = $_POST['od_godziny'] ?? '';
$do_godziny = $_POST['do_godziny'] ?? '';
$rezerwacja = $_POST['rezerwacja'] ?? '';
$id_uzytkownika = $_POST['id_uzytkownika'] ?? '';

if (!$nr_sali || !$data || !$od_godziny || !$do_godziny || !$rezerwacja || !$id_uzytkownika) {
    echo "Brakuje danych!";
    exit;
}

// Sprawdzenie kolizji terminów
$sql = "SELECT * FROM sale
        WHERE nr_sali = ?
          AND data = ?
          AND ? < do_godziny
          AND ? > od_godziny";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nr_sali, $data, $od_godziny, $do_godziny);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo "Termin zajęty";
    exit;
}

// Wstawienie rezerwacji wraz z id_uzytkownika
$sql2 = "INSERT INTO sale (nr_sali, data, od_godziny, do_godziny, rezerwacja, id_uzytkownika)
         VALUES (?, ?, ?, ?, ?, ?)";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("sssssi", $nr_sali, $data, $od_godziny, $do_godziny, $rezerwacja, $id_uzytkownika);

if ($stmt2->execute()) {
    echo "Termin dodany";
} else {
    echo "Błąd podczas dodawania terminu";
}

$conn->close();
?>
