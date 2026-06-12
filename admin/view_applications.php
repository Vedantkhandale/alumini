<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

$stats = [
    "total" => adminCount($conn, "SELECT COUNT(*) FROM job_applications"),
    "today" => adminCount($conn, "SELECT COUNT(*) FROM job_applications WHERE DATE(apply_time) = CURDATE()"),
    "jobs" => adminCount($conn, "SELECT COUNT(DISTINCT job_id) FROM job_applications"),
    "alumni" => adminCount($conn, "SELECT COUNT(DISTINCT alumni_id) FROM job_applications"),
];

$applications = adminRows(
    $conn,
    "SELECT
        ja.id,
        u.full_name AS alumni_name,
        u.email AS alumni_email,
        j.title AS job_role,
        j.company,
        ja.apply_time
     FROM job_applications ja
     JOIN alumni_users u ON ja.alumni_id = u.id
     JOIN jobs j ON ja.job_id = j.id
     ORDER BY ja.apply_time DESC"
);

adminRenderPageStart([
    "page_title" => "Applications Tracker | AlumniX Admin",
    "hero_badge" => "Application flow",
    "hero_title" => "See who is moving across published opportunities.",
    "hero_text" => "Applications now resolve directly through the alumni member table, with a cleaner tracker for role demand, applicant identity, and submission timing.",
    "active" => "applications",
    "actions" => [
        ["href" => "jobs.php", "icon" => "fas fa-briefcase", "label" => "Moderate Jobs", "variant" => "secondary"],
        ["href" => "alumni_list.php", "icon" => "fas fa-user-graduate", "label" => "Review Alumni", "variant" => "primary"],
    ],
]);
?>

<section class="metric-grid">
    <article class="metric-card">
        <div class="metric-label">Total Applications</div>
        <span class="metric-value"><?php echo number_format($stats["total"]); ?></span>
        <div class="metric-note">Complete application volume recorded across all approved roles.</div>
        <div class="metric-icon"><i class="fas fa-file-lines"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Applied Today</div>
        <span class="metric-value"><?php echo number_format($stats["today"]); ?></span>
        <div class="metric-note">Fresh applications submitted during the current day.</div>
        <div class="metric-icon"><i class="fas fa-clock"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Active Roles</div>
        <span class="metric-value"><?php echo number_format($stats["jobs"]); ?></span>
        <div class="metric-note">Distinct jobs that already have at least one application attached.</div>
        <div class="metric-icon"><i class="fas fa-briefcase"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Active Alumni</div>
        <span class="metric-value"><?php echo number_format($stats["alumni"]); ?></span>
        <div class="metric-note">Unique alumni members currently interacting with the job board.</div>
        <div class="metric-icon"><i class="fas fa-user-group"></i></div>
    </article>
</section>

<section class="panel-card">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Application Tracker</h2>
            <p class="panel-copy">Identity, role, company, and timestamp all stay visible in one high-contrast table for the admin team.</p>
        </div>
        <a class="panel-link" href="jobs.php">Back to jobs</a>
    </div>

    <?php if ($applications): ?>
        <div class="data-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Alumni</th>
                        <th>Role</th>
                        <th>Company</th>
                        <th>Applied At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td class="primary-cell">
                                <strong><?php echo adminE($application["alumni_name"]); ?></strong>
                                <span><?php echo adminE($application["alumni_email"]); ?></span>
                            </td>
                            <td><span class="status-badge status-neutral"><?php echo adminE($application["job_role"]); ?></span></td>
                            <td><?php echo adminE($application["company"]); ?></td>
                            <td><?php echo adminE(adminFormatDate((string) $application["apply_time"], "d M Y, h:i A", "Recently")); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No job applications have been submitted yet.</p>
        </div>
    <?php endif; ?>
</section>

<?php adminRenderPageEnd(); ?>
