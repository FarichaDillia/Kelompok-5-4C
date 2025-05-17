<?php
session_start();
include "../config.php";

$hargaKategori = ''; // definisi awal


// Ambil data kategori untuk dropdown
$queryCategories = "SELECT * FROM kategori";
$resultCategories = mysqli_query($conn, $queryCategories);

// Ambil harga kategori SEBELUM submit utama
if (!empty($id_kategori)) {
    $queryHarga = "SELECT daily_rate FROM kategori WHERE id_kategori = $id_kategori";
    $resultHarga = mysqli_query($conn, $queryHarga);
    if ($rowHarga = mysqli_fetch_assoc($resultHarga)) {
        $hargaKategori = $rowHarga['daily_rate'];
    }
}

    // Jika tombol submit ditekan
    if (isset($_POST['submit'])) {
        $nama = $_POST['nama'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $kode_voucher = $_POST['kode_voucher'];
        $stok = $_POST['stok'];
        $gambar = $_FILES['gambar']['name'];
        $status = $_POST['status'] ?? 'Available'; // fallback default
        $id_kategori = $_POST['id_kategori'];

        // Upload gambar
        $target_dir = "../img/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);

        $query = "INSERT INTO items (nama, deskripsi, harga, kode_voucher, stok, gambar, status, id_kategori)
                VALUES ('$nama', '$deskripsi', '$harga', '$kode_voucher', '$stok', '$gambar', '$status', '$id_kategori')";

        if (mysqli_query($conn, $query)) {
            header("Location: item.php");
            exit;
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Rentify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../lib/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../lib/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body id="page-top">
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg">
    <div class="container">
         <!-- Logo -->
         <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
        </div>
         <!-- Logo End -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                 <li class="nav-item">
                    <a class="nav-link <?php if($page == 'home') echo 'active'; ?>" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'item') echo 'active'; ?>" href="item.php">Item</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'rent') echo 'active'; ?>" href="rent.php">Rent</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'review') echo 'active'; ?>" href="review.php">Review</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if($page == 'transaction') echo 'active'; ?>" href="transaction.php">Transaction</a>
                </li>
            </ul>
        </div>
        <!-- Profil -->
        <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
        <i class="fa fa-user-circle"></i> Profile
    </a>
</div>
    </nav>
    <!-- Navbar End -->

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="card shadow mb-4 col-lg-8 mx-auto">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Tambah Barang</h6>
                    </div>
                    <div class="card-body">
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Barang</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="id_kategori" id="id_kategori" class="form-select" required onchange="this.form.submit()">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php
                                    $selectedKategori = $_POST['id_kategori'] ?? '';
                                    while ($cat = mysqli_fetch_assoc($resultCategories)) {
                                        $selected = ($selectedKategori == $cat['id_kategori']) ? 'selected' : '';
                                        echo "<option value='{$cat['id_kategori']}' $selected>" . htmlspecialchars($cat['nama_kategori']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Harga</label>
                                <input type="text" name="harga" id="harga" class="form-control" readonly required value="<?= htmlspecialchars($hargaKategori) ?>">
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                            </div>

                            <!-- Form Fields for Price and Code Voucher -->
                            <div class="mb-3">
                                <label class="form-label">Code Voucher</label>
                                <input type="text" name="kode_voucher" class="form-control" required minlength="5" maxlength="10" pattern="\d+" title="Hanya angka yang diperbolehkan dan minimal 5 digit" placeholder="Masukkan kode voucher" />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload Foto</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*" required>
                            </div>
                            <div class="text-end">
                                <a href="item.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<!-- Scripts -->
<script src="../lib/vendor/jquery/jquery.min.js"></script>
<script src="../lib/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../lib/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../lib/js/sb-admin-2.min.js"></script>

<script>
document.getElementById('id_kategori').addEventListener('change', function () {
    const kategoriId = this.value;

    if (kategoriId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "get_price.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("harga").value = xhr.responseText;
            }
        };

        xhr.send("id_kategori=" + encodeURIComponent(kategoriId));
    } else {
        document.getElementById("harga").value = '';
    }
});
</script>


</body>
</html>

