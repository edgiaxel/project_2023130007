-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.0.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for pbol
CREATE DATABASE IF NOT EXISTS `pbol` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */;
USE `pbol`;

-- Dumping structure for table pbol.barang
CREATE TABLE IF NOT EXISTS `barang` (
  `kodebrg` varchar(10) NOT NULL,
  `namabrg` varchar(30) DEFAULT NULL,
  `tarif` double DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `gambar` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`kodebrg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbol.barang: ~3 rows (approximately)
DELETE FROM `barang`;
INSERT INTO `barang` (`kodebrg`, `namabrg`, `tarif`, `stok`, `gambar`) VALUES
	('B001', 'AMD Ryzen 5 5600G', 3500000, 4, 'images/ryzen-5-5600G.jpg'),
	('B002', 'AMD Ryzen 5 7600X', 5500000, 4, 'images/ryzen-5-7600X.jpg'),
	('B003', 'AMD Ryzen 5 9600X', 7500000, 4, 'images/ryzen-5-9600X.jpg');

-- Dumping structure for table pbol.customer
CREATE TABLE IF NOT EXISTS `customer` (
  `idmember` varchar(50) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idmember`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbol.customer: ~3 rows (approximately)
DELETE FROM `customer`;
INSERT INTO `customer` (`idmember`, `nama`, `alamat`, `total`) VALUES
	('C0001', 'Sahroni', 'Dago 123 Bandung', 500000.00),
	('C0002', 'Agus', 'Cigondewah 45 Bandung', 300000.00),
	('C0003', 'Bambang', 'Cibiru 678 Bandung', 200000.00);

-- Dumping structure for table pbol.jual
CREATE TABLE IF NOT EXISTS `jual` (
  `nojual` varchar(10) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `idmember` varchar(50) NOT NULL,
  PRIMARY KEY (`nojual`) USING BTREE,
  KEY `FK_jual_customer` (`idmember`),
  CONSTRAINT `FK_jual_customer` FOREIGN KEY (`idmember`) REFERENCES `customer` (`idmember`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbol.jual: ~3 rows (approximately)
DELETE FROM `jual`;
INSERT INTO `jual` (`nojual`, `tanggal`, `idmember`) VALUES
	('00001', '2025-10-01', 'C0001'),
	('00002', '2025-10-02', 'C0002'),
	('00003', '2025-10-08', 'C0003');

-- Dumping structure for table pbol.jual_detil
CREATE TABLE IF NOT EXISTS `jual_detil` (
  `nojual` varchar(10) NOT NULL,
  `kodebrg` varchar(10) NOT NULL,
  `jumlah` int(11) DEFAULT NULL,
  KEY `FK_jual_detil_jual` (`nojual`),
  KEY `FK_jual_detil_barang` (`kodebrg`),
  CONSTRAINT `FK_jual_detil_barang` FOREIGN KEY (`kodebrg`) REFERENCES `barang` (`kodebrg`),
  CONSTRAINT `FK_jual_detil_jual` FOREIGN KEY (`nojual`) REFERENCES `jual` (`nojual`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbol.jual_detil: ~4 rows (approximately)
DELETE FROM `jual_detil`;
INSERT INTO `jual_detil` (`nojual`, `kodebrg`, `jumlah`) VALUES
	('00001', 'B001', 21),
	('00002', 'B002', 1),
	('00002', 'B001', 5),
	('00003', 'B003', 2);


-- Dumping database structure for pbw
CREATE DATABASE IF NOT EXISTS `pbw` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */;
USE `pbw`;

-- Dumping structure for table pbw.bahanbaku
CREATE TABLE IF NOT EXISTS `bahanbaku` (
  `kdbahan` char(3) NOT NULL,
  `namabahan` varchar(20) NOT NULL,
  `stok` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table pbw.bahanbaku: 5 rows
DELETE FROM `bahanbaku`;
/*!40000 ALTER TABLE `bahanbaku` DISABLE KEYS */;
INSERT INTO `bahanbaku` (`kdbahan`, `namabahan`, `stok`) VALUES
	('101', 'Terigu', 25),
	('102', 'Gula Pasir', 50),
	('103', 'Mentega', 20),
	('104', 'Garam', 10),
	('105', 'Coklat Bubuk', 5);
/*!40000 ALTER TABLE `bahanbaku` ENABLE KEYS */;

-- Dumping structure for table pbw.beli
CREATE TABLE IF NOT EXISTS `beli` (
  `nobon` char(3) NOT NULL,
  `tglbeli` date NOT NULL,
  `kdpemasok` char(3) NOT NULL,
  PRIMARY KEY (`nobon`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table pbw.beli: 4 rows
DELETE FROM `beli`;
/*!40000 ALTER TABLE `beli` DISABLE KEYS */;
INSERT INTO `beli` (`nobon`, `tglbeli`, `kdpemasok`) VALUES
	('001', '2017-01-05', '903'),
	('002', '2017-01-06', '902'),
	('003', '2017-01-09', '903'),
	('004', '2017-01-12', '901');
/*!40000 ALTER TABLE `beli` ENABLE KEYS */;

-- Dumping structure for table pbw.detilbeli
CREATE TABLE IF NOT EXISTS `detilbeli` (
  `nobon` char(3) NOT NULL,
  `kdbahan` char(3) NOT NULL,
  `banyak` int(11) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table pbw.detilbeli: 8 rows
DELETE FROM `detilbeli`;
/*!40000 ALTER TABLE `detilbeli` DISABLE KEYS */;
INSERT INTO `detilbeli` (`nobon`, `kdbahan`, `banyak`, `harga`) VALUES
	('001', '102', 5, 15000),
	('001', '103', 3, 10000),
	('002', '102', 2, 15000),
	('002', '103', 5, 11500),
	('002', '104', 1, 6500),
	('003', '105', 5, 20000),
	('004', '101', 4, 17500),
	('004', '105', 2, 22000);
/*!40000 ALTER TABLE `detilbeli` ENABLE KEYS */;

-- Dumping structure for table pbw.karyawan
CREATE TABLE IF NOT EXISTS `karyawan` (
  `NIP` char(5) DEFAULT NULL,
  `GOL` tinyint(1) DEFAULT NULL,
  `JM_ABSEN` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.karyawan: ~4 rows (approximately)
DELETE FROM `karyawan`;
INSERT INTO `karyawan` (`NIP`, `GOL`, `JM_ABSEN`) VALUES
	('94320', 1, 2),
	('95331', 3, 0),
	('95344', 2, 5),
	('97352', 3, 1);

-- Dumping structure for table pbw.mobil
CREATE TABLE IF NOT EXISTS `mobil` (
  `nomobil` varchar(12) NOT NULL,
  `merktipe` varchar(20) NOT NULL,
  `tarif` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`nomobil`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.mobil: ~4 rows (approximately)
DELETE FROM `mobil`;
INSERT INTO `mobil` (`nomobil`, `merktipe`, `tarif`) VALUES
	('D-1234-ABC', 'Honda Brio', 300000),
	('D-1919-ASD', 'Toyota Avanza', 275000),
	('D-5656-DEF', 'Daihatsu Sirion', 250000),
	('D-9090-QWE', 'Honda BRV', 375000);

-- Dumping structure for table pbw.pemasok
CREATE TABLE IF NOT EXISTS `pemasok` (
  `kdpemasok` char(3) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `alamat` varchar(20) NOT NULL,
  `tlp` varchar(10) NOT NULL,
  PRIMARY KEY (`kdpemasok`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table pbw.pemasok: 3 rows
DELETE FROM `pemasok`;
/*!40000 ALTER TABLE `pemasok` DISABLE KEYS */;
INSERT INTO `pemasok` (`kdpemasok`, `nama`, `alamat`, `tlp`) VALUES
	('901', 'CV Wijaya', 'Dago 801', '2509090'),
	('902', 'Kharisma Jaya', 'Menado 80', '5302121'),
	('903', 'PT Bandung Food', 'Riau 987', '4390808');
/*!40000 ALTER TABLE `pemasok` ENABLE KEYS */;

-- Dumping structure for table pbw.penyewa
CREATE TABLE IF NOT EXISTS `penyewa` (
  `idpenyewa` char(3) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `alamat` varchar(20) NOT NULL,
  `telepon` varchar(10) NOT NULL,
  PRIMARY KEY (`idpenyewa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.penyewa: ~3 rows (approximately)
DELETE FROM `penyewa`;
INSERT INTO `penyewa` (`idpenyewa`, `nama`, `alamat`, `telepon`) VALUES
	('701', 'Tony Wijaya', 'Dago 801', '2509090'),
	('702', 'Ronald Santoso', 'Menado 120', '5302121'),
	('703', 'Yanti Kurnia', 'Riau 987', '4390808');

-- Dumping structure for table pbw.perpus
CREATE TABLE IF NOT EXISTS `perpus` (
  `NoPeminjaman` char(5) NOT NULL,
  `NPM` char(10) DEFAULT NULL,
  `KdKategori` char(1) DEFAULT NULL,
  `TglPinjam` date DEFAULT NULL,
  `TglKembali` date DEFAULT NULL,
  PRIMARY KEY (`NoPeminjaman`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.perpus: ~3 rows (approximately)
DELETE FROM `perpus`;
INSERT INTO `perpus` (`NoPeminjaman`, `NPM`, `KdKategori`, `TglPinjam`, `TglKembali`) VALUES
	('501', '2000110010', 'A', '2007-01-05', '2007-01-20'),
	('502', '2004110300', 'B', '2007-01-07', '2007-01-10'),
	('503', '2002110250', 'A', '2007-01-10', '2007-01-15');

-- Dumping structure for table pbw.rental
CREATE TABLE IF NOT EXISTS `rental` (
  `nobon` char(3) NOT NULL DEFAULT '0',
  `idpenyewa` char(3) NOT NULL DEFAULT '',
  `nomobil` varchar(12) NOT NULL DEFAULT '0',
  `lamarental` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`nobon`),
  KEY `FK_rental_penyewa` (`idpenyewa`),
  KEY `FK_rental_mobil` (`nomobil`),
  CONSTRAINT `FK_rental_mobil` FOREIGN KEY (`nomobil`) REFERENCES `mobil` (`nomobil`),
  CONSTRAINT `FK_rental_penyewa` FOREIGN KEY (`idpenyewa`) REFERENCES `penyewa` (`idpenyewa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.rental: ~9 rows (approximately)
DELETE FROM `rental`;
INSERT INTO `rental` (`nobon`, `idpenyewa`, `nomobil`, `lamarental`) VALUES
	('001', '702', 'D-9090-QWE', 12),
	('002', '703', 'D-1919-ASD', 3),
	('003', '701', 'D-5656-DEF', 2),
	('004', '703', 'D-1234-ABC', 6),
	('005', '701', 'D-1919-ASD', 1),
	('006', '702', 'D-9090-QWE', 3),
	('101', '703', 'D-1234-ABC', 5),
	('102', '702', 'D-9090-QWE', 1),
	('222', '703', 'D-1234-ABC', 7);

-- Dumping structure for table pbw.stock
CREATE TABLE IF NOT EXISTS `stock` (
  `Id` varchar(4) NOT NULL,
  `company` varchar(25) NOT NULL,
  `price1` int(11) NOT NULL,
  `price2` int(11) NOT NULL,
  `price3` int(11) NOT NULL,
  PRIMARY KEY (`company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.stock: ~6 rows (approximately)
DELETE FROM `stock`;
INSERT INTO `stock` (`Id`, `company`, `price1`, `price2`, `price3`) VALUES
	('BDI', 'Bank Danamon Tbk', 6400, 6350, 6650),
	('IKM', 'Indosiar Karya Media Tbk', 1010, 760, 790),
	('INP', 'Intraco Penta Tbk', 2725, 2650, 2775),
	('JSM', 'Jasa Marga Tbk', 3175, 3225, 3375),
	('MTP', 'Multipolar Tbk', 245, 255, 265),
	('XLA', 'XL Axiata Tbk', 5700, 5275, 5500);

-- Dumping structure for table pbw.trans
CREATE TABLE IF NOT EXISTS `trans` (
  `NoFaktur` varchar(10) NOT NULL,
  `NmPelanggan` varchar(50) DEFAULT NULL,
  `KdProduk` char(1) DEFAULT NULL,
  `Jumlah` int(11) DEFAULT NULL,
  `CaraBayar` enum('Tunai','Kredit') DEFAULT NULL,
  PRIMARY KEY (`NoFaktur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.trans: ~7 rows (approximately)
DELETE FROM `trans`;
INSERT INTO `trans` (`NoFaktur`, `NmPelanggan`, `KdProduk`, `Jumlah`, `CaraBayar`) VALUES
	('001', 'Johannes', 'B', 3, 'Tunai'),
	('002', 'Meiliana', 'A', 12, 'Kredit'),
	('003', 'Rudiyono', 'B', 7, 'Tunai'),
	('004', 'Lusiana', 'C', 20, 'Kredit'),
	('005', 'Meilani', 'A', 5, 'Tunai'),
	('006', 'Jordan', 'D', 2, 'Tunai'),
	('007', 'Deviani', 'C', 5, 'Tunai');

-- Dumping structure for table pbw.userx
CREATE TABLE IF NOT EXISTS `userx` (
  `user_id` varchar(15) NOT NULL,
  `psw` varchar(15) NOT NULL,
  `level` tinyint(4) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table pbw.userx: ~2 rows (approximately)
DELETE FROM `userx`;
INSERT INTO `userx` (`user_id`, `psw`, `level`) VALUES
	('dago', 'dago', 1),
	('juanda', 'juanda', 2);


-- Dumping database structure for pbwl
CREATE DATABASE IF NOT EXISTS `pbwl` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */;
USE `pbwl`;

-- Dumping structure for table pbwl.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pbwl.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table pbwl.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pbwl.migrations: ~9 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2025_09_19_141628_create_pegawai_table', 1),
	(6, '2014_10_12_100000_create_password_resets_table', 2),
	(7, '2025_09_25_030123_add_alamat_and_no_telepon_to_users_table', 3),
	(8, '2025_09_26_042249_add_profile_picture_to_users_table', 4),
	(9, '2025_09_26_092200_add_profile_picture_to_pegawai_table', 5);

-- Dumping structure for table pbwl.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pbwl.password_resets: ~0 rows (approximately)
DELETE FROM `password_resets`;

-- Dumping structure for table pbwl.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pbwl.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;
INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
	('lexazeed@gmail.com', '$2y$12$hUcO9t55FMrybskuYSEKd.MR8ZUjg0OUipXdDd53HY7Vbpmyhs9k2', '2025-09-23 18:42:37');

-- Dumping structure for table pbwl.pegawai
CREATE TABLE IF NOT EXISTS `pegawai` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `gaji` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pegawai_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pbwl.pegawai: ~3 rows (approximately)
DELETE FROM `pegawai`;
INSERT INTO `pegawai` (`id`, `nama_lengkap`, `jabatan`, `email`, `profile_picture`, `no_hp`, `tanggal_lahir`, `alamat`, `gaji`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Christ Patt', 'Manager', 'chrispatt@gmail.com', 'pegawai_pics/bYrGwUAzLzRzELfEAZkGZEZ5lYK0VQoeE4zRqqI5.jpg', '081234567890', '1998-05-12', 'Jakarta, Indonesia', 10000000.00, '2025-09-19 07:21:22', '2025-09-26 02:29:11', NULL),
	(2, 'Loid Visp', 'Marketing', 'Loid12@gmail.com', 'pegawai_pics/sSjkX3Z1CFE4sf2q04YtXpk1pephxrWEz5CieVy8.jpg', '089876543210', '1995-02-14', 'Tokyo, Japan', 5000000.00, '2025-09-19 07:21:22', '2025-09-26 02:29:22', NULL),
	(4, 'Antonny Mackie', 'Supervisor', 'ant0n1e.super@gmail.com', NULL, '081325277521', '1986-06-17', 'Jl. Victor Von Doom Blok Z23', 15000000.00, '2025-09-23 18:54:56', '2025-09-26 02:35:00', NULL);

-- Dumping structure for table pbwl.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pbwl.personal_access_tokens: ~0 rows (approximately)
DELETE FROM `personal_access_tokens`;

-- Dumping structure for table pbwl.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pbwl.users: ~2 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `alamat`, `no_telepon`, `profile_picture`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(3, 'Axel', 'xgv1807@gmail.com', 'Taman Kopo Indah 1 Blok A2 no 121', '083112814399', 'avatars/ABKtSsSEg5Vl2xyDcZhjBexHa5xAwgQtOIjOeL06.jpg', NULL, '$2y$12$dODWZZYjxeIrZnb/6BrA0u7v4rvmEHyVWead/6IKzZjaOF7Xa2ucW', 'oNSV2eUHoPrcPK7SxkSEY1GYrYsjHzFo2eCr9Z4QVvLlRsDeburoU3EXsCM1', '2025-09-23 18:53:31', '2025-09-26 02:27:37'),
	(4, 'Cat', 'wee@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$oDhTmd98cGZ.PbJVj5f0.uZPSdqeL3TUDTV8CWdYvCt8VYKZ0/hle', NULL, '2025-09-24 18:48:00', '2025-09-24 18:48:00');


-- Dumping database structure for project-edgiaxel
CREATE DATABASE IF NOT EXISTS `project-edgiaxel` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */;
USE `project-edgiaxel`;

-- Dumping structure for table project-edgiaxel.car_model
CREATE TABLE IF NOT EXISTS `car_model` (
  `car_model_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturer_id` int(11) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `base_rating` int(11) NOT NULL COMMENT 'Base performance rating (Hypercar: 90-100, GT3: 60-80)',
  PRIMARY KEY (`car_model_id`),
  UNIQUE KEY `model_name` (`model_name`),
  KEY `manufacturer_id` (`manufacturer_id`),
  CONSTRAINT `car_model_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.car_model: ~17 rows (approximately)
DELETE FROM `car_model`;
INSERT INTO `car_model` (`car_model_id`, `manufacturer_id`, `model_name`, `base_rating`) VALUES
	(1, 1, 'Aston Martin Valkyrie', 91),
	(2, 2, 'Porsche 963', 98),
	(3, 3, 'Toyota GR010 Hybrid', 100),
	(4, 4, 'Cadillac V-Series.R', 94),
	(5, 5, 'BMW M Hybrid V8', 95),
	(6, 6, 'Alpine A424', 92),
	(7, 7, 'Ferrari 499P', 97),
	(8, 8, 'Peugeot 9X8', 93),
	(9, 9, 'Chevrolet Corvette Z06 GT3.R', 78),
	(10, 10, 'McLaren 720S GT3 Evo', 79),
	(11, 11, 'Mercedes-AMG GT3 Evo', 77),
	(12, 12, 'Ford Mustang GT3', 75),
	(13, 13, 'Lexus RC F GT3', 76),
	(14, 1, 'Aston Martin Vantage AMR GT3 Evo', 80),
	(15, 5, 'BMW M4 GT3 Evo', 79),
	(16, 7, 'Ferrari 296 GT3', 80),
	(17, 2, 'Porsche 911 GT3 R (992)', 78);

-- Dumping structure for table project-edgiaxel.championship_season
CREATE TABLE IF NOT EXISTS `championship_season` (
  `season_id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL,
  `status` enum('Created','Ongoing','Finished') DEFAULT 'Created',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`season_id`),
  UNIQUE KEY `year` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.championship_season: ~2 rows (approximately)
DELETE FROM `championship_season`;
INSERT INTO `championship_season` (`season_id`, `year`, `status`, `created_at`) VALUES
	(1, 2025, 'Ongoing', '2025-10-17 17:53:57'),
	(3, 2020, 'Created', '2025-10-18 12:44:42');

-- Dumping structure for table project-edgiaxel.circuit
CREATE TABLE IF NOT EXISTS `circuit` (
  `circuit_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `country` varchar(50) NOT NULL,
  `length_km` decimal(5,3) NOT NULL,
  `race_type` enum('6 Hours','8–10 Hours','24 Hours') NOT NULL,
  PRIMARY KEY (`circuit_id`),
  UNIQUE KEY `uk_circuit_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.circuit: ~9 rows (approximately)
DELETE FROM `circuit`;
INSERT INTO `circuit` (`circuit_id`, `name`, `location`, `country`, `length_km`, `race_type`) VALUES
	(1, 'Lusail International Circuit', 'Lusail', 'Qatar', 5.419, '8–10 Hours'),
	(2, 'Imola Circuit', 'Imola', 'Italy', 4.909, '6 Hours'),
	(3, 'Circuit de Spa-Francorchamps', 'Stavelot', 'Belgium', 7.004, '6 Hours'),
	(4, 'Circuit de la Sarthe', 'Le Mans', 'France', 13.629, '24 Hours'),
	(5, 'Interlagos Circuit', 'São Paulo', 'Brazil', 4.309, '6 Hours'),
	(6, 'Circuit of the Americas', 'Austin, Texas', 'United States', 5.514, '6 Hours'),
	(7, 'Fuji Speedway', 'Oyama, Shizuoka', 'Japan', 4.563, '6 Hours'),
	(8, 'Bahrain International Circuit', 'Sakhir', 'Bahrain', 5.412, '8–10 Hours');

-- Dumping structure for table project-edgiaxel.driver
CREATE TABLE IF NOT EXISTS `driver` (
  `driver_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `nationality` varchar(50) NOT NULL,
  PRIMARY KEY (`driver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.driver: ~124 rows (approximately)
DELETE FROM `driver`;
INSERT INTO `driver` (`driver_id`, `first_name`, `last_name`, `nationality`) VALUES
	(1, 'Tom', 'Gamble', 'United Kingdom'),
	(2, 'Harry', 'Tincknell', 'United Kingdom'),
	(3, 'Ross', 'Gunn', 'United Kingdom'),
	(4, 'Alex', 'Riberas', 'Spain'),
	(5, 'Marco', 'Sørensen', 'Denmark'),
	(6, 'Roman', 'De Angelis', 'Canada'),
	(7, 'Julien', 'Andlauer', 'France'),
	(8, 'Michael', 'Christensen', 'Denmark'),
	(9, 'Mathieu', 'Jaminet', 'France'),
	(10, 'Nico', 'Müller', 'Switzerland'),
	(11, 'Kévin', 'Estre', 'France'),
	(12, 'Laurens', 'Vanthoor', 'Belgium'),
	(13, 'Matt', 'Campbell', 'Australia'),
	(14, 'Pascal', 'Wehrlein', 'Germany'),
	(15, 'Kamui', 'Kobayashi', 'Japan'),
	(16, 'Nyck', 'de Vries', 'Netherlands'),
	(17, 'Mike', 'Conway', 'United Kingdom'),
	(18, 'José María', 'López', 'Argentina'),
	(19, 'Brendon', 'Hartley', 'New Zealand'),
	(20, 'Ryō', 'Hirakawa', 'Japan'),
	(21, 'Sébastien', 'Buemi', 'Switzerland'),
	(22, 'Alex', 'Lynn', 'United Kingdom'),
	(23, 'Norman', 'Nato', 'France'),
	(24, 'Will', 'Stevens', 'United Kingdom'),
	(25, 'Earl', 'Bamber', 'New Zealand'),
	(26, 'Sébastien', 'Bourdais', 'France'),
	(27, 'Jenson', 'Button', 'United Kingdom'),
	(28, 'Kevin', 'Magnussen', 'Denmark'),
	(29, 'Raffaele', 'Marciello', 'Switzerland'),
	(30, 'Dries', 'Vanthoor', 'Belgium'),
	(31, 'René', 'Rast', 'Germany'),
	(32, 'Robin', 'Frijns', 'Netherlands'),
	(33, 'Sheldon', 'van der Linde', 'South Africa'),
	(34, 'Marco', 'Wittmann', 'Germany'),
	(35, 'Paul-Loup', 'Chatin', 'France'),
	(36, 'Ferdinand', 'Habsburg', 'Austria'),
	(37, 'Charles', 'Milesi', 'France'),
	(38, 'Jules', 'Gounon', 'France'),
	(39, 'Frédéric', 'Makowiecki', 'France'),
	(40, 'Mick', 'Schumacher', 'Germany'),
	(41, 'Antonio', 'Fuoco', 'Italy'),
	(42, 'Miguel', 'Molina', 'Spain'),
	(43, 'Nicklas', 'Nielsen', 'Denmark'),
	(44, 'James', 'Calado', 'United Kingdom'),
	(45, 'Antonio', 'Giovinazzi', 'Italy'),
	(46, 'Alessandro', 'Pier Guidi', 'Italy'),
	(47, 'Phil', 'Hanson', 'United Kingdom'),
	(48, 'Robert', 'Kubica', 'Poland'),
	(49, 'Yifei', 'Ye', 'China'),
	(50, 'Paul', 'di Resta', 'United Kingdom'),
	(51, 'Mikkel', 'Jensen', 'Denmark'),
	(52, 'Jean-Éric', 'Vergne', 'France'),
	(53, 'Loïc', 'Duval', 'France'),
	(54, 'Malthe', 'Jakobsen', 'Denmark'),
	(55, 'Stoffel', 'Vandoorne', 'Belgium'),
	(56, 'Théo', 'Pourchaire', 'France'),
	(57, 'Neel', 'Jani', 'Switzerland'),
	(58, 'Nico', 'Pino', 'Chile'),
	(59, 'Nicolás', 'Varrone', 'Argentina'),
	(60, 'Eduardo', 'Barrichello', 'Brazil'),
	(61, 'Valentin', 'Hasse-Clot', 'France'),
	(62, 'Derek', 'DeBoer', 'United States'),
	(63, 'Anthony', 'McIntosh', 'United States'),
	(64, 'Mattia', 'Drudi', 'Italy'),
	(65, 'Ian', 'James', 'United Kingdom'),
	(66, 'Zacharie', 'Robichon', 'Canada'),
	(67, 'François', 'Heriau', 'France'),
	(68, 'Simon', 'Mann', 'United States'),
	(69, 'Alessio', 'Rovera', 'Italy'),
	(70, 'Francesco', 'Castellacci', 'Italy'),
	(71, 'Thomas', 'Flohr', 'Switzerland'),
	(72, 'Davide', 'Rigon', 'Italy'),
	(73, 'Augusto', 'Farfus', 'Brazil'),
	(74, 'Yasser', 'Shahin', 'Australia'),
	(75, 'Timur', 'Boguslavskiy', 'France'),
	(76, 'Pedro', 'Ebrahim', 'Brazil'),
	(77, 'Ahmad', 'Al Harthy', 'Oman'),
	(78, 'Valentino', 'Rossi', 'Italy'),
	(79, 'Kelvin', 'van der Linde', 'South Africa'),
	(80, 'Jonny', 'Edgar', 'United Kingdom'),
	(81, 'Daniel', 'Juncadella', 'Spain'),
	(82, 'Ben', 'Keating', 'United States'),
	(83, 'Rui', 'Andrade', 'Angola'),
	(84, 'Charlie', 'Eastwood', 'Republic of Ireland'),
	(85, 'Tom', 'van Rompuy', 'Belgium'),
	(86, 'Sébastien', 'Baud', 'France'),
	(87, 'James', 'Cottingham', 'United Kingdom'),
	(88, 'Grégoire', 'Saucy', 'Switzerland'),
	(89, 'Sean', 'Gelael', 'Indonesia'),
	(90, 'Darren', 'Leung', 'United Kingdom'),
	(91, 'Marino', 'Sato', 'Japan'),
	(92, 'Matteo', 'Cairoli', 'Italy'),
	(93, 'Matteo', 'Cressoni', 'Italy'),
	(94, 'Claudio', 'Schiavoni', 'Italy'),
	(95, 'Brenton', 'Grove', 'Australia'),
	(96, 'Stephen', 'Grove', 'Australia'),
	(97, 'Andrew', 'Gilbert', 'United Kingdom'),
	(98, 'Lorcan', 'Hanafin', 'United Kingdom'),
	(99, 'Fran', 'Rueda', 'Spain'),
	(100, 'Lin', 'Hodenius', 'Netherlands'),
	(101, 'Maxime', 'Martin', 'Belgium'),
	(102, 'Christian', 'Ried', 'Germany'),
	(103, 'Martin', 'Berry', 'Australia'),
	(104, 'Ben', 'Barker', 'United Kingdom'),
	(105, 'Bernardo', 'Sousa', 'Portugal'),
	(106, 'Ben', 'Tuck', 'United Kingdom'),
	(107, 'Stefano', 'Gattuso', 'Italy'),
	(108, 'Giammarco', 'Levorato', 'Italy'),
	(109, 'Dennis', 'Olsen', 'Norway'),
	(110, 'Finn', 'Gehrsitz', 'Germany'),
	(111, 'Arnold', 'Robin', 'France'),
	(112, 'Ben', 'Burnicoat', 'United Kingdom'),
	(113, 'Esteban', 'Masson', 'France'),
	(114, 'Yuichi', 'Nakayama', 'Japan'),
	(115, 'Jack', 'Hawksworth', 'United Kingdom'),
	(116, 'Clemens', 'Schmid', 'Austria'),
	(117, 'Răzvan', 'Umbrărescu', 'Romania'),
	(118, 'Rahel', 'Frey', 'Switzerland'),
	(119, 'Célia', 'Martin', 'France'),
	(120, 'Michelle', 'Gatting', 'Denmark'),
	(121, 'Sarah', 'Bovy', 'Belgium'),
	(122, 'Ryan', 'Hardwick', 'United States'),
	(123, 'Richard', 'Lietz', 'Austria'),
	(124, 'Riccardo', 'Pera', 'Italy');

-- Dumping structure for table project-edgiaxel.manufacturer
CREATE TABLE IF NOT EXISTS `manufacturer` (
  `manufacturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `category` enum('Hypercar','LMGT3') NOT NULL,
  PRIMARY KEY (`manufacturer_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.manufacturer: ~14 rows (approximately)
DELETE FROM `manufacturer`;
INSERT INTO `manufacturer` (`manufacturer_id`, `name`, `country`, `category`) VALUES
	(1, 'Aston Martin', 'United Kingdom', 'Hypercar'),
	(2, 'Porsche', 'Germany', 'Hypercar'),
	(3, 'Toyota', 'Japan', 'Hypercar'),
	(4, 'Cadillac', 'United States', 'Hypercar'),
	(5, 'BMW', 'Germany', 'Hypercar'),
	(6, 'Alpine', 'France', 'Hypercar'),
	(7, 'Ferrari', 'Italy', 'Hypercar'),
	(8, 'Peugeot', 'France', 'Hypercar'),
	(9, 'Chevrolet', 'United States', 'LMGT3'),
	(10, 'McLaren', 'United Kingdom', 'LMGT3'),
	(11, 'Mercedes', 'Germany', 'LMGT3'),
	(12, 'Ford', 'United States', 'LMGT3'),
	(13, 'Lexus', 'Japan', 'LMGT3');

-- Dumping structure for table project-edgiaxel.season_circuit
CREATE TABLE IF NOT EXISTS `season_circuit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `season_id` int(11) NOT NULL,
  `circuit_id` int(11) NOT NULL,
  `race_index` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_race_per_season` (`season_id`,`race_index`),
  KEY `circuit_id` (`circuit_id`),
  CONSTRAINT `season_circuit_ibfk_1` FOREIGN KEY (`season_id`) REFERENCES `championship_season` (`season_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `season_circuit_ibfk_2` FOREIGN KEY (`circuit_id`) REFERENCES `circuit` (`circuit_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.season_circuit: ~8 rows (approximately)
DELETE FROM `season_circuit`;
INSERT INTO `season_circuit` (`id`, `season_id`, `circuit_id`, `race_index`) VALUES
	(9, 3, 8, 1),
	(10, 3, 4, 2),
	(11, 3, 3, 3),
	(12, 3, 6, 4),
	(13, 3, 7, 5),
	(14, 3, 2, 6),
	(15, 3, 5, 7),
	(16, 3, 1, 8);

-- Dumping structure for table project-edgiaxel.team
CREATE TABLE IF NOT EXISTS `team` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `car_number` varchar(5) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `car_model_id` int(11) NOT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `category` enum('Hypercar','LMGT3') NOT NULL,
  PRIMARY KEY (`team_id`),
  UNIQUE KEY `uk_car_number_name` (`car_number`,`team_name`),
  KEY `manufacturer_id` (`manufacturer_id`),
  KEY `car_model_id` (`car_model_id`),
  CONSTRAINT `team_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`),
  CONSTRAINT `team_ibfk_2` FOREIGN KEY (`car_model_id`) REFERENCES `car_model` (`car_model_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.team: ~36 rows (approximately)
DELETE FROM `team`;
INSERT INTO `team` (`team_id`, `car_number`, `team_name`, `manufacturer_id`, `car_model_id`, `nationality`, `category`) VALUES
	(1, '007', 'Aston Martin THOR Team', 1, 1, 'United States', 'Hypercar'),
	(2, '009', 'Aston Martin THOR Team', 1, 1, 'United States', 'Hypercar'),
	(3, '5', 'Porsche Penske Motorsport', 2, 2, 'Germany', 'Hypercar'),
	(4, '6', 'Porsche Penske Motorsport', 2, 2, 'Germany', 'Hypercar'),
	(5, '7', 'Toyota Gazoo Racing', 3, 3, 'Japan', 'Hypercar'),
	(6, '8', 'Toyota Gazoo Racing', 3, 3, 'Japan', 'Hypercar'),
	(7, '12', 'Cadillac Hertz Team Jota', 4, 4, 'United States', 'Hypercar'),
	(8, '38', 'Cadillac Hertz Team Jota', 4, 4, 'United States', 'Hypercar'),
	(9, '15', 'BMW M Team WRT', 5, 5, 'Germany', 'Hypercar'),
	(10, '20', 'BMW M Team WRT', 5, 5, 'Germany', 'Hypercar'),
	(11, '35', 'Alpine Endurance Team', 6, 6, 'France', 'Hypercar'),
	(12, '36', 'Alpine Endurance Team', 6, 6, 'France', 'Hypercar'),
	(13, '50', 'Ferrari AF Corse', 7, 7, 'Italy', 'Hypercar'),
	(14, '51', 'Ferrari AF Corse', 7, 7, 'Italy', 'Hypercar'),
	(15, '83', 'AF Corse', 7, 7, 'Italy', 'Hypercar'),
	(16, '93', 'Peugeot TotalEnergies', 8, 8, 'France', 'Hypercar'),
	(17, '94', 'Peugeot TotalEnergies', 8, 8, 'France', 'Hypercar'),
	(18, '99', 'Proton Competition', 2, 2, 'Germany', 'Hypercar'),
	(19, '10', 'Racing Spirit of Léman', 1, 14, 'France', 'LMGT3'),
	(20, '27', 'Heart of Racing Team', 1, 14, 'United States', 'LMGT3'),
	(21, '21', 'Vista AF Corse', 7, 16, 'Italy', 'LMGT3'),
	(22, '54', 'Vista AF Corse', 7, 16, 'Italy', 'LMGT3'),
	(23, '31', 'The Bend Team WRT', 5, 15, 'Belgium', 'LMGT3'),
	(24, '46', 'Team WRT', 5, 15, 'Belgium', 'LMGT3'),
	(25, '33', 'TF Sport', 9, 9, 'United Kingdom', 'LMGT3'),
	(26, '81', 'TF Sport', 9, 9, 'United Kingdom', 'LMGT3'),
	(27, '59', 'United Autosports', 10, 10, 'United Kingdom', 'LMGT3'),
	(28, '95', 'United Autosports', 10, 10, 'United Kingdom', 'LMGT3'),
	(29, '60', 'Iron Lynx', 11, 11, 'Italy', 'LMGT3'),
	(30, '61', 'Iron Lynx', 11, 11, 'Italy', 'LMGT3'),
	(31, '77', 'Proton Competition', 12, 12, 'Germany', 'LMGT3'),
	(32, '88', 'Proton Competition', 12, 12, 'Germany', 'LMGT3'),
	(33, '78', 'Akkodis ASP Team', 13, 13, 'France', 'LMGT3'),
	(34, '87', 'Akkodis ASP Team', 13, 13, 'France', 'LMGT3'),
	(35, '85', 'Iron Dames', 2, 17, 'Italy', 'LMGT3'),
	(36, '92', 'Manthey 1st Phorm', 2, 17, 'Germany', 'LMGT3');

-- Dumping structure for table project-edgiaxel.team_driver
CREATE TABLE IF NOT EXISTS `team_driver` (
  `team_driver_id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  PRIMARY KEY (`team_driver_id`),
  UNIQUE KEY `uk_team_driver` (`team_id`,`driver_id`),
  KEY `driver_id` (`driver_id`),
  CONSTRAINT `team_driver_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `team` (`team_id`),
  CONSTRAINT `team_driver_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `driver` (`driver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table project-edgiaxel.team_driver: ~124 rows (approximately)
DELETE FROM `team_driver`;
INSERT INTO `team_driver` (`team_driver_id`, `team_id`, `driver_id`) VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 1, 3),
	(4, 2, 4),
	(5, 2, 5),
	(6, 2, 6),
	(7, 3, 7),
	(8, 3, 8),
	(9, 3, 9),
	(10, 3, 10),
	(11, 4, 11),
	(12, 4, 12),
	(13, 4, 13),
	(14, 4, 14),
	(15, 5, 15),
	(16, 5, 16),
	(17, 5, 17),
	(18, 5, 18),
	(19, 6, 19),
	(20, 6, 20),
	(21, 6, 21),
	(22, 7, 22),
	(23, 7, 23),
	(24, 7, 24),
	(25, 8, 25),
	(26, 8, 26),
	(27, 8, 27),
	(28, 9, 28),
	(29, 9, 29),
	(30, 9, 30),
	(35, 11, 35),
	(36, 11, 36),
	(37, 11, 37),
	(38, 12, 38),
	(39, 12, 39),
	(40, 12, 40),
	(41, 13, 41),
	(42, 13, 42),
	(43, 13, 43),
	(44, 14, 44),
	(45, 14, 45),
	(46, 14, 46),
	(47, 15, 47),
	(48, 15, 48),
	(49, 15, 49),
	(50, 16, 50),
	(51, 16, 51),
	(52, 16, 52),
	(53, 17, 53),
	(54, 17, 54),
	(55, 17, 55),
	(56, 17, 56),
	(57, 18, 57),
	(58, 18, 58),
	(59, 18, 59),
	(60, 19, 60),
	(61, 19, 61),
	(62, 19, 62),
	(63, 19, 63),
	(64, 20, 64),
	(65, 20, 65),
	(66, 20, 66),
	(67, 21, 67),
	(68, 21, 68),
	(69, 21, 69),
	(70, 22, 70),
	(71, 22, 71),
	(72, 22, 72),
	(73, 23, 73),
	(74, 23, 74),
	(75, 23, 75),
	(76, 23, 76),
	(77, 24, 77),
	(78, 24, 78),
	(79, 24, 79),
	(80, 25, 80),
	(81, 25, 81),
	(82, 25, 82),
	(83, 26, 83),
	(84, 26, 84),
	(85, 26, 85),
	(86, 27, 86),
	(87, 27, 87),
	(88, 27, 88),
	(89, 28, 89),
	(90, 28, 90),
	(91, 28, 91),
	(100, 30, 100),
	(101, 30, 101),
	(102, 30, 102),
	(103, 30, 103),
	(104, 31, 104),
	(105, 31, 105),
	(106, 31, 106),
	(107, 32, 107),
	(108, 32, 108),
	(109, 32, 109),
	(116, 34, 116),
	(117, 34, 117),
	(118, 34, 18),
	(119, 34, 115),
	(120, 35, 118),
	(121, 35, 119),
	(122, 35, 120),
	(123, 35, 121),
	(124, 36, 122),
	(125, 36, 123),
	(126, 36, 124),
	(131, 10, 31),
	(132, 10, 32),
	(133, 10, 33),
	(134, 10, 34),
	(138, 33, 110),
	(139, 33, 111),
	(140, 33, 112),
	(141, 33, 113),
	(142, 29, 92),
	(143, 29, 93),
	(144, 29, 94),
	(145, 29, 99);


-- Dumping database structure for project_2023130007
CREATE DATABASE IF NOT EXISTS `project_2023130007` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */;
USE `project_2023130007`;

-- Dumping structure for table project_2023130007.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.cache: ~0 rows (approximately)
DELETE FROM `cache`;

-- Dumping structure for table project_2023130007.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.cache_locks: ~0 rows (approximately)
DELETE FROM `cache_locks`;

-- Dumping structure for table project_2023130007.catalog_banners
CREATE TABLE IF NOT EXISTS `catalog_banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `catalog_banners_order_unique` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.catalog_banners: ~3 rows (approximately)
DELETE FROM `catalog_banners`;
INSERT INTO `catalog_banners` (`id`, `title`, `image_path`, `order`, `created_at`, `updated_at`) VALUES
	(1, 'Anime & Manga Discount!', 'banners/1.jpg', 1, '2025-10-20 03:38:27', '2025-10-20 03:38:27'),
	(2, 'Movie & TV Event Savings!', 'banners/2.jpg', 2, '2025-10-20 03:38:27', '2025-10-20 03:38:27'),
	(3, 'Game Character Sale!', 'banners/3.jpg', 3, '2025-10-20 03:38:27', '2025-10-20 03:38:27');

-- Dumping structure for table project_2023130007.costumes
CREATE TABLE IF NOT EXISTS `costumes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `series` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `condition` varchar(255) NOT NULL,
  `price_per_day` bigint(20) unsigned NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 1,
  `main_image_path` varchar(255) NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `costumes_user_id_foreign` (`user_id`),
  CONSTRAINT `costumes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.costumes: ~27 rows (approximately)
DELETE FROM `costumes`;
INSERT INTO `costumes` (`id`, `user_id`, `name`, `series`, `size`, `condition`, `price_per_day`, `stock`, `main_image_path`, `is_approved`, `tags`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 3, 'Star Lord Helmet Jacket', 'Guardians of Galaxy', 'M', 'Excellent', 120000, 1, 'costumes/star_lord_helmet_jacket.jpg', 1, '["Movie","Star Lord","Sci-Fi"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(2, 3, 'Gundam Pilot Suit', 'Mobile Suit Gundam', 'M', 'Excellent', 95000, 1, 'costumes/gundam_pilot_suit.jpg', 1, '["Anime","Gundam","Mecha"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(3, 3, 'Flash Speedster Suit', 'DC Comics', 'M', 'Excellent', 110000, 1, 'costumes/flash_speedster_suit.jpg', 1, '["Movie","Flash","Superhero"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(4, 3, 'Alien Xenomorph Suit', 'Alien', 'M', 'Excellent', 150000, 1, 'costumes/alien_xenomorph_suit.jpg', 1, '["Movie","Xenomorph","Horror"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(5, 3, 'Jedi Knight Robes', 'Star Wars', 'M', 'Excellent', 60000, 1, 'costumes/jedi_knight_robes.jpg', 1, '["Movie","Jedi","Fantasy"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(6, 3, 'Master Chief Armor', 'Halo', 'M', 'Excellent', 130000, 1, 'costumes/master_chief_armor.jpg', 1, '["Game","Master Chief","Armor"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(7, 3, 'EVA Unit-01 Plugsuit', 'Evangelion', 'M', 'Excellent', 105000, 1, 'costumes/eva_unit-01_plugsuit.jpg', 1, '["Anime","Shinji","Mecha"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(8, 4, 'Sailor Moon Uniform', 'Sailor Moon', 'M', 'Excellent', 50000, 1, 'costumes/sailor_moon_uniform.jpg', 1, '["Anime","Sailor Moon","Magical Girl"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(9, 4, 'Elsa Ice Dress', 'Frozen', 'M', 'Excellent', 70000, 1, 'costumes/elsa_ice_dress.jpg', 1, '["Movie","Elsa","Princess"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(10, 4, 'Genshin Impact (Focalors)', 'Genshin Impact', 'M', 'Excellent', 85000, 1, 'costumes/genshin_impact_focalors.jpg', 1, '["Game","Focalors","Fantasy"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(11, 4, 'Witcher Geralt Armor', 'The Witcher', 'M', 'Excellent', 90000, 1, 'costumes/witcher_geralt_armor.jpg', 1, '["Game","Geralt","Fantasy"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(12, 4, 'Cinderella Ball Gown', 'Cinderella', 'M', 'Excellent', 80000, 1, 'costumes/cinderella_ball_gown.jpg', 1, '["Movie","Cinderella","Princess"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(13, 4, 'T-Rex Kigurumi', 'Jurassic Park', 'M', 'Excellent', 45000, 1, 'costumes/t-rex_kigurumi.jpg', 1, '["Other","T-Rex","Funny"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(14, 4, 'Daenerys Targaryen Gown', 'Game of Thrones', 'M', 'Excellent', 115000, 1, 'costumes/daenerys_targaryen_gown.jpg', 1, '["TV","Daenerys","Fantasy"]', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL),
	(15, 5, 'Naruto Hokage Outfit', 'Naruto', 'M', 'Excellent', 70000, 1, 'costumes/naruto_hokage_outfit.jpg', 1, '["Anime","Naruto","Ninja"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(16, 5, 'Attack on Titan Uniform', 'Attack on Titan', 'M', 'Excellent', 60000, 1, 'costumes/attack_on_titan_uniform.jpg', 1, '["Anime","Eren","Military"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(17, 5, 'Demon Slayer Tanjiro Kimono', 'Demon Slayer', 'M', 'Excellent', 65000, 1, 'costumes/demon_slayer_tanjiro_kimono.jpg', 1, '["Anime","Tanjiro","Kimono"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(18, 5, 'One Piece Luffy Gear 5', 'One Piece', 'M', 'Excellent', 125000, 1, 'costumes/one_piece_luffy_gear_5.jpg', 1, '["Anime","Luffy","Shonen"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(19, 5, 'Kirito Black Swordsman', 'Sword Art Online', 'M', 'Excellent', 75000, 1, 'costumes/kirito_black_swordsman.jpg', 1, '["Anime","Kirito","Game"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(20, 5, 'Fullmetal Alchemist Uniform', 'Fullmetal Alchemist', 'M', 'Excellent', 80000, 1, 'costumes/fullmetal_alchemist_uniform.jpg', 1, '["Anime","Edward","Military"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(21, 5, 'Lelouch Lamperouge Zero', 'Code Geass', 'M', 'Excellent', 100000, 1, 'costumes/lelouch_lamperouge_zero.jpg', 1, '["Anime","Lelouch","Uniform"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(22, 3, 'Deadpool Suit', 'Marvel', 'M', 'New', 150000, 1, 'costumes/deadpool_suit.jpg', 0, '["Movie","Deadpool","Anti-Hero"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(23, 3, 'Space Marine Armor', 'Warhammer 40k', 'M', 'New', 200000, 1, 'costumes/space_marine_armor.jpg', 0, '["Game","Space Marine","Armor"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(24, 4, 'Alice in Wonderland Dress', 'Disney', 'M', 'New', 70000, 1, 'costumes/alice_in_wonderland_dress.jpg', 0, '["Movie","Alice","Fantasy"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(25, 4, 'Belle Ball Gown', 'Beauty and the Beast', 'M', 'New', 90000, 1, 'costumes/belle_ball_gown.jpg', 0, '["Movie","Belle","Princess"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(26, 5, 'Ichigo Bankai', 'Bleach', 'M', 'New', 110000, 1, 'costumes/ichigo_bankai.jpg', 0, '["Anime","Ichigo","Shonen"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(27, 5, 'Goku Ultra Instinct', 'Dragon Ball', 'M', 'New', 130000, 1, 'costumes/goku_ultra_instinct.jpg', 0, '["Anime","Goku","Shonen"]', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL);

-- Dumping structure for table project_2023130007.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table project_2023130007.global_discounts
CREATE TABLE IF NOT EXISTS `global_discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rate` decimal(4,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.global_discounts: ~0 rows (approximately)
DELETE FROM `global_discounts`;

-- Dumping structure for table project_2023130007.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table project_2023130007.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table project_2023130007.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.migrations: ~11 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_10_05_164609_create_permission_tables', 1),
	(5, '2025_10_05_165010_create_personal_access_tokens_table', 1),
	(6, '2025_10_06_144819_create_costumes_table', 1),
	(7, '2025_10_06_144823_create_orders_table', 1),
	(8, '2025_10_06_153558_add_details_to_users_table', 1),
	(9, '2025_10_06_154025_create_renter_stores_table', 1),
	(10, '2025_10_15_153135_create_global_discounts_table', 1),
	(11, '2025_10_18_153106_create_catalog_banners_table', 1);

-- Dumping structure for table project_2023130007.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.model_has_permissions: ~0 rows (approximately)
DELETE FROM `model_has_permissions`;

-- Dumping structure for table project_2023130007.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.model_has_roles: ~5 rows (approximately)
DELETE FROM `model_has_roles`;
INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
	(1, 'App\\Models\\User', 1),
	(2, 'App\\Models\\User', 3),
	(2, 'App\\Models\\User', 4),
	(2, 'App\\Models\\User', 5),
	(3, 'App\\Models\\User', 2);

-- Dumping structure for table project_2023130007.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_code` varchar(255) NOT NULL,
  `costume_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_price` bigint(20) unsigned NOT NULL,
  `status` enum('waiting','confirmed','borrowed','returned','completed','rejected') NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  KEY `orders_costume_id_foreign` (`costume_id`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_costume_id_foreign` FOREIGN KEY (`costume_id`) REFERENCES `costumes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.orders: ~18 rows (approximately)
DELETE FROM `orders`;
INSERT INTO `orders` (`id`, `order_code`, `costume_id`, `user_id`, `start_date`, `end_date`, `total_price`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'ORD-001', 1, 2, '2025-11-04', '2025-11-06', 360000, 'waiting', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(2, 'ORD-002', 2, 2, '2025-10-25', '2025-10-28', 380000, 'confirmed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(3, 'ORD-003', 3, 2, '2025-10-18', '2025-10-25', 880000, 'borrowed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(4, 'ORD-004', 4, 2, '2025-10-10', '2025-10-17', 1200000, 'returned', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(5, 'ORD-005', 5, 2, '2025-08-27', '2025-09-01', 360000, 'completed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(6, 'ORD-006', 6, 2, '2025-08-20', '2025-08-24', 650000, 'rejected', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(7, 'ORD-007', 14, 2, '2025-11-04', '2025-11-06', 345000, 'waiting', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(8, 'ORD-008', 24, 2, '2025-10-25', '2025-10-28', 280000, 'confirmed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(9, 'ORD-009', 25, 2, '2025-10-18', '2025-10-25', 720000, 'borrowed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(10, 'ORD-010', 8, 2, '2025-10-10', '2025-10-17', 400000, 'returned', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(11, 'ORD-011', 9, 2, '2025-08-27', '2025-09-01', 420000, 'completed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(12, 'ORD-012', 10, 2, '2025-08-20', '2025-08-24', 425000, 'rejected', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(13, 'ORD-013', 18, 2, '2025-11-04', '2025-11-06', 375000, 'waiting', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(14, 'ORD-014', 19, 2, '2025-10-25', '2025-10-28', 300000, 'confirmed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(15, 'ORD-015', 20, 2, '2025-10-18', '2025-10-25', 640000, 'borrowed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(16, 'ORD-016', 21, 2, '2025-10-10', '2025-10-17', 800000, 'returned', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(17, 'ORD-017', 26, 2, '2025-08-27', '2025-09-01', 660000, 'completed', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL),
	(18, 'ORD-018', 27, 2, '2025-08-20', '2025-08-24', 650000, 'rejected', '2025-10-20 03:38:27', '2025-10-20 03:38:27', NULL);

-- Dumping structure for table project_2023130007.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table project_2023130007.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.permissions: ~0 rows (approximately)
DELETE FROM `permissions`;

-- Dumping structure for table project_2023130007.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.personal_access_tokens: ~0 rows (approximately)
DELETE FROM `personal_access_tokens`;

-- Dumping structure for table project_2023130007.renter_stores
CREATE TABLE IF NOT EXISTS `renter_stores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `store_logo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `renter_stores_user_id_unique` (`user_id`),
  UNIQUE KEY `renter_stores_store_name_unique` (`store_name`),
  CONSTRAINT `renter_stores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.renter_stores: ~3 rows (approximately)
DELETE FROM `renter_stores`;
INSERT INTO `renter_stores` (`id`, `user_id`, `store_name`, `description`, `store_logo_path`, `created_at`, `updated_at`) VALUES
	(1, 3, 'Cosmic Threads', 'The largest collection of space and sci-fi costumes!', 'store_logos/cosmicthreads.png', '2025-10-20 03:38:26', '2025-10-20 03:38:26'),
	(2, 4, 'Fairy Dust Rentals', 'Fantasy, magic, and royal attire for all your event needs.', 'store_logos/fairydustrentals.png', '2025-10-20 03:38:26', '2025-10-20 03:38:26'),
	(3, 5, 'Weeb Central', 'The hottest anime and manga threads in the Milky Way!', 'store_logos/weebcentral.png', '2025-10-20 03:38:27', '2025-10-20 03:38:27');

-- Dumping structure for table project_2023130007.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.roles: ~3 rows (approximately)
DELETE FROM `roles`;
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
	(1, 'admin', 'web', '2025-10-20 03:38:24', '2025-10-20 03:38:24'),
	(2, 'renter', 'web', '2025-10-20 03:38:25', '2025-10-20 03:38:25'),
	(3, 'user', 'web', '2025-10-20 03:38:25', '2025-10-20 03:38:25');

-- Dumping structure for table project_2023130007.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.role_has_permissions: ~0 rows (approximately)
DELETE FROM `role_has_permissions`;

-- Dumping structure for table project_2023130007.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.sessions: ~3 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('mcEHbTK7Rvyibw4amOYsQP3AMhbLCkGDjUkDXjKJ', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXJKNld5NkJIWEpuT1RJYXRRVnVWOXNiWDB3a05vZHltMzFwaExyVSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1761040174),
	('mWPij3gznokBID6gUf0ry5bczrm99aXI0uOp0wna', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZEcxWGRPOU94NWxFbE1TZWhRdFo0Z2k4WUhwMFJNMnc3T1RCWDZGNiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9teS1vcmRlcnMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1760972924),
	('qbGN4r1IYR2CrcXFUTfCcGaQNMWvj1pWVEVZ4I1a', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiR0R4dlhnWjNrenBtbFNKa2dwNUEyV0NiYjd3dzZnVEkwVU5KWlNIbiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2Nvc3R1bWVzL2FwcHJvdmFsIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9jb3N0dW1lcy9hcHByb3ZhbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1760968600);

-- Dumping structure for table project_2023130007.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table project_2023130007.users: ~5 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `phone_number`, `address`, `profile_picture`) VALUES
	(1, 'Admin Boss', 'admin@starium.test', NULL, '$2y$12$7wgTu1WjnIXf7W4h5XXlku6i8Nk2j1ipECCnUKU.AsQMVLx2rDBaq', 'GShcsGbszkisfmAd34LYXKzaIpPb1amwAc2Vi4tTz1aa8aesYchgZxZf5QhN', '2025-10-20 03:38:25', '2025-10-20 03:38:25', NULL, NULL, 'user_profiles/3.png'),
	(2, 'Regular User', 'user@starium.test', NULL, '$2y$12$dm2sHpqqrQSoSrCb7Zou9u1NtOsPEJ3MFm6svM5O.CBM6EZa0nfOi', 'eQVxrwM70dTeOE1et202E9FILJ2fBhteiGcerUVgqOA2I7wOuOMf9hTppm3m', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL, NULL, 'user_profiles/2.png'),
	(3, 'Captain Cosmic', 'renter1@starium.test', NULL, '$2y$12$p27tETe1rRrGy38SphAi6.ZqqbxI50TsfU11mo94TO0gQ.8mr1ywa', NULL, '2025-10-20 03:38:26', '2025-10-20 03:38:26', '0811-1234-5678', 'Andromeda Galaxy Hub 1', 'user_profiles/4.png'),
	(4, 'Princess Aurora', 'renter2@starium.test', NULL, '$2y$12$iZieEYUJgIgfjn4BYZuuq.dGMzO4QmGp3LozFG9yEVoNiIFPBbhwW', NULL, '2025-10-20 03:38:26', '2025-10-20 03:38:26', '0822-9876-5432', 'Nebula Cluster HQ 7', 'user_profiles/5.png'),
	(5, 'The Anime King', 'renter3@starium.test', NULL, '$2y$12$4.dkhiN8qYtjZdjTIM50NuJaFCQmLW0pPqoKe1k4/immK30EWx2Vq', NULL, '2025-10-20 03:38:27', '2025-10-20 03:38:27', '0833-1122-3344', 'Tokyo-3 Outpost 5', 'user_profiles/1.png');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
