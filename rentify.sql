-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2025 at 02:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentify`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--
USE rentify;

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kode_voucher` int(11) DEFAULT NULL,
  `status` enum('Available','Rented','Unavailable','Deleted') DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `nama`, `deskripsi`, `harga`, `stok`, `gambar`, `kode_voucher`, `status`, `id_kategori`, `owner_id`) VALUES
(1, 'Car Sport', 'Mobil sport berperforma tinggi dengan desain aerodinamis dan mesin bertenaga, dirancang untuk memberikan pengalaman berkendara yang cepat, responsif, dan penuh gaya. Cocok untuk perjalanan eksklusif, event spesial, atau sekadar merasakan sensasi kecepatan di jalanan.', 100000, 2, 'car.jpg', 131204, 'Available', 6, 4),
(2, 'Kompor Portable', 'Kompor Portable adalah alat masak praktis yang mudah dibawa dan digunakan di luar ruangan. Cocok untuk kegiatan camping, piknik, atau event outdoor. Ringan, hemat bahan bakar, dan mudah dioperasikan, kompor ini menjadi solusi ideal untuk kebutuhan memasak saat bepergian.', 25000, 18, 'kompor.jpg', 131204, 'Available', 3, 6),
(3, 'Komputer', 'Komputer Desktop siap pakai, cocok untuk kebutuhan kerja, desain, atau event pelatihan. Dilengkapi dengan spesifikasi optimal dan sudah terinstall sistem operasi. Cocok untuk penggunaan jangka pendek maupun jangka panjang.', 50000, 4, 'computer.jpg', 131204, 'Available', 1, 8),
(4, 'Air Fryer', 'Air Fryer praktis dan hemat minyak, cocok untuk memasak gorengan sehat tanpa ribet. Dilengkapi pengatur suhu dan timer, alat ini mudah digunakan untuk menggoreng, memanggang, atau menghangatkan makanan. Cocok untuk keperluan rumah tangga, acara memasak, kos-kosan, atau sebagai uji coba sebelum membeli sendiri.', 25000, 4, 'fryer.jpg', 131204, 'Available', 3, 7),
(5, 'Skateboard', 'Skateboard stylish dan kokoh, ideal untuk pemula hingga rider berpengalaman. Cocok digunakan di skatepark, jalanan kota, atau sekadar seru-seruan bersama teman. Bisa disewa harian untuk latihan, konten kreatif, atau uji coba sebelum membeli sendiri.', 50000, 3, 'skateboard.jpg', 131204, 'Available', 5, 9);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) DEFAULT NULL,
  `daily_rate` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `daily_rate`) VALUES
(1, 'Elektronik & Gadget', 50000),
(3, 'Home Appliances', 25000),
(4, 'Cloth & Accessories', 25000),
(5, 'Sports Equipment', 50000),
(6, 'Vehicle', 100000);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) NOT NULL,
  `metode` varchar(255) NOT NULL,
  `rekening` varchar(255) NOT NULL,
  `atas_nama` varchar(255) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `pesanan_id`, `metode`, `rekening`, `atas_nama`, `tanggal_bayar`, `total_bayar`, `user_id`) VALUES
(3, 4, 'transfer_bank', '1234', 'Kevin', '2025-05-20', 100000.00, 5),
(8, 9, 'transfer_bank', '123', 'Kevin', '2025-05-20', 200000.00, 5),
(12, 13, 'transfer_bank', '1234', 'Kevin', '2025-05-26', 50000.00, 5),
(14, 15, 'transfer_bank', '1234', 'Kevin', '2025-05-26', 50000.00, 5),
(15, 16, 'transfer_bank', '1234', 'Kevin', '2025-05-26', 50000.00, 5),
(19, 20, 'transfer_bank', '123', 'Alpha', '2025-05-26', 40000.00, 1),
(20, 21, 'transfer_bank', '123', 'Alpha', '2025-05-26', 200000.00, 7),
(21, 22, 'transfer_bank', '123', 'Kevin', '2025-05-26', 25000.00, 5),
(22, 23, 'transfer_bank', '1234', 'Kevin', '2025-05-26', 100000.00, 5),
(23, 24, 'transfer_bank', '1234', 'Kevin', '2025-05-26', 1375000.00, 5);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` int(11) NOT NULL,
  `id_rent` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id_review`, `id_rent`, `id_item`, `id_user`, `rating`, `comment`) VALUES
(1, 2, 4, 5, 4, 'mantap'),
(3, 9, 1, 5, 5, 'Murah mantap'),
(4, 4, 5, 5, 4, 'bagus'),
(5, 22, 2, 5, 5, 'Berfungsi dengan baik'),
(6, 23, 4, 5, 5, 'Awet');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pesanan`
--

CREATE TABLE `riwayat_pesanan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('pending','verified','completed','cancelled') NOT NULL,
  `start_date` date DEFAULT current_timestamp(),
  `end_date` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_pesanan`
--

INSERT INTO `riwayat_pesanan` (`id`, `user_id`, `item_id`, `total_harga`, `status`, `start_date`, `end_date`) VALUES
(1, 5, 2, 100000.00, 'cancelled', '2025-05-20', '2025-05-21'),
(2, 5, 4, 50000.00, 'verified', '2025-05-20', '2025-05-21'),
(3, 5, 2, 50000.00, 'cancelled', '2025-05-20', '2025-05-21'),
(4, 5, 5, 100000.00, 'verified', '2025-05-20', '2025-05-21'),
(7, 5, 2, 75000.00, 'verified', '2025-05-20', '2025-05-22'),
(9, 5, 1, 200000.00, 'verified', '2025-05-20', '2025-05-21'),
(13, 5, 4, 50000.00, 'pending', '2025-05-26', '2025-05-27'),
(15, 5, 4, 50000.00, 'pending', '2025-05-26', '2025-05-27'),
(16, 5, 4, 50000.00, 'pending', '2025-05-26', '2025-05-27'),
(19, 5, 2, 50000.00, 'cancelled', '2025-05-26', '2025-05-27'),
(20, 1, 4, 40000.00, 'verified', '2025-05-26', '2025-05-27'),
(21, 7, 1, 200000.00, 'verified', '2025-05-26', '2025-05-27'),
(22, 5, 2, 25000.00, 'verified', '2025-05-26', '2025-05-26'),
(23, 5, 4, 100000.00, 'verified', '2025-05-26', '2025-05-29'),
(24, 5, 2, 1375000.00, 'verified', '2025-05-26', '2025-07-19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(11) DEFAULT NULL,
  `role` enum('user','admin','owner') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `alamat`, `no_telp`, `role`) VALUES
(1, 'admin', 'admin@gmail.com', 'rentify', 'Universitas Singaperbangsa Karawang', '08138780765', 'admin'),
(4, 'Faricha Dillia', '2310631250054@student.ac.id', '123', 'Bogor, Indonesia', '08111222333', 'owner'),
(5, 'Kevin Novallian', 'kevin@gmail.com', '123', 'Bogor, Indonesia', '08222333777', 'user'),
(6, 'Resya Hidayatunnisa', 'resya@gmail.com', '123', 'Cikampek, Jawa Barat', '08444555888', 'owner'),
(7, 'Roma Ulina', 'roma@gmail.com', '123', 'Bogor, Indonesia', '08999555333', 'owner'),
(8, 'Dimas Hadi Prabowo', 'dimas@gmail.com', '123', 'Bekasi, Jawa Barat', '08555666888', 'owner'),
(9, 'M Alpha Athallah', 'alpha@gmail.com', '123', 'Kranji, Jawa Barat', '08111777999', 'owner');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_kategori` (`id_kategori`),
  ADD KEY `fk_items_owner` (`owner_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pembayaran_pesanan` (`pesanan_id`),
  ADD KEY `fk_user_pembayaran` (`user_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `fk_review_user` (`id_user`),
  ADD KEY `fk_review_item` (`id_item`),
  ADD KEY `fk_review_pesanan` (`id_rent`);

--
-- Indexes for table `riwayat_pesanan`
--
ALTER TABLE `riwayat_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_pesanan`
--
ALTER TABLE `riwayat_pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_items_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `riwayat_pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_pembayaran` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_item` FOREIGN KEY (`id_item`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `fk_review_pesanan` FOREIGN KEY (`id_rent`) REFERENCES `riwayat_pesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Constraints for table `riwayat_pesanan`
--
ALTER TABLE `riwayat_pesanan`
  ADD CONSTRAINT `riwayat_pesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `riwayat_pesanan_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
