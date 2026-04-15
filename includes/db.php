<?php
$conn = new mysqli("localhost", "root", "", "alumni_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
