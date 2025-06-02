<?php
session_start();
include "config.php";

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT rp.id, rp.item_id,  rp.total_harga, rp.status, rp.start_date, rp.end_date, i.nama AS item_name 
          FROM riwayat_pesanan rp
          JOIN items i ON rp.item_id = i.id
          WHERE rp.user_id = $user_id 
          ORDER BY rp.start_date DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Sewa - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex flex-column min-vh-100">
    <style>
        body {
            background-color: #77acc7;
        }
    </style>

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

    <!-- Riwayat -->
    <div class="container rent-section py-5">
        <h2 class="section-title mb-5 fw-bold">
            <i class="fas fa-history"></i> Riwayat Sewa
        </h2>

        <table class="table table-bordered table-hover align-middle">
    <thead class="table-primary text-center">
        <tr>
            <th>Item</th>
            <th>Total Harga</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th><strong>Periode</strong></th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $start = strtotime($row['start_date']);
                    $end = strtotime($row['end_date']);
                    $periode = ($start && $end) ? (ceil(($end - $start) / (60 * 60 * 24)) + 1) : '-';
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td>Rp. <?= number_format($row['total_harga']) ?></td>
                    <td><?= $row['start_date'] !== null ? date('d M Y', strtotime($row['start_date'])) : '-' ?></td>
                    <td><?= $row['end_date'] !== null ? date('d M Y', strtotime($row['end_date'])) : '-' ?></td>
                    <td><?= is_numeric($periode) ? $periode . ' hari' : '-' ?></td>
                    <td><span class='badge bg-warning text-dark px-3 py-2'><?= ucfirst($row['status']) ?></span></td>
                    <td>
                        <?php if ($row['status'] == 'verified'): ?>
                            <a href="review.php?pesanan_id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Beri Ulasan</a>
                        <?php endif; ?>

                        <?php if ($row['status'] == 'pending'): ?>
                            <a href="batal_pesanan.php?pesanan_id=<?= $row['id'] ?>"
                               class="btn btn-danger btn-sm">
                               Batalkan
                            </a>
                        <?php elseif ($row['status'] == 'cancelled'): ?>
                            <a href="hapus_pesanan.php?pesanan_id=<?= $row['id'] ?>"
                               class="btn btn-secondary btn-sm">
                               Hapus
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8" class="text-center">Belum ada riwayat sewa.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

    </div>

    <!-- Footer -->
    <footer class="footer text-center py-4 mt-auto">
        <div class="container-fluid">
            <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
        </div>
    </footer>

    <?php if (isset($_GET['alert'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($_GET['alert'] === 'cancelled'): ?>
        Swal.fire({
            icon: 'info',
            title: 'Pesanan Dibatalkan',
            text: 'Pesanan Anda telah dibatalkan.',
            confirmButtonText: 'OK'
        });
    <?php elseif ($_GET['alert'] === 'deleted'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Pesanan Dihapus',
            text: 'Data pesanan berhasil dihapus.',
            confirmButtonText: 'OK'
        });
    <?php elseif ($_GET['alert'] === 'reviewed'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Terima kasih!',
            text: 'Ulasan Anda telah dikirim.',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
});
<?php if (isset($_GET['alert']) && $_GET['alert'] === 'review_exists'): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'info',
        title: 'Sudah Diberi Ulasan',
        text: 'Pesanan ini sudah pernah diberi ulasan sebelumnya.',
        confirmButtonText: 'OK'
    });
});
</script>
<?php endif; ?>

</script>
<?php endif; ?>
</body>
</html>
