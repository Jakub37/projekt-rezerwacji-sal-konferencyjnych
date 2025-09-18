<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "modernforms_system";

// Połączenie z bazą
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Dane z formularza
$rezerwacja = $_POST['rezerwacja'] ?? '';
$data       = $_POST['data'] ?? '';
$godzina    = $_POST['godzina'] ?? '';
$sala       = $_POST['sala'] ?? '';

// Walidacja danych
if (!$rezerwacja || !$data || !$godzina || !$sala) {
    echo "⚠️ Brak wymaganych danych!";
    exit;
}

// Wybór tabeli na podstawie sali
switch ($sala) {
    case "Sala Konferencyjna 1":
        $tabela = "sala_konf1";
        break;
    case "Sala Konferencyjna 2":
        $tabela = "sala_konf2";
        break;
    default:
        echo "⚠️ Nieprawidłowa nazwa sali!";
        exit;
}

// Sprawdzenie czy termin jest już zajęty
$sprawdzSql = "SELECT id FROM $tabela WHERE Data = ? AND Godzina = ?";
$sprawdzStmt = $conn->prepare($sprawdzSql);
if (!$sprawdzStmt) {
    die("❌ Błąd przygotowania zapytania: " . $conn->error);
}
$sprawdzStmt->bind_param("ss", $data, $godzina);
$sprawdzStmt->execute();
$sprawdzStmt->store_result();

if ($sprawdzStmt->num_rows > 0) {
    echo "⚠️ Wybrana data i godzina są już zajęte!";
    $sprawdzStmt->close();
    $conn->close();
    exit;
}
$sprawdzStmt->close();

// Generowanie losowej liczby miejsc
$miejsca = rand(10, 50);
$status  = "Zarezerwowano";

// Przygotowanie i wykonanie zapytania
$sql = "INSERT INTO $tabela (Miejsca, Data, Godzina, Status, Rezerwacja) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Błąd przygotowania zapytania: " . $conn->error);
}

$stmt->bind_param("issss", $miejsca, $data, $godzina, $status, $rezerwacja);

if ($stmt->execute()) {
    echo "✅ Rezerwowano sale pomyślnie!";
} else {
    echo "❌ Błąd zapytania: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>