<?php
session_start();
include(__DIR__ . "/../includes/db.php");
$user_id = $_SESSION['user']['id']; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Careers Portal | AlumniX</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #e11d48; --dark: #0f172a; --white: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        
        .apply-modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(8px);
        }
        .modal-content {
            background: var(--white); width: 90%; max-width: 500px; margin: 50px auto;
            padding: 35px; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .input-box { width: 100%; padding: 12px; margin-top: 8px; border: 1.5px solid #e2e8f0; border-radius: 12px; outline: none; transition: 0.3s; }
        .input-box:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.1); }
        .form-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .btn-submit { background: var(--primary); color: white; border: none; width: 100%; padding: 15px; border-radius: 12px; font-weight: 700; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); opacity: 0.9; }
    </style>
</head>
<body>

<div class="jobs-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; padding: 50px 8%;">
    <?php
    $jobs = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");
    while($job = $jobs->fetch_assoc()):
    ?>
    <div class="job-card" style="background: white; padding: 25px; border-radius: 20px; border: 1px solid #e2e8f0;">
        <h3 style="margin: 0;"><?php echo $job['title']; ?></h3>
        <p style="color: var(--primary); font-weight: 700;"><?php echo $job['company']; ?></p>
        <button onclick="openApplyModal(<?php echo $job['id']; ?>, '<?php echo $job['title']; ?>')" 
                style="margin-top:15px; width: 100%; background: var(--dark); color: white; border: none; padding: 12px; border-radius: 10px; cursor: pointer;">
            Apply Now
        </button>
    </div>
    <?php endwhile; ?>
</div>

<!-- Sexy Apply Modal -->
<div id="applyModal" class="apply-modal">
    <div class="modal-content">
        <h2 style="margin-bottom: 20px; font-weight: 800;">Apply for <span id="jobTitleLabel" style="color: var(--primary);">Job</span></h2>
        
        <form id="applicationForm">
            <input type="hidden" name="job_id" id="target_job_id">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="input-box" placeholder="mail@example.com" required>
                </div>
                <div>
                    <label class="form-label">Total Experience</label>
                    <input type="text" name="experience" class="input-box" placeholder="e.g. 2 Years" required>
                </div>
            </div>

            <div style="margin-top: 15px;">
                <label class="form-label">Known Tech Languages</label>
                <input type="text" name="tech_languages" class="input-box" placeholder="PHP, Python, JavaScript, etc." required>
            </div>

            <div style="margin-top: 15px;">
                <label class="form-label">Key Skills & Interests</label>
                <textarea name="skills" class="input-box" rows="3" placeholder="Explain your core strengths..." required></textarea>
            </div>

            <div style="margin-top: 15px;">
                <label class="form-label">Resume Link (GDrive/LinkedIn)</label>
                <input type="url" name="resume_link" class="input-box" placeholder="https://..." required>
            </div>

            <button type="submit" id="submitBtn" class="btn-submit">Send Application</button>
            <button type="button" onclick="closeModal()" style="width: 100%; background: none; border: none; color: #94a3b8; margin-top: 10px; cursor: pointer;">Close</button>
        </form>
    </div>
</div>

<script>
    function openApplyModal(id, title) {
        document.getElementById('target_job_id').value = id;
        document.getElementById('jobTitleLabel').innerText = title;
        document.getElementById('applyModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('applyModal').style.display = 'none';
    }

    // JS Logic for Real-time Apply
    document.getElementById('applicationForm').onsubmit = function(e) {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.innerText = "Applying...";
        btn.disabled = true;

        const formData = new FormData(this);
        formData.append('ajax_apply', '1');

        fetch('process_application.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if(data.includes('success')) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Your application has been sent to Admin.',
                    icon: 'success',
                    confirmButtonColor: '#e11d48'
                });
                closeModal();
                this.reset();
            } else {
                Swal.fire('Error!', 'Something went wrong.', 'error');
            }
            btn.innerText = "Send Application";
            btn.disabled = false;
        });
    };
</script>

</body>
</html>