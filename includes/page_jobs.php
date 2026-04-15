<?php
$pageTitle = "AlumniX | Jobs";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link FROM jobs WHERE status='approved' ORDER BY id DESC");
$jobCount = count($jobs);
?>

<div class="public-shell">
    <section class="subpage-hero">
        <span class="section-kicker animate-hero">Career Board</span>
        <h1 class="subpage-title animate-hero">Opportunities shared by the alumni network.</h1>
        <p class="animate-hero">Browse approved job posts, follow trusted alumni leads, and move quickly when something fits your profile.</p>

        <div class="pill-row subpage-meta animate-hero">
            <span class="pill"><?php echo number_format($jobCount); ?> approved openings</span>
            <span class="pill">Direct alumni visibility</span>
            <span class="pill">Fast to apply</span>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <span class="section-kicker">Open roles</span>
                <h2 class="section-title">Find the next strong fit.</h2>
                <p>Every listing below comes from the community and stays visible once approved.</p>
            </div>
        </div>

        <div class="subpage-grid">
            <?php if ($jobs): ?>
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "login.php";
                    $external = (bool) preg_match('/^(https?:\/\/|mailto:)/i', $jobLink);
                    ?>
                    <article class="card-panel reveal" id="job-<?php echo (int) $job["id"]; ?>">
                        <div class="panel-head">
                            <div class="icon-chip"><i class="fas fa-briefcase"></i></div>
                            <span class="tag">Live</span>
                        </div>

                        <div>
                            <h3 class="card-title"><?php echo e($job["title"]); ?></h3>
                            <p class="card-subtitle"><?php echo e($job["company"]); ?></p>
                        </div>

                        <div class="meta-row">
                            <span class="meta-chip"><i class="fas fa-location-dot"></i> <?php echo e($job["location"] ?: "Flexible"); ?></span>
                        </div>

                        <p class="card-copy"><?php echo e($job["description"] ?: "No detailed description was added for this role yet."); ?></p>

                        <div class="action-row">
                            <a href="<?php echo e($jobLink); ?>" class="card-link" <?php echo $external ? 'target="_blank" rel="noopener noreferrer"' : ""; ?>><?php echo $external ? "Apply now" : "Open portal"; ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state reveal">There are no approved jobs at the moment. Once alumni start posting, they will appear here automatically.</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include(__DIR__ . "/footer.php"); ?>
