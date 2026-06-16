<?php
session_start();
include(__DIR__ . "/../includes/db.php");

// Security Check
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION['user'];
$alumni_id = (int) $user['id'];
$msg = "";

// Handle job post submission using prepared statements
if(isset($_POST['post_job'])){
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $link = $_POST['apply_link'] ?? '';
    $desc = $_POST['description'] ?? '';

    $stmt = $conn->prepare("INSERT INTO jobs (alumni_id, title, company, location, apply_link, description, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    if($stmt){
        $stmt->bind_param('isssss', $alumni_id, $title, $company, $location, $link, $desc);
        if($stmt->execute()){
            $msg = 'success';
        } else {
            $msg = 'error';
        }
        $stmt->close();
    } else {
        $msg = 'error';
    }
}

// Fetch alumni's job posts
$stmt = $conn->prepare("SELECT id, title, company, location, apply_link, description, status, created_at FROM jobs WHERE alumni_id = ? ORDER BY id DESC");
$my_jobs = null;
if($stmt){
    $stmt->bind_param('i', $alumni_id);
    $stmt->execute();
    $my_jobs = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs | Alumni</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --primary: #e11d48; --dark: #0f172a; --border: #e2e8f0; --bg: #f8fafc; }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans',sans-serif; }
        body{ background:var(--bg); color:var(--dark); }
        .header-section{ background:#fff; padding:28px 6%; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); }
        .btn-main{ background:var(--primary); color:#fff; padding:10px 18px; border-radius:10px; border:none; cursor:pointer; font-weight:700; }
        .jobs-container{ padding:24px 6%; display:grid; grid-template-columns: repeat(auto-fill,minmax(320px,1fr)); gap:18px; }
        .job-card{ background:#fff; padding:18px; border-radius:12px; border:1px solid var(--border); position:relative; }
        .status-badge{ position:absolute; top:14px; right:14px; padding:6px 10px; border-radius:999px; font-weight:800; font-size:12px; }
        .status-pending{ background:#fef3c7; color:#b45309; }
        .status-approved{ background:#dcfce7; color:#166534; }
        .company-name{ color:var(--primary); font-weight:700; display:block; margin-bottom:8px; }
        .meta-info{ color:#64748b; font-size:13px; margin-bottom:10px; }
        .modal{ display:none; position:fixed; inset:0; background:rgba(2,6,23,0.6); align-items:center; justify-content:center; }
        .modal-content{ background:#fff; padding:20px; border-radius:10px; width:700px; max-width:94%; }
        .input-style{ width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; margin-top:8px; }
        .input-group{ margin-bottom:12px; }
        .empty-state{ grid-column:1/-1; text-align:center; padding:40px 0; color:#94a3b8; }
    </style>
</head>
<body>

<div class="header-section">
    <div>
        <h1>My <span style="color:var(--primary)">Job Posts</span></h1>
        <div style="color:#64748b; margin-top:6px">Track and manage the opportunities you've shared.</div>
    </div>
    <button class="btn-main" onclick="toggleModal()"><i class="fas fa-plus"></i>&nbsp;Post New Job</button>
</div>

<div class="jobs-container">
    <?php if($my_jobs && $my_jobs->num_rows > 0): ?>
        <?php while($row = $my_jobs->fetch_assoc()): ?>
            <?php $status_class = ($row['status'] === 'approved') ? 'status-approved' : 'status-pending'; ?>
            <div class="job-card">
                <span class="status-badge <?php echo $status_class; ?>"><?php echo strtoupper(htmlspecialchars($row['status'])); ?></span>
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <span class="company-name"><?php echo htmlspecialchars($row['company']); ?></span>
                <div class="meta-info"><i class="fas fa-location-dot"></i>&nbsp;<?php echo htmlspecialchars($row['location']); ?></div>
                <div style="font-size:14px; color:#475569; line-height:1.5; height:72px; overflow:hidden"><?php echo htmlspecialchars(substr($row['description'],0,200)); ?><?php if(strlen($row['description'])>200) echo '...'; ?></div>
                <div style="margin-top:12px;"><a href="<?php echo htmlspecialchars($row['apply_link']); ?>" target="_blank" style="color:var(--primary); font-weight:700; text-decoration:none;">View Link <i class="fas fa-arrow-right"></i></a></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-briefcase" style="font-size:36px;color:#cbd5e1;margin-bottom:8px"></i>
            <h2>No jobs posted yet</h2>
            <p style="margin-top:8px">Click "Post New Job" to share an opportunity.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="jobModal" class="modal">
    <div class="modal-content">
        <h2 style="margin-bottom:12px">Post New Opportunity</h2>
        <form method="POST">
            <div class="input-group">
                <label>Job Title</label>
                <input class="input-style" type="text" name="title" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="input-group"><label>Company</label><input class="input-style" type="text" name="company" required></div>
                <div class="input-group"><label>Location</label><input class="input-style" type="text" name="location" required></div>
            </div>
            <div class="input-group"><label>Apply Link / Email</label><input class="input-style" type="text" name="apply_link" required></div>
            <div class="input-group"><label>Description</label><textarea class="input-style" name="description" rows="4" required></textarea></div>
            <div style="display:flex;gap:10px;margin-top:8px">
                <button class="btn-main" type="submit" name="post_job">Publish Job</button>
                <button type="button" onclick="toggleModal()" style="background:#f1f5f9;border-radius:10px;padding:10px 16px;border:none;cursor:pointer;color:#475569;font-weight:700;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(){ const m=document.getElementById('jobModal'); if(!m) return; m.style.display=(m.style.display==='flex')?'none':'flex'; }
window.addEventListener('click', function(e){ const m=document.getElementById('jobModal'); if(e.target===m) m.style.display='none'; });
<?php if($msg==='success'): ?> Swal.fire({ title: 'Posted', text: 'Your job is pending admin approval.', icon: 'success', confirmButtonColor: '#e11d48' }); <?php elseif($msg==='error'): ?> Swal.fire({ title: 'Error', text: 'Could not post the job.', icon: 'error' }); <?php endif; ?>
</script>

</body>
</html>