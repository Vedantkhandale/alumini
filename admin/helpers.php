<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../includes/db.php";

function adminOnly(): void
{
    if (!isset($_SESSION["admin"])) {
        header("Location: admin_login.php");
        exit();
    }
}

function adminE($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

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
