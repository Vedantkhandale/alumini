<?php
$pageTitle = "AlumniX | Events";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

$events = fetchRows($conn, "SELECT * FROM events ORDER BY event_date ASC, id DESC");
$eventCount = count($events);
$nextEventDate = $events && !empty($events[0]["event_date"]) ? strtotime((string) $events[0]["event_date"]) : false;

if (!function_exists("eventExcerpt")) {
    function eventExcerpt($text, int $limit = 130): string
    {
        $clean = trim(preg_replace("/\s+/", " ", (string) $text));
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
            radial-gradient(circle at 12% 8%, rgba(255, 59, 59, 0.08), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, var(--soft) 100%);
        margin: 0;
        overflow-x: hidden;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .events-shell {
        width: min(1240px, calc(100% - 40px));
        margin: 0 auto;
        padding: 132px 0 70px;
        color: var(--ink);
    }

    .events-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 30px;
        align-items: end;
        margin-bottom: 36px;
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

    .events-title {
        font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
        font-size: clamp(42px, 8vw, 82px);
        line-height: 0.92;
        margin: 0;
        color: var(--ink);
        text-transform: uppercase;
        letter-spacing: 0;
        font-weight: 900;
    }

    .events-title span {
        color: var(--primary);
    }

    .events-copy {
        max-width: 700px;
        margin: 18px 0 0;
        color: var(--muted);
        font-size: clamp(15px, 1.6vw, 18px);
        line-height: 1.7;
    }

    .hero-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(130px, 1fr));
        gap: 12px;
        min-width: min(360px, 100%);
    }

    .metric-card {
        background: rgba(255, 255, 255, 0.86);
        border: 1px solid var(--line);
        border-radius: 22px;
        padding: 18px;
        box-shadow: 0 22px 70px rgba(15, 23, 42, 0.08);
    }

    .metric-card strong {
        display: block;
        font-size: 28px;
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

    .events-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px;
        margin-bottom: 20px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.82);
        border: 1px solid var(--line);
        box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
    }

    .toolbar-label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--ink);
        font-weight: 900;
    }

    .toolbar-label i {
        color: var(--primary);
    }

    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(285px, 1fr));
        gap: 22px;
    }

    .event-card {
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 22px 70px rgba(15, 23, 42, 0.07);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .event-card:hover {
        transform: translateY(-8px);
        border-color: rgba(255, 59, 59, 0.32);
        box-shadow: 0 34px 90px rgba(255, 59, 59, 0.14);
    }

    .event-media {
        position: relative;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #e8edf5;
    }

    .event-media img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        transition: transform 0.7s cubic-bezier(0.2, 1, 0.3, 1);
    }

    .event-card:hover .event-media img {
        transform: scale(1.07);
    }

    .date-badge {
        position: absolute;
        left: 16px;
        top: 16px;
        min-width: 70px;
        padding: 10px 12px;
        border-radius: 18px;
        text-align: center;
        background: rgba(15, 23, 42, 0.88);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 20px 40px rgba(2, 6, 23, 0.22);
    }

    .date-badge strong {
        display: block;
        font-size: 24px;
        line-height: 1;
    }

    .date-badge span {
        display: block;
        margin-top: 5px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    .event-body {
        display: flex;
        flex: 1;
        flex-direction: column;
        padding: 22px;
        gap: 16px;
    }

    .event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
    }

    .event-meta span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-width: 0;
        padding: 8px 10px;
        border-radius: 999px;
        background: #f4f7fb;
    }

    .event-meta i {
        color: var(--primary);
    }

    .event-card h3 {
        margin: 0;
        color: var(--ink);
        font-size: 22px;
        line-height: 1.22;
        font-weight: 900;
        overflow-wrap: anywhere;
    }

    .event-description {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.65;
    }

    .event-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 48px;
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

    .event-action:hover {
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

    @media (max-width: 900px) {
        .events-shell {
            width: min(100% - 28px, 760px);
            padding-top: 122px;
        }

        .events-hero {
            grid-template-columns: 1fr;
            align-items: start;
        }

        .hero-metrics {
            width: 100%;
        }

        .events-toolbar {
            align-items: stretch;
            flex-direction: column;
        }
    }

    @media (max-width: 560px) {
        .events-shell {
            width: min(100% - 22px, 520px);
            padding-top: 114px;
        }

        .events-title {
            font-size: clamp(38px, 14vw, 58px);
        }

        .hero-metrics {
            grid-template-columns: 1fr;
        }

        .events-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .event-body {
            padding: 18px;
        }
    }
</style>

<main class="events-shell">
    <section class="events-hero">
        <div>
            <span class="eyebrow reveal-header"><i class="fas fa-calendar-check"></i> Community Calendar</span>
            <h1 class="events-title reveal-header">Summit <span>Events</span></h1>
            <p class="events-copy reveal-header">Discover alumni meetups, career sessions, mentorship circles, and campus experiences built for real networking.</p>
        </div>

        <div class="hero-metrics reveal-header" aria-label="Event summary">
            <div class="metric-card">
                <strong><?= number_format($eventCount) ?></strong>
                <span>Scheduled</span>
            </div>
            <div class="metric-card">
                <strong><?= $nextEventDate ? e(date("d M", $nextEventDate)) : "TBA" ?></strong>
                <span>Next Event</span>
            </div>
        </div>
    </section>

    <div class="events-toolbar reveal-card">
        <div class="toolbar-label"><i class="fas fa-bolt"></i> Upcoming experiences</div>
        <div class="toolbar-label"><i class="fas fa-location-dot"></i> Online and campus ready</div>
    </div>

    <section class="events-grid">
        <?php if ($events): ?>
            <?php
            $defaultImages = [
                "https://images.unsplash.com/photo-1515169067865-5387ec356754?auto=format&fit=crop&w=900&q=80",
                "https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=900&q=80",
                "https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=900&q=80",
            ];
            ?>
            <?php foreach ($events as $index => $event): ?>
                <?php
                $eventDate = !empty($event["event_date"]) ? strtotime((string) $event["event_date"]) : false;
                $time = !empty($event["event_time"]) ? date("h:i A", strtotime((string) $event["event_time"])) : "TBA";
                $location = !empty($event["location"]) ? (string) $event["location"] : "Campus Hub";
                $description = !empty($event["description"]) ? (string) $event["description"] : "Join the AlumniX network for conversations, ideas, and meaningful connections.";
                $imagePath = !empty($event["image"]) ? __DIR__ . "/uploads/events/" . basename((string) $event["image"]) : "";
                $imageUrl = $imagePath && file_exists($imagePath)
                    ? "uploads/events/" . rawurlencode(basename((string) $event["image"]))
                    : $defaultImages[$index % count($defaultImages)];
                ?>
                <article class="event-card reveal-card">
                    <div class="event-media">
                        <img src="<?= e($imageUrl) ?>" alt="<?= e($event["title"] ?? "AlumniX event") ?>" loading="lazy">
                        <div class="date-badge">
                            <strong><?= $eventDate ? e(date("d", $eventDate)) : "--" ?></strong>
                            <span><?= $eventDate ? e(date("M", $eventDate)) : "TBA" ?></span>
                        </div>
                    </div>
                    <div class="event-body">
                        <div class="event-meta">
                            <span><i class="fas fa-clock"></i><?= e($time) ?></span>
                            <span><i class="fas fa-map-marker-alt"></i><?= e($location) ?></span>
                        </div>
                        <h3><?= e($event["title"] ?? "AlumniX Event") ?></h3>
                        <p class="event-description"><?= e(eventExcerpt($description)) ?></p>
                        <a href="registration.php" class="event-action">Reserve Spot <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">No events found. New alumni experiences will show here soon.</div>
        <?php endif; ?>
    </section>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (!window.gsap) return;

        gsap.registerPlugin(ScrollTrigger);

        gsap.from(".reveal-header", {
            y: 32,
            opacity: 0,
            duration: 0.8,
            stagger: 0.08,
            ease: "power3.out"
        });

        gsap.from(".reveal-card", {
            scrollTrigger: {
                trigger: ".events-grid",
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
