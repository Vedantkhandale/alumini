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
    padding: 25px;
    border-radius: 16px;
    border: 1px solid #eee;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    text-align: center;
    transition: 0.3s;
}

/* AVATAR 🔥 */
.avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #ff3b3b;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin: 0 auto 15px;
}

/* NAME */
.card h3 {
    color: #222;
    margin-bottom: 5px;
}

/* COURSE */
.course {
    color: #666;
    font-size: 14px;
}

/* COMPANY */
.company {
    color: #999;
    font-size: 13px;
    margin-top: 5px;
}

/* BADGE 🔥 */
.badge {
    display: inline-block;
    margin-top: 10px;
    padding: 5px 12px;
    background: #f1f1f1;
    border-radius: 20px;
    font-size: 12px;
}

/* HOVER */
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

/* MOBILE */
@media(max-width:768px){
    .page {
        padding: 30px 20px;
    }
}

</style>

<div class="page">

    <h2 class="page-title">👨‍🎓 Our Alumni Network</h2>

    <div class="grid">

    <?php
    include("includes/db.php");
    $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC");

    if($res->num_rows > 0){
        while($row = $res->fetch_assoc()){

            $initial = strtoupper(substr($row['name'], 0, 1));

            echo "<div class='card'>";
            echo "<div class='avatar'>".$initial."</div>";
            echo "<h3>".$row['name']."</h3>";
            echo "<p class='course'>".$row['course']."</p>";
            echo "<p class='company'>".$row['company']."</p>";
            echo "<div class='badge'>Alumni</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No alumni found</p>";
    }
    ?>

    </div>

</div>

<?php include("includes/footer.php"); ?>