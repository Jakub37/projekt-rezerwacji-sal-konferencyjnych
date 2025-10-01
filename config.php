<?php
$host = 'localhost';
$uzytkownik = 'root';
$haslo = '';
$baza = 'modernforms_system';

function db_connect() {
	global $host, $uzytkownik, $haslo, $baza;
	$conn = new mysqli($host, $uzytkownik, $haslo, $baza);
	if ($conn->connect_error) {
		die("Błąd połączenia: " . $conn->connect_error);
	}
	$conn->set_charset('utf8mb4'); 
	return $conn;
}

// Ustawienia SMTP do wysyłki e‑mail (wypełnij danymi dostawcy lub sandboxu)
// (Usunięto stare wysyłanie maili/SMTP – skrypt przypomnień jest w send_reminders.php i używa mail())
?>