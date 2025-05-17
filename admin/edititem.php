<?php
session_start();
include "../config.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit;
}

// ambil id dari url
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: item.php");
    exit;
}

// ambil data item sesuai id
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "<script>alert('Item tidak ditemukan'); window.location='item.php';</script>";
    exit;
}

// ambil kategori untuk dropdown
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
                // hapus gambar lama kalau ada
                if ($item['gambar'] && file_exists($target_dir . $item['gambar'])) {
                    unlink($target_dir . $item['gambar']);
                }
                $item['gambar'] = $newFilename;
            }
        }
    }



    if (empty($errors)) {
        $stmtUpdate = $conn->prepare("UPDATE items SET nama=?, id_kategori=?, deskripsi=?, harga=?, kode_voucher=?, stok=?, gambar=?, status=? WHERE id=?");
        $stmtUpdate->bind_param("sisdssssi", $nama, $id_kategori, $deskripsi, $harga, $kode_voucher, $stok, $item['gambar'], $status, $id);
        if ($stmtUpdate->execute()) {
            $success = true;
        } else {
            $errors[] = "Gagal update data: " . $conn->error;
        }
    }


    // Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $jumlah = $_POST['jumlah'];
    $periode = $_POST['periode'];

    // Update data pesanan di database
    $query = "UPDATE pesanan SET 
                jumlah = '$jumlah',
                periode = '$periode'
              WHERE id = $id";

     if (mysqli_query($conn, $query)) {
        // Jika update berhasil, arahkan ke halaman home (dashboard.php)
        echo "<script>alert('Item berhasil diupdate!'); window.location.href='item.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate item.');</script>";
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
</head>
<body id="page-top">
<div id="wrapper">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a href="#"><img src="img/logo.jpg" alt="Logo" class="logo-img"></a>
                </div>
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
                <div class="account">
                    <a href="login.php" class="btn search-button btn-md d-none d-md-block ml-4"><i class="fa fa-user-circle"></i> Owner</a>
                </div>
            </nav>

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
                <label for="id_kategori" class="form-label">Kategori</label>
                <select name="id_kategori" id="id_kategori" class="form-select" required">
                    <option value="">-- Pilih Kategori --</option>
                    <?php while ($cat = mysqli_fetch_assoc($resultCategories)): ?>
                        <option value="<?= $cat['id_kategori'] ?>" <?= ($cat['id_kategori'] == $item['id_kategori']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nama_kategori']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" id="harga" name="harga" id="harga" class="form-control" required readonly value="<?= htmlspecialchars($item['harga']) ?>">
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($item['deskripsi']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="kode_voucher" class="form-label">Kode Voucher</label>
                <input type="text" id="kode_voucher" name="kode_voucher" class="form-control" required minlength="5" maxlength="10" pattern="\d+" title="Hanya angka" value="<?= htmlspecialchars($item['kode_voucher']) ?>">
            </div>

            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" id="stok" name="stok" class="form-control" required value="<?= htmlspecialchars($item['stok']) ?>">
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Upload Gambar (kosongkan jika tidak ingin ganti)</label>
                <input type="file" id="gambar" name="gambar" class="form-control" accept="image/*">
                <?php if ($item['gambar'] && file_exists("../img/" . $item['gambar'])): ?>
                    <img src="../img/<?= htmlspecialchars($item['gambar']) ?>" alt="gambar" width="120" class="mt-2">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="Available" <?= $item['status'] == 'Available' ? 'selected' : '' ?>>Available</option>
                    <option value="Rented" <?= $item['status'] == 'Rented' ? 'selected' : '' ?>>Rented</option>
                </select>
            </div>

            <div class="text-end">
                <a href="item.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>

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


</body>
</html>
