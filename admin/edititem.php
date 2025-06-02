<?php
session_start();
include "../config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../login.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: item.php");
    exit;
}

// Ambil data item sesuai ID dan owner
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "<script>alert('Item tidak ditemukan atau bukan milik Anda'); window.location='item.php';</script>";
    exit;
}

// Ambil kategori untuk dropdown
$queryCategories = "SELECT * FROM kategori";
$resultCategories = mysqli_query($conn, $queryCategories);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $id_kategori = $_POST['id_kategori'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $kode_voucher = $_POST['kode_voucher'];
    $stok = $_POST['stok'];
    $status = $_POST['status'];

    // upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','gif'];
        $filename = $_FILES['gambar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format gambar harus jpg, jpeg, png, atau gif";
        } else {
            $target_dir = "../img/";
            $newFilename = uniqid() . '.' . $ext;
            $target_file = $target_dir . $newFilename;
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
                $errors[] = "Gagal upload gambar";
            } else {
                if ($item['gambar'] && file_exists($target_dir . $item['gambar'])) {
                    unlink($target_dir . $item['gambar']);
                }
                $item['gambar'] = $newFilename;
            }
        }
    }

    if (empty($errors)) {
        $stmtUpdate = $conn->prepare("UPDATE items SET nama=?, id_kategori=?, deskripsi=?, harga=?, kode_voucher=?, stok=?, gambar=?, status=? WHERE id=? AND owner_id=?");
        if ($stmtUpdate === false) {
            $errors[] = "Prepare gagal: " . $conn->error;
        } else {
            $stmtUpdate->bind_param("sisdssssii", $nama, $id_kategori, $deskripsi, $harga, $kode_voucher, $stok, $item['gambar'], $status, $id, $owner_id);
           if ($stmtUpdate->execute()) {
    $success = true;
            } else {
                $errors[] = "Gagal update data: " . $stmtUpdate->error;
            }
        }
    }
}
?>


<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

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
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
     <style>
    body {
      background-color: #77acc7;
    }
    .form-container {
      background-color: #fff;
      border-radius: 15px;
      padding: 40px;
      max-width: 700px;
      margin: auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    h2 {
      color: #333;
      font-weight: bold;
      margin-bottom: 30px;
    }
    .form-label {
      font-weight: 600;
      color: #333;
    }
    .form-control, .form-select {
      border-radius: 10px;
      padding: 12px;
      font-size: 15px;
    }
    .form-control:focus, .form-select:focus {
      box-shadow: 0 0 0 0.2rem rgba(43, 58, 103, 0.25);
      border-color: #2b3a67;
    }
    .btn-primary {
      background-color: #2b3a67;
      border: none;
      padding: 12px 25px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #1f2a4d;
    }
    .form-img-preview {
      max-height: 100px;
      margin-top: 10px;
      border-radius: 10px;
    }
  </style>
</head>
<body id="page-top">
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
           <nav class="navbar navbar-expand-lg">
  <div class="container">
    <a href="#"><img src="../img/logo.jpg" alt="Logo" class="logo-img"></a>
  </div>
  <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="item.php">Item</a></li>
      <li class="nav-item"><a class="nav-link" href="rent.php">Rent</a></li>
      <li class="nav-item"><a class="nav-link" href="review.php">Review</a></li>
      <li class="nav-item"><a class="nav-link" href="transaction.php">Transaction</a></li>
    </ul>
  </div>
  <div class="profile">
    <a href="../profile.php" class="btn search-button btn-md d-none d-md-block ml-4 text-white fw-normal">
      <i class="fa fa-user-circle"></i> Profile
    </a>
  </div>
</nav>
<div class="container py-5">
  <div class="form-container">
    <h2 class="text-center">Edit Barang</h2>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($item['nama']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="id_kategori" id="id_kategori" class="form-select" required>
          <option value="">-- Pilih Kategori --</option>
          <?php
          mysqli_data_seek($resultCategories, 0);
          while ($cat = mysqli_fetch_assoc($resultCategories)) {
              $selected = ($cat['id_kategori'] == $item['id_kategori']) ? 'selected' : '';
              echo "<option value='{$cat['id_kategori']}' $selected>" . htmlspecialchars($cat['nama_kategori']) . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Harga</label>
        <input type="text" name="harga" id="harga" class="form-control bg-light" readonly required value="<?= htmlspecialchars($item['harga']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($item['deskripsi']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Kode Voucher</label>
        <input type="text" name="kode_voucher" class="form-control" required minlength="5" maxlength="10" pattern="\d+" title="Hanya angka minimal 5 digit" value="<?= htmlspecialchars($item['kode_voucher']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Stok</label>
        <input type="number" name="stok" class="form-control" required value="<?= htmlspecialchars($item['stok']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Foto (kosongkan jika tidak ganti)</label>
        <input type="file" name="gambar" class="form-control" accept="image/*">
        <?php if (!empty($item['gambar'])): ?>
          <img src="../img/<?= htmlspecialchars($item['gambar']) ?>" class="form-img-preview" alt="Gambar Lama">
        <?php endif; ?>
      </div>

      <div class="mb-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
          <option value="Available" <?= $item['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
          <option value="Rented" <?= $item['status'] === 'Rented' ? 'selected' : '' ?>>Rented</option>
        </select>
      </div>

      <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>
<script src="../lib/vendor/jquery/jquery.min.js"></script>

<script>
document.getElementById('id_kategori').addEventListener('change', function () {
    const kategoriId = this.value;

    if (kategoriId) {
        fetch('get_price.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id_kategori=' + encodeURIComponent(kategoriId)
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('harga').value = data;
        })
        .catch(error => {
            console.error('Error fetching price:', error);
        });
    } else {
        document.getElementById('harga').value = '';
    }
});
</script>
<?php if ($success): ?>
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Item berhasil diupdate!',
    showConfirmButton: false,
    timer: 2000
  }).then(() => {
    window.location.href = 'item.php';
  });
</script>
<?php endif; ?>


<footer class="footer text-center py-4">
  <p>&copy; 2025 Rentify - Team 5. All rights reserved.</p>
</footer>

</body>
</html>
