-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 21, 2025 at 11:41 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ims`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(191) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `name`) VALUES
(2, 1, 'Bag'),
(3, 1, 'asada'),
(4, 2, 'asdasd');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `category_id` int DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `name`, `description`, `category_id`, `price`, `quantity`, `image`, `date_added`, `date_updated`) VALUES
(1, 1, 'Jans', 'asad', 2, 240.00, 21, '1761033659_485866618_683895377308254_3288419452111830842_n (2).jpg', '2025-10-21 08:00:59', '2025-10-21 10:38:24'),
(2, 1, 'Jorts', 'asad', 2, 222.00, 11, '1761035483_WIN_20250330_02_50_11_Pro.jpg', '2025-10-21 08:31:23', '2025-10-21 10:38:30'),
(3, 2, 'adsa', 'aaaaa', 4, 22.00, 1, '1761043978_WIN_20250409_00_17_04_Pro.jpg', '2025-10-21 10:52:58', '2025-10-21 10:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `product_movements`
--

DROP TABLE IF EXISTS `product_movements`;
CREATE TABLE IF NOT EXISTS `product_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `movement_type` varchar(50) NOT NULL,
  `quantity_change` int NOT NULL,
  `new_quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_movements`
--

INSERT INTO `product_movements` (`id`, `product_id`, `movement_type`, `quantity_change`, `new_quantity`, `created_at`) VALUES
(1, 1, 'manual_add', 22, 24, '2025-10-21 08:18:35'),
(2, 1, 'manual_remove', -23, 1, '2025-10-21 08:18:56'),
(3, 1, 'manual_add', 21, 22, '2025-10-21 08:19:13'),
(4, 1, 'sale', -1, 21, '2025-10-21 08:23:24'),
(5, 2, 'initial_stock', 12, 12, '2025-10-21 08:31:23'),
(6, 2, 'sale', -1, 11, '2025-10-21 08:37:37'),
(7, 3, 'initial_stock', 11111, 11111, '2025-10-21 10:52:58'),
(8, 3, 'sale', -11111, 0, '2025-10-21 10:58:15'),
(9, 3, 'manual_add', 1, 1, '2025-10-21 10:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
CREATE TABLE IF NOT EXISTS `sales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quantity_sold` int NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `sale_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `user_id`, `quantity_sold`, `price_per_item`, `total_price`, `sale_date`) VALUES
(1, 1, 1, 1, 240.00, 240.00, '2025-10-21 08:23:24'),
(2, 2, 1, 1, 222.00, 222.00, '2025-10-21 08:37:37'),
(3, 3, 2, 11111, 22.00, 244442.00, '2025-10-21 10:58:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`) VALUES
(1, 'John Doe', 'john@gmail.com', '$2y$10$3AJ4jztzpRT2YIaEPAHfku.wh/TlVeUS8FXYsPvz49xAbSf6EUIgS'),
(2, 'asdada', 'jon@gmail.com', '$2y$10$azt9uWsYW5feKLcYc6OBPOP99cSyK0YBm0lQcDVYrC2IdmPf4G95K');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
