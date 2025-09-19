<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "modernforms_system";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$nr_sali    = $_POST['nr_sali'] ?? '';
$data       = $_POST['data'] ?? '';
$od_godziny = $_POST['od_godziny'] ?? '';
$do_godziny = $_POST['do_godziny'] ?? '';
$rezerwacja = $_POST['rezerwacja'] ?? '';

if (!$nr_sali || !$data || !$od_godziny || !$do_godziny || !$rezerwacja) {
    echo "Wszystkie pola są wymagane!";
    exit;
}

// Sprawdzenie czy sala jest już zajęta w tym terminie i godzinach
$sprawdzSql = "SELECT id FROM sale 
    WHERE nr_sali = ? 
    AND data = ? 
    AND (
        (od_godziny < ? AND do_godziny > ?) OR
        (od_godziny < ? AND do_godziny > ?) OR
        (od_godziny >= ? AND do_godziny <= ?)
    )";
$sprawdzStmt = $conn->prepare($sprawdzSql);
$sprawdzStmt->bind_param(
    "isssssss",
    $nr_sali, $data,
    $do_godziny, $od_godziny,
    $od_godziny, $do_godziny,
    $od_godziny, $do_godziny
);
$sprawdzStmt->execute();
$sprawdzStmt->store_result();

if ($sprawdzStmt->num_rows > 0) {
    echo "Wybrana sala jest już zajęta w tym terminie i godzinach!";
    $sprawdzStmt->close();
    $conn->close();
    exit;
}
$sprawdzStmt->close();

// Dodanie rezerwacji
$sql = "INSERT INTO sale (nr_sali, data, od_godziny, do_godziny, rezerwacja) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $nr_sali, $data, $od_godziny, $do_godziny, $rezerwacja);

if ($stmt->execute()) {
    echo "Rezerwacja dodana!";
} else {
    echo "Błąd rezerwacji: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>