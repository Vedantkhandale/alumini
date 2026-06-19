<?php
session_start();
include(__DIR__ . "/../includes/db.php");

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$userEmail = $_SESSION['user']['email'] ?? '';
$userId = (int) ($_SESSION['user']['id'] ?? 0);

$user = null;
$stmt = $conn->prepare("SELECT * FROM alumni WHERE email = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Auto-detect columns layout for loading view
$columns = [];
$tableCheck = $conn->query("DESCRIBE alumni");
if ($tableCheck) { while ($row = $tableCheck->fetch_assoc()) { $columns[] = $row['Field']; } }

$db_fullname = in_array('fullname', $columns) ? 'fullname' : (in_array('name', $columns) ? 'name' : 'fullname');
$db_dept = in_array('department', $columns) ? 'department' : 'department';
$db_grad = in_array('graduation_year', $columns) ? 'graduation_year' : (in_array('grad_year', $columns) ? 'grad_year' : 'graduation_year');
$db_company = in_array('current_company', $columns) ? 'current_company' : (in_array('company', $columns) ? 'company' : 'current_company');
$db_loc = in_array('location', $columns) ? 'location' : 'location';
$db_about = in_array('about_me', $columns) ? 'about_me' : (in_array('about', $columns) ? 'about' : 'about_me');
$db_photo = in_array('profile_photo', $columns) ? 'profile_photo' : (in_array('image', $columns) ? 'image' : 'profile_photo');

$fullname = $user[$db_fullname] ?? ($_SESSION['user']['full_name'] ?? 'Alumni Member');
$department = $user[$db_dept] ?? ($_SESSION['user']['department'] ?? 'Not set');
$graduation_year = $user[$db_grad] ?? ($_SESSION['user']['grad_year'] ?? 'N/A');
$current_company = $user[$db_company] ?? ($_SESSION['user']['company'] ?? 'Independent');
$location = $user[$db_loc] ?? ($_SESSION['user']['location'] ?? 'Worldwide');
$about_me = $user[$db_about] ?? ($_SESSION['user']['about'] ?? 'No bio available yet.');
$profile_photo = $user[$db_photo] ?? ($_SESSION['user']['image'] ?? null);

$profileImage = !empty($profile_photo) && file_exists(__DIR__ . "/../uploads/profiles/" . $profile_photo)
    ? "../uploads/profiles/" . $profile_photo
    : "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background=e11d48&color=fff&bold=true&size=240";

$jobsPosted = 0; $pendingJobs = 0;
if ($userId > 0) {
    $jobsPosted = (int) ($conn->query("SELECT COUNT(*) AS cnt FROM jobs WHERE alumni_id = '$userId'")?->fetch_assoc()['cnt'] ?? 0);
    $pendingJobs = (int) ($conn->query("SELECT COUNT(*) AS cnt FROM jobs WHERE alumni_id = '$userId' AND status = 'pending'")?->fetch_assoc()['cnt'] ?? 0);
}

function esc($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($fullname); ?> | Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        :root { --bg: #f8fbff; --surface: #ffffff; --surface-soft: #fff1f2; --primary: #e11d48; --primary-soft: #fee2e6; --text: #0f172a; --muted: #64748b; --shadow: 0 25px 60px rgba(15, 23, 42, 0.08); --border: #e2e8f0; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); }
        .page { padding: 40px 6%; max-width: 1180px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 34px; }
        .topbar h1 { margin: 0; font-size: 34px; font-weight: 800; }
        .nav-links a { padding: 12px 18px; border-radius: 14px; background: var(--surface); border: 1px solid var(--border); font-weight: 700; text-decoration: none; color: var(--text); }
        .profile-panel { display: grid; grid-template-columns: 360px 1fr; gap: 28px; align-items: start; }
        .card { background: var(--surface); border-radius: 28px; padding: 28px; box-shadow: var(--shadow); border: 1px solid var(--border); position: relative; }
        .edit-profile-btn { position: absolute; top: 20px; right: 20px; background: var(--surface); border: 1px solid var(--border); color: var(--muted); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; z-index: 10; }
        .edit-profile-btn:hover { background: var(--primary); color: #fff; transform: scale(1.1); }
        .hero-card { display: grid; gap: 18px; text-align: center; }
        .avatar { width: 180px; height: 180px; border-radius: 32px; overflow: hidden; border: 6px solid var(--primary-soft); margin: 0 auto; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-name { margin: 0; font-size: 28px; font-weight: 800; }
        .user-role { color: var(--muted); font-size: 14px; text-transform: uppercase; }
        .bio { color: var(--muted); line-height: 1.8; }
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 24px; }
        .stat-card { background: var(--surface-soft); border-radius: 20px; padding: 20px; }
        .stat-card label { display: block; font-size: 12px; color: var(--muted); font-weight: 700; }
        .stat-card strong { font-size: 26px; display: block; margin-top: 2px; }
        .profile-details { display: grid; gap: 18px; }
        .detail-row { display: grid; grid-template-columns: 140px 1fr; gap: 12px; }
        .detail-key { color: var(--muted); font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .detail-value { color: var(--text); font-size: 15px; }
        .section { margin-top: 34px; }
        .section h2 { margin: 0 0 22px; font-size: 22px; font-weight: 800; }
        .activity-card { background: var(--surface); border-radius: 24px; padding: 24px; border: 1px solid var(--border); }
        
        /* Modal Engine */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; pointer-events: none; transition: 0.3s ease; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-card { background: var(--surface); width: 100%; max-width: 600px; border-radius: 28px; padding: 32px; border: 1px solid var(--border); max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .modal-header h3 { margin: 0; font-size: 22px; font-weight: 800; }
        .close-modal-btn { background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: var(--muted); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
        .form-group label { font-size: 11px; font-weight: 800; color: var(--muted); text-transform: uppercase; }
        .form-input { padding: 13px 16px; border-radius: 12px; border: 1.5px solid var(--border); font-family: inherit; font-size: 14px; outline: none; background: #fbfcfd; }
        .form-input:focus { border-color: var(--primary); background: #fff; }
        .btn-submit-profile { background: #0f172a; color: #fff; border: none; padding: 16px; border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.2s; }
        .btn-submit-profile:hover { background: var(--primary); }
        #imagePreview { width: 60px; height: 60px; border-radius: 12px; object-fit: cover; border: 2px solid var(--border); display: none; margin-top: 8px; }
        @media (max-width: 980px) { .profile-panel { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="page">
    <div class="topbar">
        <div>
            <h1>My Alumni Profile</h1>
            <div style="color: var(--muted); margin-top: 6px;">Clean member profile with live auto-detection engine.</div>
        </div>
        <div class="nav-links"><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></div>
    </div>

    <div class="profile-panel">
        <div class="card hero-card">
            <button class="edit-profile-btn" onclick="toggleModal(true)"><i class="fas fa-edit"></i></button>
            <div class="avatar"><img id="display-avatar" src="<?php echo esc($profileImage); ?>"></div>
            <h2 class="user-name" id="card-name"><?php echo esc($fullname); ?></h2>
            <div class="user-role" id="card-role"><?php echo esc($department); ?> · Class of <?php echo esc($graduation_year); ?></div>
            <p class="bio" id="card-bio"><?php echo esc($about_me ?: 'Your profile is your network identity.'); ?></p>
            
            <div class="stats-grid">
                <div class="stat-card"><label>Jobs Posted</label><strong><?php echo esc($jobsPosted); ?></strong></div>
                <div class="stat-card"><label>Pending Approval</label><strong><?php echo esc($pendingJobs); ?></strong></div>
            </div>
        </div>

        <div>
            <div class="card profile-details">
                <div class="detail-row"><div class="detail-key">Full Name</div><div class="detail-value" id="detail-name"><?php echo esc($fullname); ?></div></div>
                <div class="detail-row"><div class="detail-key">Email</div><div class="detail-value"><?php echo esc($userEmail); ?></div></div>
                <div class="detail-row"><div class="detail-key">Department</div><div class="detail-value" id="detail-dept"><?php echo esc($department); ?></div></div>
                <div class="detail-row"><div class="detail-key">Graduation</div><div class="detail-value" id="detail-grad"><?php echo esc($graduation_year); ?></div></div>
                <div class="detail-row"><div class="detail-key">Current Company</div><div class="detail-value" id="detail-company"><?php echo esc($current_company); ?></div></div>
                <div class="detail-row"><div class="detail-key">Location</div><div class="detail-value" id="detail-loc"><?php echo esc($location); ?></div></div>
            </div>
            <section class="section">
                <h2>About Me</h2>
                <div class="activity-card"><p id="detail-about"><?php echo esc($about_me); ?></p></div>
            </section>
        </div>
    </div>
</div>

<div class="modal-overlay" id="editProfileModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>Edit Profile Details 📝</h3>
            <button class="close-modal-btn" onclick="toggleModal(false)"><i class="fas fa-times"></i></button>
        </div>
        <form id="editProfileForm" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group"><label>Full Name</label><input type="text" name="fullname" class="form-input" value="<?php echo esc($fullname); ?>" required></div>
                <div class="form-group"><label>Email (Read-Only)</label><input type="email" class="form-input" value="<?php echo esc($userEmail); ?>" readonly></div>
            </div>
            <div class="form-grid">
                <div class="form-group"><label>Department</label><input type="text" name="department" class="form-input" value="<?php echo esc($department); ?>" required></div>
                <div class="form-group"><label>Graduation Year</label><input type="text" name="graduation_year" class="form-input" value="<?php echo esc($graduation_year); ?>" required></div>
            </div>
            <div class="form-grid">
                <div class="form-group"><label>Current Company</label><input type="text" name="current_company" class="form-input" value="<?php echo esc($current_company); ?>"></div>
                <div class="form-group"><label>Location</label><input type="text" name="location" class="form-input" value="<?php echo esc($location); ?>"></div>
            </div>
            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" name="profile_photo" class="form-input" accept="image/*" onchange="previewImage(this)">
                <img id="imagePreview" src="#">
            </div>
            <div class="form-group"><label>About / Professional Bio</label><textarea name="about_me" class="form-input" rows="4"><?php echo esc($about_me); ?></textarea></div>
            <button type="submit" class="btn-submit-profile">Save & Update Profile</button>
        </form>
    </div>
</div>

<script>
    function toggleModal(open) {
        const modal = document.getElementById('editProfileModal');
        if (open) {
            modal.classList.add('active');
            gsap.fromTo('#editProfileModal .modal-card', { y: 40, opacity: 0 }, { y: 0, opacity: 1, duration: 0.4 });
        } else {
            gsap.to('#editProfileModal .modal-card', { y: 30, opacity: 0, duration: 0.3, onComplete: () => modal.classList.remove('active') });
        }
    }

    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch('update_profile.php', { method: 'POST', body: formData })
        .then(async response => {
            const text = await response.text();
            try { return JSON.parse(text); } catch(err) { throw new Error(text); }
        })
        .then(data => {
            if(data.status === 'success') {
                toggleModal(false);
                document.getElementById('card-name').innerText = data.user.fullname;
                document.getElementById('detail-name').innerText = data.user.fullname;
                document.getElementById('card-role').innerText = data.user.department + " · Class of " + data.user.graduation_year;
                document.getElementById('detail-dept').innerText = data.user.department;
                document.getElementById('detail-grad').innerText = data.user.graduation_year;
                document.getElementById('detail-company').innerText = data.user.current_company || 'Independent';
                document.getElementById('detail-loc').innerText = data.user.location || 'Global';
                document.getElementById('card-bio').innerText = data.user.about_me || 'Your profile is your network identity.';
                document.getElementById('detail-about').innerText = data.user.about_me || '';
                if(data.user.image_url) document.getElementById('display-avatar').src = data.user.image_url;

                Swal.fire({ title: 'Success!', text: data.message, icon: 'success', confirmButtonColor: '#e11d48' });
            } else {
                Swal.fire({ title: 'Update Failed!', text: data.message, icon: 'error', confirmButtonColor: '#e11d48' });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Database Engine Notice!',
                html: '<div style="text-align:left; font-family:monospace; max-height:200px; overflow-y:auto; background:#f1f5f9; padding:10px; border-radius:8px; font-size:12px;">' + error.message + '</div>',
                icon: 'error',
                confirmButtonColor: '#e11d48'
            });
        });
    });
</script>
</body>
</html>