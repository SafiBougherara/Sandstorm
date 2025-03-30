-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour sandstorm
CREATE DATABASE IF NOT EXISTS `sandstorm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `sandstorm`;

-- Listage de la structure de table sandstorm. categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `description` text,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table sandstorm.categories : ~8 rows (environ)
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `parent_id`, `created_at`) VALUES
	(1, 'Electronics', 'electronics', 'bi-laptop', 'Computers, phones, and other electronic devices', NULL, '2025-03-22 14:38:33'),
	(2, 'Vehicles', 'vehicles', 'bi-car-front', 'Cars, motorcycles, and other vehicles', NULL, '2025-03-22 14:38:33'),
	(3, 'Home & Garden', 'home-garden', 'bi-house', 'Furniture, gardening tools, and home accessories', NULL, '2025-03-22 14:38:33'),
	(4, 'Fashion', 'fashion', 'bi-bag', 'Clothing, shoes, and accessories', NULL, '2025-03-22 14:38:33'),
	(5, 'Sports & Leisure', 'sports-leisure', 'bi-bicycle', 'Sports equipment and outdoor gear', NULL, '2025-03-22 14:38:33'),
	(6, 'Books & Media', 'books-media', 'bi-book', 'Books, movies, music, and games', NULL, '2025-03-22 14:38:33'),
	(7, 'Services', 'services', 'bi-gear', 'Professional and personal services', NULL, '2025-03-22 14:38:33'),
	(8, 'Other', 'other', 'bi-three-dots', 'Miscellaneous items', NULL, '2025-03-22 14:38:33');

-- Listage de la structure de table sandstorm. favorites
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `listing_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_listing` (`user_id`,`listing_id`),
  KEY `listing_id` (`listing_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table sandstorm.favorites : ~0 rows (environ)

-- Listage de la structure de table sandstorm. listings
CREATE TABLE IF NOT EXISTS `listings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  `status` enum('active','sold','expired','draft') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table sandstorm.listings : ~0 rows (environ)
INSERT INTO `listings` (`id`, `title`, `description`, `price`, `location`, `user_id`, `category_id`, `status`, `created_at`, `updated_at`) VALUES
	(10, 'yyyyyy', 'oooooooooooo', 747.00, 'ooooooooooo', 7, 7, 'active', '2025-03-27 13:28:23', '2025-03-30 10:59:22');

-- Listage de la structure de table sandstorm. listing_images
CREATE TABLE IF NOT EXISTS `listing_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `listing_id` int NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `listing_id` (`listing_id`),
  CONSTRAINT `listing_images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table sandstorm.listing_images : ~1 rows (environ)
INSERT INTO `listing_images` (`id`, `listing_id`, `image_path`, `is_primary`, `created_at`) VALUES
	(6, 10, 'uploads/listings/67e55277ede2a_pricing-table-03.png', 1, '2025-03-27 13:28:23');

-- Listage de la structure de table sandstorm. messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `listing_id` int NOT NULL,
  `message` text NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `listing_id` (`listing_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table sandstorm.messages : ~0 rows (environ)

-- Listage de la structure de table sandstorm. reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reviewer_id` int NOT NULL,
  `reviewed_id` int NOT NULL,
  `listing_id` int NOT NULL,
  `rating` tinyint NOT NULL,
  `comment` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `reviewed_id` (`reviewed_id`),
  KEY `listing_id` (`listing_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewed_id`) REFERENCES `users` (`id`),
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table sandstorm.reviews : ~0 rows (environ)

-- Listage de la structure de table sandstorm. users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Listage des données de la table sandstorm.users : ~0 rows (environ)
INSERT INTO `users` (`id`, `mail`, `pass`, `username`, `created_at`, `updated_at`) VALUES
	(5, 'bougherarasafi@gmail.com', '$2y$10$Go3liftDoW3ABMtBDVGdPuZj10vldMf2GqE3bJLCsm7dxfTLAkrHe', 'safi', '2025-03-23 10:53:59', '2025-03-23 10:53:59'),
	(6, 'soso.verra@gmail.com', '$2y$10$hdackVeItOn36pPzG2vbZeUUYT6pqPF8WY4nemQIKm0Y2U6qPK5Wi', 'Sonny', '2025-03-27 07:45:20', '2025-03-27 07:45:20'),
	(7, 'test@test.fr', '$2y$10$Kc1xcxEO6CarES1IezoOtOmf7fTZnfEUoEKkAOfJtINKibiLuzgsW', 'test', '2025-03-27 13:24:52', '2025-03-30 11:07:24');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
