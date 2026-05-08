<?php
session_start();

echo "Current Directory: " . __DIR__ . "<br>";
echo "Looking for: " . realpath(__DIR__ . "/../includes/db.php");


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

// 2. Alumni ki posts fetch karna
$my_jobs = $conn->query("SELECT * FROM jobs WHERE alumni_id='$alumni_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs | AlumniX</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #e11d48; /* Premium Crimson Red */
            --dark: #0f172a;
            --accent: #f43f5e;
            --white: #ffffff;
            --border: #e2e8f0;
            --bg: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background: var(--bg); color: var(--dark); overflow-x: hidden; }

        /* Header Styling */
        .header-section {
            background: #fff;
            padding: 50px 8% 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
        }

        .header-section h1 { font-weight: 800; font-size: 32px; color: var(--dark); }
        .header-section span { color: var(--primary); }
        
        .btn-main {
            background: var(--primary);
            color: #fff;
            padding: 14px 28px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(225, 29, 72, 0.2);
        }
        .btn-main:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(225, 29, 72, 0.3); }

        /* Grid Styling */
        .jobs-container {
            padding: 40px 8%;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .job-card {
            background: #fff;
            padding: 30px;
            border-radius: 24px;
            border: 1px solid var(--border);
            position: relative;
            transition: 0.4s;
        }
        .job-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }

        .status-badge {
            position: absolute;
            top: 25px;
            right: 25px;
            font-size: 11px;
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-approved { background: #dcfce7; color: #166534; }

        .job-card h3 { font-size: 20px; font-weight: 700; margin-bottom: 8px; margin-right: 70px; }
        .company-name { color: var(--primary); font-weight: 800; font-size: 15px; display: block; margin-bottom: 12px; }
        
        .meta-info { display: flex; align-items: center; gap: 15px; color: #64748b; font-size: 14px; margin-bottom: 20px; }
        .meta-info i { color: var(--primary); }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: #fff;
            width: 95%;
            max-width: 550px;
            margin: 50px auto;
            padding: 40px;
            border-radius: 30px;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 12px; font-weight: 800; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
        
        .input-style {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            background: #f8fafc;
            font-size: 15px;
            transition: 0.3s;
            outline: none;
        }
        .input-style:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.1); }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Empty State */
        .empty-state { grid-column: 1/-1; text-align: center; padding: 100px 0; }
        .empty-state i { font-size: 60px; color: #cbd5e1; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="header-section">
    <div>
        <h1>My <span>Job Posts</span></h1>
        <p style="color: #64748b; font-weight: 500; margin-top: 5px;">Track and manage the opportunities you've shared.</p>
    </div>
    <button class="btn-main" onclick="toggleModal()">
        <i class="fas fa-plus"></i> Post New Job
    </button>
</div>

<div class="jobs-container">
    <?php if($my_jobs->num_rows > 0): ?>
        <?php while($row = $my_jobs->fetch_assoc()): ?>
            <?php 
                $status_class = ($row['status'] == 'approved') ? 'status-approved' : 'status-pending';
            ?>
            <div class="job-card">
                <span class="status-badge <?php echo $status_class; ?>">
                    <i class="fas <?php echo ($row['status'] == 'approved') ? 'fa-check-circle' : 'fa-clock'; ?>"></i>
                    <?php echo strtoupper($row['status']); ?>
                </span>
                
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <span class="company-name"><?php echo htmlspecialchars($row['company']); ?></span>
                
                <div class="meta-info">
                    <span><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                </div>

                <hr style="border: 0; border-top: 1.5px solid #f1f5f9; margin-bottom: 20px;">
                
                <p style="font-size: 14px; color: #475569; line-height: 1.6; height: 70px; overflow: hidden;">
                    <?php echo htmlspecialchars(substr($row['description'], 0, 120)); ?>...
                </p>

                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <a href="<?php echo htmlspecialchars($row['apply_link']); ?>" target="_blank" style="font-size: 13px; font-weight: 700; color: var(--primary); text-decoration: none;">
                        View Full Link <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-briefcase"></i>
            <h2 style="color: #94a3b8;">No jobs posted yet</h2>
            <p style="color: #cbd5e1;">Click the button above to post your first career opportunity.</p>
        </div>
    <?php endif; ?>
</div>

<div id="jobModal" class="modal">
    <div class="modal-content">
        <h2 style="margin-bottom: 30px; font-weight: 800;">Post <span>New Opportunity</span></h2>
        
        <form method="POST">
            <div class="input-group">
                <label>Job Title</label>
                <input type="text" name="title" class="input-style" placeholder="e.g. Senior Web Developer" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="input-group">
                    <label>Company</label>
                    <input type="text" name="company" class="input-style" placeholder="e.g. Google" required>
                </div>
                <div class="input-group">
                    <label>Location</label>
                    <input type="text" name="location" class="input-style" placeholder="e.g. Remote / Bangalore" required>
                </div>
            </div>

            <div class="input-group">
                <label>Apply Link / Referral Email</label>
                <input type="text" name="apply_link" class="input-style" placeholder="https://careers.company.com/..." required>
            </div>

            <div class="input-group">
                <label>Brief Description</label>
                <textarea name="description" class="input-style" rows="4" placeholder="Mention key skills or requirements..." required></textarea>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button type="submit" name="post_job" class="btn-main" style="flex: 1; justify-content: center;">Publish Job</button>
                <button type="button" class="btn-main" onclick="toggleModal()" style="background: #f1f5f9; color: #64748b; flex: 1; justify-content: center; box-shadow: none;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('jobModal');
        modal.style.display = (modal.style.display === 'block') ? 'none' : 'block';
    }

    // Close modal on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('jobModal');
        if (event.target == modal) { modal.style.display = "none"; }
    }

    // Success Alerts
    <?php if($msg == "success"): ?>
    Swal.fire({
        title: 'Successfully Posted!',
        text: 'Your job has been sent for admin approval.',
        icon: 'success',
        confirmButtonColor: '#e11d48',
        background: '#fff',
        iconColor: '#e11d48'
    });
    <?php elseif($msg == "error"): ?>
    Swal.fire({ title: 'Error!', text: 'Something went wrong.', icon: 'error' });
    <?php endif; ?>
</script>

</body>
</html>