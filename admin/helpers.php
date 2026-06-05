<?php
/**
 * AlumniX Pro - Global Helper Engine
 * Handle Security, Database Fetching, and UI Utilities
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/account_mail.php";

/**
 * 🔒 Middleware: Admin Access Only
 */
function adminOnly(): void
{
    if (!isset($_SESSION["admin"])) {
        header("Location: admin_login.php?error=unauthorized");
        exit();
    }
}

/**
 * 🛡️ Security: XSS Protection
 */
function adminE($value): string
{
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
}

/**
 * 📊 Analytics: Count Records
 */
function adminCount(mysqli $conn, string $sql): int
{
    $result = $conn->query($sql);
    if (!($result instanceof mysqli_result)) {
        return 0;
    }
    $row = $result->fetch_row();
    $result->free();
    return (int) ($row[0] ?? 0);
}

/**
 * 🗂️ Data: Fetch Multiple Rows as Array
 */
function adminRows(mysqli $conn, string $sql): array
{
    $rows = [];
    $result = $conn->query($sql);
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }
    return $rows;
}

function adminSetFlash(string $type, string $message, array $meta = []): void
{
    $_SESSION["admin_flash"] = array_merge([
        "type" => $type,
        "message" => $message,
    ], $meta);
}

function adminPullFlash(): ?array
{
    if (!isset($_SESSION["admin_flash"])) {
        return null;
    }

    $flash = $_SESSION["admin_flash"];
    unset($_SESSION["admin_flash"]);

    return is_array($flash) ? $flash : null;
}

/**
 * 🎭 UI: Status Badge Generator
 */
function getStatusBadge(string $status): string
{
    $status = strtolower($status);
    $styles = [
        'approved' => 'background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;',
        'active'   => 'background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;',
        'pending'  => 'background: #fef3c7; color: #b45309; border: 1px solid #fde68a;',
        'rejected' => 'background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;',
    ];

    $style = $styles[$status] ?? 'background: #f1f5f9; color: #475569;';

    return "<span style='padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; $style'>" . strtoupper($status) . "</span>";
}

/**
 * ⏱️ Utility: Time Ago
 */
function timeAgo($timestamp)
{
    $time = time() - strtotime($timestamp);
    if ($time < 1) return 'just now';
    $tokens = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    ];
    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
}

function alumnixApproveUserEngine($conn, $memberId) {
    $stmt = $conn->prepare("SELECT id, full_name, email, status FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) return ['ok' => false, 'message' => 'Query error.'];

    $stmt->bind_param('i', $memberId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$user) return ['ok' => false, 'message' => 'Member not found.'];

    $status = strtolower(trim((string) $user['status']));
    if (in_array($status, array('approved', 'active'))) {
        return ['ok' => false, 'message' => 'Already approved.'];
    }

    $plainPassword = substr(str_shuffle("23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz"), 0, 10);
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    $update = $conn->prepare("UPDATE users SET status = 'approved', password = ? WHERE id = ?");
    $update->bind_param('si', $hashedPassword, $memberId);
    $saved = $update->execute();
    $update->close();

    if (!$saved) {
        $conn->rollback();
        return ['ok' => false, 'message' => 'Database update failed.'];
    }

    $mailSent = false;
    if (!empty($user['email'])) {
        $mailSent = alumnixSendApprovalCredentials($user['full_name'], $user['email'], $plainPassword);
    }

    if (!$mailSent) {
        $conn->rollback();
        return [
            'ok' => false,
            'mail_sent' => false,
            'message' => 'Approval stopped: automatic email failed. ' . alumnixLastMailError(),
            'name' => $user['full_name'],
            'email' => $user['email']
        ];
    }

    $conn->commit();

    return [
        'ok' => true,
        'mail_sent' => true,
        'message' => 'Approved and credentials emailed automatically.',
        'name' => $user['full_name'],
        'email' => $user['email']
    ];
}
