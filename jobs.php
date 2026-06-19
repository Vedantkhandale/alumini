<?php
$pageTitle = "AlumniX | Career Board";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link, company_logo FROM jobs WHERE status='approved' ORDER BY id DESC");
$jobCount = count($jobs);
$locationCount = count(array_unique(array_filter(array_map(static function ($job) {
    return trim((string) ($job["location"] ?? ""));
}, $jobs))));

if (!function_exists("jobExcerpt")) {
    function jobExcerpt($text, int $limit = 145): string
    {
        $clean = trim(preg_replace("/\s+/", " ", (string) $text));
        if ($clean === "") {
            return "Role details will be shared by the hiring team during the application process.";
        }

        if (function_exists("mb_strimwidth")) {
            return mb_strimwidth($clean, 0, $limit, "...");
        }

        return strlen($clean) > $limit ? substr($clean, 0, $limit - 3) . "..." : $clean;
    }
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    :root {
        --primary: #ff3b3b;
        --ink: #0f172a;
        --muted: #64748b;
        --line: rgba(148, 163, 184, 0.22);
        --soft: #f8fbff;
        --card: #ffffff;
    }

    html,
    body {
        background:
            radial-gradient(circle at 86% 8%, rgba(255, 59, 59, 0.09), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, var(--soft) 100%);
        margin: 0;
        overflow-x: hidden;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .jobs-shell {
        width: min(1240px, calc(100% - 40px));
        margin: 0 auto;
        padding: 132px 0 72px;
        color: var(--ink);
    }

    .jobs-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 390px);
        gap: 30px;
        align-items: end;
        margin-bottom: 28px;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 34px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 59, 59, 0.1);
        color: var(--primary);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 1.6px;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .jobs-title {
        font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(42px, 8vw, 84px);
        line-height: 0.92;
        margin: 0;
        color: var(--ink);
        text-transform: uppercase;
        letter-spacing: 0;
        font-weight: 900;
    }

    .jobs-title span {
        color: var(--primary);
    }

    .jobs-copy {
        max-width: 710px;
        margin: 18px 0 0;
        color: var(--muted);
        font-size: clamp(15px, 1.6vw, 18px);
        line-height: 1.7;
    }

    .job-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .metric-card {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid var(--line);
        border-radius: 22px;
        padding: 18px;
        box-shadow: 0 22px 70px rgba(15, 23, 42, 0.08);
    }

    .metric-card strong {
        display: block;
        font-size: 30px;
        color: var(--primary);
        line-height: 1;
    }

    .metric-card span {
        display: block;
        margin-top: 8px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .job-toolbar {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: center;
        padding: 16px;
        margin-bottom: 22px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.84);
        border: 1px solid var(--line);
        box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
    }

    .search-wrap {
        position: relative;
        min-width: 0;
    }

    .search-wrap i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
    }

    .job-search {
        width: 100%;
        min-height: 50px;
        border: 1px solid var(--line);
        border-radius: 16px;
        padding: 0 16px 0 44px;
        background: #fff;
        color: var(--ink);
        font-size: 14px;
        font-weight: 700;
        outline: none;
        transition: border-color 0.22s ease, box-shadow 0.22s ease;
    }

    .job-search:focus {
        border-color: rgba(255, 59, 59, 0.42);
        box-shadow: 0 0 0 4px rgba(255, 59, 59, 0.1);
    }

    .toolbar-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 50px;
        padding: 0 18px;
        border-radius: 16px;
        background: #0f172a;
        color: #fff;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }

    .toolbar-pill i {
        color: #ff8a7a;
    }

    .jobs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 22px;
    }

    .job-card {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 18px;
        min-height: 100%;
        padding: 22px;
        border-radius: 26px;
        background: var(--card);
        border: 1px solid var(--line);
        box-shadow: 0 22px 70px rgba(15, 23, 42, 0.07);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
        position: relative;
        overflow: hidden;
    }

    .job-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 59, 59, 0.1), transparent 34%);
        opacity: 0;
        transition: opacity 0.28s ease;
        pointer-events: none;
    }

    .job-card:hover {
        transform: translateY(-8px);
        border-color: rgba(255, 59, 59, 0.32);
        box-shadow: 0 34px 90px rgba(255, 59, 59, 0.14);
    }

    .job-card:hover::before {
        opacity: 1;
    }

    .job-top {
        display: grid;
        grid-template-columns: 62px minmax(0, 1fr);
        gap: 14px;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .company-mark {
        width: 62px;
        height: 62px;
        display: grid;
        place-items: center;
        border-radius: 18px;
        background: linear-gradient(135deg, #ff4d4d, #ff8a66);
        color: #fff;
        font-size: 24px;
        font-weight: 900;
        box-shadow: 0 18px 42px rgba(255, 59, 59, 0.2);
        overflow: hidden;
    }

    .company-mark img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .company-name {
        margin: 0 0 7px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        overflow-wrap: anywhere;
    }

    .status-chip {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 7px;
        padding: 7px 10px;
        border-radius: 999px;
        background: rgba(255, 59, 59, 0.1);
        color: var(--primary);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .card-title {
        position: relative;
        z-index: 1;
        margin: 0;
        color: var(--ink);
        font-size: 24px;
        font-weight: 900;
        line-height: 1.18;
        overflow-wrap: anywhere;
    }

    .meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        position: relative;
        z-index: 1;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-height: 36px;
        padding: 8px 11px;
        border-radius: 999px;
        background: #f4f7fb;
        color: var(--muted);
        border: 1px solid rgba(148, 163, 184, 0.14);
        font-size: 12px;
        font-weight: 800;
    }

    .chip i {
        color: var(--primary);
    }

    .job-desc {
        position: relative;
        z-index: 1;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.7;
        margin: 0;
    }

    .btn-apply {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 50px;
        margin-top: auto;
        border-radius: 16px;
        background: var(--ink);
        color: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        transition: background 0.24s ease, transform 0.24s ease;
    }

    .btn-apply:hover {
        background: var(--primary);
        color: #fff;
        transform: translateY(-2px);
    }

    .empty-state {
        grid-column: 1 / -1;
        padding: 42px 22px;
        border-radius: 28px;
        background: #fff;
        border: 1px dashed var(--line);
        text-align: center;
        color: var(--muted);
        font-weight: 800;
    }

    .empty-state.is-hidden {
        display: none;
    }

    @media (max-width: 900px) {
        .jobs-shell {
            width: min(100% - 28px, 760px);
            padding-top: 122px;
        }

        .jobs-hero {
            grid-template-columns: 1fr;
            align-items: start;
        }

        .job-toolbar {
            grid-template-columns: 1fr;
        }

        .toolbar-pill {
            width: 100%;
        }
    }

    @media (max-width: 560px) {
        .jobs-shell {
            width: min(100% - 22px, 520px);
            padding-top: 114px;
        }

        .jobs-title {
            font-size: clamp(38px, 14vw, 58px);
        }

        .job-metrics {
            grid-template-columns: 1fr;
        }

        .jobs-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .job-card {
            padding: 18px;
            border-radius: 22px;
        }

        .job-top {
            grid-template-columns: 54px minmax(0, 1fr);
        }

        .company-mark {
            width: 54px;
            height: 54px;
            border-radius: 16px;
        }
    }
</style>

<main class="jobs-shell">
    <section class="jobs-hero">
        <div>
            <span class="eyebrow reveal-top"><i class="fas fa-briefcase"></i> Opportunity Hub</span>
            <h1 class="jobs-title reveal-top">Career <span>Board</span></h1>
            <p class="jobs-copy reveal-top">Find verified roles shared through the AlumniX network, from startup teams to growing companies and campus hiring partners.</p>
        </div>

        <div class="job-metrics reveal-top" aria-label="Job summary">
            <div class="metric-card">
                <strong><?= number_format($jobCount) ?></strong>
                <span>Live roles</span>
            </div>
            <div class="metric-card">
                <strong><?= number_format($locationCount ?: 1) ?></strong>
                <span>Locations</span>
            </div>
        </div>
    </section>

    <div class="job-toolbar reveal-card">
        <label class="search-wrap" for="jobSearch">
            <i class="fas fa-search"></i>
            <input type="search" id="jobSearch" class="job-search" placeholder="Search title, company, or location">
        </label>
        <div class="toolbar-pill"><i class="fas fa-circle-check"></i> Approved listings</div>
    </div>

    <section class="jobs-grid" id="jobsGrid">
        <?php if ($jobs): ?>
            <?php foreach ($jobs as $job): ?>
                <?php
                $jobLink = !empty($job["apply_link"]) ? (string) $job["apply_link"] : "login.php";
                $external = (bool) preg_match("/^(https?:\/\/|mailto:)/i", $jobLink);
                $company = trim((string) ($job["company"] ?? "")) ?: "AlumniX Partner";
                $title = trim((string) ($job["title"] ?? "")) ?: "Open Position";
                $location = trim((string) ($job["location"] ?? "")) ?: "Remote";
                $logoPath = !empty($job["company_logo"]) ? __DIR__ . "/uploads/logos/" . basename((string) $job["company_logo"]) : "";
                $logoUrl = $logoPath && file_exists($logoPath) ? "uploads/logos/" . rawurlencode(basename((string) $job["company_logo"])) : "";
                $companyInitial = strtoupper(substr($company, 0, 1));
                $searchText = strtolower($title . " " . $company . " " . $location);
                ?>
                <article class="job-card reveal-card" data-search="<?= e($searchText) ?>">
                    <div class="job-top">
                        <div class="company-mark">
                            <?php if ($logoUrl): ?>
                                <img src="<?= e($logoUrl) ?>" alt="<?= e($company) ?> logo" loading="lazy">
                            <?php else: ?>
                                <?= e($companyInitial) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="company-name"><?= e($company) ?></p>
                            <span class="status-chip"><i class="fas fa-check-circle"></i> Verified</span>
                        </div>
                    </div>

                    <h3 class="card-title"><?= e($title) ?></h3>

                    <div class="meta-row">
                        <span class="chip"><i class="fas fa-map-marker-alt"></i><?= e($location) ?></span>
                        <span class="chip"><i class="fas fa-briefcase"></i>Full-Time</span>
                    </div>

                    <p class="job-desc"><?= e(jobExcerpt($job["description"] ?? "")) ?></p>

                    <a href="<?= e($jobLink) ?>" class="btn-apply"<?= $external ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                        View Position <i class="fas fa-arrow-right"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="empty-state<?= $jobs ? ' is-hidden' : '' ?>" id="jobsEmpty">No matching jobs found. Try a different search.</div>
    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const search = document.getElementById("jobSearch");
        const cards = Array.from(document.querySelectorAll(".job-card"));
        const emptyState = document.getElementById("jobsEmpty");

        function filterJobs() {
            const query = (search?.value || "").trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const matches = !query || card.dataset.search.includes(query);
                card.style.display = matches ? "flex" : "none";
                if (matches) visibleCount += 1;
            });

            if (emptyState) {
                emptyState.classList.toggle("is-hidden", visibleCount > 0);
            }
        }

        search?.addEventListener("input", filterJobs);

        if (!window.gsap) return;

        gsap.registerPlugin(ScrollTrigger);

        gsap.from(".reveal-top", {
            y: 32,
            opacity: 0,
            duration: 0.8,
            stagger: 0.08,
            ease: "power3.out"
        });

        gsap.from(".reveal-card", {
            scrollTrigger: {
                trigger: ".jobs-grid",
                start: "top 88%",
                toggleActions: "play none none none"
            },
            y: 34,
            opacity: 0,
            duration: 0.7,
            stagger: 0.08,
            ease: "power3.out"
        });
    });
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
