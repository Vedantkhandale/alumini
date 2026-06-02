<?php

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

function alumnixMailFromAddress(): string
{
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'alumnix.local';
    $host = preg_replace('/:\d+$/', '', $host);

    if (!preg_match('/^[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $host)) {
        $host = 'alumnix.local';
    }

    return 'noreply@' . $host;
}

function alumnixSendHtmlMail(string $to, string $subject, string $html): bool
{
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type:text/html;charset=UTF-8';
    $headers[] = 'From: AlumniX <' . alumnixMailFromAddress() . '>';

    return @mail($to, $subject, $html, implode("\r\n", $headers));
}

function alumnixGeneratePassword(int $length = 10): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#$%';
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

    $html = "
    <html>
        <body style=\"margin:0;padding:24px;background:#f6f7fb;font-family:Arial,sans-serif;color:#0f172a;\">
            <table role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
                <tr>
                    <td align=\"center\">
                        <table role=\"presentation\" width=\"620\" cellspacing=\"0\" cellpadding=\"0\" style=\"max-width:620px;background:#ffffff;border-radius:24px;overflow:hidden;border:1px solid #e2e8f0;\">
                            <tr>
                                <td style=\"padding:32px;background:linear-gradient(135deg,#0f172a,#1e293b);color:#ffffff;\">
                                    <h1 style=\"margin:0;font-size:28px;\">AlumniX Registration Received</h1>
                                    <p style=\"margin:12px 0 0;opacity:0.9;\">Your request is safely in the review queue.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style=\"padding:32px;\">
                                    <p style=\"margin:0 0 14px;font-size:16px;\">Hi {$safeName},</p>
                                    <p style=\"margin:0 0 14px;line-height:1.7;color:#475569;\">Your alumni registration has been received and is currently pending admin approval.</p>
                                    <p style=\"margin:0 0 14px;line-height:1.7;color:#475569;\">Once approved, your login ID and generated password will be sent to this email address.</p>
                                    <p style=\"margin:0 0 24px;line-height:1.7;color:#475569;\">Meanwhile, you can explore the public site here: <a href=\"{$safeHomeLink}\" style=\"color:#ef4444;font-weight:700;\">{$safeHomeLink}</a></p>
                                    <p style=\"margin:0;color:#0f172a;font-weight:700;\">AlumniX Team</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
    </html>";

    return alumnixSendHtmlMail($email, 'AlumniX Registration Received - Pending Approval', $html);
}

function alumnixSendApprovalCredentials(string $fullName, string $email, string $plainPassword): bool
{
    $loginLink = alumnixBaseUrl() . '/login.php';
    $safeName = alumnixEscape($fullName);
    $safeEmail = alumnixEscape($email);
    $safePassword = alumnixEscape($plainPassword);
    $safeLoginLink = alumnixEscape($loginLink);

    $html = "
    <html>
        <body style=\"margin:0;padding:24px;background:#f6f7fb;font-family:Arial,sans-serif;color:#0f172a;\">
            <table role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
                <tr>
                    <td align=\"center\">
                        <table role=\"presentation\" width=\"640\" cellspacing=\"0\" cellpadding=\"0\" style=\"max-width:640px;background:#ffffff;border-radius:24px;overflow:hidden;border:1px solid #e2e8f0;\">
                            <tr>
                                <td style=\"padding:32px;background:linear-gradient(135deg,#ff4d4d,#ff8b65);color:#ffffff;\">
                                    <h1 style=\"margin:0;font-size:28px;\">Your AlumniX Account Is Approved</h1>
                                    <p style=\"margin:12px 0 0;opacity:0.95;\">Your login credentials are now ready.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style=\"padding:32px;\">
                                    <p style=\"margin:0 0 14px;font-size:16px;\">Hi {$safeName},</p>
                                    <p style=\"margin:0 0 18px;line-height:1.7;color:#475569;\">Your alumni membership has been approved. Use the details below to access your account.</p>
                                    <table role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" style=\"border-collapse:separate;border-spacing:0 12px;\">
                                        <tr>
                                            <td style=\"padding:16px 18px;background:#fff5f5;border:1px solid #fecaca;border-radius:18px;\">
                                                <div style=\"font-size:12px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#b91c1c;\">Login ID</div>
                                                <div style=\"margin-top:6px;font-size:16px;font-weight:700;color:#0f172a;\">{$safeEmail}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style=\"padding:16px 18px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:18px;\">
                                                <div style=\"font-size:12px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;color:#475569;\">Generated Password</div>
                                                <div style=\"margin-top:6px;font-size:18px;font-weight:800;color:#0f172a;\">{$safePassword}</div>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style=\"margin:0 0 18px;line-height:1.7;color:#475569;\">Login here: <a href=\"{$safeLoginLink}\" style=\"color:#ef4444;font-weight:700;\">{$safeLoginLink}</a></p>
                                    <p style=\"margin:0 0 10px;line-height:1.7;color:#475569;\">You can use the forgot password flow anytime if you want to reset it later.</p>
                                    <p style=\"margin:0;color:#0f172a;font-weight:700;\">AlumniX Team</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
    </html>";

    return alumnixSendHtmlMail($email, 'AlumniX Login Credentials', $html);
}

function alumnixApproveUser(mysqli $conn, int $userId): array
{
    $stmt = $conn->prepare("SELECT id, full_name, email, role, status FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) {
        return ['ok' => false, 'message' => 'Unable to prepare approval query.'];
    }

    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$user) {
        return ['ok' => false, 'message' => 'Member not found.'];
    }

    $status = strtolower(trim((string) ($user['status'] ?? 'pending')));
    if (in_array($status, ['approved', 'active'], true)) {
        return ['ok' => false, 'message' => 'Member is already approved.'];
    }

    $plainPassword = alumnixGeneratePassword();
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $update = $conn->prepare("UPDATE users SET status = 'approved', password = ? WHERE id = ?");
    if (!$update) {
        return ['ok' => false, 'message' => 'Unable to update member status.'];
    }

    $update->bind_param('si', $hashedPassword, $userId);
    $saved = $update->execute();
    $update->close();

    if (!$saved) {
        return ['ok' => false, 'message' => 'Member approval could not be saved.'];
    }

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
