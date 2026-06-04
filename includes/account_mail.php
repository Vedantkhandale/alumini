<?php
/**
 * AlumniX Pro - Mailer Wrapper
 * File: includes/account_mail.php
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Path check: Ensure ye PHPMailer ke paths sahi hain
require_once __DIR__ . '/../vendor/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer-master/src/SMTP.php';

function alumnixSendApprovalCredentials($fullName, $email, $plainPassword) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Config
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'YOUR_OFFICIAL_GMAIL@gmail.com'; // Apna Email yahan daal
        $mail->Password   = 'YOUR_16_DIGIT_APP_PASSWORD';    // Yahan App Password daal
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender
        $mail->setFrom($mail->Username, 'AlumniX Portal');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'AlumniX Login Credentials';
        $mail->Body    = "
            <div style='font-family: sans-serif; padding: 20px; color: #333;'>
                <h2>Hi " . htmlspecialchars($fullName) . ",</h2>
                <p>Your AlumniX account has been approved!</p>
                <p><strong>Temporary Password:</strong> 
                   <span style='background: #eee; padding: 5px; font-weight: bold;'>" . htmlspecialchars($plainPassword) . "</span>
                </p>
                <p>Please login and change your password immediately.</p>
            </div>";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}