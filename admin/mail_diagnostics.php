<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

$flash = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send_test_mail"])) {
    $targetEmail = trim((string) ($_POST["target_email"] ?? ""));
    $sent = alumnixSendTestEmail($targetEmail, "Admin");
    $flash = [
        "type" => $sent ? "success" : "error",
        "message" => $sent
            ? "Test email sent successfully."
            : "Test email failed.",
        "detail" => $sent
            ? "Inbox and spam folder dono check kar lo."
            : alumnixLastMailError(),
    ];
}

$config = alumnixMailConfig();
$passwordMasked = $config["password"] === ""
    ? "Not configured"
    : str_repeat("*", max(0, strlen($config["password"]) - 4)) . substr($config["password"], -4);
$gmailHint = (stripos($config["host"], "gmail.com") !== false || stripos($config["username"], "@gmail.com") !== false);

adminRenderPageStart([
    "page_title" => "Mail Diagnostics | AlumniX Admin",
    "hero_badge" => "Mail diagnostics",
    "hero_title" => "SMTP status with a live test path.",
    "hero_text" => "Check current mail settings, send a live test email, and catch exact SMTP errors before approving or rejecting any member.",
    "active" => "mail",
    "actions" => [
        ["href" => "alumni_list.php", "icon" => "fas fa-user-graduate", "label" => "Back to Alumni", "variant" => "secondary"],
        ["href" => "admin_dashboard.php", "icon" => "fas fa-table-cells-large", "label" => "Dashboard", "variant" => "primary"],
    ],
]);
?>

<?php adminRenderFlash($flash); ?>

<section class="metric-grid">
    <article class="metric-card">
        <div class="metric-label">SMTP Host</div>
        <span class="metric-value"><?php echo adminE($config["host"] ?: "N/A"); ?></span>
        <div class="metric-note">Current outgoing mail server.</div>
        <div class="metric-icon"><i class="fas fa-server"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Port / Security</div>
        <span class="metric-value"><?php echo adminE((string) $config["port"]); ?> / <?php echo adminE($config["encryption"] ?: "none"); ?></span>
        <div class="metric-note">Transport layer currently configured.</div>
        <div class="metric-icon"><i class="fas fa-shield-halved"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Sender Email</div>
        <span class="metric-value"><?php echo adminE($config["username"] ?: "N/A"); ?></span>
        <div class="metric-note">Authenticated sender account.</div>
        <div class="metric-icon"><i class="fas fa-envelope"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Login Base URL</div>
        <span class="metric-value"><?php echo adminE($config["base_url"] ?: "Auto"); ?></span>
        <div class="metric-note">Used inside approval emails.</div>
        <div class="metric-icon"><i class="fas fa-link"></i></div>
    </article>
</section>

<section class="panel-card">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Send Test Email</h2>
            <p class="panel-copy">Run one live SMTP test with the exact same configuration used for approval and rejection emails.</p>
        </div>
        <span class="panel-link">Live check</span>
    </div>

    <form method="post" class="data-shell" style="display: grid; gap: 18px;">
        <input type="hidden" name="send_test_mail" value="1">
        <div style="display: grid; gap: 10px;">
            <label for="target_email" style="font-weight: 700; color: #0f172a;">Recipient email</label>
            <input
                id="target_email"
                type="email"
                name="target_email"
                required
                placeholder="Enter an inbox you can check now"
                value="<?php echo adminE($config["username"]); ?>"
                style="min-height: 48px; border-radius: 16px; border: 1px solid rgba(15, 23, 42, 0.12); padding: 0 16px; font-size: 14px;"
            >
        </div>
        <div class="row-actions" style="justify-content: flex-start;">
            <button type="submit" class="action-pill approve" style="border: none; cursor: pointer;">Send Test Mail</button>
        </div>
    </form>
</section>

<section class="panel-card">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">Current Diagnostics</h2>
            <p class="panel-copy">These checks help explain the most common Gmail-related failures on this server.</p>
        </div>
        <span class="panel-link">Read-only</span>
    </div>

    <div class="data-shell" style="display: grid; gap: 12px;">
        <div class="record-card" style="grid-template-columns: 1fr;">
            <strong>Masked password</strong>
            <span><?php echo adminE($passwordMasked); ?></span>
        </div>
        <div class="record-card" style="grid-template-columns: 1fr;">
            <strong>Gmail app password hint</strong>
            <span><?php echo $gmailHint ? "If this is Gmail, use a fresh 16-character app password from Google Account security." : "Not a Gmail SMTP account."; ?></span>
        </div>
        <div class="record-card" style="grid-template-columns: 1fr;">
            <strong>Current login URL in mail</strong>
            <span><?php echo adminE(alumnixGetBaseUrl() . '/login.php'); ?></span>
        </div>
    </div>
</section>

<?php adminRenderPageEnd(); ?>
