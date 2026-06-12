<?php
// Error reporting on rakho taaki query ki galti turant pakdi jaye
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli("localhost", "root", "", "alumni_system");
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

function alumnixBootstrapSchema(mysqli $conn): void
{
    static $bootstrapped = false;
    if ($bootstrapped) {
        return;
    }

    $bootstrapped = true;

    try {
        $result = $conn->query("SHOW COLUMNS FROM alumni_users LIKE 'password'");
        $hasPassword = $result instanceof mysqli_result && $result->num_rows > 0;
        if ($result instanceof mysqli_result) {
            $result->free();
        }

        if (!$hasPassword) {
            $conn->query("ALTER TABLE alumni_users ADD COLUMN password VARCHAR(255) NULL AFTER email");
        }
    } catch (Throwable $schemaError) {
    }
}

alumnixBootstrapSchema($conn);
?>
