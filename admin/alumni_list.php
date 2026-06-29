<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["approve"])) {
    $id = (int) $_GET["approve"];
    $result = alumnixApproveUserEngine($conn, $id);
    $meta = [
        "mail_error" => $result["mail_error"] ?? "",
        "mail_blocked" => !empty($result["mail_blocked"]),
    ];
    $flashType = $result["ok"] ? "success" : "error";
    adminSetFlash($flashType, $result["message"], $meta);
    header("Location: alumni_list.php");
    exit();
}

if (isset($_GET["reject"])) {
    $id = (int) $_GET["reject"];
    $result = alumnixRejectUserEngine($conn, $id);
    adminSetFlash($result["ok"] ? "success" : "error", $result["message"], [
        "mail_error" => $result["mail_error"] ?? "",
        "mail_blocked" => !empty($result["mail_blocked"]),
    ]);
    header("Location: alumni_list.php");
    exit();
}

if (isset($_GET["resend_approval"])) {
    $id = (int) $_GET["resend_approval"];
    $result = alumnixResendApprovalEmailEngine($conn, $id);
    adminSetFlash($result["ok"] ? "success" : "error", $result["message"], [
        "mail_error" => $result["mail_error"] ?? "",
        "mail_blocked" => !empty($result["mail_blocked"]),
    ]);
    header("Location: alumni_list.php");
    exit();
}

if (isset($_GET["resend_rejection"])) {
    $id = (int) $_GET["resend_rejection"];
    $result = alumnixResendRejectionEmailEngine($conn, $id);
    adminSetFlash($result["ok"] ? "success" : "error", $result["message"], [
        "mail_error" => $result["mail_error"] ?? "",
    ]);
    header("Location: alumni_list.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM alumni_users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    adminSetFlash("success", "Member record deleted.");
    header("Location: alumni_list.php");
    exit();
}

$flash = adminPullFlash();
$stats = [
    "pending" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni' AND status='pending'"),
    "approved" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni' AND status IN ('approved', 'active')"),
    "rejected" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni' AND status='rejected'"),
    "total" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni'"),
];

$alumniUsers = adminRows(
    $conn,
    "SELECT
        id,
        full_name,
        email,
        student_id,
        batch,
        batch_start,
        batch_end,
        grad_year,
        company,
        status,
        created_at
     FROM alumni_users
     WHERE role='alumni'
     ORDER BY
        CASE status
            WHEN 'pending' THEN 0
            WHEN 'approved' THEN 1
            WHEN 'active' THEN 1
            WHEN 'rejected' THEN 2
            ELSE 3
        END,
        id DESC"
);

adminRenderPageStart([
    "page_title" => "Alumni Moderation | AlumniX Admin",
    "hero_badge" => "Member approvals",
    "hero_title" => "Tighter alumni review with cleaner alignment.",
    "hero_text" => "Every member request now reads from the live alumni table, with sharper status visibility, mandatory approval emails, rejection notices, and resend controls in one place.",
    "active" => "alumni",
    "actions" => [
        ["href" => "admin_dashboard.php", "icon" => "fas fa-table-cells-large", "label" => "Dashboard", "variant" => "secondary"],
        ["href" => "jobs.php", "icon" => "fas fa-briefcase", "label" => "Moderate Jobs", "variant" => "primary"],
    ],
]);
?>

<?php adminRenderFlash($flash); ?>

<section class="metric-grid">
    <article class="metric-card">
        <div class="metric-label">Pending</div>
        <span class="metric-value"><?php echo number_format($stats["pending"]); ?></span>
        <div class="metric-note">Requests still waiting for approval and credential generation.</div>
        <div class="metric-icon"><i class="fas fa-user-clock"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Approved</div>
        <span class="metric-value"><?php echo number_format($stats["approved"]); ?></span>
        <div class="metric-note">Alumni members who can already access the member side.</div>
        <div class="metric-icon"><i class="fas fa-user-check"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Rejected</div>
        <span class="metric-value"><?php echo number_format($stats["rejected"]); ?></span>
        <div class="metric-note">Profiles intentionally blocked from going live.</div>
        <div class="metric-icon"><i class="fas fa-user-xmark"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Total Alumni</div>
        <span class="metric-value"><?php echo number_format($stats["total"]); ?></span>
        <div class="metric-note">Overall alumni records currently available in `alumni_users`.</div>
        <div class="metric-icon"><i class="fas fa-users"></i></div>
    </article>
</section>

<section class="panel-card">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Alumni Queue</h2>
            <p class="panel-copy">Profile identity, batch data, graduation year, company info, and moderation actions stay aligned in a single review table, including fresh access-email resend support.</p>
        </div>
        <span class="panel-link"><?php echo number_format(count($alumniUsers)); ?> row(s)</span>
    </div>

    <?php if ($alumniUsers): ?>
        <div class="data-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Alumni</th>
                        <th>Batch</th>
                        <th>Status</th>
                        <th>Company</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumniUsers as $user): ?>
                        <?php $status = strtolower((string) ($user["status"] ?: "pending")); ?>
                        <tr>
                            <td class="primary-cell">
                                <strong><?php echo adminE($user["full_name"]); ?></strong>
                                <span><?php echo adminE($user["email"]); ?></span>
                                <div class="chip-row" style="margin-top: 12px;">
                                    <span class="chip"><i class="fas fa-id-card"></i> <?php echo adminE($user["student_id"] ?: "No student ID"); ?></span>
                                    <span class="chip"><i class="fas fa-calendar"></i> Grad <?php echo adminE(adminGraduationYear($user)); ?></span>
                                </div>
                            </td>
                            <td><?php echo adminE(adminBatchLabel($user)); ?></td>
                            <td><span class="status-badge <?php echo adminStatusClass($status); ?>"><?php echo adminE($status); ?></span></td>
                            <td><?php echo adminE($user["company"] ?: "Not added"); ?></td>
                            <td><?php echo adminE(adminFormatDate((string) $user["created_at"], "d M Y", "Recently")); ?></td>
                            <td>
                                <div class="row-actions">
                                    <?php if (!in_array($status, ["approved", "active"], true)): ?>
                                        <a href="?approve=<?php echo (int) $user["id"]; ?>" class="action-pill approve" onclick="return confirm('Approve this member? A login email with a new password must be delivered before approval is saved.');">Approve</a>
                                    <?php else: ?>
                                        <a href="?resend_approval=<?php echo (int) $user["id"]; ?>" class="action-pill ghost" onclick="return confirm('Send a fresh access email? This will reset the member password to a new temporary one.');">Resend Access Mail</a>
                                    <?php endif; ?>
                                    <?php if ($status !== "rejected"): ?>
                                        <a href="?reject=<?php echo (int) $user["id"]; ?>" class="action-pill reject" onclick="return confirm('Reject this member? A rejection email will be sent automatically.');">Reject</a>
                                    <?php else: ?>
                                        <a href="?resend_rejection=<?php echo (int) $user["id"]; ?>" class="action-pill ghost" onclick="return confirm('Resend the rejection email to this member?');">Resend Reject Mail</a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo (int) $user["id"]; ?>" class="action-pill delete" onclick="return confirm('Delete this member permanently?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <p>No alumni members found yet.</p>
        </div>
    <?php endif; ?>
</section>

<?php adminRenderPageEnd(); ?>
