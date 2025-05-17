<?php
include "../config.php";

if (isset($_POST['id_kategori'])) {
    $id_kategori = (int)$_POST['id_kategori'];
    $query = "SELECT daily_rate FROM kategori WHERE id_kategori = $id_kategori";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo $row['daily_rate'];
    } else {
        echo 0;
    }
}
?>
