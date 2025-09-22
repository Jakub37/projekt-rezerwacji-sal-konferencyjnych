<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

$conn = new mysqli("localhost", "root", "", "modernforms_system");
if ($conn->connect_error) {
    http_response_code(500);
    die("Błąd połączenia: " . $conn->connect_error);
}

$id = $_POST['id'] ?? '';
$id_uzytkownika = $_POST['id_uzytkownika'] ?? '';

if ($id === '' || $id_uzytkownika === '') {
    http_response_code(400);
    die("Brak wymaganych danych");
}

$id = (int)$id;
$id_uzytkownika = (int)$id_uzytkownika;

// Sprawdź czy rekord istnieje i kto jest właścicielem
$check_sql = "SELECT id_uzytkownika FROM sale WHERE id = ?";
if (!($stmt = $conn->prepare($check_sql))) {
    http_response_code(500);
    die("Błąd przygotowania zapytania: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    die("Rezerwacja nie istnieje");
}
$row = $result->fetch_assoc();
if ((int)$row['id_uzytkownika'] !== $id_uzytkownika) {
    http_response_code(403);
    die("Nie masz uprawnień do usunięcia tej rezerwacji");
}
$stmt->close();

// Usuń rekord (razem z kontrolą właściciela)
$delete_sql = "DELETE FROM sale WHERE id = ? AND id_uzytkownika = ?";
if (!($stmt = $conn->prepare($delete_sql))) {
    http_response_code(500);
    die("Błąd przygotowania zapytania: " . $conn->error);
}
$stmt->bind_param("ii", $id, $id_uzytkownika);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Rezerwacja usunięta";
    } else {
        echo "Nie udało się usunąć rezerwacji";
    }
} else {
    http_response_code(500);
    echo "Błąd: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
