<?php

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
        // ⚠️ Agar error dekhna ho toh niche wali line se uncomment (// hatao) kar lena:
        // $mail->SMTPDebug = 2; 

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;
        $mail->Username   = 'YOUR_OFFICIAL_GMAIL@gmail.com';      // 👈 Apna Admin Gmail ID daalo
        $mail->Password   = 'YOUR_16_DIGIT_APP_PASSWORD';         // 👈 16-digit Google App Password (Bina space ke)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
        $mail->Port       = 587;                                  

        // --- 👥 SENDER & RECEIVER ---
        $mail->setFrom('YOUR_OFFICIAL_GMAIL@gmail.com', 'AlumniX Portal');
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
    $safeHomeLink = alumnixEscape($homeLink);

    $html = "<html><body><p>Hi {$safeName}, your registration is pending.</p></body></html>";
    return alumnixSendHtmlMail($email, 'AlumniX Registration Received', $html);
}

function alumnixSendApprovalCredentials(string $fullName, string $email, string $plainPassword): bool
{
    $loginLink = alumnixBaseUrl() . '/login.php';
    $safeName = alumnixEscape($fullName);
    $safeEmail = alumnixEscape($email);
    $safePassword = alumnixEscape($plainPassword);
    $safeLoginLink = alumnixEscape($loginLink);

    $html = "<html><body><p>Hi {$safeName}, Approved! User: {$safeEmail}, Pass: {$safePassword}</p></body></html>";
    return alumnixSendHtmlMail($email, 'AlumniX Login Credentials', $html);
}

function alumnixApproveUser(mysqli $conn, int $userId): array
{
    $stmt = $conn->prepare("SELECT id, full_name, email, role, status FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) return ['ok' => false, 'message' => 'Unable to prepare approval query.'];

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$user) return ['ok' => false, 'message' => 'Member not found.'];

    $status = strtolower(trim((string) ($user['status'] ?? 'pending')));
    if (in_array($status, ['approved', 'active'], true)) {
        return ['ok' => false, 'message' => 'Member is already approved.'];
    }

    $plainPassword = alumnixGeneratePassword();
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users SET status = 'approved', password = ? WHERE id = ?");
    if (!$update) return ['ok' => false, 'message' => 'Unable to update member status.'];

    $update->bind_param('si', $hashedPassword, $userId);
    $saved = $update->execute();
    $update->close();

    if (!$saved) return ['ok' => false, 'message' => 'Member approval could not be saved.'];

    $mailSent = false;
    if (!empty($user['email'])) {
        $mailSent = alumnixSendApprovalCredentials((string) $user['full_name'], (string) $user['email'], $plainPassword);
    }

    return [
        'ok' => true,
        'mail_sent' => $mailSent,
        'message' => $mailSent
            ? 'Member approved and login credentials emailed.'
            : 'Member approved, but email delivery failed. Share the generated password manually.',
        'name' => (string) ($user['full_name'] ?? ''),
        'email' => (string) ($user['email'] ?? ''),
        'password' => $plainPassword,
    ];
}