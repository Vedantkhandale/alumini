<?php
session_start();
include(__DIR__ . "/../includes/db.php");
$user_id = $_SESSION['user']['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Careers | AlumniX</title>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
	<style>
		:root{--accent:#ec4899;--dark:#0f172a;--bg:#f8fafc;--card:#ffffff}
		body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);margin:0;padding:0}
		.page{padding:56px 6%}
		.page-header{display:flex;justify-content:space-between;align-items:end;margin-bottom:20px}
		.page-title{font-size:28px;font-weight:800}
		.subtitle{color:#64748b;font-weight:600}
		.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
		.job-card{background:var(--card);border-radius:20px;padding:18px;border:1px solid #eef2f6;box-shadow:0 14px 30px rgba(15,23,42,0.04);transition:transform .28s,box-shadow .28s}
		.job-card:hover{transform:translateY(-6px);box-shadow:0 28px 64px rgba(15,23,42,0.08)}
		.job-meta{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:10px}
		.job-title{font-size:18px;font-weight:800;margin:0}
		.job-company{color:var(--accent);font-weight:800}
		.job-desc{color:#475569;font-size:13px;margin:10px 0}
		.job-actions{display:flex;gap:10px;margin-top:12px}
		.btn-apply{flex:1;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#ec4899,#f97316);color:#fff;font-weight:800;cursor:pointer}
		.btn-details{flex:1;padding:12px;border-radius:12px;border:1px solid #eef2f6;background:#fff;color:#0f172a;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}
		.badge-source{margin-top:10px;font-size:12px;color:#94a3b8;font-weight:700}
		@media(max-width:1100px){.grid{grid-template-columns:repeat(2,1fr)}}
		@media(max-width:700px){.grid{grid-template-columns:1fr}.page{padding:30px 5%}}
		.btn-details.small{padding:8px 10px}
		.filters{display:flex;gap:8px}
	</style>
</head>
<body>

<div class="page">
	<div class="page-header">
		<div>
			<div class="page-title">Careers</div>
			<div class="subtitle">All admin-approved jobs are listed below. Alumni job posts are reviewed by admin before they go live.</div>
		</div>
		<div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
			<div class="filters">
				<button id="filter-all" class="btn-details small">All</button>
				<button id="filter-admin" class="btn-details small">Admin</button>
				<button id="filter-alumni" class="btn-details small">Alumni</button>
			</div>
			<a href="my_jobs.php" class="btn-details">My Posts</a>
			<a href="post_jobs.php" class="btn-details">Post a Job</a>
		</div>
	</div>

	<div class="grid" id="jobsGrid">
		<?php
		$jobs = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");
		if ($jobs && $jobs->num_rows > 0) {
			while ($job = $jobs->fetch_assoc()) {
				$logo = !empty($job['logo']) ? 'uploads/logos/'.htmlspecialchars($job['logo']) : 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=800&q=60';
				$location = !empty($job['location']) ? htmlspecialchars($job['location']) : 'Remote / Onsite';
				$snippet = !empty($job['description']) ? (strlen($job['description'])>120 ? htmlspecialchars(substr($job['description'],0,120)).'...' : htmlspecialchars($job['description'])) : '';
				$source = $job['posted_by'] ?? $job['source'] ?? $job['created_by'] ?? 'admin';
		?>
		<div class="job-card reveal-card" data-source="<?php echo htmlspecialchars($source) ?>">
			<div class="job-meta">
				<div style="display:flex;gap:12px;align-items:center">
					<div style="width:56px;height:56px;border-radius:12px;overflow:hidden;background:#f3f4f6;flex:0 0 56px"><img src="<?php echo $logo ?>" style="width:100%;height:100%;object-fit:cover"></div>
					<div>
						<h4 class="job-title"><?php echo htmlspecialchars($job['title']) ?></h4>
						<div class="job-company"><?php echo htmlspecialchars($job['company']) ?></div>
					</div>
				</div>
				<div style="text-align:right;color:#64748b;font-weight:700;font-size:13px"><?php echo $location ?></div>
			</div>
			<div class="job-desc"><?php echo $snippet ?></div>
			<div class="job-actions">
				<button class="btn-apply" onclick="openApplyModal(<?php echo $job['id'] ?>,'<?php echo addslashes(htmlspecialchars($job['title'])) ?>')">Apply</button>
				<a href="go_to_job.php?job_id=<?php echo $job['id'] ?>" class="btn-details">Details</a>
			</div>
			<div class="badge-source">Source: <?php echo ucfirst(htmlspecialchars($source)); ?></div>
		</div>
		<?php }
		} else {
			echo '<div style="grid-column:1/-1;text-align:center;color:#94a3b8;padding:40px;border-radius:12px;background:#fff">No jobs available.</div>';
		}
		?>
	</div>
</div>

<!-- Apply Modal -->
<div id="applyModal" class="apply-modal">
	<div class="modal-panel" style="display:none"></div>
</div>

<script>
	// GSAP animations
	document.addEventListener('DOMContentLoaded', function(){
		if(window.gsap) gsap.from('.reveal-card',{y:40,opacity:0,duration:0.9,stagger:0.08,delay:0.15});

		// Filters
		const grid = document.getElementById('jobsGrid');
		const cards = Array.from(grid.querySelectorAll('.job-card'));
		function applyFilter(source){
			cards.forEach(c=>{
				if(source==='all' || (c.dataset.source && c.dataset.source.toLowerCase()===source)){
					c.style.display='block';
				} else c.style.display='none';
			});
		}
		document.getElementById('filter-all').addEventListener('click', ()=>applyFilter('all'));
		document.getElementById('filter-admin').addEventListener('click', ()=>applyFilter('admin'));
		document.getElementById('filter-alumni').addEventListener('click', ()=>applyFilter('alumni'));

		// Modal open placeholder (keeps previous apply flow)
		window.openApplyModal = function(id,title){
			Swal.fire({
				title: 'Apply for '+title,
				html: '<p>To apply, open the job details and submit your application.</p>',
				confirmButtonText: 'Open Job',
				showCancelButton:true
			}).then(res=>{ if(res.isConfirmed) window.location.href='go_to_job.php?job_id='+id; });
		};
	});
</script>

</body>
</html>