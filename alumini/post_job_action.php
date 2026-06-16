<?php
session_start();

// include DB connection
$db_paths = ["includes/db.php", "../includes/db.php", "../../includes/db.php", "db.php"];
$connected = false;
foreach ($db_paths as $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        include(__DIR__ . '/' . $path);
        $connected = true;
        break;
    }
}

header('Content-Type: application/json');

if (!$connected) {
    echo json_encode(['status' => 'error', 'message' => 'Database file not found']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
    $alumni_id = (int) $_SESSION['user']['id'];
    $title = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
    $company = mysqli_real_escape_string($conn, $_POST['company'] ?? '');
    $location = mysqli_real_escape_string($conn, $_POST['location'] ?? 'Remote');
    $apply_link = mysqli_real_escape_string($conn, $_POST['apply_link'] ?? '#');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

    if (!$title || !$company || !$apply_link) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields missing.']);
        exit();
    }

    $sql = "INSERT INTO jobs (alumni_id, title, company, location, apply_link, description, status) 
            VALUES ('$alumni_id', '$title', '$company', '$location', '$apply_link', '$description', 'pending')";

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
?>