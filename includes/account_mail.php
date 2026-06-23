<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer-master/src/SMTP.php';

function alumnixMailConfig(): array
{
    $configPath = __DIR__ . '/../config/mail.php';
    $fileConfig = file_exists($configPath) ? require $configPath : [];

    return [
        "host" => getenv("ALUMNIX_SMTP_HOST") ?: ($fileConfig["host"] ?? "smtp.gmail.com"),
        "username" => getenv("ALUMNIX_SMTP_USER") ?: ($fileConfig["username"] ?? ""),
        "password" => getenv("ALUMNIX_SMTP_PASS") ?: ($fileConfig["password"] ?? ""),
        "port" => (int) (getenv("ALUMNIX_SMTP_PORT") ?: ($fileConfig["port"] ?? 587)),
        "encryption" => strtolower((string) (getenv("ALUMNIX_SMTP_ENCRYPTION") ?: ($fileConfig["encryption"] ?? "tls"))),
        "from_name" => getenv("ALUMNIX_MAIL_FROM_NAME") ?: ($fileConfig["from_name"] ?? "AlumniX Portal"),
        "reply_to" => getenv("ALUMNIX_MAIL_REPLY_TO") ?: ($fileConfig["reply_to"] ?? ""),
    ];
}

function alumnixSetMailError(string $message): void
{
    $GLOBALS["alumnix_last_mail_error"] = $message;
}

function alumnixLastMailError(): string
{
    return (string) ($GLOBALS["alumnix_last_mail_error"] ?? "Email delivery failed. Check SMTP settings.");
}

function alumnixLogMailError(string $email, string $context, string $message): void
{
    $errorDir = __DIR__ . '/../uploads/mail_outbox';
    if (!is_dir($errorDir)) {
        mkdir($errorDir, 0775, true);
    }
    $line = json_encode([
        "created_at" => date("Y-m-d H:i:s"),
        "email" => $email,
        "context" => $context,
        "error" => $message,
    ], JSON_UNESCAPED_SLASHES);
    if ($line !== false) {
        file_put_contents($errorDir . "/mail_errors.log", $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function alumnixGetLoginUrl(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443') {
        $scheme = 'https';
    }

    return $scheme . '://' . $host . '/alumini/login.php';
}

function alumnixSendApprovalCredentials($fullName, $email, $plainPassword) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();

    if (
        empty($config["host"]) ||
        empty($config["username"]) ||
        empty($config["password"]) ||
        stripos((string) $config["username"], "YOUR_") !== false ||
        stripos((string) $config["password"], "YOUR_") !== false
    ) {
        alumnixSetMailError("SMTP username/password missing in config/mail.php.");
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config["host"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config["username"];
        $mail->Password   = $config["password"];
        $mail->SMTPSecure = $config["encryption"] === "ssl"
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config["port"];
        $mail->CharSet    = "UTF-8";

        $mail->setFrom($config["username"], $config["from_name"]);
        if (!empty($config["reply_to"])) {
            $mail->addReplyTo($config["reply_to"], $config["from_name"]);
        }
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your AlumniX account is approved';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 28px; color: #0f172a;'>
                <div style='max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 18px; padding: 28px; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0 0 10px; color: #f43f5e; font-weight: 700;'>AlumniX Approval</p>
                    <h2 style='margin: 0 0 14px;'>Hi " . htmlspecialchars($fullName, ENT_QUOTES, "UTF-8") . ",</h2>
                    <p style='line-height: 1.6;'>Your AlumniX account has been approved. You can now login using your email address and the temporary password below.</p>
                    <div style='background: #fff1f2; border: 1px solid #fecdd3; border-radius: 14px; padding: 16px; margin: 18px 0;'>
                        <p style='margin: 0 0 8px; font-size: 13px; color: #64748b;'>Login Email</p>
                        <strong>" . htmlspecialchars($email, ENT_QUOTES, "UTF-8") . "</strong>
                        <p style='margin: 16px 0 8px; font-size: 13px; color: #64748b;'>Temporary Password</p>
                        <strong style='font-size: 20px; letter-spacing: 1px;'>" . htmlspecialchars($plainPassword, ENT_QUOTES, "UTF-8") . "</strong>
                    </div>
                    <p style='line-height: 1.6;'>Please change this password after your first login.</p>
                </div>
            </div>";
        $mail->AltBody = "Hi {$fullName}, your AlumniX account is approved. Login email: {$email}. Temporary password: {$plainPassword}. Please change it after first login.";

        if (!$mail->send()) {
            $errorMessage = $mail->ErrorInfo ?: "Email delivery failed.";
            alumnixSetMailError($errorMessage);
            alumnixLogMailError($email, 'approval_credentials', $errorMessage);
            return false;
        }

        return true;
    } catch (Exception $e) {
        $errorMessage = $mail->ErrorInfo ?: $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'approval_credentials', $errorMessage);
        return false;
    }
}

function alumnixSendApprovalNotice($fullName, $email) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();

    if (
        empty($config["host"]) ||
        empty($config["username"]) ||
        empty($config["password"]) ||
        stripos((string) $config["username"], "YOUR_") !== false ||
        stripos((string) $config["password"], "YOUR_") !== false
    ) {
        alumnixSetMailError("SMTP username/password missing in config/mail.php.");
        return false;
    }

    $loginUrl = alumnixGetLoginUrl();
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config["host"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config["username"];
        $mail->Password   = $config["password"];
        $mail->SMTPSecure = $config["encryption"] === "ssl"
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config["port"];
        $mail->CharSet    = "UTF-8";

        $mail->setFrom($config["username"], $config["from_name"]);
        if (!empty($config["reply_to"])) {
            $mail->addReplyTo($config["reply_to"], $config["from_name"]);
        }
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your AlumniX account is approved';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 28px; color: #0f172a;'>
                <div style='max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 18px; padding: 28px; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0 0 10px; color: #f43f5e; font-weight: 700;'>AlumniX Approval</p>
                    <h2 style='margin: 0 0 14px;'>Hi " . htmlspecialchars($fullName, ENT_QUOTES, "UTF-8") . ",</h2>
                    <p style='line-height: 1.6;'>Your AlumniX account has been approved.</p>
                    <div style='background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 14px; padding: 16px; margin: 18px 0;'>
                        <p style='margin: 0 0 8px; font-size: 13px; color: #64748b;'>Login page</p>
                        <strong><a href='" . htmlspecialchars($loginUrl, ENT_QUOTES, "UTF-8") . "' style='color: #4338ca; text-decoration: none;'>" . htmlspecialchars($loginUrl, ENT_QUOTES, "UTF-8") . "</a></strong>
                        <p style='margin: 16px 0 0; font-size: 13px; color: #64748b;'>Login Email</p>
                        <strong>" . htmlspecialchars($email, ENT_QUOTES, "UTF-8") . "</strong>
                    </div>
                    <p style='line-height: 1.6;'>Use the password you chose during registration.</p>
                </div>
            </div>";
        $mail->AltBody = "Hi {$fullName}, your AlumniX account is approved. Login at {$loginUrl} with your email address and the password you set during registration.";

        if (!$mail->send()) {
            $errorMessage = $mail->ErrorInfo ?: "Email delivery failed.";
            alumnixSetMailError($errorMessage);
            alumnixLogMailError($email, 'approval_notice', $errorMessage);
            return false;
        }

        return true;
    } catch (Exception $e) {
        $errorMessage = $mail->ErrorInfo ?: $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'approval_notice', $errorMessage);
        return false;
    }
}

function alumnixSendJobApprovalNotice($fullName, $email, $jobTitle, $company) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();

    if (
        empty($email) ||
        empty($config["host"]) ||
        empty($config["username"]) ||
        empty($config["password"]) ||
        stripos((string) $config["username"], "YOUR_") !== false ||
        stripos((string) $config["password"], "YOUR_") !== false
    ) {
        alumnixSetMailError("SMTP username/password missing in config/mail.php.");
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config["host"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config["username"];
        $mail->Password   = $config["password"];
        $mail->SMTPSecure = $config["encryption"] === "ssl"
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config["port"];
        $mail->CharSet    = "UTF-8";

        $mail->setFrom($config["username"], $config["from_name"]);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your AlumniX job post is live';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 28px; color: #0f172a;'>
                <div style='max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 18px; padding: 28px; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0 0 10px; color: #f43f5e; font-weight: 700;'>Job Approved</p>
                    <h2 style='margin: 0 0 14px;'>Hi " . htmlspecialchars($fullName ?: "there", ENT_QUOTES, "UTF-8") . ",</h2>
                    <p style='line-height: 1.6;'>Your job post is approved and visible to alumni members.</p>
                    <div style='background: #fff1f2; border: 1px solid #fecdd3; border-radius: 14px; padding: 16px; margin: 18px 0;'>
                        <strong>" . htmlspecialchars($jobTitle, ENT_QUOTES, "UTF-8") . "</strong>
                        <p style='margin: 8px 0 0; color: #64748b;'>" . htmlspecialchars($company ?: "Company not specified", ENT_QUOTES, "UTF-8") . "</p>
                    </div>
                </div>
            </div>";
        $mail->AltBody = "Your AlumniX job post is approved: {$jobTitle} at {$company}.";

        if (!$mail->send()) {
            $errorMessage = $mail->ErrorInfo ?: "Email delivery failed.";
            alumnixSetMailError($errorMessage);
            alumnixLogMailError($email, 'job_approval', $errorMessage);
            return false;
        }

        return true;
    } catch (Exception $e) {
        $errorMessage = $mail->ErrorInfo ?: $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'job_approval', $errorMessage);
        return false;
    }
}

function alumnixSendRegistrationConfirmation($fullName, $email) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();

    if (
        empty($config["host"]) ||
        empty($config["username"]) ||
        empty($config["password"]) ||
        stripos((string) $config["username"], "YOUR_") !== false ||
        stripos((string) $config["password"], "YOUR_") !== false
    ) {
        alumnixSetMailError("SMTP username/password missing in config/mail.php.");
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config["host"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config["username"];
        $mail->Password   = $config["password"];
        $mail->SMTPSecure = $config["encryption"] === "ssl"
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config["port"];
        $mail->CharSet    = "UTF-8";

        $mail->setFrom($config["username"], $config["from_name"]);
        if (!empty($config["reply_to"])) {
            $mail->addReplyTo($config["reply_to"], $config["from_name"]);
        }
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Registration Confirmed - Awaiting Approval';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 28px; color: #0f172a;'>
                <div style='max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 18px; padding: 28px; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0 0 10px; color: #f43f5e; font-weight: 700;'>Registration Received</p>
                    <h2 style='margin: 0 0 14px;'>Hi " . htmlspecialchars($fullName, ENT_QUOTES, "UTF-8") . ",</h2>
                    <p style='line-height: 1.6;'>Thank you for registering with AlumniX! Your registration has been received and is pending admin approval.</p>
                    <div style='background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 14px; padding: 16px; margin: 18px 0;'>
                        <p style='margin: 0 0 8px; font-size: 13px; color: #64748b;'><strong>What happens next?</strong></p>
                        <p style='margin: 8px 0; font-size: 13px; line-height: 1.6; color: #64748b;'>1. Our admin team will review your profile<br>2. You'll receive an email with your login credentials<br>3. Use those credentials to access the AlumniX Portal</p>
                    </div>
                    <p style='line-height: 1.6; color: #64748b; font-size: 13px;'>In the meantime, if you have any questions, feel free to reach out to us.</p>
                </div>
            </div>";
        $mail->AltBody = "Hi {$fullName}, thank you for registering with AlumniX. Your registration is pending admin approval. You will receive your login credentials via email once approved.";

        if (!$mail->send()) {
            $errorMessage = $mail->ErrorInfo ?: "Email delivery failed.";
            alumnixSetMailError($errorMessage);
            alumnixLogMailError($email, 'registration_confirmation', $errorMessage);
            return false;
        }

        return true;
    } catch (Exception $e) {
        $errorMessage = $mail->ErrorInfo ?: $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'registration_confirmation', $errorMessage);
        return false;
    }
}

function alumnixSendPasswordResetEmail($fullName, $email, $resetToken) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();

    if (
        empty($config["host"]) ||
        empty($config["username"]) ||
        empty($config["password"]) ||
        stripos((string) $config["username"], "YOUR_") !== false ||
        stripos((string) $config["password"], "YOUR_") !== false
    ) {
        alumnixSetMailError("SMTP username/password missing in config/mail.php.");
        return false;
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443') {
        $scheme = 'https';
    }
    
    $resetUrl = $scheme . '://' . $host . '/alumini/reset_password.php?token=' . urlencode($resetToken) . '&email=' . urlencode($email);
    
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $config["host"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config["username"];
        $mail->Password   = $config["password"];
        $mail->SMTPSecure = $config["encryption"] === "ssl"
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config["port"];
        $mail->CharSet    = "UTF-8";

        $mail->setFrom($config["username"], $config["from_name"]);
        if (!empty($config["reply_to"])) {
            $mail->addReplyTo($config["reply_to"], $config["from_name"]);
        }
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - AlumniX';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 28px; color: #0f172a;'>
                <div style='max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 18px; padding: 28px; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0 0 10px; color: #f43f5e; font-weight: 700;'>Password Reset</p>
                    <h2 style='margin: 0 0 14px;'>Hi " . htmlspecialchars($fullName, ENT_QUOTES, "UTF-8") . ",</h2>
                    <p style='line-height: 1.6;'>We received a request to reset your AlumniX password. Click the button below to create a new password.</p>
                    <div style='text-align: center; margin: 24px 0;'>
                        <a href='" . htmlspecialchars($resetUrl, ENT_QUOTES, "UTF-8") . "' style='display: inline-block; background: #e11d48; color: #fff; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 700;'>Reset Password</a>
                    </div>
                    <p style='line-height: 1.6; color: #64748b; font-size: 13px;'>Or copy this link in your browser:<br><a href='" . htmlspecialchars($resetUrl, ENT_QUOTES, "UTF-8") . "' style='color: #4338ca; word-break: break-all;'>" . htmlspecialchars($resetUrl, ENT_QUOTES, "UTF-8") . "</a></p>
                    <div style='background: #fef2f2; border: 1px solid #fecdd3; border-radius: 12px; padding: 14px; margin: 18px 0;'>
                        <p style='margin: 0; font-size: 12px; color: #991b1b;'><strong>Security Note:</strong> This link expires in 24 hours. If you didn't request a password reset, please ignore this email.</p>
                    </div>
                </div>
            </div>";
        $mail->AltBody = "Hi {$fullName}, click this link to reset your password: {$resetUrl}. This link expires in 24 hours.";

        if (!$mail->send()) {
            $errorMessage = $mail->ErrorInfo ?: "Email delivery failed.";
            alumnixSetMailError($errorMessage);
            alumnixLogMailError($email, 'password_reset', $errorMessage);
            return false;
        }

        return true;
    } catch (Exception $e) {
        $errorMessage = $mail->ErrorInfo ?: $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'password_reset', $errorMessage);
        return false;
    }
}
