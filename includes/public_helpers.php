<?php
/**
 * AlumniX Core Utility Helpers
 * High performance, secure, and clean.
 */

if (!function_exists("e")) {
    /**
     * Escape HTML for secure output.
     */
    function e($value): string
    {
        return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
    }
}

if (!function_exists("fetchRows")) {
    /**
     * Fetch multiple rows securely. 
     * Supports optional params for prepared statements.
     */
    function fetchRows(mysqli $conn, string $sql, array $params = []): array
    {
        $rows = [];
        
        // Using Prepared Statements for better security
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("SQL Prepare Error: " . $conn->error);
            return [];
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // Assuming string as default
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result instanceof mysqli_result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }

        $stmt->close();
        return $rows;
    }
}

if (!function_exists("fetchCount")) {
    /**
     * Returns a single integer count result.
     */
    function fetchCount(mysqli $conn, string $sql, array $params = []): int
    {
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) return 0;

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if (!($result instanceof mysqli_result)) {
            $stmt->close();
            return 0;
        }

        $row = $result->fetch_row();
        $result->free();
        $stmt->close();

        return (int) ($row[0] ?? 0);
    }
}

/**
 * Sexy Addition: Time Ago Helper
 * Taaki Jobs/Events mein "2 days ago" jaisa feel aaye.
 */
function timeAgo($timestamp) {
    if (!$timestamp) return "N/A";
    $time = is_numeric($timestamp) ? $timestamp : strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) return "Just now";
    $intervals = [
        31536000 => 'year',
        2592000  => 'month',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute'
    ];
    
    foreach ($intervals as $secs => $label) {
        if ($diff >= $secs) {
            $count = floor($diff / $secs);
            return $count . " " . $label . ($count > 1 ? "s" : "") . " ago";
        }
    }
}