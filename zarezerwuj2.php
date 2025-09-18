<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "modernforms_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$rezerwacja = $_POST['rezerwacja'] ?? '';
$data = $_POST['data'] ?? '';
$godzina = $_POST['godzina'] ?? '';
$sala = $_POST['sala'] ?? '';

if (!$rezerwacja || !$data || !$godzina || !$sala) {
    die("⚠️ Brak wymaganych danych!");
}

switch ($sala) {
    case 'Sala Konferencyjna 1':
        $table = 'sala_konf1';
        break;
    case 'Sala Konferencyjna 2':
        $table = 'sala_konf2';
        break;
    default:
        die("⚠️ Nieznana sala!");
}

$miejsca = rand(10, 50);
$status = "Zarezerwowano";

$sql = "INSERT INTO sala_konf2 (Miejsca, Data, Godzina, Status, Rezerwacja) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if(!$stmt){
    die("❌ Błąd w prepare(): " . $conn->error);
}

$stmt->bind_param("issss", $miejsca, $data, $godzina, $status, $rezerwacja);

if ($stmt->execute()) {
    echo "✅ Rezerwacja zapisana pomyślnie!";
} else {
    echo "❌ Błąd zapytania: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
