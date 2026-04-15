<?php
session_start();
include("../includes/db.php");

// Security Check
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['user'];
$alumni_id = $user['id'];
$msg = "";

// 1. Job Post karne ka logic
if(isset($_POST['post_job'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO jobs (alumni_id, title, company, location, apply_link, description, status) 
            VALUES ('$alumni_id', '$title', '$company', '$location', '$link', '$desc', 'pending')";
    
    if($conn->query($sql)){
        $msg = "success";
    } else {
        $msg = "error";
    }
}

// 2. Sirf is alumni ki posts fetch karna
$my_jobs = $conn->query("SELECT * FROM jobs WHERE alumni_id='$alumni_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Job | AlumniX</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #ff4d4d; --dark: #1e293b; --bg: #f8fafc; }
        body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; padding-bottom: 50px; }
        
        .header-section { background: #fff; padding: 40px 8%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .btn-post { background: var(--primary); color: #fff; padding: 12px 25px; border-radius: 12px; text-decoration: none; font-weight: 700; border: none; cursor: pointer; }
        
        .jobs-container { padding: 40px 8%; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .job-card { background: #fff; padding: 25px; border-radius: 20px; border: 1px solid #e2e8f0; position: relative; }
        .status-badge { position: absolute; top: 20px; right: 20px; font-size: 10px; padding: 5px 12px; border-radius: 20px; font-weight: 800; text-transform: uppercase; background: #f0fdf4; color: #10b981; }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); }
        .modal-content { background: #fff; width: 90%; max-width: 500px; margin: 50px auto; padding: 30px; border-radius: 24px; position: relative; }
        .input-style { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #e2e8f0; border-radius: 10px; background: #f9fafb; outline: none; }
    </style>
</head>
<body>

<div class="header-section">
    <div>
        <h1 style="margin: 0;">Manage <span>Jobs</span></h1>
        <p style="color: #64748b;">Share career opportunities with your community.</p>
    </div>
    <button class="btn-post" onclick="toggleModal()">+ Post New Job</button>
</div>

<div class="jobs-container">
    <?php if($my_jobs->num_rows > 0): ?>
        <?php while($row = $my_jobs->fetch_assoc()): ?>
            <div class="job-card">
                <span class="status-badge"><?php echo $row['status']; ?></span>
                <h3 style="margin: 0; color: var(--dark);"><?php echo $row['title']; ?></h3>
                <p style="color: var(--primary); font-weight: 700; font-size: 14px; margin: 5px 0;"><?php echo $row['company']; ?></p>
                <p style="color: #64748b; font-size: 13px;"><i class="fas fa-map-marker-alt"></i> <?php echo $row['location']; ?></p>
                <hr style="border: 0; border-top: 1px solid #f1f5f9; margin: 15px 0;">
                <p style="font-size: 13px; color: #475569;"><?php echo substr($row['description'], 0, 100); ?>...</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column: 1/-1; text-align: center; color: #64748b;">No jobs posted yet. Start by clicking the button above!</p>
    <?php endif; ?>
</div>

<div id="jobModal" class="modal">
    <div class="modal-content">
        <h2 style="margin-bottom: 20px;">Post a New <span>Job</span></h2>
        <form method="POST">
            <label style="font-size: 11px; font-weight: 800; color: #64748b;">JOB TITLE</label>
            <input type="text" name="title" class="input-style" placeholder="Software Engineer" required>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label style="font-size: 11px; font-weight: 800; color: #64748b;">COMPANY</label>
                    <input type="text" name="company" class="input-style" placeholder="Google" required>
                </div>
                <div>
                    <label style="font-size: 11px; font-weight: 800; color: #64748b;">LOCATION</label>
                    <input type="text" name="location" class="input-style" placeholder="Remote / Pune" required>
                </div>
            </div>

            <label style="font-size: 11px; font-weight: 800; color: #64748b;">APPLY LINK / EMAIL</label>
            <input type="text" name="apply_link" class="input-style" placeholder="https://careers.google.com/..." required>

            <label style="font-size: 11px; font-weight: 800; color: #64748b;">DESCRIPTION</label>
            <textarea name="description" class="input-style" rows="4" placeholder="Brief about role and requirements..." required></textarea>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" name="post_job" class="btn-post" style="flex: 1;">Publish Job</button>
                <button type="button" class="btn-post" onclick="toggleModal()" style="background: #f1f5f9; color: #64748b; flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('jobModal');
        modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
    }

    // Success Alert
    <?php if($msg == "success"): ?>
    Swal.fire({ title: 'Success!', text: 'Job posted successfully.', icon: 'success', confirmButtonColor: '#ff4d4d' });
    <?php elseif($msg == "error"): ?>
    Swal.fire({ title: 'Error!', text: 'Something went wrong.', icon: 'error' });
    <?php endif; ?>
</script>

</body>
</html>
