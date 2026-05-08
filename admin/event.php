<?php
// Note: Is file ko 'event.php' ke naam se save karein.
require_once __DIR__ . "/helpers.php";
adminOnly();

// --- 🛠️ EVENT LOGIC ---
if(isset($_POST['add'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $date = $_POST['date'];
    $location = mysqli_real_escape_string($conn, $_POST['location'] ?? 'Campus Hall');

    $conn->query("INSERT INTO events (title, description, event_date, location) 
                  VALUES ('$title', '$desc', '$date', '$location')");
    header("Location: event.php?msg=created");
    exit();
}

// Delete Logic
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM events WHERE id=$id");
    header("Location: event.php?msg=deleted");
    exit();
}

$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Manager | AlumniX Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #ff3e3e;
            --dark: #0f172a;
            --bg: #f8fafc;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: var(--dark); }

        /* --- 🏛️ LAYOUT --- */
        .wrapper { display: grid; grid-template-columns: 400px 1fr; min-height: 100vh; }

        /* --- 📝 FORM SIDE --- */
        .form-panel { 
            background: white; padding: 40px; border-right: 1px solid var(--border);
            position: sticky; top: 0; height: 100vh;
        }
        .form-panel h2 { font-weight: 800; margin-bottom: 30px; letter-spacing: -1px; }

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; color: #64748b; }
        .input-group input, .input-group textarea {
            width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--border);
            font-weight: 600; outline: none; transition: 0.3s;
        }
        .input-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(255, 62, 62, 0.1); }

        .submit-btn {
            width: 100%; padding: 15px; background: var(--dark); color: white; border: none;
            border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.3s;
        }
        .submit-btn:hover { background: var(--primary); transform: translateY(-2px); }

        /* --- 📅 DISPLAY SIDE --- */
        .display-panel { padding: 60px; overflow-y: auto; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        
        .event-card {
            background: white; border-radius: 24px; padding: 30px; margin-bottom: 20px;
            border: 1px solid var(--border); display: flex; align-items: center; gap: 25px;
            transition: 0.4s;
        }
        .event-card:hover { transform: scale(1.02); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }

        .date-badge {
            min-width: 80px; height: 80px; background: #fff1f1; border-radius: 20px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: var(--primary); border: 1px solid #fee2e2;
        }
        .date-badge .day { font-size: 1.5rem; font-weight: 800; }
        .date-badge .month { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }

        .event-info { flex-grow: 1; }
        .event-info h3 { font-size: 1.3rem; font-weight: 800; margin-bottom: 5px; }
        .event-info p { color: #64748b; font-size: 0.9rem; line-height: 1.5; margin-bottom: 10px; }
        .event-meta { display: flex; gap: 20px; font-size: 0.8rem; font-weight: 700; color: #94a3b8; }

        .del-btn { color: #cbd5e1; transition: 0.3s; cursor: pointer; border: none; background: none; font-size: 1.1rem; }
        .del-btn:hover { color: var(--primary); }

        @media (max-width: 900px) { .wrapper { grid-template-columns: 1fr; } .form-panel { height: auto; position: relative; } }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- Left: Add Event Form -->
    <aside class="form-panel">
        <a href="admin_dashboard.php" style="text-decoration: none; color: var(--text-dim); font-weight: 700; font-size: 0.8rem; margin-bottom: 20px; display: block;">
            <i class="fas fa-arrow-left"></i> BACK TO CONSOLE
        </a>
        <h2>Create <span>New Event</span></h2>
        
        <form method="POST">
            <div class="input-group">
                <label>Event Title</label>
                <input type="text" name="title" placeholder="e.g. Annual Alumni Meet 2026" required>
            </div>
            <div class="input-group">
                <label>Date & Time</label>
                <input type="date" name="date" required>
            </div>
            <div class="input-group">
                <label>Location</label>
                <input type="text" name="location" placeholder="e.g. Main Auditorium">
            </div>
            <div class="input-group">
                <label>Detailed Description</label>
                <textarea name="desc" rows="5" placeholder="What is this event about?" required></textarea>
            </div>
            <button name="add" class="submit-btn">Publish Event Now</button>
        </form>
    </aside>

    <!-- Right: Event List -->
    <main class="display-panel">
        <div class="header-flex">
            <div>
                <h1 style="font-weight: 800;">Upcoming Events</h1>
                <p style="color: #64748b;">Manage and monitor all active campus events.</p>
            </div>
            <div class="date-badge" style="background: var(--dark); color: white;">
                <span class="day"><?= date('d') ?></span>
                <span class="month"><?= date('M') ?></span>
            </div>
        </div>

        <div class="event-list">
            <?php if($events->num_rows > 0): ?>
                <?php while($row = $events->fetch_assoc()): 
                    $timestamp = strtotime($row['event_date']);
                ?>
                <div class="event-card">
                    <div class="date-badge">
                        <span class="day"><?= date('d', $timestamp) ?></span>
                        <span class="month"><?= date('M', $timestamp) ?></span>
                    </div>
                    <div class="event-info">
                        <h3><?= adminE($row['title']) ?></h3>
                        <p><?= adminE(substr($row['description'], 0, 120)) ?>...</p>
                        <div class="event-meta">
                            <span><i class="fas fa-clock"></i> <?= date('Y', $timestamp) ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?= adminE($row['location'] ?? 'Online') ?></span>
                        </div>
                    </div>
                    <button onclick="confirmDel(<?= $row['id'] ?>)" class="del-btn">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 100px 0; color: #94a3b8;">
                    <i class="far fa-calendar-times" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <h2>No events scheduled yet.</h2>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
    function confirmDel(id) {
        Swal.fire({
            title: 'Delete Event?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e3e',
            cancelButtonColor: '#0f172a',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'event.php?delete=' + id;
            }
        })
    }

    // Success Alerts
    const params = new URLSearchParams(window.location.search);
    if(params.has('msg')){
        Swal.fire({
            icon: 'success',
            title: 'Action Successful',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }
</script>

</body>
</html>