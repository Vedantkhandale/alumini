<?php
/**
 * AlumniX - Job Application Tracker & Redirector
 * This file logs the application event for the Admin and redirects the user to the external job link.
 */

session_start();

// --- 1. SECURE DATABASE CONNECTION ---
// Searching for db.php in multiple likely locations
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
    die("CRITICAL ERROR: Database configuration file 'db.php' not found. Please verify folder structure.");
}

// --- 2. VALIDATION & REDIRECTION LOGIC ---
if (isset($_GET['job_id']) && isset($_SESSION['user'])) {
    
    // Sanitize job_id (Force to Integer for security) and get Alumni ID from session
    $job_id = (int)$_GET['job_id'];
    $alumni_id = $_SESSION['user']['id'];

    // A. Prevent Duplicate Logs (Check if user already clicked this specific job)
    $check_stmt = $conn->prepare("SELECT id FROM job_applications WHERE job_id = ? AND alumni_id = ?");
    $check_stmt->bind_param("ii", $job_id, $alumni_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result && $check_result->num_rows == 0) {
        // B. Log the application for Admin reporting
        $log_stmt = $conn->prepare("INSERT INTO job_applications (job_id, alumni_id) VALUES (?, ?)");
        $log_stmt->bind_param("ii", $job_id, $alumni_id);
        $log_stmt->execute();
    }

    // C. Retrieve the destination URL (External Apply Link)
    $link_stmt = $conn->prepare("SELECT apply_link FROM jobs WHERE id = ?");
    $link_stmt->bind_param("i", $job_id);
    $link_stmt->execute();
    $link_result = $link_stmt->get_result();
    
    if ($link_result && $link_result->num_rows > 0) {
        $job_data = $link_result->fetch_assoc();
        $destination = $job_data['apply_link'];

        // D. Final Redirect
        if (!empty($destination) && $destination !== "#") {
            header("Location: " . $destination);
            exit();
        } else {
            // Friendly alert if the admin/alumni didn't provide a valid URL
            echo "<script>
                    alert('Note: This job does not have a direct application link. Please check back later.');
                    window.location.href='dashboard.php';
                  </script>";
        }
    } else {
        // Handle invalid Job ID
        echo "<script>
                alert('Error: The selected job listing could not be found.');
                window.location.href='dashboard.php';
              </script>";
    }
    exit();

} else {
    // Unauthorized access or missing ID
    header("Location: dashboard.php");
    exit();
}
?>
<?php
session_start();
include("includes/db.php");

if (isset($_GET['job_id']) && isset($_SESSION['user'])) {
    $job_id = (int)$_GET['job_id'];
    $alumni_id = $_SESSION['user']['id'];

    // Track Application
    $conn->query("INSERT INTO job_applications (job_id, alumni_id) VALUES ('$job_id', '$alumni_id')");

    // Redirect to Actual Link
    $res = $conn->query("SELECT apply_link FROM jobs WHERE id='$job_id'");
    $job = $res->fetch_assoc();
    
    header("Location: " . ($job['apply_link'] ?? 'dashboard.php'));
    exit();
}
header("Location: dashboard.php");

?>