<?php
// send_reminders.php – sprawdza terminy i wysyła przypomnienie 30 min przed startem
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';

date_default_timezone_set('Europe/Warsaw');
$conn = db_connect();

// Upewnij się, że mamy kolumnę do oznaczania wysłanych przypomnień (idempotentnie)
$colCheck = $conn->query("SHOW COLUMNS FROM sale LIKE 'reminder_sent'");
if ($colCheck && $colCheck->num_rows === 0) {
    @ $conn->query("ALTER TABLE sale ADD COLUMN reminder_sent TINYINT(1) NOT NULL DEFAULT 0");
}

// Okno 29–30 min przed rozpoczęciem (łatwiejsze trafienie w testach)
$sql = "
    SELECT s.id, s.nr_sali, s.data, s.od_godziny, u.email, u.Imie, u.Nazwisko
    FROM sale s
    JOIN uzytkownicy u ON u.id_uzytkownika = s.id_uzytkownika
    WHERE s.reminder_sent = 0
      AND TIMESTAMP(s.data, s.od_godziny)
          BETWEEN (NOW() + INTERVAL 29 MINUTE) AND (NOW() + INTERVAL 30 MINUTE)
";
$res = $conn->query($sql);
if (!$res) { echo "[ERR] SELECT: " . $conn->error . "\n"; exit(1); }
if ($res->num_rows === 0) {
    echo "[INFO] Brak terminów w oknie 29–30 min. Serwer: " . date('Y-m-d H:i:s') . "\n";
}

while ($row = $res->fetch_assoc()) {
	$id    = (int)$row['id'];
	$email = trim((string)$row['email']);
	if ($email === '') { echo "[SKIP] Pusty e-mail id={$id}\n"; continue; }

	$start   = $row['data'] . ' ' . substr((string)$row['od_godziny'], 0, 5);
	$key     = $id . '|' . $start;
	if (isset($cache[$key])) { echo "[SKIP] Już wysłane id={$id}\n"; continue; }

	$nr    = (int)$row['nr_sali'];
	$sala  = $nr === 1 ? 'Administracyjna' : ($nr === 2 ? 'Handlowy' : ('Sala ' . $nr));
	$subject = 'Przypomnienie: Rezerwacja sali konferencyjnej';
	$body    = "Cześć {$row['Imie']} {$row['Nazwisko']},\n\nSpotkanie w sali {$sala} rozpoczyna się za 30 minut.\nTermin: {$start}.\n\nDo zobaczenia!";

    $sent = send_email($email, $subject, $body);
    if ($sent) {
        // Oznacz w bazie, aby nie dublować wysyłki
        $upd = $conn->prepare("UPDATE sale SET reminder_sent = 1 WHERE id = ?");
        if ($upd) { $upd->bind_param('i', $id); $upd->execute(); $upd->close(); }
        echo "[OK] {$email} (id={$id})\n";
    }
    else      { echo "[ERR] wysyłka do {$email} (id={$id}) | " . get_last_mail_error() . "\n"; }
}

$conn->close();
saveCache($cacheFile, $cache);
echo "[DONE] " . date('Y-m-d H:i:s') . "\n";
?>

