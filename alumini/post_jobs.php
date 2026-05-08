<?php
session_start();
include("../includes/db.php");

// 1. SESSION CHECK (Security)
if(!isset($_SESSION['user'])){ 
    header("Location: ../login.php"); 
    exit(); 
}

// 2. FETCH ALUMNI DATA (Ensuring ID exists)
$email = $_SESSION['user']['email'];
$res = $conn->query("SELECT id FROM alumni WHERE email='$email'");
if($res->num_rows == 0) {
    die("Error: Alumni profile not found. Contact Admin.");
}
$user = $res->fetch_assoc();
$alumni_id = $user['id'];

$msg = "";
$status = "";

// 3. POST HANDLING
if(isset($_POST['post'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);
    $logo = mysqli_real_escape_string($conn, $_POST['logo_url']);

    // Inserting with 'pending' status for Admin moderation
    $sql = "INSERT INTO jobs (alumni_id, title, company, description, location, apply_link, logo, status) 
            VALUES ('$alumni_id', '$title', '$company', '$desc', '$loc', '$link', '$logo', 'pending')";

    if($conn->query($sql)){
        $msg = "Job submitted successfully! It will be live after Admin approval.";
        $status = "success";
    } else {
        $msg = "Database Error: " . $conn->error;
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Job | AlumniX</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --primary: #e11d48; --dark: #0f172a; --gray: #64748b; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin:0; 
        }

        .form-card { 
            background: white; 
            padding: 40px; 
            border-radius: 30px; 
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08); 
            width: 100%; 
            max-width: 550px;
            border: 1px solid #f1f5f9;
        }

        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { font-weight: 800; color: var(--dark); font-size: 28px; margin: 0; }
        .header p { color: var(--gray); font-size: 14px; margin-top: 5px; }

        label { font-size: 11px; font-weight: 800; color: var(--gray); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; display: block; }
        
        input, textarea { 
            width: 100%; padding: 14px; margin-bottom: 20px; 
            border: 1.5px solid #e2e8f0; border-radius: 12px; 
            outline: none; transition: 0.3s; font-size: 14px; background: #fbfcfd;
        }

        input:focus, textarea:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.05); }

        .btn-post { 
            background: var(--dark); color: white; border: none; width: 100%; 
            padding: 16px; border-radius: 12px; font-weight: 700; 
            cursor: pointer; transition: 0.3s; font-size: 16px; 
        }

        .btn-post:hover { background: var(--primary); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(225, 29, 72, 0.2); }

        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { text-decoration: none; color: var(--gray); font-size: 13px; font-weight: 600; transition: 0.3s; }
        .back-link a:hover { color: var(--primary); }
    </style>
</head>
<body>

<div class="form-card">
    <div class="header">
        <h2>Post an Opportunity 🚀</h2>
        <p>Help your fellow alumni find their next big role.</p>
    </div>

    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Job Title</label>
                <input type="text" name="title" placeholder="Frontend Dev" required>
            </div>
            <div>
                <label>Company</label>
                <input type="text" name="company" placeholder="Google / Amazon" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <label>Location</label>
                <input type="text" name="location" placeholder="Nagpur / Remote" required>
            </div>
            <div>
                <label>Company Logo URL</label>
                <input type="text" name="logo_url" placeholder="https://logo.com/img.png">
            </div>
        </div>

        <label>Application Link / Referral Email</label>
        <input type="text" name="apply_link" placeholder="Career Page URL or Apply Link" required>

        <label>Short Description</label>
        <textarea name="description" placeholder="Requirements, Skills, and Role summary..." rows="4" required></textarea>

        <button name="post" class="btn-post">Submit for Admin Approval</button>
    </form>

    <div class="back-link">
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<?php if($msg): ?>
<script>
    Swal.fire({
        title: '<?php echo ($status == "success") ? "Submission Received!" : "Error!"; ?>',
        text: '<?php echo $msg; ?>',
        icon: '<?php echo $status; ?>',
        confirmButtonColor: '#e11d48'
    });
</script>
<?php endif; ?>

</body>
</html>