<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// include DB connection (search parent includes)
$db_paths = ["../includes/db.php", "includes/db.php", "../../includes/db.php", "db.php"];
$connected = false;
foreach ($db_paths as $p) {
    if (file_exists(__DIR__ . '/' . $p)) {
        include_once(__DIR__ . '/' . $p);
        $connected = true;
        break;
    }
    // try absolute path
    if (file_exists(__DIR__ . '/../' . $p)) {
        include_once(__DIR__ . '/../' . $p);
        $connected = true;
        break;
    }
}

if (!$connected) {
    die('Database configuration not found.');
}

// RSVP handling (alumni must be logged in)
if (isset($_GET['rsvp']) && is_numeric($_GET['rsvp'])) {
    if (!isset($_SESSION['user'])) {
        header('Location: ../login.php');
        exit;
    }
    $alumni_id = (int) $_SESSION['user']['id'];
    $event_id = (int) $_GET['rsvp'];
    $check = $conn->query("SELECT * FROM event_applications WHERE event_id='$event_id' AND alumni_id='$alumni_id'");
    if ($check && $check->num_rows == 0) {
        $conn->query("INSERT INTO event_applications (event_id, alumni_id) VALUES ('$event_id', '$alumni_id')");
        header('Location: events_clean.php?msg=applied');
        exit;
    } else {
        header('Location: events_clean.php?msg=exists');
        exit;
    }
}

// Fetch events from DB (admin-managed table)
$events_res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Events — AlumniX</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#ff3b3b;--bg:#f7fafc;--card:#fff}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);margin:0;padding:36px}
        .wrap{max-width:1200px;margin:0 auto}
        .head{display:flex;justify-content:space-between;align-items:end;margin-bottom:20px}
        .title{font-weight:800;font-size:28px}
        .controls{display:flex;gap:8px;align-items:center}
        .btn{background:#fff;border:1px solid #eee;padding:8px 12px;border-radius:10px;cursor:pointer}
        .btn.primary{background:var(--primary);color:#fff;border:none}
        .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
        .event-card{background:var(--card);border-radius:14px;padding:12px;border:1px solid #eee;box-shadow:0 12px 30px rgba(0,0,0,0.04);transition:transform .28s}
        .event-card:hover{transform:translateY(-6px)}
        .media{width:100%;aspect-ratio:16/9;border-radius:10px;overflow:hidden;background:#f3f4f6}
        .media img{width:100%;height:100%;object-fit:cover}
        .meta{display:flex;justify-content:space-between;align-items:center;margin-top:10px}
        .meta .when{font-weight:800;color:var(--primary)}
        .meta .where{color:#64748b}
        .desc{margin-top:8px;color:#475569;font-size:14px}
        .actions{display:flex;gap:8px;margin-top:12px}
        .actions a{padding:10px 12px;border-radius:10px;text-decoration:none}
        .actions .link{background:#000;color:#fff}
        .actions .rsvp{background:#fff;border:1px solid #eee;color:#0f172a}
        @media(max-width:1100px){.grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:700px){.grid{grid-template-columns:1fr}.head{flex-direction:column;align-items:flex-start;gap:12px}}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="head">
            <div>
                <div class="title">Events</div>
                <div style="color:#64748b">Official events from admin and community</div>
            </div>
            <div class="controls">
                <button id="filterAll" class="btn">All</button>
                <button id="filterAdmin" class="btn">Admin</button>
                <button id="filterAlumni" class="btn">Alumni</button>
                <a href="post_event.php" class="btn primary">Post Event</a>
            </div>
        </div>

        <div class="grid" id="eventsGrid">
            <?php if ($events_res && $events_res->num_rows > 0): while($row = $events_res->fetch_assoc()):
                $eDate = strtotime($row['event_date'] ?? '');
                $time = !empty($row['event_time']) ? date('h:i A', strtotime($row['event_time'])) : 'TBA';
                $loc = !empty($row['location']) ? htmlspecialchars($row['location']) : 'Online';
                $img = !empty($row['image']) && file_exists(__DIR__ . '/uploads/events/' . $row['image']) ? 'uploads/events/'.$row['image'] : 'https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=800&q=80';
                $source = $row['posted_by'] ?? $row['source'] ?? $row['created_by'] ?? 'admin';
            ?>
            <div class="event-card" data-source="<?php echo htmlspecialchars($source) ?>">
                <div class="media"><img src="<?php echo $img ?>" alt=""></div>
                <div class="meta">
                    <div class="when"><?php echo date('d M, Y', $eDate) ?> <?php echo $time ?></div>
                    <div class="where"><?php echo $loc ?></div>
                </div>
                <h3 style="margin:8px 0"><?php echo htmlspecialchars($row['title'] ?? $row['event_name'] ?? 'Untitled') ?></h3>
                <div class="desc"><?php echo htmlspecialchars(substr($row['description'] ?? '',0,140)) ?><?php echo (strlen($row['description'] ?? '')>140? '...':'') ?></div>
                <div class="actions">
                    <a href="event_details.php?id=<?php echo $row['id'] ?>" class="link">Details</a>
                    <a href="?rsvp=<?php echo $row['id'] ?>" class="rsvp">RSVP</a>
                </div>
                <div style="margin-top:8px;font-size:12px;color:#94a3b8;font-weight:700">Source: <?php echo ucfirst(htmlspecialchars($source)) ?></div>
            </div>
            <?php endwhile; else: ?>
                <div style="grid-column:1/-1;text-align:center;color:#64748b;padding:40px;background:#fff;border-radius:10px">No events found.</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // JS: filters + animations
        document.addEventListener('DOMContentLoaded', function(){
            if(window.gsap) gsap.registerPlugin(ScrollTrigger);
            if(window.gsap) gsap.from('.event-card',{y:40,opacity:0,duration:0.8,stagger:0.08});

            const grid = document.getElementById('eventsGrid');
            const cards = Array.from(grid.querySelectorAll('.event-card'));
            function applyFilter(source){
                cards.forEach(c=>{
                    if(source==='all' || (c.dataset.source && c.dataset.source.toLowerCase()===source)) c.style.display='block'; else c.style.display='none';
                });
            }
            document.getElementById('filterAll').addEventListener('click', ()=>applyFilter('all'));
            document.getElementById('filterAdmin').addEventListener('click', ()=>applyFilter('admin'));
            document.getElementById('filterAlumni').addEventListener('click', ()=>applyFilter('alumni'));

            // show messages
            const params = new URLSearchParams(location.search);
            if(params.get('msg')==='applied') Swal.fire('Success','You have successfully registered for the event.','success');
            if(params.get('msg')==='exists') Swal.fire('Info','You already registered for this event.','info');
        });
    </script>
</body>
</html>
