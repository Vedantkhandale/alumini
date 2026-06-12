<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

$stats = [
    "verified_alumni" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni' AND status IN ('approved', 'active')"),
    "pending_alumni" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni' AND status='pending'"),
    "active_jobs" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='approved'"),
    "pending_jobs" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='pending'"),
    "upcoming_events" => adminCount($conn, "SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()"),
    "applications" => adminCount($conn, "SELECT COUNT(*) FROM job_applications"),
];

$totalAlumni = max(1, $stats["verified_alumni"] + $stats["pending_alumni"]);
$approvalPercent = (int) round(($stats["verified_alumni"] / $totalAlumni) * 100);
$pendingWork = $stats["pending_alumni"] + $stats["pending_jobs"];

$recentJobs = adminRows(
    $conn,
    "SELECT
        j.id,
        j.title,
        j.company,
        j.location,
        j.status,
        u.full_name AS owner_name,
        COUNT(ja.id) AS applications
     FROM jobs j
     LEFT JOIN alumni_users u ON j.alumni_id = u.id
     LEFT JOIN job_applications ja ON ja.job_id = j.id
     GROUP BY j.id, j.title, j.company, j.location, j.status, u.full_name
     ORDER BY
        CASE j.status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END,
        j.id DESC
     LIMIT 6"
);

$pendingAlumni = adminRows(
    $conn,
    "SELECT
        id,
        full_name,
        email,
        student_id,
        batch,
        batch_start,
        batch_end
     FROM alumni_users
     WHERE role='alumni' AND status='pending'
     ORDER BY id DESC
     LIMIT 5"
);

$upcomingEvents = adminRows(
    $conn,
    "SELECT id, title, event_date, location
     FROM events
     WHERE event_date >= CURDATE()
     ORDER BY event_date ASC, id DESC
     LIMIT 4"
);

$recentApplications = adminRows(
    $conn,
    "SELECT
        ja.apply_time,
        u.full_name AS alumni_name,
        j.title AS job_title,
        j.company
     FROM job_applications ja
     JOIN alumni_users u ON ja.alumni_id = u.id
     JOIN jobs j ON ja.job_id = j.id
     ORDER BY ja.apply_time DESC
     LIMIT 5"
);

adminRenderPageStart([
    "page_title" => "Admin Dashboard | AlumniX",
    "hero_badge" => "Live command center",
    "hero_title" => "A sharper admin view for faster moderation.",
    "hero_text" => "Track approvals, job flow, event visibility, and application activity from one tighter workspace without changing the core behavior of the admin tools.",
    "active" => "dashboard",
    "actions" => [
        ["href" => "alumni_list.php", "icon" => "fas fa-user-check", "label" => "Review Alumni", "variant" => "secondary"],
        ["href" => "jobs.php", "icon" => "fas fa-briefcase", "label" => "Moderate Jobs", "variant" => "primary"],
    ],
]);
?>

<?php if ($pendingWork > 0): ?>
    <section class="queue-banner">
        <div>
            <strong><?php echo number_format($pendingWork); ?> items need attention</strong>
            <span><?php echo number_format($stats["pending_alumni"]); ?> alumni request(s) and <?php echo number_format($stats["pending_jobs"]); ?> job post(s) are waiting in the queue.</span>
        </div>
        <a class="small-btn secondary" href="<?php echo $stats["pending_alumni"] > 0 ? "alumni_list.php" : "jobs.php"; ?>">
            <i class="fas fa-arrow-right"></i> Open Queue
        </a>
    </section>
<?php endif; ?>

<section class="metric-grid">
    <article class="metric-card">
        <div class="metric-label">Verified Alumni</div>
        <span class="metric-value"><?php echo number_format($stats["verified_alumni"]); ?></span>
        <div class="metric-note"><?php echo $approvalPercent; ?>% approval health across all alumni records.</div>
        <div class="metric-icon"><i class="fas fa-user-graduate"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Pending Review</div>
        <span class="metric-value"><?php echo number_format($pendingWork); ?></span>
        <div class="metric-note">Combined moderation load from member and job submissions.</div>
        <div class="metric-icon"><i class="fas fa-hourglass-half"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Approved Jobs</div>
        <span class="metric-value"><?php echo number_format($stats["active_jobs"]); ?></span>
        <div class="metric-note"><?php echo number_format($stats["applications"]); ?> total applications currently tracked.</div>
        <div class="metric-icon"><i class="fas fa-briefcase"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Upcoming Events</div>
        <span class="metric-value"><?php echo number_format($stats["upcoming_events"]); ?></span>
        <div class="metric-note">Live calendar touchpoints available to the alumni network.</div>
        <div class="metric-icon"><i class="fas fa-calendar-days"></i></div>
    </article>
</section>

<section class="content-grid">
    <div class="panel-stack">
        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Approval Health</h2>
                    <p class="panel-copy">A quick read on member approvals, pending requests, and job moderation load.</p>
                </div>
                <a class="panel-link" href="alumni_list.php">Open alumni queue</a>
            </div>
            <div class="record-list">
                <article class="record-card">
                    <div class="record-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <span class="record-title"><?php echo $approvalPercent; ?>% of alumni profiles are approved</span>
                        <div class="record-meta">Approved or active members: <?php echo number_format($stats["verified_alumni"]); ?></div>
                    </div>
                    <span class="status-badge status-approved">Healthy</span>
                </article>
                <article class="record-card">
                    <div class="record-icon"><i class="fas fa-envelope-open-text"></i></div>
                    <div>
                        <span class="record-title"><?php echo number_format($stats["pending_alumni"]); ?> alumni request(s) are waiting</span>
                        <div class="record-meta">Approve pending members to generate and mail login credentials.</div>
                    </div>
                    <span class="status-badge status-pending">Pending</span>
                </article>
                <article class="record-card">
                    <div class="record-icon"><i class="fas fa-layer-group"></i></div>
                    <div>
                        <span class="record-title"><?php echo number_format($stats["pending_jobs"]); ?> job post(s) need moderation</span>
                        <div class="record-meta">Keep the public board clean before posts go live to alumni.</div>
                    </div>
                    <span class="status-badge status-neutral">Queue</span>
                </article>
            </div>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Job Pipeline</h2>
                    <p class="panel-copy">Latest roles, owner identity, moderation status, and application volume in one place.</p>
                </div>
                <a class="panel-link" href="jobs.php">Manage jobs</a>
            </div>
            <?php if ($recentJobs): ?>
                <div class="data-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Owner</th>
                                <th>Status</th>
                                <th>Apps</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentJobs as $job): ?>
                                <?php $status = strtolower((string) ($job["status"] ?: "pending")); ?>
                                <tr>
                                    <td class="primary-cell">
                                        <strong><?php echo adminE($job["title"]); ?></strong>
                                        <span><?php echo adminE($job["company"] ?: "Unknown company"); ?> · <?php echo adminE($job["location"] ?: "Flexible"); ?></span>
                                    </td>
                                    <td><?php echo adminE($job["owner_name"] ?: "Unknown alumni"); ?></td>
                                    <td><span class="status-badge <?php echo adminStatusClass($status); ?>"><?php echo adminE($status); ?></span></td>
                                    <td><?php echo number_format((int) $job["applications"]); ?></td>
                                    <td>
                                        <div class="row-actions">
                                            <a class="small-btn" href="jobs.php"><i class="fas fa-sliders"></i> Manage</a>
                                            <a class="small-btn secondary" href="view_applications.php"><i class="fas fa-arrow-right"></i> Apps</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-briefcase"></i>
                    <p>No jobs have been posted yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="panel-stack">
        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Pending Alumni</h2>
                    <p class="panel-copy">Latest member requests waiting for approval.</p>
                </div>
                <a class="panel-link" href="alumni_list.php">Open list</a>
            </div>
            <?php if ($pendingAlumni): ?>
                <div class="record-list">
                    <?php foreach ($pendingAlumni as $member): ?>
                        <article class="record-card">
                            <div class="record-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <span class="record-title"><?php echo adminE($member["full_name"]); ?></span>
                                <div class="record-meta">
                                    <?php echo adminE($member["email"]); ?><br>
                                    <?php echo adminE($member["student_id"] ?: "No student ID"); ?> · <?php echo adminE(adminBatchLabel($member)); ?>
                                </div>
                            </div>
                            <span class="status-badge status-pending">Pending</span>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>No pending alumni right now.</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Upcoming Events</h2>
                    <p class="panel-copy">Public schedule blocks that are about to go live or stay visible.</p>
                </div>
                <a class="panel-link" href="event.php">Manage events</a>
            </div>
            <?php if ($upcomingEvents): ?>
                <div class="record-list">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <article class="record-card">
                            <div class="date-tile">
                                <div>
                                    <strong><?php echo adminE(adminFormatDate((string) $event["event_date"], "d", "--")); ?></strong>
                                    <span><?php echo adminE(adminFormatDate((string) $event["event_date"], "M", "TBA")); ?></span>
                                </div>
                            </div>
                            <div>
                                <span class="record-title"><?php echo adminE($event["title"]); ?></span>
                                <div class="record-meta"><?php echo adminE(adminFormatDate((string) $event["event_date"], "d M Y", "Date TBA")); ?> · <?php echo adminE($event["location"] ?: "Location TBA"); ?></div>
                            </div>
                            <span class="status-badge status-neutral">Live</span>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-xmark"></i>
                    <p>No upcoming events scheduled.</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="panel-card">
            <div class="panel-head">
                <div>
                    <h2 class="panel-title">Recent Applications</h2>
                    <p class="panel-copy">The freshest candidate movement across approved roles.</p>
                </div>
                <a class="panel-link" href="view_applications.php">Open tracker</a>
            </div>
            <?php if ($recentApplications): ?>
                <div class="record-list">
                    <?php foreach ($recentApplications as $application): ?>
                        <article class="record-card">
                            <div class="record-icon"><i class="fas fa-file-signature"></i></div>
                            <div>
                                <span class="record-title"><?php echo adminE($application["alumni_name"]); ?></span>
                                <div class="record-meta">
                                    <?php echo adminE($application["job_title"]); ?> at <?php echo adminE($application["company"]); ?><br>
                                    <?php echo adminE(adminFormatDate((string) $application["apply_time"], "d M Y, h:i A", "Recently")); ?>
                                </div>
                            </div>
                            <span class="status-badge status-approved">Active</span>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No job applications have been submitted yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</section>

<?php adminRenderPageEnd(); ?>
