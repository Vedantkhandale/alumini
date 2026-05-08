<?php
/**
 * AlumniX Pro - Global Helper Engine
 * Handle Security, Database Fetching, and UI Utilities
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../includes/db.php";

/**
 * 🔒 Middleware: Admin Access Only
 */
function adminOnly(): void
{
    if (!isset($_SESSION["admin"])) {
        // Agar admin session nahi hai, redirect to login with error
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

/**
 * 🎭 UI: Status Badge Generator
 * Helps in creating consistent badges across the app
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
    
    return "<span style='padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; $style'>".strtoupper($status)."</span>";
}

/**
 * ⏱️ Utility: Time Ago (e.g. 2 hours ago)
 */
function timeAgo($timestamp) {
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
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
    }
}
?>