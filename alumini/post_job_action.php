<?php
session_start();

// --- 1. DB CONNECTION (Using your flexible path logic) ---
$db_paths = ["includes/db.php", "../includes/db.php", "../../includes/db.php", "db.php"];
$connected = false;
foreach ($db_paths as $path) {
    if (file_exists($path)) {
        include($path);
        $connected = true;
        break;
    }
}

if (!$connected) {
    echo json_encode(['status' => 'error', 'message' => 'Database file not found']);
    exit();
}

// --- 2. POST LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    
    // Inputs sanitize kar rahe hain
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $location = mysqli_real_escape_string($conn, $_POST['location'] ?? 'Remote');
    $apply_link = mysqli_real_escape_string($conn, $_POST['apply_link'] ?? '#');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $alumni_id = $_SESSION['user']['id'];

    // Status 'pending' rakha hai taaki Admin approve kare (Safety ke liye)
    // Agar tujhe direct approve chahiye toh status 'approved' kar dena
    $sql = "INSERT INTO jobs (title, company, location, alumni_id, apply_link, description, status) 
            VALUES ('$title', '$company', '$location', '$alumni_id', '$apply_link', '$description', 'pending')";
    
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
}
?>
<?php
session_start();
header('Content-Type: application/json');
include("includes/db.php"); // Path check kar lena bhai

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    $alumni_id = $_SESSION['user']['id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO jobs (alumni_id, title, company, location, apply_link, description, status) 
            VALUES ('$alumni_id', '$title', '$company', '$loc', '$link', '$desc', 'pending')";
    
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
}
?>