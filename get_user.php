<?php
require_once __DIR__ . '/config.php';

$conn = db_connect();

$id = $_GET['id'] ?? '';

// zabezpieczenie i konwersja na int
$id = intval($id);

if ($id <= 0) {
    echo json_encode(["error" => "Nieprawidłowe ID użytkownika"]);
    $conn->close();
    exit;
}

$sql = "SELECT imie, nazwisko, telefon, email FROM uzytkownicy WHERE id_uzytkownika = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Brak danych"]);
}

$stmt->close();
$conn->close();
