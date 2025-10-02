<?php
require_once __DIR__ . '/config.php';

$conn = db_connect();

// Pobranie danych z formularza
$nr_sali = $_POST['nr_sali'] ?? '';
$data = $_POST['data'] ?? '';
$od_godziny = $_POST['od_godziny'] ?? '';
$do_godziny = $_POST['do_godziny'] ?? '';
$id_uzytkownika = $_POST['id_uzytkownika'] ?? '';

// Sprawdzenie, czy wszystkie dane zostały przesłane
if (!$nr_sali || !$data || !$od_godziny || !$do_godziny || !$id_uzytkownika) {
    echo "Brakuje danych!";
    exit;
}

// Walidacja godzin: HH:MM lub HH:MM:SS -> zapis jako HH:MM
$normalizeTime = function($t) {
    if (!$t) return $t;
    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $t)) {
        return false;
    }
    $parts = explode(':', $t);
    $hh = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
    $mm = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
    return $hh . ':' . $mm;
};

$od_norm = $normalizeTime($od_godziny);
$do_norm = $normalizeTime($do_godziny);

if ($od_norm === false || $do_norm === false) {
    echo "Nieprawidłowy format godzin";
    exit;
}

$od_godziny = $od_norm;
$do_godziny = $do_norm;

if ($od_godziny >= $do_godziny) {
    echo "Nieprawidłowe dane godzin";
    exit;
}

// Sprawdzenie, czy rezerwacja nie jest w przeszłości
$startTs = strtotime($data . ' ' . $od_godziny . ':00');
if ($startTs === false || $startTs < time()) {
    echo "Nie można rezerwować terminu w przeszłości";
    exit;
}

// Sprawdzenie kolizji z innymi rezerwacjami
$sql = "SELECT * FROM sale
        WHERE nr_sali = ?
          AND data = ?
          AND ? < do_godziny
          AND ? > od_godziny";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nr_sali, $data, $od_godziny, $do_godziny);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo "Termin zajęty";
    exit;
}

// Dodanie nowej rezerwacji
$sql2 = "INSERT INTO sale (nr_sali, data, od_godziny, do_godziny, id_uzytkownika)
         VALUES (?, ?, ?, ?, ?)";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("ssssi", $nr_sali, $data, $od_godziny, $do_godziny, $id_uzytkownika);

if ($stmt2->execute()) {
    echo "Termin dodany";
} else {
    echo "Błąd podczas dodawania terminu";
}

$conn->close();
?>
