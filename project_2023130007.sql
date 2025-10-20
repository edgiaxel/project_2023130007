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

-- Dumping data for table project_2023130007.sessions: ~0 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('5vktl0n4psbVDQWj7Wg9B6lxW7Uh27vIwwpSts7q', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU2kwZHk4TFpOZ1BqdUllQnVSeFk2NkdVaEtJa2xaSVRkZDlGR2FKUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1760970504),
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
	(1, 'Admin Boss', 'admin@starium.test', NULL, '$2y$12$7wgTu1WjnIXf7W4h5XXlku6i8Nk2j1ipECCnUKU.AsQMVLx2rDBaq', 'TiRxDgipmwHrMs4l6jNvIgIjeXO2jMLaMrDYSUTBGvj4vK9Z9asbJvssb8yz', '2025-10-20 03:38:25', '2025-10-20 03:38:25', NULL, NULL, 'user_profiles/3.png'),
	(2, 'Regular User', 'user@starium.test', NULL, '$2y$12$dm2sHpqqrQSoSrCb7Zou9u1NtOsPEJ3MFm6svM5O.CBM6EZa0nfOi', 'vem3kvwO5lIpIhzr5KJSrEAMWhbHGAtsO3nPMYXEyo2IsckjhNprImmOY8ja', '2025-10-20 03:38:26', '2025-10-20 03:38:26', NULL, NULL, 'user_profiles/2.png'),
	(3, 'Captain Cosmic', 'renter1@starium.test', NULL, '$2y$12$p27tETe1rRrGy38SphAi6.ZqqbxI50TsfU11mo94TO0gQ.8mr1ywa', NULL, '2025-10-20 03:38:26', '2025-10-20 03:38:26', '0811-1234-5678', 'Andromeda Galaxy Hub 1', 'user_profiles/4.png'),
	(4, 'Princess Aurora', 'renter2@starium.test', NULL, '$2y$12$iZieEYUJgIgfjn4BYZuuq.dGMzO4QmGp3LozFG9yEVoNiIFPBbhwW', NULL, '2025-10-20 03:38:26', '2025-10-20 03:38:26', '0822-9876-5432', 'Nebula Cluster HQ 7', 'user_profiles/5.png'),
	(5, 'The Anime King', 'renter3@starium.test', NULL, '$2y$12$4.dkhiN8qYtjZdjTIM50NuJaFCQmLW0pPqoKe1k4/immK30EWx2Vq', NULL, '2025-10-20 03:38:27', '2025-10-20 03:38:27', '0833-1122-3344', 'Tokyo-3 Outpost 5', 'user_profiles/1.png');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
