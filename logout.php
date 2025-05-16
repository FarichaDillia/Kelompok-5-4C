<?php
session_start();
session_unset();     // Hapus semua variabel session
session_destroy();   // Hancurkan session

// Arahkan ke halaman awal (navbar.php atau index.php)
header("Location: navbar.php");
exit();
