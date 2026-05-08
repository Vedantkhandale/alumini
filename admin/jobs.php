<?php
// Note: Is file ko 'jobs.php' ke naam se save karein.
require_once __DIR__ . "/helpers.php";
adminOnly();

// --- ⚡ MODERATION LOGIC ---
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $conn->query("UPDATE jobs SET status='approved' WHERE id=$id");
    header("Location: jobs.php?res=approved");
    exit();
}

if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $conn->query("UPDATE jobs SET status='rejected' WHERE id=$id");
    header("Location: jobs.php?res=rejected");
    exit();
}

// Fetch Jobs with Alumni Name
// Note: SQL query handle kar rahi hai agar alumni table mein 'full_name' column hai
$jobs = $conn->query("SELECT jobs.*, users.full_name 
                      FROM jobs 
                      JOIN users ON jobs.alumni_id = users.id 
                      ORDER BY jobs.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Moderation | AlumniX Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #ff3e3e;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #0f172a;
            --bg: #f8fafc;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: var(--dark); }

        .shell { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        /* --- 🚀 TOPBAR --- */
        .top-nav {
            background: white; border-bottom: 1px solid var(--border);
            padding: 20px 5%; display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 100;
        }
        .top-nav h1 { font-size: 1.4rem; font-weight: 800; letter-spacing: -1px; }
        .top-nav span { color: var(--primary); }

        /* --- 📦 JOB CARDS --- */
        .job-grid { display: grid; gap: 20px; margin-top: 30px; }
        
        .job-card {
            background: white; border-radius: 20px; border: 1px solid var(--border);
            padding: 25px; display: flex; justify-content: space-between; align-items: center;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .job-card:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 15px 30px rgba(0,0,0,0.04); }

        .job-main { display: flex; align-items: center; gap: 20px; }
        .company-icon {
            width: 60px; height: 60px; background: #f1f5f9; border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: var(--dark); border: 1px solid var(--border);
        }

        .job-info h3 { font-size: 1.2rem; font-weight: 800; margin-bottom: 5px; color: var(--dark); }
        .posted-by { font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 10px; }
        .posted-by span { color: var(--primary); }

        .meta-tags { display: flex; gap: 10px; }
        .tag { padding: 4px 12px; background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; font-size: 0.75rem; font-weight: 700; color: #64748b; }

        /* --- ⚙️ ACTIONS --- */
        .action-area { display: flex; flex-direction: column; align-items: flex-end; gap: 12px; }
        
        .btn-group { display: flex; gap: 8px; }
        .btn-mod {
            padding: 10px 18px; border-radius: 12px; font-size: 0.8rem; font-weight: 800;
            text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: 0.3s;
        }
        .btn-approve { background: #dcfce7; color: #15803d; }
        .btn-approve:hover { background: #10b981; color: white; }
        .btn-reject { background: #fee2e2; color: #b91c1c; }
        .btn-reject:hover { background: #ef4444; color: white; }

        @media (max-width: 768px) { .job-card { flex-direction: column; text-align: center; } .job-main { flex-direction: column; } .action-area { align-items: center; } }
    </style>
</head>
<body>

<nav class="top-nav">
    <h1>Job <span>Moderation</span></h1>
    <div style="display: flex; gap: 10px;">
        <a href="admin_dashboard.php" style="text-decoration:none; color:var(--dark); font-weight:700; font-size:0.85rem;">Dashboard</a>
        <span style="color:var(--border);">|</span>
        <a href="event.php" style="text-decoration:none; color:var(--dark); font-weight:700; font-size:0.85rem;">Events</a>
    </div>
</nav>

<div class="shell">
    <div class="job-grid">
        <?php if ($jobs->num_rows > 0): ?>
            <?php while($row = $jobs->fetch_assoc()): ?>
                <div class="job-card">
                    <div class="job-main">
                        <div class="company-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="job-info">
                            <h3><?= adminE($row['title']) ?></h3>
                            <p class="posted-by">Posted by <span><?= adminE($row['full_name']) ?></span></p>
                            <div class="meta-tags">
                                <span class="tag"><i class="fas fa-building"></i> <?= adminE($row['company'] ?? 'Corporate') ?></span>
                                <span class="tag"><i class="fas fa-map-marker-alt"></i> <?= adminE($row['location'] ?? 'Remote') ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="action-area">
                        <!-- Helper function for sexy badges -->
                        <?= getStatusBadge($row['status']) ?>
                        
                        <?php if ($row['status'] === 'pending'): ?>
                            <div class="btn-group">
                                <a href="?approve=<?= $row['id'] ?>" class="btn-mod btn-approve"><i class="fas fa-check"></i> Approve</a>
                                <a href="javascript:void(0)" onclick="confirmReject(<?= $row['id'] ?>)" class="btn-mod btn-reject"><i class="fas fa-times"></i> Reject</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 100px 0;">
                <i class="fas fa-search" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 20px;"></i>
                <h2 style="color: #94a3b8;">No jobs posted yet.</h2>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmReject(id) {
        Swal.fire({
            title: 'Reject Job Post?',
            text: "This job won't be visible to alumni members.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#0f172a',
            confirmButtonText: 'Yes, Reject It'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'jobs.php?reject=' + id;
            }
        })
    }

    // GSAP Animation
    document.addEventListener("DOMContentLoaded", function() {
        const cards = document.querySelectorAll('.job-card');
        cards.forEach((card, index) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            setTimeout(() => {
                card.style.transition = "0.5s ease-out";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, index * 100);
        });
    });
</script>

</body>
</html>