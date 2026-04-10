<?php include("includes/header.php"); ?>

<style>

/* PAGE */
.page {
    padding: 50px 80px;
}

/* TITLE */
.page-title {
    text-align: center;
    font-size: 28px;
    margin-bottom: 40px;
    color: #222;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

/* CARD */
.card {
    background: #ffffff;
    padding: 22px;
    border-radius: 14px;
    border: 1px solid #eee;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: 0.3s;
}

/* TITLE */
.card h3 {
    color: #222;
    margin-bottom: 8px;
}

/* COMPANY */
.company {
    color: #555;
    font-weight: 500;
}

/* LOCATION */
.location {
    color: #888;
    font-size: 13px;
    margin-top: 5px;
}

/* APPLY BUTTON 🔥 */
.apply-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 16px;
    background: #ff3b3b;
    color: white;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s;
}

.apply-btn:hover {
    background: #e60023;
    transform: translateY(-2px);
}

/* HOVER */
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

/* MOBILE */
@media(max-width:768px){
    .page {
        padding: 30px 20px;
    }
}

</style>

<div class="page">

    <h2 class="page-title">💼 Available Jobs</h2>

    <div class="grid">

    <?php
    include("includes/db.php");
    $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");

    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            echo "<div class='card'>";
            echo "<h3>".$row['title']."</h3>";
            echo "<p class='company'>".$row['company']."</p>";
            echo "<p class='location'>📍 ".$row['location']."</p>";
            echo "<a href='#' class='apply-btn'>Apply Now</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No jobs available right now</p>";
    }
    ?>

    </div>

</div>

<?php include("includes/footer.php"); ?>