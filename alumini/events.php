<?php 
include(__DIR__ . "/../includes/header.php"); 
include(__DIR__ . "/../includes/db.php"); 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
:root{--primary:#ff3b3b;--bg-white:#ffffff;--card-bg:#ffffff;--text-main:#000;--text-muted:#555;--border:#eee}
body{background:var(--bg-white);margin:0;padding:0;font-family:'Plus Jakarta Sans',sans-serif;overflow-x:hidden}
.page{padding:80px 6% 60px;position:relative;z-index:10}
.mesh-bg{position:fixed;top:0;left:0;width:100%;height:100%;background:radial-gradient(circle at 10% 10%, rgba(255,59,59,0.05) 0%, transparent 40%);z-index:-1;pointer-events:none}
.page-header{text-align:center;margin-bottom:40px}
.page-title{font-size:clamp(28px,5vw,56px);font-weight:800;color:var(--text-main);text-transform:uppercase;letter-spacing:-1px}
.page-title span{color:var(--primary)}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.event-card{background:var(--card-bg);border:1px solid var(--border);border-radius:18px;padding:12px;box-shadow:0 10px 20px rgba(0,0,0,0.06);transition:transform .28s,box-shadow .28s}
.event-card:hover{transform:translateY(-6px);box-shadow:0 26px 56px rgba(0,0,0,0.08);border-color:var(--primary)}
.img-wrap{width:100%;aspect-ratio:16/10;border-radius:12px;overflow:hidden;background:#f3f4f6}
.img-wrap img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .8s}
.event-card:hover .img-wrap img{transform:scale(1.06)}
.date-badge{position:absolute;top:12px;right:12px;background:var(--primary);color:#fff;padding:8px 12px;border-radius:12px;font-weight:800;font-size:12px}
.content-area{padding:12px}
.meta-info{font-size:12px;color:var(--text-muted);margin-bottom:8px;display:flex;gap:10px}
.event-card h3{font-size:18px;margin:6px 0 12px}
.btn-premium{display:block;width:100%;padding:12px;background:#000;color:#fff;text-align:center;border-radius:12px;font-weight:800;text-decoration:none}
.btn-premium:hover{background:var(--primary)}
@media(max-width:1100px){.grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){.grid{grid-template-columns:1fr}}
</style>

<div class="page">
    <div class="mesh-bg"></div>
    <div class="page-header" style="display:flex;justify-content:space-between;align-items:end;gap:12px">
        <div>
            <h1 class="page-title">Events <span>Calendar</span></h1>
        </div>
        <div style="display:flex;gap:8px;align-items:center">
            <div>
                <button id="ev-filter-all" class="btn-premium" style="background:#fff;color:#000;padding:8px 12px;border-radius:10px;border:1px solid #eee;margin-right:6px">All</button>
                <button id="ev-filter-admin" class="btn-premium" style="background:#fff;color:#000;padding:8px 12px;border-radius:10px;border:1px solid #eee;margin-right:6px">Admin</button>
                <button id="ev-filter-alumni" class="btn-premium" style="background:#fff;color:#000;padding:8px 12px;border-radius:10px;border:1px solid #eee">Alumni</button>
            </div>
            <a href="post_event.php" class="btn-premium" style="background:#000;color:#fff;padding:10px 14px;border-radius:10px">Post Event</a>
        </div>
    </div>

    <div class="grid" id="eventsGrid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $eDate = strtotime($row['event_date']);
                $time = isset($row['event_time']) ? date('h:i A', strtotime($row['event_time'])) : 'TBA';
                $loc = !empty($row['location']) ? $row['location'] : 'Online';
                $defaultImages = [
                    'https://images.unsplash.com/photo-1515169067865-5387ec356754?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=800&q=80'
                ];
                $imgUrl = !empty($row['image']) ? 'uploads/events/'.$row['image'] : $defaultImages[$row['id'] % count($defaultImages)];
        ?>
        <?php $source = $row['posted_by'] ?? $row['source'] ?? $row['created_by'] ?? 'admin'; ?>
        <div class="event-card reveal-card" data-source="<?php echo htmlspecialchars($source) ?>">
            <div style="position:relative">
                <div class="img-wrap"><img src="<?= $imgUrl ?>" alt="Event" loading="lazy"></div>
                <div class="date-badge"><?= date('d M', $eDate) ?></div>
            </div>
            <div class="content-area">
                <div class="meta-info"><span><i class="fas fa-clock"></i> <?= $time ?></span><span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($loc) ?></span></div>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <a href="event_details.php?id=<?= $row['id'] ?>" class="btn-premium">View Details</a>
                <div style="margin-top:8px;font-size:12px;color:#94a3b8;font-weight:700">Source: <?php echo ucfirst(htmlspecialchars($source)); ?></div>
            </div>
        </div>
        <?php }
        } else {
            echo "<h2 style='grid-column:1/-1;text-align:center;color:#999'>No Events Found</h2>";
        }
        ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded',function(){
        gsap.registerPlugin(ScrollTrigger);
        gsap.from('.page-title',{y:-40,opacity:0,duration:0.9,ease:'back.out(1.7)'});
        gsap.from('.reveal-card',{scrollTrigger:{trigger:'.grid',start:'top 85%',toggleActions:'play none none none'},y:50,opacity:0,duration:0.9,stagger:0.12});

        // Event filters
        const grid = document.getElementById('eventsGrid');
        const cards = Array.from(grid.querySelectorAll('.event-card'));
        function applyFilter(source){
            cards.forEach(c=>{
                if(source==='all' || (c.dataset.source && c.dataset.source.toLowerCase()===source)){
                    c.style.display='block';
                } else c.style.display='none';
            });
        }
        document.getElementById('ev-filter-all').addEventListener('click', ()=>applyFilter('all'));
        document.getElementById('ev-filter-admin').addEventListener('click', ()=>applyFilter('admin'));
        document.getElementById('ev-filter-alumni').addEventListener('click', ()=>applyFilter('alumni'));
    });
</script>

<?php include(__DIR__ . "/../includes/footer.php"); ?>