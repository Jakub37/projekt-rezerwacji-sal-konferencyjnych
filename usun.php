<?php
$conn = new mysqli("localhost", "root", "", "modernforms_system");
if ($conn->connect_error) die("Błąd połączenia");

$nr_sali = $_POST['nr_sali'] ?? '';
$data = $_POST['data'] ?? '';
$od_godziny = $_POST['od_godziny'] ?? '';
$rezerwacja = $_POST['rezerwacja'] ?? '';

if (!$nr_sali || !$data || !$od_godziny || !$rezerwacja) {
    die("Brak wymaganych danych");
}

$sql = "DELETE FROM sale WHERE nr_sali=? AND data=? AND od_godziny=? AND rezerwacja=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $nr_sali, $data, $od_godziny, $rezerwacja);

if ($stmt->execute()) {
    echo "Usunięto rezerwację";
} else {
    echo "Błąd podczas usuwania";
}
