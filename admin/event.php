<?php require __DIR__ . "/page_event.php"; return; ?>
<?php
session_start();
include("../includes/db.php");

if(isset($_POST['add'])){
    $title = $_POST['title'];
    $desc = $_POST['desc'];
    $date = $_POST['date'];

    $conn->query("INSERT INTO events (title,description,event_date)
    VALUES ('$title','$desc','$date')");
}

?>

<form method="POST">
    <input type="text" name="title" placeholder="Event Title"><br>
    <textarea name="desc" placeholder="Description"></textarea><br>
    <input type="date" name="date"><br>
    <button name="add">Add Event</button>
</form>

<hr>

<?php
$res = $conn->query("SELECT * FROM events");

while($row = $res->fetch_assoc()){
    echo "<h3>".$row['title']."</h3>";
    echo $row['event_date']."<br><hr>";
}
?>
