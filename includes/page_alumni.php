<?php
$pageTitle = "AlumniX | Network";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

$alumniMembers = fetchRows($conn, "SELECT id, name, course, batch, company FROM alumni ORDER BY id DESC");
?>

<style>
    /* Hero Section Styling */
    .subpage-hero {
        padding: 120px 20px 60px;
        text-align: center;
        background: linear-gradient(180deg, rgba(255,59,59,0.05) 0%, transparent 100%);
    }
    
    .section-kicker {
        display: inline-block;
        color: #ff3b3b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 12px;
        margin-bottom: 15px;
    }

    .subpage-title {
        font-size: clamp(32px, 5vw, 48px);
        font-weight: 800;
        color: #111;
        letter-spacing: -1px;
        line-height: 1.1;
        margin-bottom: 20px;
    }

    /* Grid Layout */
    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
        padding: 40px 0;
    }

    /* Modern Card Design */
    .card-panel {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .card-panel:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-color: rgba(255,59,59,0.2);
    }

    .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    /* Avatar Chip */
    .avatar-chip {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, #ff3b3b, #ff7b7b);
        color: #fff;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 700;
        box-shadow: 0 8px 15px rgba(255, 59, 59, 0.2);
    }

    .tag {
        padding: 6px 12px;
        background: #f0f2f5;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        color: #666;
        text-transform: uppercase;
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #111;
        margin-bottom: 4px;
    }

    .card-subtitle {
        font-size: 14px;
        color: #666;
        font-weight: 500;
    }

    .meta-row {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px dashed #eee;
    }

    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #444;
        font-weight: 600;
    }

    .meta-chip i {
        color: #ff3b3b;
        font-size: 14px;
    }

    .pill-row .pill {
        background: rgba(255, 59, 59, 0.1);
        color: #ff3b3b;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
    }
</style>

<div class="public-shell">
    <section class="subpage-hero">
        <span class="section-kicker">Network Directory</span>
        <h1 class="subpage-title">The people carrying <br>the name forward.</h1>
        
        <div class="pill-row subpage-meta">
            <span class="pill"><i class="fas fa-users"></i> <?php echo number_format(count($alumniMembers)); ?> Members</span>
            <span class="pill"><i class="fas fa-graduation-cap"></i> Verified Network</span>
        </div>
    </section>

    <section class="section" style="max-width: 1200px; margin: auto; padding: 0 20px;">
        <div class="subpage-grid">
            <?php if ($alumniMembers): ?>
                <?php foreach ($alumniMembers as $alumnus): ?>
                    <?php $initial = strtoupper(substr((string) ($alumnus["name"] ?? "A"), 0, 1)); ?>
                    
                    <article class="card-panel">
                        <div class="panel-head">
                            <div class="avatar-chip"><?php echo e($initial); ?></div>
                            <span class="tag">Batch <?php echo e($alumnus["batch"] ?: "N/A"); ?></span>
                        </div>

                        <div>
                            <h3 class="card-title"><?php echo e($alumnus["name"]); ?></h3>
                            <p class="card-subtitle"><?php echo e($alumnus["course"] ?: "Course not listed"); ?></p>
                        </div>

                        <div class="meta-row">
                            <span class="meta-chip">
                                <i class="fas fa-briefcase"></i> 
                                <?php echo e($alumnus["company"] ?: "Exploring Opportunities"); ?>
                            </span>
                        </div>
                    </article>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No profiles found. The network is just getting started!</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include(__DIR__ . "/footer.php"); ?>