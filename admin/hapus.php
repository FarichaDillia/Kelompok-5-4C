<?php
session_start();
include "../config.php";
if ($_SESSION["role"] != "admin") { header("Location: ../login.php"); }

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM items WHERE id=$id");
header("Location: barang.php");
?>
