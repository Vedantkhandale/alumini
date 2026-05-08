<!-- Query for Career Page -->
<?php
// Replace your current include with this:
include(__DIR__ . "/../includes/db.php");
$result = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");
while($job = $result->fetch_assoc()){
?>
   <div class="jobs-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 20px;">
    <?php
    $result = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");
    if ($result && $result->num_rows > 0) {
        while($job = $result->fetch_assoc()){
    ?>
        <div class="job-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 25px; transition: 0.3s; position: relative;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <img src="<?php echo !empty($job['logo']) ? htmlspecialchars($job['logo']) : 'https://cdn-icons-png.flaticon.com/512/262/262253.png'; ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;" alt="logo">
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p style="margin: 0; font-size: 13px; color: #e11d48; font-weight: 700;"><?php echo htmlspecialchars($job['company']); ?></p>
                </div>
            </div>

            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
                <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($job['location']); ?>
            </p>

            <a href="go_to_job.php?job_id=<?php echo $job['id']; ?>" 
               target="_blank" 
               style="display: block; width: 100%; text-align: center; background: #0f172a; color: #fff; padding: 12px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px; transition: 0.3s;"
               onmouseover="this.style.background='#e11d48'" 
               onmouseout="this.style.background='#0f172a'">
                Apply Now
            </a>
        </div>
    <?php 
        } 
    } else {
        echo "<p style='text-align:center; grid-column: 1/-1; color:#64748b;'>No approved jobs found.</p>";
    }
    ?>
</div>
<?php } ?>