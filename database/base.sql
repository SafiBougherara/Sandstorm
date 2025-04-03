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
)
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
)
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
)
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
)
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
)