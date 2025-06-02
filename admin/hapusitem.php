<?php
session_start();
include "../config.php";

$id = $_GET['id'] ?? null;

function showAlert($icon, $title, $text, $redirect) {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Alert</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = '$redirect';
            });
        </script>
    </body>
    </html>";
    exit;
}

if (!$id || !is_numeric($id)) {
    showAlert("error", "ID Tidak Valid", "Data tidak dapat diproses", "item.php");
}

// Eksekusi soft delete
$stmt = $conn->prepare("UPDATE items SET status = 'Deleted' WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    showAlert("success", "Berhasil!", "Item berhasil dihapus", "item.php");
} else {
    showAlert("error", "Gagal!", "Item gagal dihapus", "item.php");
}
?>
