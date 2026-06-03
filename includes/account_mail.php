<?php
/**
 * AlumniX Pro - Global Helper Engine
 * Handle Security, Database Fetching, and UI Utilities
 */

// 🚀 PHPMailer ki dependency load karo
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Sahi manual paths bina autoload file ke crash kiye chalega
require_once dirname(__DIR__) . '/vendor/PHPMailer-master/src/Exception.php';
require_once dirname(__DIR__) . '/vendor/PHPMailer-master/src/PHPMailer.php';
require_once dirname(__DIR__) . '/vendor/PHPMailer-master/src/SMTP.php';

function alumnixEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function alumnixBaseUrl(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    $host = preg_replace('/:\d+$/', '', $host);

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $path = trim(dirname($scriptName), '/.');

    if ($path !== '' && substr($path, -6) === '/admin') {
        $path = trim(dirname($path), '/.');
    }

    return rtrim($scheme . '://' . $host . ($path ? '/' . $path : ''), '/');
}

/**
 * 📨 Updated fully functional PHPMailer Integration
 */
function alumnixSendHtmlMail(string $to, string $subject, string $html): bool
{
    $mail = new PHPMailer(true);

    try {
        // --- ⚙️ SMTP CONFIGURATION ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;
        
        // ⚠️ YAHA APNI REAL DETAILS INPUT KARO BHAI:
        $mail->Username   = 'YOUR_OFFICIAL_GMAIL@gmail.com';      // 👈 Apna Admin Gmail ID daalo
        $mail->Password   = 'YOUR_16_DIGIT_APP_PASSWORD';         // 👈 16-digit Google App Password (Bina space ke)
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
        $mail->Port       = 587;                                  

        // --- 👥 SENDER & RECEIVER ---
        $mail->setFrom($mail->Username, 'AlumniX Portal');
        $mail->addAddress($to);

        // --- 📝 CONTENT ---
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;

        $mail->send();
        return true; 
    } catch (Exception $e) {
        return false; 
    }
}

function alumnixGeneratePassword(int $length = 10): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#%';
    $maxIndex = strlen($alphabet) - 1;
    $password = '';

    for ($index = 0; $index < $length; $index++) {
        $password .= $alphabet[random_int(0, $maxIndex)];
    }

    return $password;
}

function alumnixSendPendingApprovalEmail(string $fullName, string $email): bool
{
    $homeLink = alumnixBaseUrl() . '/index.php';
    $safeName = alumnixEscape($fullName);

    $html = "<html><body><p>Hi {$safeName}, your registration is pending.</p></body></html>";
    return alumnixSendHtmlMail($email, 'AlumniX Registration Received', $html);
}

function alumnixSendApprovalCredentials(string $fullName, string $email, string $plainPassword): bool
{
    $loginLink = alumnixBaseUrl() . '/login.php';
    $safeName = alumnixEscape($fullName);
    $safeEmail = alumnixEscape($email);
    $safePassword = alumnixEscape($plainPassword);

    $html = "
    <div style='font-family: sans-serif; padding: 20px; color: #333;'>
        <h2>Hi {$safeName},</h2>
        <p>Your AlumniX account has been successfully approved by the administrator!</p>
        <p>You can now log in using the credentials below:</p>
        <hr style='border: none; border-top: 1px solid #eee;' />
        <p><strong>Login Email:</strong> {$safeEmail}</p>
        <p><strong>Temporary Password:</strong> <code style='background: #f4f4f5; padding: 4px 8px; border-radius: 4px;'>{$safePassword}</code></p>
        <hr style='border: none; border-top: 1px solid #eee;' />
        <p><a href='{$loginLink}' style='background: #ff4d4d; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block;'>Login to Portal</a></p>
    </div>";
    
    return alumnixSendHtmlMail($email, 'AlumniX Login Credentials Available', $html);
}

// =========================================================================
// 🚀 NAMING SYNCHRONIZATION: Linked directly with Dashboard Core Router
// =========================================================================
// function alumnixApproveUserEngine($conn, $memberId)
// {
//     // 1. Fetch user (PHP 5.3 compatible)
//     $sql = "SELECT id, full_name, email, role, status FROM users WHERE id = '" . (int)$memberId . "' LIMIT 1";
//     $result = $conn->query($sql);
//     $user = $result ? $result->fetch_assoc() : null;

//     if (!$user) return ['ok' => false, 'message' => 'Member not found.'];

//     $status = strtolower(trim((string) ($user['status'] ?? 'pending')));
//     if ($status == 'approved' || $status == 'active') {
//         return ['ok' => false, 'message' => 'Member is already approved.'];
//     }

//     // 2. Password Generate (PHP 5.3 compatible)
//     $plainPassword = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
//     // PHP 5.3 mein PASSWORD_BCRYPT hota hai, lekin password_hash() shayad na ho. 
//     // Agar error aaye toh 'md5($plainPassword)' use karna, par filhal ye try karo:
//     $hashedPassword = crypt($plainPassword, '$2a$07$usesomadasillystringforsalt$');

//     // 3. Update Status
//     $updateSql = "UPDATE users SET status = 'approved', password = '" . $conn->real_escape_string($hashedPassword) . "' WHERE id = '" . (int)$memberId . "'";
//     $saved = $conn->query($updateSql);

//     if (!$saved) return ['ok' => false, 'message' => 'Member approval could not be saved.'];

//     $mailSent = false;
//     if (!empty($user['email'])) {
//         // Yeh function tumhari account_mail.php mein hona chahiye
//         $mailSent = alumnixSendApprovalCredentials((string) $user['full_name'], (string) $user['email'], $plainPassword);
//     }

//     return [
//         'ok' => true,
//         'mail_sent' => $mailSent,
//         'message' => $mailSent ? 'Approved and emailed.' : 'Approved, but email failed. Password: ' . $plainPassword,
//         'name' => (string) ($user['full_name'] ?? ''),
//         'email' => (string) ($user['email'] ?? ''),
//         'password' => $plainPassword,
//     ];
// }
?>