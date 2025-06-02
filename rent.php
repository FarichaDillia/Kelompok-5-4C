<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['verifikasi'])) {
    $id = intval($_GET['verifikasi']);
    mysqli_query($conn, "UPDATE riwayat_pesanan SET status='verified' WHERE id = $id");
}

// Hapus item dari keranjang
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($i) use ($removeId) {
            return $i['id'] != $removeId;
        });
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reset index
    }
    header("Location: rent.php?removed=true");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Sewa - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #77acc7;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="navbar.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
            <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
            <li class="nav-item"><a class="nav-link" href="riwayat.php">History</a></li>
        </ul>
    </div>
    <div class="profile">
        <a href="profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
            <i class="fa fa-user-circle"></i> Profile
        </a>
    </div>
</nav>

<!-- Konten Keranjang -->
<div class="container rent-section py-5">
    <h2 class="section-title mb-5 fw-bold">
        <i class="fas fa-shopping-cart"></i> Keranjang Sewa
    </h2>

    <form action="checkout.php" method="POST">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th><input type="checkbox" id="select_all"> Pilih Semua</th>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Total</th>
                    <th>Hapus</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($cart) === 0): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Keranjang Anda kosong.</td>
                    </tr>
                <?php else: ?>
                    <?php $grandTotal = 0; ?>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_items[]" value="<?= $item['id'] ?>"></td>
                            <td><img src="img/<?= htmlspecialchars($item['gambar']) ?>" width="80"></td>
                            <td><?= htmlspecialchars($item['nama']) ?></td>
                            <td>Rp. <?= number_format($item['harga']) ?></td>
                            <td>Rp. <?= number_format($item['harga'] * $item['qty']) ?></td>
                            <td>
                                <a href="rent.php?remove=<?= $item['id'] ?>" class="btn btn-secondary">Hapus</a>
                            </td>
                        </tr>
                        <?php $grandTotal += $item['harga'] * $item['qty']; ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Keseluruhan:</strong></td>
                        <td colspan="2"><strong>Rp. <?= number_format($grandTotal) ?></strong></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (count($cart) > 0): ?>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-5 py-2">
                    <i class="fas fa-check-circle"></i> Checkout
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<!-- Footer -->
<footer class="footer text-center py-4 mt-auto">
    <div class="container-fluid">
        <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
    </div>
</footer>

<!-- JavaScript: Pilih Semua -->
<script>
document.getElementById('select_all').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>

<!-- SweetAlert: Item Dihapus -->
<?php if (isset($_GET['removed']) && $_GET['removed'] === 'true'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Item telah dihapus dari keranjang.',
        showConfirmButton: false,
        timer: 1800
    });
});
</script>
<?php endif; ?>

</body>
</html>
