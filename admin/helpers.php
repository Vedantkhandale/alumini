<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/account_mail.php";

function adminOnly(): void
{
    if (!isset($_SESSION["admin"])) {
        header("Location: admin_login.php?error=unauthorized");
        exit();
    }
}

function adminE($value): string
{
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
}

function adminCount(mysqli $conn, string $sql): int
{
    $result = $conn->query($sql);
    if (!($result instanceof mysqli_result)) {
        return 0;
    }

    $row = $result->fetch_row();
    $result->free();

    return (int) ($row[0] ?? 0);
}

function adminRows(mysqli $conn, string $sql): array
{
    $rows = [];
    $result = $conn->query($sql);
    if ($result instanceof mysqli_result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $result->free();
    }

    return $rows;
}

function adminSetFlash(string $type, string $message, array $meta = []): void
{
    $_SESSION["admin_flash"] = array_merge([
        "type" => $type,
        "message" => $message,
    ], $meta);
}

function adminPullFlash(): ?array
{
    if (!isset($_SESSION["admin_flash"])) {
        return null;
    }

    $flash = $_SESSION["admin_flash"];
    unset($_SESSION["admin_flash"]);

    return is_array($flash) ? $flash : null;
}

function adminSaveMailOutbox(array $payload): void
{
    $outboxDir = __DIR__ . "/../uploads/mail_outbox";
    if (!is_dir($outboxDir)) {
        mkdir($outboxDir, 0775, true);
    }

    $line = json_encode(array_merge([
        "created_at" => date("Y-m-d H:i:s"),
    ], $payload), JSON_UNESCAPED_SLASHES);

    if ($line !== false) {
        file_put_contents($outboxDir . "/approval_credentials.log", $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function adminStatusClass(string $status): string
{
    $status = strtolower(trim($status));
    if ($status === "approved" || $status === "active") {
        return "status-approved";
    }
    if ($status === "rejected") {
        return "status-rejected";
    }
    if ($status === "pending") {
        return "status-pending";
    }

    return "status-neutral";
}

function adminCurrentAdminName(): string
{
    $name = trim((string) ($_SESSION["admin"] ?? "Administrator"));
    return $name !== "" ? $name : "Administrator";
}

function adminInitials(string $value): string
{
    $parts = preg_split('/\s+/', trim($value)) ?: [];
    $letters = "";

    foreach ($parts as $part) {
        if ($part === "") {
            continue;
        }

        $letters .= strtoupper(substr($part, 0, 1));
        if (strlen($letters) >= 2) {
            break;
        }
    }

    return $letters !== "" ? $letters : "AX";
}

function adminSidebarItems(): array
{
    return [
        ["key" => "dashboard", "href" => "admin_dashboard.php", "icon" => "fas fa-table-cells-large", "label" => "Dashboard"],
        ["key" => "alumni", "href" => "alumni_list.php", "icon" => "fas fa-user-graduate", "label" => "Alumni"],
        ["key" => "jobs", "href" => "jobs.php", "icon" => "fas fa-briefcase", "label" => "Jobs"],
        ["key" => "applications", "href" => "view_applications.php", "icon" => "fas fa-file-lines", "label" => "Applications"],
        ["key" => "events", "href" => "event.php", "icon" => "fas fa-calendar-days", "label" => "Events"],
        ["key" => "logout", "href" => "logout.php", "icon" => "fas fa-power-off", "label" => "Logout", "logout" => true],
    ];
}

function adminRenderPageStart(array $config): void
{
    $pageTitle = adminE($config["page_title"] ?? "Admin Workspace | AlumniX");
    $heroBadge = adminE($config["hero_badge"] ?? "Admin Workspace");
    $heroTitle = adminE($config["hero_title"] ?? "Control room");
    $heroText = adminE($config["hero_text"] ?? "");
    $active = (string) ($config["active"] ?? "dashboard");
    $actions = is_array($config["actions"] ?? null) ? $config["actions"] : [];
    $adminName = adminCurrentAdminName();
    $initials = adminInitials($adminName);

    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo "<title>{$pageTitle}</title>";
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">';
    echo '<link rel="stylesheet" href="admin_theme.css">';
    echo '</head>';
    echo '<body class="admin-theme">';
    echo '<div class="admin-shell">';
    echo '<aside class="admin-sidebar">';
    echo '<div class="brand-lockup">';
    echo '<div class="brand-mark"><i class="fas fa-graduation-cap"></i></div>';
    echo '<div class="brand-copy"><strong>AlumniX</strong><span>Admin Workspace</span></div>';
    echo '</div>';
    echo '<div class="admin-profile">';
    echo '<div class="profile-mark">' . adminE($initials) . '</div>';
    echo '<div class="profile-copy"><strong>' . adminE($adminName) . '</strong><span>Administrator</span></div>';
    echo '</div>';
    echo '<nav class="sidebar-nav">';

    foreach (adminSidebarItems() as $item) {
        $isActive = $active === $item["key"];
        $classes = "sidebar-link" . ($isActive ? " is-active" : "") . (!empty($item["logout"]) ? " logout" : "");
        echo '<a class="' . adminE($classes) . '" href="' . adminE($item["href"]) . '">';
        echo '<i class="' . adminE($item["icon"]) . '"></i>';
        echo '<span>' . adminE($item["label"]) . '</span>';
        echo '</a>';
    }

    echo '</nav>';
    echo '<div class="sidebar-foot">';
    echo '<strong>Fast moderation flow</strong>';
    echo '<p>High-contrast workspace with tighter spacing, sharper hierarchy, and cleaner review paths across admin tools.</p>';
    echo '</div>';
    echo '</aside>';
    echo '<main class="admin-main">';
    echo '<header class="workspace-hero">';
    echo '<div class="hero-copy">';
    echo '<div class="hero-badge"><i class="fas fa-bolt"></i> ' . $heroBadge . '</div>';
    echo '<h1 class="hero-title">' . $heroTitle . '</h1>';
    if ($heroText !== "") {
        echo '<p class="hero-text">' . $heroText . '</p>';
    }
    echo '</div>';
    echo '<div class="hero-actions">';

    foreach ($actions as $action) {
        if (!is_array($action) || empty($action["label"])) {
            continue;
        }

        $variant = ($action["variant"] ?? "secondary") === "primary" ? "primary" : "secondary";
        $icon = !empty($action["icon"]) ? '<i class="' . adminE((string) $action["icon"]) . '"></i>' : "";
        echo '<a class="action-btn ' . adminE($variant) . '" href="' . adminE((string) ($action["href"] ?? "#")) . '">';
        echo $icon . '<span>' . adminE((string) $action["label"]) . '</span>';
        echo '</a>';
    }

    echo '</div>';
    echo '</header>';
}

function adminRenderPageEnd(): void
{
    echo '</main>';
    echo '</div>';
    echo '</body>';
    echo '</html>';
}

function adminRenderFlash(?array $flash): void
{
    if (!$flash) {
        return;
    }

    $type = strtolower((string) ($flash["type"] ?? "success"));
    $class = $type === "error"
        ? "flash-error"
        : ($type === "warning" ? "flash-warning" : "flash-success");

    echo '<section class="flash-banner ' . adminE($class) . '">';
    echo '<h3>' . adminE((string) ($flash["message"] ?? "Update complete.")) . '</h3>';

    if (!empty($flash["detail"])) {
        echo '<p>' . adminE((string) $flash["detail"]) . '</p>';
    }

    if (!empty($flash["credential_password"])) {
        echo '<p>SMTP delivery did not complete, so these credentials are shown once and also logged locally.</p>';
        echo '<code>Login: ' . adminE((string) ($flash["credential_email"] ?? "")) . '</code>';
        echo '<code>Password: ' . adminE((string) ($flash["credential_password"] ?? "")) . '</code>';
    }

    if (!empty($flash["mail_error"])) {
        echo '<p><strong>Email delivery issue:</strong> ' . adminE((string) $flash["mail_error"]) . '</p>';
        if (stripos((string) $flash["mail_error"], 'authenticate') !== false) {
            echo '<p>Please verify SMTP credentials and provider settings. For Gmail, use an app password or OAuth credential instead of a regular account password.</p>';
        }
        if (!empty($flash["mail_blocked"])) {
            echo '<p>Email delivery is mandatory for this action, so no status change was saved.</p>';
        } elseif (empty($flash["credential_password"])) {
            echo '<p>Please notify the user manually since the approval email could not be delivered.</p>';
        }
    }

    echo '</section>';
}

function adminBatchLabel(array $row): string
{
    $batch = trim((string) ($row["batch_label"] ?? $row["batch"] ?? ""));
    if ($batch !== "") {
        return $batch;
    }

    $start = trim((string) ($row["batch_start"] ?? ""));
    $end = trim((string) ($row["batch_end"] ?? ""));
    if ($start !== "" && $end !== "") {
        return $start . " - " . $end;
    }
    if ($start !== "") {
        return $start;
    }
    if ($end !== "") {
        return $end;
    }

    return "N/A";
}

function adminGraduationYear(array $row): string
{
    $year = trim((string) ($row["grad_year"] ?? $row["graduation_year"] ?? ""));
    return $year !== "" ? $year : "N/A";
}

function adminFormatDate(?string $value, string $format, string $fallback = "TBA"): string
{
    if (!$value) {
        return $fallback;
    }

    $timestamp = strtotime($value);
    if (!$timestamp) {
        return $fallback;
    }

    return date($format, $timestamp);
}

function timeAgo($timestamp): string
{
    $time = time() - strtotime((string) $timestamp);
    if ($time < 1) {
        return "just now";
    }

    $tokens = [
        31536000 => "year",
        2592000 => "month",
        604800 => "week",
        86400 => "day",
        3600 => "hour",
        60 => "minute",
        1 => "second",
    ];

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) {
            continue;
        }

        $numberOfUnits = (int) floor($time / $unit);
        return $numberOfUnits . " " . $text . ($numberOfUnits > 1 ? "s" : "") . " ago";
    }

    return "just now";
}

function alumnixAdminFetchMember(mysqli $conn, int $memberId): ?array
{
    $stmt = $conn->prepare("SELECT id, full_name, email, status, password FROM alumni_users WHERE id = ? LIMIT 1");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $memberId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $user ?: null;
}

function alumnixGenerateTemporaryPassword(int $length = 10): string
{
    $alphabet = "23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
    $lastIndex = strlen($alphabet) - 1;
    $password = "";

    for ($index = 0; $index < $length; $index++) {
        $password .= $alphabet[random_int(0, $lastIndex)];
    }

    return $password;
}

function alumnixLogModerationMailFailure(string $type, array $user, string $error): void
{
    adminSaveMailOutbox([
        "type" => $type,
        "name" => $user["full_name"] ?? "",
        "email" => $user["email"] ?? "",
        "error" => $error,
    ]);
}

function alumnixApproveUserEngine(mysqli $conn, int $memberId): array
{
    $user = alumnixAdminFetchMember($conn, $memberId);
    if (!$user) {
        return ["ok" => false, "message" => "Member not found."];
    }

    $status = strtolower(trim((string) $user["status"]));
    if (in_array($status, ["approved", "active"], true)) {
        return ["ok" => false, "message" => "Already approved. Use resend access email if the member needs fresh credentials."];
    }

    if (empty($user["email"])) {
        return ["ok" => false, "message" => "Approval stopped because the member email address is missing."];
    }

    $plainPassword = alumnixGenerateTemporaryPassword();
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        return ["ok" => false, "message" => "Unable to generate a secure password."];
    }

    $conn->begin_transaction();

    $update = $conn->prepare("UPDATE alumni_users SET status = 'approved', password = ? WHERE id = ?");
    if (!$update) {
        $conn->rollback();
        return ["ok" => false, "message" => "Update query failed."];
    }
    $update->bind_param("si", $hashedPassword, $memberId);

    $saved = $update->execute();
    $update->close();

    if (!$saved) {
        $conn->rollback();
        return ["ok" => false, "message" => "Database update failed."];
    }

    $mailSent = alumnixSendApprovalCredentials($user["full_name"], $user["email"], $plainPassword);

    if (!$mailSent) {
        $conn->rollback();
        alumnixLogModerationMailFailure("approval_notification", $user, alumnixLastMailError());

        return [
            "ok" => false,
            "mail_blocked" => true,
            "message" => "Approval stopped because the access email could not be delivered.",
            "mail_error" => alumnixLastMailError(),
        ];
    }

    $conn->commit();

    return [
        "ok" => true,
        "mail_sent" => true,
        "message" => "Approved and credentials emailed automatically.",
        "name" => $user["full_name"],
        "email" => $user["email"],
    ];
}

function alumnixResendApprovalEmailEngine(mysqli $conn, int $memberId): array
{
    $user = alumnixAdminFetchMember($conn, $memberId);
    if (!$user) {
        return ["ok" => false, "message" => "Member not found."];
    }

    $status = strtolower(trim((string) $user["status"]));
    if (!in_array($status, ["approved", "active"], true)) {
        return ["ok" => false, "message" => "Only approved members can receive a fresh access email."];
    }

    if (empty($user["email"])) {
        return ["ok" => false, "message" => "Resend stopped because the member email address is missing."];
    }

    $plainPassword = alumnixGenerateTemporaryPassword();
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        return ["ok" => false, "message" => "Unable to generate a secure password."];
    }

    $conn->begin_transaction();
    $update = $conn->prepare("UPDATE alumni_users SET password = ? WHERE id = ?");
    if (!$update) {
        $conn->rollback();
        return ["ok" => false, "message" => "Update query failed."];
    }

    $update->bind_param("si", $hashedPassword, $memberId);
    $saved = $update->execute();
    $update->close();

    if (!$saved) {
        $conn->rollback();
        return ["ok" => false, "message" => "Database update failed."];
    }

    if (!alumnixSendApprovalCredentials($user["full_name"], $user["email"], $plainPassword)) {
        $conn->rollback();
        alumnixLogModerationMailFailure("approval_resend", $user, alumnixLastMailError());

        return [
            "ok" => false,
            "mail_blocked" => true,
            "message" => "Fresh access email could not be delivered, so the existing password was kept unchanged.",
            "mail_error" => alumnixLastMailError(),
        ];
    }

    $conn->commit();

    return [
        "ok" => true,
        "message" => "Fresh access email sent successfully. The member now has a new temporary password in that email.",
    ];
}

function alumnixRejectUserEngine(mysqli $conn, int $memberId): array
{
    $user = alumnixAdminFetchMember($conn, $memberId);
    if (!$user) {
        return ["ok" => false, "message" => "Member not found."];
    }

    $status = strtolower(trim((string) $user["status"]));
    if ($status === "rejected") {
        return ["ok" => false, "message" => "Already rejected. Use resend rejection email if needed."];
    }

    if (empty($user["email"])) {
        return ["ok" => false, "message" => "Rejection stopped because the member email address is missing."];
    }

    $conn->begin_transaction();
    $update = $conn->prepare("UPDATE alumni_users SET status = 'rejected' WHERE id = ?");
    if (!$update) {
        $conn->rollback();
        return ["ok" => false, "message" => "Update query failed."];
    }

    $update->bind_param("i", $memberId);
    $saved = $update->execute();
    $update->close();

    if (!$saved) {
        $conn->rollback();
        return ["ok" => false, "message" => "Database update failed."];
    }

    if (!alumnixSendRejectionNotice($user["full_name"], $user["email"])) {
        $conn->rollback();
        alumnixLogModerationMailFailure("account_rejection", $user, alumnixLastMailError());

        return [
            "ok" => false,
            "mail_blocked" => true,
            "message" => "Rejection stopped because the rejection email could not be delivered.",
            "mail_error" => alumnixLastMailError(),
        ];
    }

    $conn->commit();

    return [
        "ok" => true,
        "message" => "Member rejected and rejection email sent automatically.",
    ];
}

function alumnixResendRejectionEmailEngine(mysqli $conn, int $memberId): array
{
    $user = alumnixAdminFetchMember($conn, $memberId);
    if (!$user) {
        return ["ok" => false, "message" => "Member not found."];
    }

    $status = strtolower(trim((string) $user["status"]));
    if ($status !== "rejected") {
        return ["ok" => false, "message" => "Only rejected members can receive the rejection email again."];
    }

    if (empty($user["email"])) {
        return ["ok" => false, "message" => "Resend stopped because the member email address is missing."];
    }

    if (!alumnixSendRejectionNotice($user["full_name"], $user["email"])) {
        alumnixLogModerationMailFailure("rejection_resend", $user, alumnixLastMailError());

        return [
            "ok" => false,
            "message" => "Rejection email could not be delivered.",
            "mail_error" => alumnixLastMailError(),
        ];
    }

    return [
        "ok" => true,
        "message" => "Rejection email sent again successfully.",
    ];
}
