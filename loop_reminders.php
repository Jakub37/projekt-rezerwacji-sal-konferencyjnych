<?php
// loop_reminders.php – prosty pętla bez Harmonogramu
// Uruchamiaj z CLI:  C:\xampp\php\php.exe -f loop_reminders.php
// Zatrzymanie: utwórz plik stop_loop.txt w katalogu, pętla zakończy się po najbliższym cyklu

date_default_timezone_set('Europe/Warsaw');
@ini_set('display_errors', 1);
@error_reporting(E_ALL);
set_time_limit(0); // pozwól działać bez limitu czasu

$appDir = __DIR__;
$phpExe = 'C:\\xampp\\php\\php.exe';
$script = $appDir . DIRECTORY_SEPARATOR . 'send_reminders.php';
$stopFile = $appDir . DIRECTORY_SEPARATOR . 'stop_loop.txt';

if (!file_exists($script)) {
    echo "[ERR] Brak pliku send_reminders.php\n";
    exit(1);
}

echo "[LOOP] Start: " . date('Y-m-d H:i:s') . "\n";
while (true) {
    // uruchom cykl
    $cmd = '"' . $phpExe . '" -f ' . '"' . $script . '"';
    echo "[RUN] " . date('Y-m-d H:i:s') . " -> $cmd\n";
    // wywołaj podproces i przechwyć wyjście
    $output = [];
    $code = 0;
    @exec($cmd . ' 2>&1', $output, $code);
    foreach ($output as $line) {
        echo $line . "\n";
    }
    echo "[SLEEP] 600s\n";

    // przerwij, jeśli pojawi się plik stop
    if (file_exists($stopFile)) {
        echo "[STOP] Wykryto stop_loop.txt – kończę.\n";
        @unlink($stopFile);
        break;
    }

    sleep(60);
}
echo "[LOOP] Koniec: " . date('Y-m-d H:i:s') . "\n";
?>

