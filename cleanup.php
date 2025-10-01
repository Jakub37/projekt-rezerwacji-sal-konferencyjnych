<?php
require_once __DIR__ . '/config.php';

$conn = db_connect();

// Usuń rezerwacje, których koniec był ponad godzinę temu
$sql = "DELETE FROM sale WHERE TIMESTAMP(data, do_godziny) < (NOW() - INTERVAL 1 HOUR)";

if ($conn->query($sql) === true) {
	echo "OK";
} else {
	echo "ERR: " . $conn->error;
}

$conn->close();
?>

