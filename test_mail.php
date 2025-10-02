<?php
require_once 'mailer.php';

// Spróbuj wysłać maila na jakiś adres (tu testowy)
$sent = send_email('ahaxdok@interia.pl', 'Test', 'To jest test');

if (!$sent) {
    // Tu wywołujesz funkcję i wypisujesz błąd
    echo "Błąd wysyłki: " . get_last_mail_error() . "\n";
} else {
    echo "Mail wysłany poprawnie.\n";
}
?>
