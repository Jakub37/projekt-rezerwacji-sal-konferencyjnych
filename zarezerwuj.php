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

if (!$nr_sali || !$data || !$od_godziny || !$do_godziny || !$rezerwacja) {
    echo "Brakuje danych!";
    exit;
}

$new_start = $od_godziny;
$new_end = $do_godziny;

// Zapytanie sprawdzające kolizję
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

// Jeśli nie ma kolizji, wstaw rezerwację
$sql2 = "INSERT INTO sale (nr_sali, data, od_godziny, do_godziny, rezerwacja)
         VALUES (?, ?, ?, ?, ?)";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("sssss", $nr_sali, $data, $od_godziny, $do_godziny, $rezerwacja);

if ($stmt2->execute()) {
    echo "Termin dodany";
} else {
    echo "Błąd podczas dodawania terminu";
}

$conn->close();
?>