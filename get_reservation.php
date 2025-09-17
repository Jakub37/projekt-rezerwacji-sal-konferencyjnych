<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "twoj_uzytkownik";
$password = "twoje_haslo";
$dbname = "twoja_baza";

// Połączenie z bazą
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Błąd połączenia z bazą']);
    exit;
}

// Pobierz 5 najbliższych rezerwacji od dziś
$sql = "SELECT id, Miejsca, Data, Godzina, Status, Rezerwacja 
        FROM sala_konf1 
        WHERE Data >= CURDATE() 
        ORDER BY Data, Godzina 
        LIMIT 5";

$result = $conn->query($sql);

$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
} else {
    echo json_encode(['error' => 'Błąd zapytania']);
}

$conn->close();
