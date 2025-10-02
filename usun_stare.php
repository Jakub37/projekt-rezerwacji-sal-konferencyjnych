<?php
require_once __DIR__ . '/config.php';

$conn = db_connect();

date_default_timezone_set('Europe/Warsaw');
$now = date('Y-m-d H:i:s');

$sqlDelete = "DELETE FROM sale 
              WHERE TIMESTAMP(CONCAT(data, ' ', do_godziny, ':00')) < DATE_SUB(?, INTERVAL 5 MINUTE)";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("s", $now);
$stmtDelete->execute();

$conn->close();

// Nic nie echo, żeby nic się nie wyświetlało
?>
