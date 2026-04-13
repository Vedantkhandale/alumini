<?php
session_start();
if(file_exists("../includes/db.php")){ include("../includes/db.php"); } else { include("includes/db.php"); }

if(!isset($_SESSION['user'])){ header("Location: ../login.php"); exit(); }
$user = $_SESSION['user'];
$alumni_id = $user['id'] ?? 0;
$msg = "";

if(isset($_POST['post_job'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO jobs (alumni_id, title, company, location, apply_link, description, status) 
            VALUES ('$alumni_id', '$title', '$company', '$location', '$link', '$desc', 'pending')";
    if($conn->query($sql)) { $msg = "pending"; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Dashboard | AlumniX</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #ff4d4d;
            --primary-glow: rgba(255, 77, 77, 0.15);
            --dark: #0f172a;
            --slate: #64748b;
            --bg: #f4f7ff;
            --glass: rgba(255, 255, 255, 0.8);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: var(--dark); overflow-x: hidden; }

        /* Modern Mesh Background Effect */
        body::before {
            content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(255, 77, 77, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(15, 23, 42, 0.05) 0%, transparent 40%);
            z-index: -1;
        }

        .nav {
            padding: 1rem 8%; background: var(--glass); backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.3);
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 30px rgba(0,0,0,0.03);
        }
        .logo { font-size: 24px; font-weight: 800; color: var(--dark); text-decoration: none; letter-spacing: -1px; }
        .logo span { color: var(--primary); }

        .container { padding: 40px 8%; max-width: 1440px; margin: auto; }

        /* Sexy Floating Header */
        .header-card {
            background: #0f172a;
            padding: 60px; border-radius: 40px; color: white;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.3);
            margin-bottom: 50px; position: relative; overflow: hidden;
        }
        .header-card::after {
            content: ""; position: absolute; right: -50px; top: -50px; width: 200px; height: 200px;
            background: var(--primary); filter: blur(100px); opacity: 0.2;
        }

        .header-content h1 { font-size: 36px; font-weight: 800; margin-bottom: 10px; background: linear-gradient(to right, #fff, #94a3b8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .btn-post {
            background: var(--primary); color: white; padding: 18px 32px;
            border-radius: 20px; border: none; font-weight: 700; cursor: pointer;
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex; align-items: center; gap: 12px; box-shadow: 0 15px 30px var(--primary-glow);
        }
        .btn-post:hover { transform: scale(1.05) translateY(-5px); box-shadow: 0 20px 40px var(--primary-glow); }

        /* The "Sexy" Job Grid */
        .job-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        
        .job-card {
            background: white; padding: 32px; border-radius: 35px;
            border: 1px solid rgba(0,0,0,0.02); transition: 0.4s;
            position: relative; display: flex; flex-direction: column;
        }
        .job-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 40px 80px -20px rgba(0,0,0,0.1);
            border-color: var(--primary-glow);
        }

        .company-logo-ui {
            width: 55px; height: 55px; background: #f8fafc; border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: var(--primary); margin-bottom: 20px;
            border: 1px solid #f1f5f9; font-size: 22px;
        }

        .job-title { font-size: 22px; font-weight: 800; color: var(--dark); margin-bottom: 8px; letter-spacing: -0.5px; }
        .comp-name { font-weight: 700; font-size: 14px; color: var(--slate); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; }

        .job-badges { display: flex; gap: 10px; margin-bottom: 25px; }
        .badge {
            padding: 6px 14px; border-radius: 12px; font-size: 12px; font-weight: 700;
            background: #f1f5f9; color: var(--slate); display: flex; align-items: center; gap: 6px;
        }
        .badge-loc { background: #fff1f1; color: var(--primary); }

        .job-desc { color: var(--slate); font-size: 14px; line-height: 1.6; margin-bottom: 30px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        .apply-btn {
            background: #0f172a; color: white; padding: 16px;
            text-align: center; border-radius: 20px; text-decoration: none;
            font-weight: 800; font-size: 14px; transition: 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .job-card:hover .apply-btn { background: var(--primary); box-shadow: 0 10px 25px var(--primary-glow); }

        /* Modal Ultra Sexy */
        .modal {
            display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(15px); z-index: 2000; align-items: center; justify-content: center;
        }
        .modal-body {
            background: white; padding: 45px; border-radius: 40px; width: 90%; max-width: 550px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.2); animation: slideUp 0.5s ease;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 12px; font-weight: 800; color: var(--slate); margin-bottom: 8px; letter-spacing: 0.5px; }
        .input-group input, .input-group textarea {
            width: 100%; padding: 16px; border-radius: 18px; border: 2px solid #f1f5f9;
            background: #f8fafc; font-size: 14px; font-weight: 600; transition: 0.3s;
        }
        .input-group input:focus { border-color: var(--primary); outline: none; background: white; box-shadow: 0 0 0 5px var(--primary-glow); }
    </style>
</head>
<body>

<nav class="nav">
    <a href="#" class="logo">Alumni<span>X</span></a>
    <div style="display: flex; gap: 20px; align-items: center;">
        <div style="text-align: right; line-height: 1;">
            <p style="font-weight: 800; font-size: 14px; color: var(--dark);"><?php echo htmlspecialchars($user['full_name']); ?></p>
            <span style="font-size: 11px; font-weight: 700; color: var(--primary);">ACTIVE USER</span>
        </div>
        <a href="../logout.php" style="background: #fff; height: 45px; width: 45px; border-radius: 15px; display: flex; align-items: center; justify-content: center; border: 1px solid #eee; color: var(--slate); transition: 0.3s;"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</nav>

<div class="container">
    <div class="header-card">
        <div class="header-content">
            <h1>Welcome Back, <?php echo htmlspecialchars(explode(' ', $user['full_name'])[0]); ?>!</h1>
            <p style="opacity: 0.6; font-weight: 500;">Your hub for growth, networking, and opportunities.</p>
        </div>
        <button class="btn-post" onclick="toggleModal()"><i class="fas fa-bolt"></i> Post New Job</button>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px;">
        <h2 style="font-size: 28px; font-weight: 800; letter-spacing: -1px;">Open Roles</h2>
        <div style="background: white; padding: 10px 20px; border-radius: 100px; font-size: 13px; font-weight: 700; color: var(--slate); border: 1px solid #eee;">
            <i class="fas fa-filter"></i> All Vacancies
        </div>
    </div>

    <div class="job-grid">
        <?php
        $jobs = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");
        if($jobs && $jobs->num_rows > 0):
            while($job = $jobs->fetch_assoc()):
                $comp = $job['company'] ?? 'Startup';
                $char = strtoupper(substr($comp, 0, 1));
        ?>
            <div class="job-card">
                <div class="company-logo-ui"><?php echo $char; ?></div>
                <h3 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h3>
                <div class="comp-name"><?php echo htmlspecialchars($comp); ?></div>
                
                <div class="job-badges">
                    <div class="badge badge-loc"><i class="fas fa-location-arrow"></i> <?php echo htmlspecialchars($job['location'] ?? 'Remote'); ?></div>
                    <div class="badge"><i class="far fa-calendar-alt"></i> <?php echo date('M d', strtotime($job['posted_at'] ?? 'now')); ?></div>
                </div>

                <p class="job-desc"><?php echo htmlspecialchars($job['description']); ?></p>

                <a href="<?php echo htmlspecialchars($job['apply_link'] ?? '#'); ?>" target="_blank" class="apply-btn">
                    Quick Apply <i class="fas fa-external-link-alt" style="font-size: 12px;"></i>
                </a>
            </div>
        <?php endwhile; else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; background: white; border-radius: 40px; border: 2px dashed #eee;">
                <i class="fas fa-briefcase" style="font-size: 50px; color: #eee; margin-bottom: 20px;"></i>
                <p style="color: var(--slate); font-weight: 700;">No opportunities live yet. Be the first to post!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="jobModal" class="modal">
    <div class="modal-body">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="font-weight: 800; font-size: 24px;">Post a <span style="color: var(--primary);">Job</span></h2>
            <i class="fas fa-times-circle" onclick="toggleModal()" style="cursor: pointer; color: var(--slate); font-size: 24px;"></i>
        </div>
        <form method="POST">
            <div class="input-group">
                <label>JOB TITLE</label>
                <input type="text" name="title" placeholder="e.g. Senior Product Designer" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="input-group">
                    <label>COMPANY</label>
                    <input type="text" name="company" placeholder="e.g. Google" required>
                </div>
                <div class="input-group">
                    <label>LOCATION</label>
                    <input type="text" name="location" placeholder="Remote / Nagpur" required>
                </div>
            </div>
            <div class="input-group">
                <label>APPLICATION LINK</label>
                <input type="url" name="apply_link" placeholder="https://careers.company.com/..." required>
            </div>
            <div class="input-group">
                <label>DESCRIPTION</label>
                <textarea name="description" rows="4" placeholder="Briefly describe the role..."></textarea>
            </div>
            <button type="submit" name="post_job" class="btn-post" style="width: 100%; justify-content: center;">Publish Opportunity</button>
        </form>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('jobModal');
        modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
    }

    <?php if($msg == "pending"): ?>
    Swal.fire({
        title: 'Successfully Sent!', text: 'Your post is in the review queue.', icon: 'success',
        confirmButtonColor: '#ff4d4d', borderRadius: '30px', background: '#fff'
    });
    <?php endif; ?>
</script>

</body>
</html>