<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["approve"])) {
    $id = (int) $_GET["approve"];
    $job = null;
    $stmt = $conn->prepare(
        "SELECT jobs.title, jobs.company, alumni_users.full_name, alumni_users.email
         FROM jobs
         LEFT JOIN alumni_users ON jobs.alumni_id = alumni_users.id
         WHERE jobs.id = ?
         LIMIT 1"
    );
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $job = $result ? $result->fetch_assoc() : null;
        $stmt->close();
    }

    $update = $conn->prepare("UPDATE jobs SET status='approved' WHERE id = ?");
    if ($update) {
        $update->bind_param("i", $id);
        $update->execute();
        $update->close();
    }

    $message = "Job approved and visible to alumni members.";
    if ($job && !empty($job["email"])) {
        $mailSent = alumnixSendJobApprovalNotice($job["full_name"], $job["email"], $job["title"], $job["company"]);
        $message = $mailSent
            ? "Job approved and submitter notified by email."
            : "Job approved, but notification email failed. " . alumnixLastMailError();
    }

    adminSetFlash(strpos($message, "failed") !== false ? "warning" : "success", $message);
    header("Location: jobs.php");
    exit();
}

if (isset($_GET["reject"])) {
    $id = (int) $_GET["reject"];
    $stmt = $conn->prepare("UPDATE jobs SET status='rejected' WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    adminSetFlash("warning", "Job marked as rejected.");
    header("Location: jobs.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    adminSetFlash("success", "Job deleted permanently.");
    header("Location: jobs.php");
    exit();
}

$flash = adminPullFlash();
$stats = [
    "pending" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='pending'"),
    "approved" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='approved'"),
    "rejected" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='rejected'"),
    "total" => adminCount($conn, "SELECT COUNT(*) FROM jobs"),
];

$jobs = adminRows(
    $conn,
    "SELECT
        jobs.*,
        alumni_users.full_name,
        alumni_users.email
     FROM jobs
     LEFT JOIN alumni_users ON jobs.alumni_id = alumni_users.id
     ORDER BY
        CASE jobs.status
            WHEN 'pending' THEN 0
            WHEN 'approved' THEN 1
            WHEN 'rejected' THEN 2
            ELSE 3
        END,
        jobs.id DESC"
);

adminRenderPageStart([
    "page_title" => "Job Moderation | AlumniX Admin",
    "hero_badge" => "Opportunity pipeline",
    "hero_title" => "Moderate jobs with less clutter and better hierarchy.",
    "hero_text" => "Owner identity now resolves from the live alumni table, while approvals, rejections, and deletions stay one click away in a tighter review surface.",
    "active" => "jobs",
    "actions" => [
        ["href" => "view_applications.php", "icon" => "fas fa-file-lines", "label" => "View Applications", "variant" => "secondary"],
        ["href" => "event.php", "icon" => "fas fa-calendar-days", "label" => "Manage Events", "variant" => "primary"],
    ],
]);
?>

<?php adminRenderFlash($flash); ?>

<section class="metric-grid">
    <article class="metric-card">
        <div class="metric-label">Pending Jobs</div>
        <span class="metric-value"><?php echo number_format($stats["pending"]); ?></span>
        <div class="metric-note">Roles waiting to be checked before they become visible to alumni.</div>
        <div class="metric-icon"><i class="fas fa-briefcase"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Approved Jobs</div>
        <span class="metric-value"><?php echo number_format($stats["approved"]); ?></span>
        <div class="metric-note">Current public opportunities already available on the career board.</div>
        <div class="metric-icon"><i class="fas fa-circle-check"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Rejected Jobs</div>
        <span class="metric-value"><?php echo number_format($stats["rejected"]); ?></span>
        <div class="metric-note">Posts removed from the public pipeline due to quality or relevance issues.</div>
        <div class="metric-icon"><i class="fas fa-ban"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Total Jobs</div>
        <span class="metric-value"><?php echo number_format($stats["total"]); ?></span>
        <div class="metric-note">Overall job entries currently stored in the moderation queue.</div>
        <div class="metric-icon"><i class="fas fa-layer-group"></i></div>
    </article>
</section>

<section class="panel-card">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Job Review Table</h2>
            <p class="panel-copy">Every row shows role details, owner identity, live status, and the moderation controls that matter.</p>
        </div>
        <a class="panel-link" href="view_applications.php">Open applications tracker</a>
    </div>

    <?php if ($jobs): ?>
        <div class="data-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Apply</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                        <?php
                        $status = strtolower((string) ($job["status"] ?: "pending"));
                        $owner = $job["full_name"] ?: ($job["email"] ?: "Unknown alumni");
                        $description = trim((string) ($job["description"] ?? ""));
                        if (strlen($description) > 120) {
                            $description = substr($description, 0, 117) . "...";
                        }
                        ?>
                        <tr>
                            <td class="primary-cell">
                                <strong><?php echo adminE($job["title"]); ?></strong>
                                <span><?php echo adminE($job["company"] ?: "Unknown company"); ?> · <?php echo adminE($description ?: "No description provided."); ?></span>
                            </td>
                            <td><?php echo adminE($owner); ?></td>
                            <td><span class="status-badge <?php echo adminStatusClass($status); ?>"><?php echo adminE($status); ?></span></td>
                            <td><?php echo adminE($job["location"] ?: "Flexible"); ?></td>
                            <td>
                                <?php if (!empty($job["apply_link"])): ?>
                                    <a class="small-btn secondary" href="<?php echo adminE($job["apply_link"]); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-arrow-up-right-from-square"></i> Open
                                    </a>
                                <?php else: ?>
                                    <span class="status-badge status-neutral">Internal</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <?php if ($status !== "approved"): ?>
                                        <a href="?approve=<?php echo (int) $job["id"]; ?>" class="action-pill approve" onclick="return confirm('Approve this job post?');">Approve</a>
                                    <?php endif; ?>
                                    <?php if ($status !== "rejected"): ?>
                                        <a href="?reject=<?php echo (int) $job["id"]; ?>" class="action-pill reject" onclick="return confirm('Reject this job post?');">Reject</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo (int) $job["id"]; ?>" class="action-pill delete" onclick="return confirm('Delete this job permanently?');">Delete</a>
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
            <p>No jobs have been submitted yet.</p>
        </div>
    <?php endif; ?>
</section>

<?php adminRenderPageEnd(); ?>
