<?php
$conn = new mysqli("localhost", "root", "", "modernforms_system");
if ($conn->connect_error) die("Błąd połączenia");

$nr_sali = $_POST['nr_sali'] ?? '';
$data = $_POST['data'] ?? '';
$od_godziny = $_POST['od_godziny'] ?? '';
$do_godziny = $_POST['do_godziny'] ?? '';
$rezerwacja = $_POST['rezerwacja'] ?? '';

if (!$nr_sali || !$data || !$od_godziny || !$do_godziny || !$rezerwacja) {
    die("Brak wymaganych danych");
}

$sql = "UPDATE sale SET nr_sali=?, data=?, od_godziny=?, do_godziny=? 
        WHERE nr_sali=? AND data=? AND od_godziny=? AND rezerwacja=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssisss", $nr_sali, $data, $od_godziny, $do_godziny,
                               $nr_sali, $data, $od_godziny, $rezerwacja);

if ($stmt->execute()) {
    echo "Zatwierdzono zmiany rezerwacji";
} else {
    echo "Błąd podczas aktualizacji";
}
