<?php
$pageTitle = "AlumniX | Career Board";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

// Fetching jobs (Searching enabled if needed later)
$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link FROM jobs WHERE status='approved' ORDER BY id DESC");
$jobCount = count($jobs);
?>

<style>
    /* Hero Section - Minimal & Clean */
    .subpage-hero {
        padding: 120px 20px 60px;
        text-align: center;
        background: radial-gradient(circle at top, rgba(255, 59, 59, 0.08) 0%, transparent 70%);
    }

    .subpage-title {
        font-size: clamp(32px, 5vw, 44px);
        font-weight: 800;
        letter-spacing: -1.5px;
        color: #111;
        margin-bottom: 20px;
    }

    /* Search Bar Styling */
    .search-container {
        max-width: 600px;
        margin: -30px auto 40px;
        position: relative;
        z-index: 5;
    }

    .search-bar {
        width: 100%;
        padding: 18px 25px;
        border-radius: 50px;
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        font-size: 16px;
        outline: none;
        transition: 0.3s;
    }

    .search-bar:focus {
        border-color: #ff3b3b;
        box-shadow: 0 10px 30px rgba(255, 59, 59, 0.1);
    }

    /* Grid & Cards */
    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 25px;
        padding-bottom: 80px;
    }

    .card-panel {
        background: #fff;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .card-panel:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: rgba(255, 59, 59, 0.15);
    }

    .icon-chip {
        width: 52px;
        height: 52px;
        background: #fff5f5;
        color: #ff3b3b;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .status-tag {
        font-size: 10px;
        font-weight: 800;
        background: #e7f9ed;
        color: #1db954; /* Spotify Green jaisa fresh green */
        padding: 5px 12px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 15px 0 5px;
        line-height: 1.2;
    }

    .card-subtitle {
        font-size: 15px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #94a3b8;
        font-weight: 600;
        background: #f8fafc;
        padding: 6px 14px;
        border-radius: 50px;
    }

    .card-copy {
        font-size: 14px;
        color: #475569;
        line-height: 1.6;
        margin: 20px 0 30px;
    }

    /* Apply Button */
    .btn-apply {
        display: block;
        text-align: center;
        padding: 14px;
        background: #111;
        color: #fff;
        text-decoration: none;
        border-radius: 16px;
        font-weight: 700;
        font-size: 15px;
        transition: 0.3s;
    }

    .btn-apply:hover {
        background: #ff3b3b;
        box-shadow: 0 10px 25px rgba(255, 59, 59, 0.3);
    }
</style>

<div class="public-shell">
    <section class="subpage-hero">
        <span class="section-kicker">Opportunity Hub</span>
        <h1 class="subpage-title">Move your career <br>forward with AlumniX.</h1>
        
        <div class="pill-row subpage-meta" style="justify-content:center; margin-top: 20px;">
            <span class="pill" style="border-radius: 50px; padding: 10px 20px; background: #fff;">
                <strong><?php echo number_format($jobCount); ?></strong> live roles
            </span>
        </div>
    </section>

    <div class="search-container">
        <input type="text" id="jobSearch" class="search-bar" placeholder="Search by title, company, or location...">
    </div>

    <section class="section" style="max-width: 1240px; margin: auto; padding: 0 20px;">
        <div class="subpage-grid" id="jobsGrid">
            <?php if ($jobs): ?>
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "login.php";
                    $external = (bool) preg_match('/^(https?:\/\/|mailto:)/i', $jobLink);
                    ?>
                    
                    <article class="card-panel job-card">
                        <div>
                            <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center;">
                                <div class="icon-chip"><i class="fas fa-briefcase"></i></div>
                                <span class="status-tag">Verified</span>
                            </div>

                            <h3 class="card-title"><?php echo e($job["title"]); ?></h3>
                            <p class="card-subtitle"><?php echo e($job["company"]); ?></p>

                            <div class="meta-row">
                                <span class="meta-chip"><i class="fas fa-location-dot" style="color:#ff3b3b"></i> <?php echo e($job["location"] ?: "Flexible"); ?></span>
                            </div>

                            <p class="card-copy"><?php echo e($job["description"] ?: "No description provided."); ?></p>
                        </div>

                        <a href="<?php echo e($jobLink); ?>" 
                           class="btn-apply" 
                           <?php echo $external ? 'target="_blank" rel="noopener noreferrer"' : ""; ?>>
                            <?php echo $external ? 'Apply Now' : 'Join to View'; ?>
                        </a>
                    </article>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No jobs found. Check back later!</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
document.getElementById('jobSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('.job-card');
    
    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        card.style.display = text.includes(filter) ? "flex" : "none";
    });
});
</script>

<?php include(__DIR__ . "/footer.php"); ?>