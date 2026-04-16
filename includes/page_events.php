<?php
$pageTitle = "AlumniX | Events";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

$events = fetchRows($conn, "SELECT id, title, description, event_date FROM events ORDER BY event_date ASC");
?>

<style>
    /* Hero Section Enhancements */
    .subpage-hero {
        padding: 140px 20px 80px;
        text-align: center;
        background: radial-gradient(circle at top, rgba(255,59,59,0.08) 0%, transparent 70%);
    }

    .subpage-title {
        font-size: clamp(34px, 6vw, 52px);
        font-weight: 800;
        letter-spacing: -1.5px;
        color: #111;
        margin: 15px 0;
    }

    /* Grid layout */
    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        padding: 50px 0;
    }

    /* Premium Event Card */
    .card-panel {
        background: #fff;
        border-radius: 24px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .card-panel:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.08);
        border-color: rgba(255,59,59,0.15);
    }

    /* Date Badge Style */
    .date-chip {
        width: 65px;
        height: 75px;
        background: #f8f9fa;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid #eee;
        transition: 0.3s;
    }

    .card-panel:hover .date-chip {
        background: #ff3b3b;
        color: #fff;
        border-color: #ff3b3b;
        box-shadow: 0 8px 20px rgba(255, 59, 59, 0.3);
    }

    .date-chip strong {
        font-size: 24px;
        line-height: 1;
        font-weight: 800;
    }

    .date-chip span {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        margin-top: 4px;
    }

    .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 25px;
    }

    .status-tag {
        font-size: 11px;
        font-weight: 800;
        padding: 5px 12px;
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-title {
        font-size: 22px;
        font-weight: 700;
        color: #111;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .card-subtitle {
        font-size: 14px;
        color: #ff3b3b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 15px;
    }

    .card-copy {
        font-size: 15px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    /* Action Link */
    .card-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #111;
        font-weight: 700;
        font-size: 14px;
        transition: 0.2s;
    }

    .card-link i {
        font-size: 12px;
        transition: 0.3s;
    }

    .card-link:hover {
        color: #ff3b3b;
    }

    .card-link:hover i {
        transform: translateX(5px);
    }

    .pill-row .pill {
        background: #fff;
        border: 1px solid #eee;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        color: #444;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
</style>

<div class="public-shell">
    <section class="subpage-hero">
        <span class="section-kicker">Community Calendar</span>
        <h1 class="subpage-title">Events that keep the <br>alumni circle warm.</h1>
        
        <div class="pill-row subpage-meta">
            <span class="pill"><i class="far fa-calendar-check" style="color: #ff3b3b; margin-right: 8px;"></i> <?php echo count($events); ?> Scheduled</span>
            <span class="pill"><i class="fas fa-bolt" style="color: #ff3b3b; margin-right: 8px;"></i> Networking Nights</span>
        </div>
    </section>

    <section class="section" style="max-width: 1200px; margin: auto; padding: 0 20px;">
        <div class="subpage-grid">
            <?php if ($events): ?>
                <?php foreach ($events as $event): ?>
                    <?php $eventDate = !empty($event["event_date"]) ? strtotime($event["event_date"]) : false; ?>
                    
                    <article class="card-panel">
                        <div class="panel-head">
                            <div class="date-chip">
                                <strong><?php echo $eventDate ? date("d", $eventDate) : "--"; ?></strong>
                                <span><?php echo $eventDate ? date("M", $eventDate) : "TBD"; ?></span>
                            </div>
                            <span class="status-tag">Upcoming</span>
                        </div>

                        <div>
                            <h3 class="card-title"><?php echo e($event["title"]); ?></h3>
                            <div class="card-subtitle">
                                <i class="far fa-clock"></i> 
                                <?php echo $eventDate ? e(date("l, d M Y", $eventDate)) : "Date to be announced"; ?>
                            </div>
                            <p class="card-copy"><?php echo e($event["description"] ?: "Join us for an evening of networking and community building."); ?></p>
                        </div>

                        <div class="action-row">
                            <a href="registration.php" class="card-link">
                                RSVP Now <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">No events scheduled. Stay tuned!</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include(__DIR__ . "/footer.php"); ?>