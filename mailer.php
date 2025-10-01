<?php
// mailer.php – cienki wrapper na PHPMailer (SMTP)
// Wymaga trzech plików z PHPMailer/src: PHPMailer.php, SMTP.php, Exception.php

require_once __DIR__ . '/config.php';

// UWAGA: przed użyciem wklej pliki PHPMailer do lib/phpmailer/
require_once __DIR__ . '/lib/phpmailer/Exception.php';
require_once __DIR__ . '/lib/phpmailer/PHPMailer.php';
require_once __DIR__ . '/lib/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Przechowuje ostatni błąd wysyłki do diagnostyki
$LAST_MAIL_ERROR = '';

/**
 * Wysyła e‑mail przez SMTP (PHPMailer). Zwraca true/false.
 * Uzupełnij dane SMTP poniżej.
 */
function send_email(string $to, string $subject, string $body): bool
{
	$smtpHost = 'poczta.interia.pl';
	$smtpPort = 587;               // 587 (TLS) lub 465 (SSL)
	$smtpUser = 'ahaxdok@interia.pl';
	$smtpPass = 'XDXDXD!@#abc';
	$fromEmail = $smtpUser;        // zwykle ten sam co login
	$fromName  = 'Powiadomienia';

    global $LAST_MAIL_ERROR;
    $LAST_MAIL_ERROR = '';

    // Produkcyjnie wyłączone logowanie SMTP (ustaw na true przy diagnostyce)
    $enableSmtpDebug = false;

    $mail = new PHPMailer(true);
	try {
		$mail->isSMTP();
		$mail->Host       = $smtpHost;
		$mail->SMTPAuth   = true;
        $mail->AuthType   = 'LOGIN';
		$mail->Username   = $smtpUser;
		$mail->Password   = $smtpPass;
        // Dobierz szyfrowanie do portu
        if ($smtpPort === 465) {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
        }
        $mail->Port       = $smtpPort;
        $mail->CharSet    = 'UTF-8';
        if ($enableSmtpDebug) {
            $mail->SMTPDebug  = 2;            // szczegóły sesji SMTP
            $mail->Debugoutput = 'error_log'; // log do error_log
        }

		$mail->setFrom($fromEmail, $fromName);
		$mail->addAddress($to);

		$mail->Subject = $subject;
		$mail->Body    = $body;

		return $mail->send();
	} catch (Exception $e) {
        $LAST_MAIL_ERROR = $e->getMessage();
        if (isset($mail) && property_exists($mail, 'ErrorInfo') && $mail->ErrorInfo) {
            $LAST_MAIL_ERROR .= ' | ' . $mail->ErrorInfo;
        }
        return false;
	}
}

function get_last_mail_error(): string
{
    global $LAST_MAIL_ERROR;
    return $LAST_MAIL_ERROR;
}
?>

