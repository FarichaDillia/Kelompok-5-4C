<?php
include "../config.php";
$id = $_GET['id'];
mysqli_query($conn, "UPDATE pesanan SET status='verified' WHERE id=$id");
header("Location: rent.php");
?>

