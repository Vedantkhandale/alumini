<!-- Query for Career Page -->
<?php
$result = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");
while($job = $result->fetch_assoc()){
?>
    <div class="job-card">
        <img src="<?php echo $job['logo'] ?: 'default-logo.png'; ?>" width="50">
        <h3><?php echo $job['title']; ?></h3>
        <p><?php echo $job['company']; ?> | <?php echo $job['location']; ?></p>
        <a href="<?php echo $job['apply_link']; ?>" target="_blank">Apply Now</a>
    </div>
<?php } ?>