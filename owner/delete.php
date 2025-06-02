<?php
include "../config.php";

$message = "";
$redirect = false;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus review
    $deleteReview = mysqli_query($conn, "DELETE FROM review WHERE id_item = $id");

    if ($deleteReview) {
        // Lanjut hapus item
        $deleteItem = mysqli_query($conn, "DELETE FROM items WHERE id = $id");

        if ($deleteItem) {
            header("Location: delete.php?deleted=1");
            exit;
        } else {
            $message = "Gagal menghapus item!";
            $redirect = true;
        }
    } else {
        $message = "Gagal menghapus review!";
        $redirect = true;
    }
} else {
    $message = "ID tidak ditemukan.";
    $redirect = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Notifikasi</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if ($redirect): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: <?= json_encode($message) ?>,
        confirmButtonText: 'OK'
      }).then(() => {
        window.location.href = 'dashboard.php';
      });
    });
  </script>
<?php endif; ?>
</body>
</html>
