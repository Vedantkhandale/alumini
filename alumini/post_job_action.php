<?php
// post_job_action.php - Isse aapka original code change nahi hoga
session_start();
include('db.php'); // Aapki DB file ka path

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $alumni_id = $_SESSION['user']['id'];

    // Status 'approved' rakha hai taaki turant dikhne lage
    $sql = "INSERT INTO jobs (title, company, alumni_id, status) VALUES ('$title', '$company', '$alumni_id', 'approved')";
    
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}
?>