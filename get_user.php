<?php

$servername = "localhost";
$username = "root";       
$password = "";           
$dbname = "modernforms_system";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$imie = $_GET['imie'] ?? '';
$nazwisko = $_GET['nazwisko'] ?? '';

// zabezpieczenie przed SQL injection
$imie = $conn->real_escape_string($imie);
$nazwisko = $conn->real_escape_string($nazwisko);


$sql = "SELECT imie, nazwisko, telefon, email FROM uzytkownicy WHERE imie='$imie' AND nazwisko='$nazwisko' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Brak danych"]);
}

$conn->close();
?>
