<?php
// Hibakeresés bekapcsolása (fejlesztés alatt hasznos)
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 1. ELÉRÉSI UTAK - Ellenőrizd, hogy a mappa neve pontosan ez-e!
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // 2. SZERVER BEÁLLÍTÁSOK
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;         // Vedd ki a kommentet (töröld a // jelet), ha hibát keresel
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';             // Gmail SMTP szervere
    $mail->SMTPAuth   = true;
    $mail->Username   = 'clockytimer@gmail.com';      // <-- ÍRD BE A GMAIL CÍMED
    $mail->Password   = 'wsyp chxu zxbe umyp';        // <-- IDE AZ ALKALMAZÁSJELSZÓ KELL
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Magyar ékezetek beállítása
    $mail->CharSet = 'UTF-8';

    // 3. CÍMZETTEK
    $mail->setFrom('SAJAT_EMAIL@gmail.com', 'Clocky Értesítő');
    $mail->addAddress('CIMZETT_EMAIL@valami.com');    // <-- IDE JÖN A TELEFONON HASZNÁLT CÍM

    // 4. TARTALOM
    $mail->isHTML(true);
    $mail->Subject = 'Figyelem! Értesítés a Clocky rendszertől';
    $mail->Body    = '<h1>Szia!</h1><p>Ez egy automatikus üzenet, ami <b>közvetlenül a PHP-ból</b> érkezett a telefonodra.</p>';
    $mail->AltBody = 'Szia! Ez a szöveges változat olyan telefonokra, amik nem jelenítik meg a HTML-t.';

    $mail->send();
    echo '<div style="color: green; font-weight: bold;">Sikerült! Az üzenet elküldve.</div>';

} catch (Exception $e) {
    echo "<div style='color: red;'>Hiba történt a küldés során: {$mail->ErrorInfo}</div>";
}
?>