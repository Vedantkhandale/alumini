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
        "host"       => getenv("ALUMNIX_SMTP_HOST") ?: ($fileConfig["host"] ?? "smtp.gmail.com"),
        "username"   => getenv("ALUMNIX_SMTP_USER") ?: ($fileConfig["username"] ?? ""),
        "password"   => getenv("ALUMNIX_SMTP_PASS") ?: ($fileConfig["password"] ?? ""),
        "port"       => (int) (getenv("ALUMNIX_SMTP_PORT") ?: ($fileConfig["port"] ?? 587)),
        "encryption" => strtolower((string) (getenv("ALUMNIX_SMTP_ENCRYPTION") ?: ($fileConfig["encryption"] ?? "tls"))),
        "from_name"  => getenv("ALUMNIX_MAIL_FROM_NAME") ?: ($fileConfig["from_name"] ?? "AlumniX Portal"),
        "reply_to"   => getenv("ALUMNIX_MAIL_REPLY_TO") ?: ($fileConfig["reply_to"] ?? ""),
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
        "email"      => $email,
        "context"    => $context,
        "error"      => $message,
    ], JSON_UNESCAPED_SLASHES);
    if ($line !== false) {
        file_put_contents($errorDir . "/mail_errors.log", $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function alumnixGetBaseUrl(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = 'http';
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    } elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443') {
        $scheme = 'https';
    }
    return $scheme . '://' . $host . '/alumni';
}

/**
 * Helper to initialize PHPMailer with standard configurations
 */
function alumnixMailerFactory(array $config): PHPMailer
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $config["host"];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config["username"];
    $mail->Password   = $config["password"];
    $mail->Port       = $config["port"];
    $mail->CharSet    = "UTF-8";

    switch ($config["encryption"]) {
        case 'ssl':
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            break;
        case 'tls':
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            break;
        default:
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
            break;
    }

    $mail->setFrom($config["username"], $config["from_name"]);
    if (!empty($config["reply_to"])) {
        $mail->addReplyTo($config["reply_to"], $config["from_name"]);
    }

    return $mail;
}

function alumnixValidateConfig(array $config): bool
{
    if (
        empty($config["host"]) ||
        empty($config["username"]) ||
        empty($config["password"]) ||
        stripos((string) $config["username"], "YOUR_") !== false ||
        stripos((string) $config["password"], "YOUR_") !== false
    ) {
        alumnixSetMailError("SMTP credentials missing or unconfigured in configuration rules.");
        return false;
    }
    return true;
}

function alumnixSendApprovalCredentials($fullName, $email, $plainPassword) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();
    if (!alumnixValidateConfig($config)) return false;

    $loginUrl = alumnixGetBaseUrl() . '/login.php'; 

    try {
        $mail = alumnixMailerFactory($config);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your AlumniX account is approved';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 28px; color: #0f172a;'>
                <div style='max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 18px; padding: 28px; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0 0 10px; color: #f43f5e; font-weight: 700;'>AlumniX Approval</p>
                    <h2 style='margin: 0 0 14px;'>Hi " . htmlspecialchars($fullName, ENT_QUOTES, "UTF-8") . ",</h2>
                    <p style='line-height: 1.6;'>Your AlumniX account has been approved. You can now login using the details below.</p>
                    
                    <div style='background: #fff1f2; border: 1px solid #fecdd3; border-radius: 14px; padding: 16px; margin: 18px 0;'>
                        <p style='margin: 0 0 4px; font-size: 13px; color: #64748b;'>Login Page URL</p>
                        <strong><a href='" . htmlspecialchars($loginUrl, ENT_QUOTES, "UTF-8") . "' style='color: #e11d48; text-decoration: none;'>" . htmlspecialchars($loginUrl, ENT_QUOTES, "UTF-8") . "</a></strong>
                        
                        <p style='margin: 16px 0 4px; font-size: 13px; color: #64748b;'>Login Email</p>
                        <strong>" . htmlspecialchars($email, ENT_QUOTES, "UTF-8") . "</strong>
                        
                        <p style='margin: 16px 0 4px; font-size: 13px; color: #64748b;'>Temporary Password</p>
                        <strong style='font-size: 20px; letter-spacing: 1px; color: #0f172a;'>" . htmlspecialchars($plainPassword, ENT_QUOTES, "UTF-8") . "</strong>
                    </div>
                    <p style='line-height: 1.6;'>Please change this password immediately after your first login for security purposes.</p>
                </div>
            </div>";
        $mail->AltBody = "Hi {$fullName}, your AlumniX account is approved. Login at {$loginUrl}. Email: {$email}. Temporary password: {$plainPassword}. Please change it after first login.";

        return $mail->send();
    } catch (Exception $e) {
        $errorMessage = isset($mail) ? ($mail->ErrorInfo ?: $e->getMessage()) : $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'approval_credentials', $errorMessage);
        return false;
    }
}

function alumnixSendApprovalNotice($fullName, $email) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();
    if (!alumnixValidateConfig($config)) return false;

    $loginUrl = alumnixGetBaseUrl() . '/login.php';

    try {
        $mail = alumnixMailerFactory($config);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your AlumniX account is approved';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; background: #f8fafc; padding: 28px; color: #0f172a;'>
                <div style='max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 18px; padding: 28px; border: 1px solid #e2e8f0;'>
                    <p style='margin: 0 0 10px; color: #f43f5e; font-weight: 700;'>AlumniX Approval</p>
                    <h2 style='margin: 0 0 14px;'>Hi " . htmlspecialchars($fullName, ENT_QUOTES, "UTF-8") . ",</h2>
                    <p style='line-height: 1.6;'>Your AlumniX account has been approved. You can now log in using your credentials.</p>
                    
                    <div style='background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 14px; padding: 16px; margin: 18px 0;'>
                        <p style='margin: 0 0 4px; font-size: 13px; color: #64748b;'>Login Page URL</p>
                        <strong><a href='" . htmlspecialchars($loginUrl, ENT_QUOTES, "UTF-8") . "' style='color: #4338ca; text-decoration: none;'>" . htmlspecialchars($loginUrl, ENT_QUOTES, "UTF-8") . "</a></strong>
                        
                        <p style='margin: 16px 0 4px; font-size: 13px; color: #64748b;'>Login Email</p>
                        <strong>" . htmlspecialchars($email, ENT_QUOTES, "UTF-8") . "</strong>
                    </div>
                    <p style='line-height: 1.6;'>Please use the original password you chose during the registration process.</p>
                </div>
            </div>";
        $mail->AltBody = "Hi {$fullName}, your AlumniX account is approved. Login at {$loginUrl} with your email address and the password you set during registration.";

        return $mail->send();
    } catch (Exception $e) {
        $errorMessage = isset($mail) ? ($mail->ErrorInfo ?: $e->getMessage()) : $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'approval_notice', $errorMessage);
        return false;
    }
}

function alumnixSendJobApprovalNotice($fullName, $email, $jobTitle, $company) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();
    if (empty($email) || !alumnixValidateConfig($config)) return false;

    try {
        $mail = alumnixMailerFactory($config);
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

        return $mail->send();
    } catch (Exception $e) {
        $errorMessage = isset($mail) ? ($mail->ErrorInfo ?: $e->getMessage()) : $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'job_approval', $errorMessage);
        return false;
    }
}

function alumnixSendRegistrationConfirmation($fullName, $email) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();
    if (!alumnixValidateConfig($config)) return false;

    try {
        $mail = alumnixMailerFactory($config);
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

        return $mail->send();
    } catch (Exception $e) {
        $errorMessage = isset($mail) ? ($mail->ErrorInfo ?: $e->getMessage()) : $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'registration_confirmation', $errorMessage);
        return false;
    }
}

function alumnixSendPasswordResetEmail($fullName, $email, $resetToken) {
    alumnixSetMailError("");
    $config = alumnixMailConfig();
    if (!alumnixValidateConfig($config)) return false;
    
    $resetUrl = alumnixGetBaseUrl() . '/reset_password.php?token=' . urlencode($resetToken) . '&email=' . urlencode($email);
    
    try {
        $mail = alumnixMailerFactory($config);
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

        return $mail->send();
    } catch (Exception $e) {
        $errorMessage = isset($mail) ? ($mail->ErrorInfo ?: $e->getMessage()) : $e->getMessage();
        alumnixSetMailError($errorMessage);
        alumnixLogMailError($email, 'password_reset', $errorMessage);
        return false;
    }
}