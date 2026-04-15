<?php
if (!function_exists("e")) {
    function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
    }
}

if (!function_exists("fetchRows")) {
    function fetchRows(mysqli $conn, string $sql): array
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
}

if (!function_exists("fetchCount")) {
    function fetchCount(mysqli $conn, string $sql): int
    {
        $result = $conn->query($sql);

        if (!($result instanceof mysqli_result)) {
            return 0;
        }

        $row = $result->fetch_row();
        $result->free();

        return (int) ($row[0] ?? 0);
    }
}
