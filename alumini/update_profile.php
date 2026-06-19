<?php
session_start();
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(E_ALL);

include(__DIR__ . "/../includes/db.php");

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please login again.']);
    exit;
}

$userEmail = $_SESSION['user']['email'] ?? '';
$userId = (int) ($_SESSION['user']['id'] ?? 0);

// --- SMART LAYER: Database Columns Auto-Detection ---
$columns = [];
$tableCheck = $conn->query("DESCRIBE alumni");
if ($tableCheck) {
    while ($row = $tableCheck->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
}

// Column mapping detect karo
$db_fullname = in_array('fullname', $columns) ? 'fullname' : (in_array('name', $columns) ? 'name' : '');
$db_dept = in_array('department', $columns) ? 'department' : '';
$db_grad = in_array('graduation_year', $columns) ? 'graduation_year' : (in_array('grad_year', $columns) ? 'grad_year' : '');
$db_company = in_array('current_company', $columns) ? 'current_company' : (in_array('company', $columns) ? 'company' : '');
$db_loc = in_array('location', $columns) ? 'location' : '';
$db_about = in_array('about_me', $columns) ? 'about_me' : (in_array('about', $columns) ? 'about' : '');
$db_photo = in_array('profile_photo', $columns) ? 'profile_photo' : (in_array('image', $columns) ? 'image' : '');

// Form values sanitization
$fullname = htmlspecialchars(trim($_POST['fullname'] ?? $_POST['name'] ?? ''));
$department = htmlspecialchars(trim($_POST['department'] ?? ''));
$graduation_year = htmlspecialchars(trim($_POST['graduation_year'] ?? $_POST['grad_year'] ?? ''));
$current_company = htmlspecialchars(trim($_POST['current_company'] ?? $_POST['company'] ?? ''));
$location = htmlspecialchars(trim($_POST['location'] ?? ''));
$about_me = htmlspecialchars(trim($_POST['about_me'] ?? $_POST['about'] ?? ''));

if (empty($fullname) || empty($department) || empty($graduation_year)) {
    echo json_encode(['status' => 'error', 'message' => 'Full Name, Department, and Graduation Year are mandatory fields.']);
    exit;
}

// User existence check
$currentRes = null;
if ($userId > 0) {
    $query = $conn->prepare("SELECT * FROM alumni WHERE id = ? LIMIT 1");
    $query->bind_param('i', $userId);
    $query->execute();
    $currentRes = $query->get_result()->fetch_assoc();
    $query->close();
}
if (!$currentRes && !empty($userEmail)) {
    $query = $conn->prepare("SELECT * FROM alumni WHERE email = ? LIMIT 1");
    $query->bind_param('s', $userEmail);
    $query->execute();
    $currentRes = $query->get_result()->fetch_assoc();
    $query->close();
}

$recordExists = $currentRes ? true : false;
$imageName = $currentRes[$db_photo] ?? null;

if ($recordExists) {
    $userId = (int)$currentRes['id'];
}

// Profile Photo Upload Handler
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
    $fileName = $_FILES['profile_photo']['name'];
    $fileSize = $_FILES['profile_photo']['size'];
    
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        if ($fileSize < 5000000) { 
            $newFileName = 'alx-' . ($userId > 0 ? $userId : time()) . '-' . time() . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../uploads/profiles/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                if ($recordExists && !empty($imageName) && file_exists($uploadFileDir . $imageName)) {
                    @unlink($uploadFileDir . $imageName);
                }
                $imageName = $newFileName;
            }
        }
    }
}

// Dynamic SQL Builder (Automatic alignment with database layout)
$update_fields = [];
$insert_fields = [];
$insert_placeholders = [];
$types = '';
$params = [];

if (!$recordExists) {
    $insert_fields[] = 'email'; $insert_placeholders[] = '?'; $types .= 's'; $params[] = &$userEmail;
}
if ($db_fullname) {
    if ($recordExists) { $update_fields[] = "$db_fullname = ?"; } else { $insert_fields[] = $db_fullname; $insert_placeholders[] = '?'; }
    $types .= 's'; $params[] = &$fullname;
}
if ($db_dept) {
    if ($recordExists) { $update_fields[] = "$db_dept = ?"; } else { $insert_fields[] = $db_dept; $insert_placeholders[] = '?'; }
    $types .= 's'; $params[] = &$department;
}
if ($db_grad) {
    if ($recordExists) { $update_fields[] = "$db_grad = ?"; } else { $insert_fields[] = $db_grad; $insert_placeholders[] = '?'; }
    $types .= 's'; $params[] = &$graduation_year;
}
if ($db_company) {
    if ($recordExists) { $update_fields[] = "$db_company = ?"; } else { $insert_fields[] = $db_company; $insert_placeholders[] = '?'; }
    $types .= 's'; $params[] = &$current_company;
}
if ($db_loc) {
    if ($recordExists) { $update_fields[] = "$db_loc = ?"; } else { $insert_fields[] = $db_loc; $insert_placeholders[] = '?'; }
    $types .= 's'; $params[] = &$location;
}
if ($db_about) {
    if ($recordExists) { $update_fields[] = "$db_about = ?"; } else { $insert_fields[] = $db_about; $insert_placeholders[] = '?'; }
    $types .= 's'; $params[] = &$about_me;
}
if ($db_photo) {
    if ($recordExists) { $update_fields[] = "$db_photo = ?"; } else { $insert_fields[] = $db_photo; $insert_placeholders[] = '?'; }
    $types .= 's'; $params[] = &$imageName;
}

if ($recordExists) {
    $sql = "UPDATE alumni SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $types .= 'i'; $params[] = &$userId;
} else {
    $sql = "INSERT INTO alumni (" . implode(", ", $insert_fields) . ") VALUES (" . implode(", ", $insert_placeholders) . ")";
}

$stmt = $conn->prepare($sql);
if ($stmt) {
    $bind_names = array_merge([$types], $params);
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
    
    if ($stmt->execute()) {
        if (!$recordExists) { $_SESSION['user']['id'] = $conn->insert_id; }
        
        // Sync Session Data Live
        $_SESSION['user']['full_name'] = $fullname;
        $_SESSION['user']['department'] = $department;
        $_SESSION['user']['grad_year'] = $graduation_year;
        
        $newImageUrl = !empty($imageName) ? "../uploads/profiles/" . $imageName : "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background=e11d48&color=fff&bold=true&size=240";

        echo json_encode([
            'status' => 'success',
            'message' => 'Profile live synced successfully!',
            'user' => [
                'fullname' => $fullname,
                'department' => $department,
                'graduation_year' => $graduation_year,
                'current_company' => $current_company,
                'location' => $location,
                'about_me' => $about_me,
                'image_url' => $newImageUrl
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Query Execution Failed: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'SQL Preparation Failure: ' . $conn->error]);
}
?>