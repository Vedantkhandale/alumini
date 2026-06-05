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
            alumnixSetMailError($mail->ErrorInfo ?: "Email delivery failed.");
            return false;
        }

        return true;
    } catch (Exception $e) {
        alumnixSetMailError($mail->ErrorInfo ?: $e->getMessage());
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
            alumnixSetMailError($mail->ErrorInfo ?: "Email delivery failed.");
            return false;
        }

        return true;
    } catch (Exception $e) {
        alumnixSetMailError($mail->ErrorInfo ?: $e->getMessage());
        return false;
    }
}
