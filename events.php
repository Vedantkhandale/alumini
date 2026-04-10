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
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
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

.card h3 {
    color: #222;
    margin-bottom: 10px;
}

.card p {
    color: #666;
    font-size: 14px;
}

/* DATE BADGE 🔥 */
.date {
    display: inline-block;
    margin-top: 10px;
    padding: 5px 12px;
    background: #ff3b3b;
    color: white;
    border-radius: 20px;
    font-size: 12px;
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

    <h2 class="page-title">📅 Upcoming Events</h2>

    <div class="grid">

    <?php
    include("includes/db.php");
    $res = $conn->query("SELECT * FROM events ORDER BY event_date DESC");

    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){
            echo "<div class='card'>";
            echo "<h3>".$row['title']."</h3>";
            echo "<p>Event Date:</p>";
            echo "<span class='date'>".$row['event_date']."</span>";
            echo "</div>";
        }
    } else {
        echo "<p>No events available</p>";
    }
    ?>

    </div>

</div>

<?php include("includes/footer.php"); ?>