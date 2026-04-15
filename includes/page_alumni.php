<?php
$pageTitle = "AlumniX | Alumni";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

$alumniMembers = fetchRows($conn, "SELECT id, name, course, batch, company FROM alumni ORDER BY id DESC");
?>

<div class="public-shell">
    <section class="subpage-hero">
        <span class="section-kicker animate-hero">Directory</span>
        <h1 class="subpage-title animate-hero">The people carrying the name forward.</h1>
        <p class="animate-hero">Browse alumni profiles, spot shared backgrounds, and keep the network human instead of faceless.</p>

        <div class="pill-row subpage-meta animate-hero">
            <span class="pill"><?php echo number_format(count($alumniMembers)); ?> alumni listed</span>
            <span class="pill">Batch and course visibility</span>
            <span class="pill">Growing community directory</span>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <span class="section-kicker">Community</span>
                <h2 class="section-title">Profiles with context, not just names.</h2>
                <p>Every card shows the core details someone would need before reaching out or taking inspiration.</p>
            </div>
        </div>

        <div class="subpage-grid">
            <?php if ($alumniMembers): ?>
                <?php foreach ($alumniMembers as $alumnus): ?>
                    <?php $initial = strtoupper(substr((string) ($alumnus["name"] ?? "A"), 0, 1)); ?>
                    <article class="card-panel reveal" id="alumni-<?php echo (int) $alumnus["id"]; ?>">
                        <div class="panel-head">
                            <div class="avatar-chip"><?php echo e($initial); ?></div>
                            <span class="tag"><?php echo e($alumnus["batch"] ?: "Alumni"); ?></span>
                        </div>

                        <div>
                            <h3 class="card-title"><?php echo e($alumnus["name"]); ?></h3>
                            <p class="card-subtitle"><?php echo e($alumnus["course"] ?: "Course not listed"); ?></p>
                        </div>

                        <div class="meta-row">
                            <span class="meta-chip"><i class="fas fa-building"></i> <?php echo e($alumnus["company"] ?: "Company not listed"); ?></span>
                        </div>

                        <p class="card-copy">A visible part of the growing alumni network, helping keep relationships and inspiration close to campus.</p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state reveal">No alumni profiles are listed yet. Once records are added, this directory will become the public face of the network.</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include(__DIR__ . "/footer.php"); ?>
