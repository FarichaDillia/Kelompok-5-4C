<?php
session_start();
include "../config.php";
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// ambil list items untuk dropdown
$queryItems = "SELECT id, nama FROM items";
$resultItems = mysqli_query($conn, $queryItems);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';
    $periode = $_POST['periode'] ?? '';

    // validasi sederhana
    if (!$item_id) $errors[] = "Item harus dipilih";
    if (!$jumlah || !is_numeric($jumlah) || $jumlah < 1) $errors[] = "Jumlah harus angka dan minimal 1";
    if (!$periode || !is_numeric($periode) || $periode < 1) $errors[] = "Periode harus angka dan minimal 1";
    

    if (empty($errors)) {
        // ambil harga item
        $queryHarga = "SELECT harga FROM items WHERE id = ?";
        $stmt = $conn->prepare($queryHarga);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $resultHarga = $stmt->get_result();
        $rowHarga = $resultHarga->fetch_assoc();
        $harga = $rowHarga ? $rowHarga['harga'] : 0;

        $total_price = $harga * $jumlah * $periode;

        // insert ke pesanan dengan status default (misal 'pending')
        $insertQuery = "INSERT INTO pesanan (item_id, jumlah, periode, total_price, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt2 = $conn->prepare($insertQuery);
        $stmt2->bind_param("iiid", $item_id, $jumlah, $periode, $total_price);

        if ($stmt2->execute()) {
            $success = true;
        } else {
            $errors[] = "Gagal menyimpan data: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Tambah Pesanan - Rentify</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h1>Tambah Pesanan Baru</h1>

  <?php if ($success): ?>
    <div class="alert alert-success">Pesanan berhasil ditambahkan. <a href="dashboard.php">Kembali ke Dashboard</a></div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error) ?></li>
      <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label for="item_id" class="form-label">Pilih Item</label>
      <select name="item_id" id="item_id" class="form-select" required>
        <option value="">-- Pilih Item --</option>
        <?php while ($row = mysqli_fetch_assoc($resultItems)) : ?>
          <option value="<?= $row['id'] ?>" <?= (isset($_POST['item_id']) && $_POST['item_id'] == $row['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($row['nama']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Logo -->
         <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
        </div>
         <!-- Logo End -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
                <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
                <li class="nav-item"><a class="nav-link" href="review.php">Review</a></li>
                <li class="nav-item"><a class="nav-link" href="transaction.php">Transaction</a></li>
            </ul>
        </div>
        <!-- Account -->
        <div class="account">
            <a href="login.php" class="btn search-button btn-md d-none d-md-block ml-4"><i class="fa fa-user-circle"></i> Owner</a>
        </div>
        <!-- Account End -->
    </nav>

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
                            </div>

                            <!-- Menampilkan harga berdasarkan kategori -->
                            <div class="mb-3">
                                <label class="form-label">Harga</label>
                                <input type="text" name="harga" class="form-control" id="harga" required readonly>
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
  </form>
</body>
</html>
