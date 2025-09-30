<?php
require_once __DIR__ . '/config.php';

$conn = db_connect();
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Błąd połączenia z bazą."]));
}

$imie = $_GET['imie'] ?? '';
$nazwisko = $_GET['nazwisko'] ?? '';
$haslo = $_GET['haslo'] ?? '';

if ($imie && $nazwisko && $haslo) {
    $stmt = $conn->prepare("SELECT haslo FROM uzytkownicy WHERE imie=? AND nazwisko=?");
    $stmt->bind_param("ss", $imie, $nazwisko);
    $stmt->execute();
    $stmt->bind_result($dbHaslo);
    if ($stmt->fetch()) {
        if ($haslo === $dbHaslo) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Błędne hasło"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Nie znaleziono użytkownika"]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Brak danych wejściowych"]);
}

$conn->close();
?>
