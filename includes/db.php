<?php
$conn = new mysqli("localhost", "root", "", "alumni_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>