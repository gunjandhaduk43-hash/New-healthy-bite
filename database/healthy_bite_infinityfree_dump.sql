-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: healthy_bite
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'Platform Super Admin','2026-08-30 10:20:38','2026-08-30 10:20:38'),(2,'Tenant Administrator','2026-08-30 10:20:38','2026-08-30 10:20:38'),(3,'Operations Admin','2026-08-30 10:20:38','2026-08-30 10:20:38'),(4,'Support Staff','2026-08-30 10:20:38','2026-08-30 10:20:38');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_branches_restaurant_id` (`restaurant_id`),
  KEY `idx_branches_status` (`status`),
  CONSTRAINT `fk_branches_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,1,'Indiranagar Branch','100 Feet Road, HAL 2nd Stage, Indiranagar','Bengaluru','Karnataka','+918025201234','active','2026-09-14 08:47:24','2026-09-16 03:27:59'),(2,1,'Koramangala Branch',NULL,'Bengaluru','Karnataka',NULL,'active','2026-09-16 03:27:59','2026-09-16 03:27:59'),(3,1,'Whitefield Branch',NULL,'Bengaluru','Karnataka',NULL,'active','2026-09-16 03:27:59','2026-09-16 03:27:59'),(4,2,'Koregaon Park Branch',NULL,'Pune','Maharashtra',NULL,'active','2026-09-16 03:27:59','2026-09-16 03:27:59'),(5,2,'FC Road Branch',NULL,'Pune','Maharashtra',NULL,'active','2026-09-16 03:27:59','2026-09-16 03:27:59'),(6,3,'Bandra Main Branch',NULL,'Mumbai','Maharashtra',NULL,'active','2026-09-16 03:27:59','2026-09-16 03:27:59');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_categories_restaurant_id` (`restaurant_id`),
  KEY `idx_categories_slug` (`slug`),
  KEY `idx_categories_restaurant_sort` (`restaurant_id`,`status`,`sort_order`),
  CONSTRAINT `fk_categories_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,1,'Bowls','bowls','Nutrient-packed warm and cold signature protein bowls','cat_bowls.jpg',2,'active','2026-09-14 08:47:24','2026-09-14 19:03:27'),(2,1,'Wraps','wraps','Whole-wheat high-protein roll-ups','cat_wraps.jpg',3,'active','2026-09-14 08:47:24','2026-09-14 19:03:27'),(3,1,'Beverages','beverages','Clean functional protein shakes and cold-pressed juices','cat_beverages.jpg',7,'active','2026-09-14 08:47:24','2026-09-14 19:03:27'),(4,1,'Desserts','desserts','Guilt-free unrefined sweet treats','cat_desserts.jpg',8,'active','2026-09-14 08:47:24','2026-09-14 19:03:27'),(5,1,'Main Meals','main-meals','Wholesome and delicious meals to fuel your day',NULL,1,'active','2026-09-14 19:03:27','2026-09-14 19:03:27'),(6,1,'Salads','salads','Fresh organic greens and antioxidant rich salads',NULL,4,'active','2026-09-14 19:03:27','2026-09-14 19:03:27'),(7,1,'Soups','soups','Hearty protein-infused broths and vegetable soups',NULL,5,'active','2026-09-14 19:03:27','2026-09-14 19:03:27'),(8,1,'Appetizers','appetizers','Light, guilt-free starters and grilled small plates',NULL,6,'active','2026-09-14 19:03:27','2026-09-14 19:03:27'),(9,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',9,'active','2026-09-22 02:08:14','2026-09-22 02:08:14'),(10,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test-52c4','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',10,'inactive','2026-09-22 02:12:24','2026-09-22 02:12:24'),(11,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test-97b8','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',11,'inactive','2026-09-22 02:13:11','2026-09-22 02:13:11'),(12,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test-118a','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',12,'inactive','2026-09-22 03:19:27','2026-09-22 03:19:27'),(13,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test-f169','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',13,'inactive','2026-09-22 03:27:33','2026-09-22 03:27:33'),(14,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test-8b96','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',14,'inactive','2026-09-23 03:17:58','2026-09-23 03:17:58'),(15,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test-d6c0','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',15,'inactive','2026-09-23 03:19:04','2026-09-23 03:19:04'),(16,1,'QA Dynamic Dessert Test','qa-dynamic-dessert-test-1d5d','Category created during automated QA testing','/assets/images/foods/placeholder-dish.svg',16,'inactive','2026-09-23 03:34:40','2026-09-23 03:34:40');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_customers_mobile` (`mobile`),
  KEY `idx_customers_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Gunjan Dhaduk','+919876543210','rahul.sharma@example.com','2026-09-14 08:47:24','2026-09-16 03:27:59'),(2,'Riya Mehta','+919988776655',NULL,'2026-09-14 08:57:57','2026-09-16 03:27:59'),(3,'Kabir Rao','+919876501234',NULL,'2026-09-14 09:07:33','2026-09-16 03:27:59'),(4,'Ananya Sen','9876543210','tester@healthybite.com','2026-09-14 09:23:22','2026-09-16 03:27:59'),(5,'Dashboard Tester','9876543210','tester@healthybite.com','2026-09-14 09:23:45','2026-09-14 09:23:45'),(6,'Dashboard Tester','9876543210','tester@healthybite.com','2026-09-14 09:24:17','2026-09-14 09:24:17'),(7,'Dashboard Tester','9876543210','tester@healthybite.com','2026-09-14 09:25:22','2026-09-14 09:25:22'),(8,'Dashboard Tester','9876543210','tester@healthybite.com','2026-09-14 09:26:26','2026-09-14 09:26:26'),(13,'gunjan','78951545454555',NULL,'2026-09-15 07:35:39','2026-09-15 07:35:39'),(15,'gg',NULL,NULL,'2026-09-15 08:08:47','2026-09-15 08:08:47'),(16,'gg',NULL,NULL,'2026-09-15 10:05:08','2026-09-15 10:05:08'),(17,'Rahul Sharma',NULL,NULL,'2026-09-15 11:20:49','2026-09-15 11:20:49'),(18,'gg',NULL,NULL,'2026-09-16 03:39:04','2026-09-16 03:39:04'),(19,'gg',NULL,NULL,'2026-09-17 00:18:03','2026-09-17 00:18:03'),(20,'tt',NULL,NULL,'2026-09-17 00:47:04','2026-09-17 00:47:04'),(21,'gg',NULL,NULL,'2026-09-17 03:35:18','2026-09-17 03:35:18'),(22,'Alex Rivera','9876543210',NULL,'2026-09-22 00:34:38','2026-09-22 00:34:38'),(23,'Rahul Sharma',NULL,NULL,'2026-09-22 00:51:02','2026-09-22 00:51:02'),(24,'QA Sugar Tester','9876543210',NULL,'2026-09-22 01:13:36','2026-09-22 01:13:36'),(25,'QA Sugar Tester','9876543210',NULL,'2026-09-22 01:14:03','2026-09-22 01:14:03'),(26,'QA Sugar Tester','9876543210',NULL,'2026-09-22 01:15:08','2026-09-22 01:15:08'),(27,'QA Sugar Tester','9876543210',NULL,'2026-09-22 01:15:32','2026-09-22 01:15:32'),(28,'QA Null Sugar Tester','9876543210',NULL,'2026-09-22 01:15:32','2026-09-22 01:15:32'),(29,'QA Automated Dynamic Test','9876543210','qa.test@healthybite.local','2026-09-22 02:12:24','2026-09-22 02:12:24'),(30,'QA Automated Dynamic Test','9876543210','qa.test@healthybite.local','2026-09-22 02:13:11','2026-09-22 02:13:11'),(31,'QA Sugar Tester','9876543210',NULL,'2026-09-22 02:13:31','2026-09-22 02:13:31'),(32,'QA Null Sugar Tester','9876543210',NULL,'2026-09-22 02:13:31','2026-09-22 02:13:31'),(33,'QA Automated Dynamic Test','9876543210','qa.test@healthybite.local','2026-09-22 03:19:27','2026-09-22 03:19:27'),(34,'QA Automated Dynamic Test','9876543210','qa.test@healthybite.local','2026-09-22 03:27:33','2026-09-22 03:27:33'),(35,'ff',NULL,NULL,'2026-09-22 03:29:09','2026-09-22 03:29:09'),(36,'QA Automated Dynamic Test','9876543210','qa.test@healthybite.local','2026-09-23 03:18:00','2026-09-23 03:18:00'),(37,'QA Automated Dynamic Test','9876543210','qa.test@healthybite.local','2026-09-23 03:19:04','2026-09-23 03:19:04'),(38,'QA Automated Dynamic Test','9876543210','qa.test@healthybite.local','2026-09-23 03:34:40','2026-09-23 03:34:40'),(39,'gunjan',NULL,NULL,'2026-10-01 12:18:29','2026-10-01 12:18:29');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_customizations`
--

DROP TABLE IF EXISTS `food_customizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `food_customizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `food_item_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `group_name` varchar(100) NOT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `calories_adjustment` int(11) DEFAULT NULL,
  `protein_adjustment` decimal(6,2) DEFAULT NULL,
  `carbs_adjustment` decimal(6,2) DEFAULT NULL,
  `fat_adjustment` decimal(6,2) DEFAULT NULL,
  `fiber_adjustment` decimal(6,2) DEFAULT NULL,
  `sugar_adjustment` decimal(6,2) DEFAULT NULL,
  `sodium_adjustment` decimal(7,2) DEFAULT NULL,
  `caffeine_adjustment` decimal(6,2) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `min_quantity` int(10) unsigned NOT NULL DEFAULT 0,
  `max_quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_food_customizations_food_item_id` (`food_item_id`),
  KEY `idx_food_customizations_group` (`food_item_id`,`group_name`),
  KEY `idx_food_customizations_available` (`is_available`),
  CONSTRAINT `fk_food_customizations_food_item` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_customizations`
--

LOCK TABLES `food_customizations` WRITE;
/*!40000 ALTER TABLE `food_customizations` DISABLE KEYS */;
INSERT INTO `food_customizations` VALUES (11,13,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(12,13,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(13,14,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(14,14,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(15,15,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(16,15,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(17,16,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(18,16,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(19,17,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(20,17,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(21,18,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(22,18,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(23,19,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(24,19,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(25,20,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(26,20,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(27,21,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(28,21,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(29,22,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(30,22,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(31,23,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(32,23,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(33,24,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(34,24,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(35,25,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(36,25,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(37,26,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(38,26,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(39,27,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(40,27,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(41,28,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(42,28,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(43,29,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(44,29,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(45,30,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(46,30,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(49,32,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(50,32,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(51,33,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(52,33,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(53,34,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(54,34,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(55,35,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(56,35,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(57,36,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(58,36,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(59,37,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(60,37,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(61,38,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(62,38,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(63,39,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(64,39,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(65,40,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(66,40,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(67,41,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(68,41,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(69,42,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(70,42,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(71,43,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(72,43,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(73,44,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(74,44,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(75,45,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(76,45,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(77,46,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(78,46,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(79,47,'Extra Protein Scoop / Serving',NULL,'Portion Add-on',40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(80,47,'Avocado Slices',NULL,'Portion Add-on',50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,0,0,1,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(81,1,'Brown rice','','1. Choose base',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(82,1,'Quinoa','','1. Choose base',30.00,40,4.00,6.00,1.00,NULL,0.50,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(83,1,'Tandoori paneer · 100 g','','2. Choose protein',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(84,1,'Extra paneer · 50 g','','2. Choose protein',40.00,90,10.00,1.00,5.00,NULL,0.25,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(85,1,'Mint yogurt','','3. Choose sauce',0.00,0,0.00,0.00,0.00,NULL,1.00,NULL,NULL,0,0,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(86,1,'Spicy green chutney','','3. Choose sauce',0.00,0,0.00,0.00,0.00,NULL,0.50,NULL,NULL,0,0,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(87,1,'Pickled onion','','4. Toppings & add-ons',0.00,10,0.20,2.00,0.00,NULL,1.50,NULL,NULL,0,0,4,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(88,1,'Avocado','','4. Toppings & add-ons',50.00,80,1.00,4.00,7.00,NULL,0.30,NULL,NULL,0,0,4,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(89,1,'Roasted seeds','','4. Toppings & add-ons',25.00,45,3.00,1.50,3.50,NULL,0.20,NULL,NULL,0,0,4,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(90,1,'Cherry tomatoes','','4. Toppings & add-ons',0.00,15,0.80,3.00,0.20,NULL,1.20,NULL,NULL,0,0,4,1,4,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(91,1,'Extra greens','','4. Toppings & add-ons',20.00,10,1.00,1.50,0.10,NULL,0.20,NULL,NULL,0,0,4,1,5,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(92,1,'Grilled mushrooms','','4. Toppings & add-ons',40.00,45,3.00,4.00,1.50,NULL,0.40,NULL,NULL,0,0,4,1,6,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(93,2,'Steamed Brown Rice','','1. Choose base',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(94,2,'Riced Cauliflower','','1. Choose base',35.00,-110,2.00,-28.00,0.00,NULL,-0.50,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(95,2,'Grilled Chicken Breast · 120 g','','2. Choose protein',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(96,2,'Double Chicken · Extra 80 g','','2. Choose protein',70.00,120,22.00,0.00,3.00,NULL,0.00,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(97,2,'Citrus Herb Vinaigrette','','3. Choose sauce',0.00,0,0.00,0.00,0.00,NULL,1.00,NULL,NULL,0,0,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(98,2,'Teriyaki Sesame Glaze','','3. Choose sauce',0.00,25,0.50,5.00,0.50,NULL,4.50,NULL,NULL,0,0,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(99,2,'Steamed Broccoli','','4. Toppings & add-ons',0.00,20,2.00,3.00,0.20,NULL,0.50,NULL,NULL,0,0,4,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(100,2,'Hass Avocado','','4. Toppings & add-ons',50.00,80,1.00,4.00,7.00,NULL,0.30,NULL,NULL,0,0,4,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(101,2,'Toasted Sesame Seeds','','4. Toppings & add-ons',20.00,35,1.50,1.00,3.00,NULL,0.10,NULL,NULL,0,0,4,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(102,2,'Grilled Mushrooms','','4. Toppings & add-ons',40.00,45,3.00,4.00,1.50,NULL,0.40,NULL,NULL,0,0,4,1,4,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(103,48,'Sourdough Artisan Crust','','1. Choose Crust',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(104,48,'High-Protein Whole Wheat Crust','','1. Choose Crust',35.00,20,7.00,-4.00,0.50,NULL,0.50,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(105,48,'San Marzano Tomato & Low-Fat Mozzarella','','2. Sauce & Cheese',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(106,48,'Pesto Base with Buffalo Mozzarella','','2. Sauce & Cheese',45.00,60,4.00,1.00,5.00,NULL,0.50,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(107,48,'Kalamata Olives','','3. Gourmet Toppings',25.00,30,0.30,1.00,3.00,NULL,0.00,NULL,NULL,0,0,4,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(108,48,'Charred Bell Peppers','','3. Gourmet Toppings',0.00,15,0.50,3.00,0.10,NULL,1.20,NULL,NULL,0,0,4,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(109,48,'Grilled Mushrooms','','3. Gourmet Toppings',35.00,35,2.50,3.00,1.00,NULL,0.40,NULL,NULL,0,0,4,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(110,48,'Sliced Jalapenos','','3. Gourmet Toppings',0.00,10,0.20,1.50,0.00,NULL,0.20,NULL,NULL,0,0,4,1,4,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(111,48,'Extra Paneer Cubes','','3. Gourmet Toppings',45.00,70,8.00,1.00,4.00,NULL,0.40,NULL,NULL,0,0,4,1,5,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(112,49,'Whole Wheat Brioche Bun','','1. Bun Choice',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(113,49,'Multigrain Seeded Bun','','1. Bun Choice',20.00,15,3.00,2.00,1.00,NULL,0.50,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(114,49,'Crisp Lettuce Wrap (Low-Carb)','','1. Bun Choice',0.00,-130,-1.00,-25.00,-1.00,NULL,-1.50,NULL,NULL,1,1,1,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(115,49,'Grilled Herb Chicken Patty','','2. Patty Choice',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(116,49,'Double Patty (Extra 100g)','','2. Patty Choice',80.00,150,24.00,0.00,4.00,NULL,0.00,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(117,49,'Chipotle Greek Yogurt Spread','','3. Sauce Choice',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,0,0,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(118,49,'Mint Coriander Mayo','','3. Sauce Choice',0.00,20,0.50,1.00,1.80,NULL,0.50,NULL,NULL,0,0,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(119,49,'Caramelized Onions','','4. Add-ons',0.00,15,0.30,3.00,0.10,NULL,2.50,NULL,NULL,0,0,3,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(120,49,'Dill Pickles','','4. Add-ons',0.00,5,0.10,1.00,0.00,NULL,0.30,NULL,NULL,0,0,3,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(121,49,'Fresh Avocado Slices','','4. Add-ons',45.00,65,1.00,3.00,6.00,NULL,0.30,NULL,NULL,0,0,3,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(122,49,'Low-Fat Cheddar Slice','','4. Add-ons',30.00,50,4.00,0.50,3.50,NULL,0.20,NULL,NULL,0,0,3,1,4,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(123,31,'Whole Wheat Herb Croutons','','Garnish & Crunch',25.00,40,1.50,7.00,1.00,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(124,31,'Toasted Pumpkin Seeds','','Garnish & Crunch',25.00,35,2.00,1.00,2.50,NULL,0.20,NULL,NULL,0,0,2,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(125,31,'Grated Parmesan Sprinkle','','Garnish & Crunch',30.00,35,3.00,0.20,2.50,NULL,0.00,NULL,NULL,0,0,2,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(126,4,'Skimmed Milk','','1. Milk Choice',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(127,4,'Barista Oat Milk','','1. Milk Choice',30.00,25,-1.00,5.00,1.00,NULL,3.50,NULL,NULL,1,1,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(128,4,'Almond Milk','','1. Milk Choice',30.00,-15,-1.50,-1.00,0.50,NULL,0.50,NULL,NULL,1,1,1,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(129,4,'Regular Ice','','2. Ice Level',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,0,0,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(130,4,'Less Ice','','2. Ice Level',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,0,0,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(131,4,'Extra Half Scoop Whey Isolate','','3. Boosters',35.00,55,12.00,0.50,0.50,NULL,0.50,NULL,NULL,0,0,2,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(132,4,'Chia Seeds Infusion','','3. Boosters',20.00,30,1.50,2.00,2.00,NULL,0.20,NULL,NULL,0,0,2,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(133,7,'Zero-Cal Chocolate Drizzle','','Guilt-Free Toppings',20.00,15,0.20,3.00,0.20,NULL,0.00,NULL,NULL,0,0,3,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(134,7,'Crushed Roasted Almonds','','Guilt-Free Toppings',30.00,50,2.00,1.50,4.50,NULL,0.50,NULL,NULL,0,0,3,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(135,7,'Fresh Wildberry Compote','','Guilt-Free Toppings',30.00,25,0.40,6.00,0.10,NULL,4.50,NULL,NULL,0,0,3,1,3,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(136,6,'Served Warm (Oven Heats)','Warmed up for gooey fudge center','1. Serving Style',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,1,'2026-09-15 12:14:12','2026-09-22 01:05:18'),(137,6,'Served Chilled (Dense Fudge)','Chilled firm texture','1. Serving Style',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,1,2,'2026-09-15 12:14:12','2026-09-22 01:05:18'),(138,6,'Berry Compote','Fresh tart berry reduction','2. Gourmet Toppings',30.00,25,0.50,6.00,0.20,NULL,4.50,NULL,NULL,0,0,3,1,1,'2026-09-15 12:14:12','2026-09-22 01:05:18'),(139,6,'Roasted Almond Flakes','Crunchy toasted sliced almonds','2. Gourmet Toppings',25.00,45,2.00,1.50,3.50,NULL,0.50,NULL,NULL,0,0,3,1,2,'2026-09-15 12:14:12','2026-09-22 01:05:18'),(140,6,'Sugar-Free Dark Chocolate Drizzle','Rich antioxidant dark cocoa drizzle','2. Gourmet Toppings',20.00,30,0.50,2.00,2.00,NULL,0.50,NULL,NULL,0,0,3,1,3,'2026-09-15 12:14:12','2026-09-22 01:05:18'),(141,6,'Scoop of Protein Vanilla Ice Cream','High-protein low-sugar vanilla scoop','3. Add-ons',60.00,90,10.00,8.00,2.50,NULL,3.00,NULL,NULL,0,0,1,1,1,'2026-09-15 12:14:12','2026-09-22 01:05:18');
/*!40000 ALTER TABLE `food_customizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_items`
--

DROP TABLE IF EXISTS `food_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `food_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `allergens` varchar(255) DEFAULT NULL,
  `food_type` enum('vegetarian','non_vegetarian','vegan','jain','other') NOT NULL DEFAULT 'vegetarian',
  `base_price` decimal(10,2) NOT NULL,
  `calories` int(10) unsigned DEFAULT NULL,
  `protein` decimal(6,2) DEFAULT NULL,
  `carbs` decimal(6,2) DEFAULT NULL,
  `fat` decimal(6,2) DEFAULT NULL,
  `fiber` decimal(6,2) DEFAULT NULL,
  `sugar` decimal(6,2) DEFAULT NULL,
  `sodium` decimal(7,2) DEFAULT NULL,
  `caffeine` decimal(6,2) DEFAULT NULL,
  `serving_size` varchar(100) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_food_items_restaurant_id` (`restaurant_id`),
  KEY `idx_food_items_category_id` (`category_id`),
  KEY `idx_food_items_slug` (`slug`),
  KEY `idx_food_items_available` (`is_available`),
  KEY `idx_food_items_featured` (`is_featured`),
  KEY `idx_food_items_popular` (`is_popular`),
  KEY `idx_food_items_scoping` (`restaurant_id`,`category_id`,`is_available`),
  CONSTRAINT `fk_food_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_food_items_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_items`
--

LOCK TABLES `food_items` WRITE;
/*!40000 ALTER TABLE `food_items` DISABLE KEYS */;
INSERT INTO `food_items` VALUES (1,1,1,'Paneer Protein Bowl','paneer-protein-bowl','Tandoori paneer, brown rice, fresh greens, cucumber, pickled onion and mint yogurt.','https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&auto=format&fit=crop&q=80','Paneer, Brown Rice, Lettuce, Cucumber, Pickled Onion, Mint Yogurt, Fresh Herbs','Contains milk. Prepared in a facility that also handles nuts, soy and gluten.','vegetarian',249.00,520,38.00,48.00,18.00,8.00,5.00,420.00,NULL,'420 g',1,1,1,'2026-09-14 08:47:24','2026-09-22 01:05:17'),(2,1,1,'Chicken Rice Bowl','chicken-rice-bowl','Grilled sous-vide chicken breast, steamed brown rice, fresh steamed broccoli, cherry tomatoes and citrus herb dressing.','https://images.unsplash.com/photo-1543339308-43e59d6b73a6?w=800&auto=format&fit=crop&q=80','Chicken Breast, Brown Rice, Steamed Broccoli, Cherry Tomatoes, Citrus Vinaigrette, Fresh Herbs','Prepared in a facility that handles nuts, dairy, soy and gluten.','non_vegetarian',279.00,560,44.00,50.00,14.00,6.00,4.00,510.00,NULL,'440 g',1,1,1,'2026-09-14 08:47:24','2026-09-22 01:05:17'),(3,1,2,'Grilled Paneer Wrap','grilled-paneer-wrap','Whole wheat tortilla stuffed with grilled paneer, crunchy peppers, and mint curd dressing.','https://images.unsplash.com/photo-1626700051175-6818013e1d4f?w=800&auto=format&fit=crop&q=80','Whole Wheat Tortilla, Grilled Paneer, Bell Peppers, Romaine, Mint Yogurt Sauce','Gluten, Dairy','vegetarian',219.00,410,22.00,35.00,16.00,6.00,3.00,380.00,NULL,'240g wrap',1,1,1,'2026-09-14 08:47:24','2026-09-22 01:05:17'),(4,1,3,'Iced Protein Coffee','iced-protein-coffee','Cold-brewed Arabica espresso blended with pure isolate whey protein, cold milk, and natural vanilla.','https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=800&auto=format&fit=crop&q=80','Arabica Espresso, Whey Isolate, Low-Fat Milk, Pure Vanilla Extract','Dairy','vegetarian',165.00,140,15.00,8.00,5.00,0.00,4.00,110.00,95.00,'350ml cup',1,1,1,'2026-09-14 08:47:24','2026-09-22 01:05:17'),(5,1,3,'Banana Protein Smoothie','banana-protein-smoothie','Thick creamy blend of Robusta bananas, Greek yogurt, chia seeds, and clean plant protein.','https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=800&auto=format&fit=crop&q=80','Bananas, Greek Yogurt, Plant Protein, Chia Seeds, Ceylon Cinnamon','Dairy','vegetarian',195.00,290,24.00,36.00,4.00,5.00,18.00,140.00,NULL,'400ml glass',1,0,1,'2026-09-14 08:47:24','2026-09-22 01:05:17'),(6,1,4,'Chocolate Protein Brownie','chocolate-protein-brownie','Dense, decadent fudge brownie made with Dutch dark cocoa, almond flour, and whey protein. Refined sugar free.','https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=800&auto=format&fit=crop&q=80','Almond Flour, Dark Cocoa, Whey Isolate, Stevia, Eggs, Coconut Oil','Nuts, Dairy, Egg','vegetarian',149.00,210,12.00,20.00,9.00,4.00,8.00,120.00,NULL,'85g square',1,1,1,'2026-09-14 08:47:24','2026-09-22 01:05:17'),(7,1,4,'Protein Ice Cream','protein-ice-cream','Slow-churned low-fat protein ice cream sweetened with erythritol.','https://images.unsplash.com/photo-1570197788417-0e82375c9371?w=800&auto=format&fit=crop&q=80','Milk Protein, Skimmed Milk, Erythritol, Vanilla Extract','Dairy','vegetarian',180.00,180,20.00,16.00,5.00,2.00,6.00,90.00,NULL,'120g cup',1,0,1,'2026-09-14 08:47:24','2026-09-22 01:05:17'),(12,1,5,'Grilled Herb Chicken Platter','grilled-herb-chicken-platter','Tender herb-marinated chicken breast served with steamed brown rice and roasted garden vegetables.','https://images.unsplash.com/photo-1532550907401-a500c9a57435?w=800&auto=format&fit=crop&q=80','Chicken Breast, Brown Rice, Broccoli, Zucchini, Bell Peppers, Olive Oil, Fresh Rosemary',NULL,'non_vegetarian',289.00,540,44.00,32.00,16.00,6.00,4.00,450.00,NULL,NULL,1,0,1,'2026-09-14 19:03:27','2026-09-22 01:05:17'),(13,1,5,'Paneer Steak with Quinoa & Greens','paneer-steak-quinoa-greens','Thick-cut grilled cottage cheese steak accompanied by organic quinoa, wilted spinach, and balsamic glaze.','https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=800&auto=format&fit=crop&q=80','Paneer, Quinoa, Baby Spinach, Cherry Tomatoes, Olive Oil, Balsamic Reduction','Dairy','vegetarian',269.00,510,32.00,36.00,20.00,8.00,5.00,410.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(14,1,5,'Smoked Salmon with Steamed Asparagus','smoked-salmon-steamed-asparagus','Wild Atlantic salmon fillet seared with dill lemon butter, served alongside fresh asparagus spears and baby potatoes.','https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=800&auto=format&fit=crop&q=80','Atlantic Salmon, Tender Asparagus, Baby Potatoes, Dill, Lemon, Cold Pressed Olive Oil','Fish','non_vegetarian',349.00,490,42.00,14.00,18.00,4.00,2.00,480.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(15,1,5,'Tofu & Broccoli Stir-Fry Meal','tofu-broccoli-stir-fry-meal','Crisp organic tofu cubes tossed in a light ginger-tamari glaze with crunchy florets and red quinoa.','https://images.unsplash.com/photo-1512058564366-18510be2db19?w=800&auto=format&fit=crop&q=80','Organic Firm Tofu, Broccoli, Snap Peas, Red Quinoa, Ginger, Tamari, Sesame Seeds','Soy, Sesame','vegan',239.00,390,26.00,42.00,12.00,7.00,4.00,380.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(16,1,5,'Rosemary Garlic Chicken Breast','rosemary-garlic-chicken-breast','Slow-grilled lean chicken breast seasoned with mountain rosemary, crushed garlic, and steamed baby greens.','https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=800&auto=format&fit=crop&q=80','Skinless Chicken Breast, Garlic, Rosemary, Olive Oil, French Beans, Carrots',NULL,'non_vegetarian',279.00,460,48.00,18.00,12.00,4.00,2.00,420.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(17,1,1,'Grilled Chicken Bowl','grilled-chicken-bowl','Lean grilled chicken, brown rice, fresh veggies, house sauce.','https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&auto=format&fit=crop&q=80','Lean Chicken, Brown Rice, Broccoli, Carrots, House Herb Sauce',NULL,'non_vegetarian',249.00,520,42.00,38.00,14.00,6.00,3.00,460.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(18,1,1,'Salmon Power Bowl','salmon-power-bowl','Grilled salmon, brown rice, avocado, steamed vegetables.','https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&auto=format&fit=crop&q=80','Atlantic Salmon, Brown Rice, Hass Avocado, Steamed Veggies, Sesame','Fish, Sesame','non_vegetarian',299.00,560,40.00,42.00,18.00,5.00,3.00,490.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(19,1,1,'Quinoa Buddha Bowl','quinoa-buddha-bowl','Roasted veggies, quinoa, chickpeas, hummus.','https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?w=800&auto=format&fit=crop&q=80','Tricolor Quinoa, Spiced Chickpeas, Roasted Pumpkin, Beetroot Hummus','Sesame','vegan',199.00,430,18.00,55.00,12.00,9.00,4.00,370.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(20,1,1,'Chicken Teriyaki Bowl','chicken-teriyaki-bowl','Teriyaki chicken, steamed rice, broccoli, sesame.','/assets/images/foods/chicken-teriyaki-bowl.jpg','Chicken Breast, Steamed Jasmine Rice, Broccoli, Low-Sugar Teriyaki Sauce','Soy, Sesame','non_vegetarian',249.00,510,38.00,60.00,10.00,4.00,8.00,520.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-23 03:17:12'),(21,1,1,'Paneer Tikka Bowl','paneer-tikka-bowl','Tandoori paneer, brown rice, salad, mint sauce.','https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=800&auto=format&fit=crop&q=80','Tandoori Marinated Paneer, Brown Rice, Cucumber Salad, Greek Yogurt Mint Dip','Dairy','vegetarian',239.00,470,26.00,50.00,15.00,7.00,4.00,430.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(22,1,2,'Smoked Chicken Breast Wrap','smoked-chicken-breast-wrap','Succulent lean smoked chicken breast, shredded greens, tomatoes, and light garlic spread in multigrain flatbread.','https://images.unsplash.com/photo-1528735602780-2552fd46c7af?w=800&auto=format&fit=crop&q=80','Multigrain Wrap, Smoked Chicken, Lettuce, Tomatoes, Light Garlic Yogurt Dip','Gluten, Dairy','non_vegetarian',249.00,450,36.00,38.00,12.00,5.00,2.00,440.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(23,1,2,'Spiced Falafel & Hummus Wrap','spiced-falafel-hummus-wrap','Baked chickpea falafel patties with authentic tahini hummus, pickled cucumbers, and crisp iceberg.','https://images.unsplash.com/photo-1540914124281-342587941389?w=800&auto=format&fit=crop&q=80','Whole Wheat Flatbread, Baked Falafel, Tahini Hummus, Pickled Cucumbers, Tomatoes','Gluten, Sesame','vegan',199.00,420,16.00,52.00,14.00,8.00,4.00,390.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(24,1,2,'Zesty Tofu & Avocado Wrap','zesty-tofu-avocado-wrap','Seared lemon herb tofu, creamy avocado slices, shredded purple cabbage, and chipotle lime drizzle.','https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=800&auto=format&fit=crop&q=80','Seeded Tortilla, Tofu, Hass Avocado, Red Cabbage, Cilantro Lime Vinaigrette','Gluten, Soy','vegan',229.00,380,20.00,34.00,16.00,7.00,3.00,360.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(25,1,2,'Egg White & Spinach Protein Wrap','egg-white-spinach-protein-wrap','Fluffy scrambled farm egg whites, baby spinach, roasted mushrooms, and feta cheese in warm whole wheat wrap.','https://images.unsplash.com/photo-1525351484163-7529414344d8?w=800&auto=format&fit=crop&q=80','Whole Wheat Wrap, Free Range Egg Whites, Spinach, Mushrooms, Crumbled Feta','Gluten, Egg, Dairy','vegetarian',189.00,340,28.00,32.00,8.00,4.00,2.00,390.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(26,1,6,'Mediterranean Greek Feta Salad','mediterranean-greek-feta-salad','Crunchy Persian cucumbers, Kalamata olives, juicy tomatoes, red onions, and Greek feta tossed in oregano dressing.','https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=800&auto=format&fit=crop&q=80','Cucumbers, Cherry Tomatoes, Kalamata Olives, Greek Feta, Extra Virgin Olive Oil','Dairy','vegetarian',219.00,280,12.00,16.00,18.00,5.00,4.00,460.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(27,1,6,'Warm Grilled Chicken Caesar Salad','warm-grilled-chicken-caesar-salad','Crispy romaine hearts topped with sliced grilled chicken breast, whole grain croutons, and yogurt parmesan dressing.','https://images.unsplash.com/photo-1550304943-4f24f54ddde9?w=800&auto=format&fit=crop&q=80','Romaine Lettuce, Chicken Breast, Whole Grain Croutons, Shaved Parmesan, Greek Yogurt Caesar Dressing','Dairy, Gluten','non_vegetarian',269.00,390,38.00,12.00,16.00,4.00,2.00,480.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(28,1,6,'Avocado & Edamame Crunch Salad','avocado-edamame-crunch-salad','Diced buttery Hass avocado, steamed edamame beans, baby spinach, pumpkin seeds, and sesame citrus vinaigrette.','https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?w=800&auto=format&fit=crop&q=80','Avocado, Edamame, Baby Spinach, Pumpkin Seeds, Orange Citrus Vinaigrette','Soy, Sesame','vegan',249.00,340,15.00,22.00,20.00,9.00,3.00,310.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(29,1,6,'Roasted Beetroot & Goat Cheese Salad','roasted-beetroot-goat-cheese-salad','Sweet oven-roasted beets, peppery arugula leaves, toasted walnuts, and crumbled artisanal goat cheese.','https://images.unsplash.com/photo-1505576399279-565b52d4ac71?w=800&auto=format&fit=crop&q=80','Roasted Beetroot, Wild Arugula, Goat Cheese, California Walnuts, Honey Mustard Vinaigrette','Dairy, Tree Nuts','vegetarian',239.00,290,11.00,26.00,14.00,6.00,12.00,340.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(30,1,6,'Sprouted Moong & Pomegranate Salad','sprouted-moong-pomegranate-salad','Living sprouted green mung beans, sweet ruby pomegranate pearls, diced cucumber, chaat spices, and fresh lemon.','https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=800&auto=format&fit=crop&q=80','Sprouted Mung, Pomegranate, Cucumbers, Fresh Mint, Lemon Juice, Himalayan Salt',NULL,'vegan',179.00,220,14.00,38.00,3.00,8.00,6.00,280.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(31,1,7,'Hearty Minestrone with Beans','hearty-minestrone-with-beans','Italian country-style soup packed with slow-simmered tomatoes, cannellini white beans, zucchini, and aromatic herbs.','https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&auto=format&fit=crop&q=80','San Marzano Tomatoes, Cannellini Beans, Zucchini, Carrots, Celery, Thyme',NULL,'vegan',169.00,180,9.00,28.00,3.00,6.00,4.00,390.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(32,1,7,'Clear Chicken & Herb Broth','clear-chicken-herb-broth','Warm nourishing bone broth made with free-range chicken, ginger, black peppercorns, scallions, and shredded chicken breast.','/assets/images/foods/clear-chicken-broth.jpg','Chicken Bone Broth, Shredded Chicken, Fresh Ginger, Scallions, Parsley',NULL,'non_vegetarian',189.00,160,24.00,6.00,3.00,1.00,1.00,440.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-23 03:17:12'),(33,1,7,'Cream of Roasted Mushroom (Light)','cream-of-roasted-mushroom-light','Button and shiitake wild mushrooms roasted to deep umami and pureed with cashew cream. Dairy free.','https://images.unsplash.com/photo-1547592180-85f173990554?w=800&auto=format&fit=crop&q=80','Shiitake Mushrooms, Button Mushrooms, Cashew Cream, Vegetable Stock, Garlic, Chives','Tree Nuts','vegan',179.00,150,6.00,14.00,7.00,3.00,2.00,360.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(34,1,7,'Broccoli & Almond Protein Soup','broccoli-almond-protein-soup','Bright green velvety broccoli soup enriched with blanched almonds, pea protein, and crushed black pepper.','/assets/images/foods/broccoli-almond-protein-soup.jpg','Fresh Broccoli, Roasted Almonds, Pea Protein, Vegetable Broth, Black Pepper','Tree Nuts','vegan',189.00,190,12.00,16.00,8.00,5.00,3.00,320.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-23 03:17:12'),(35,1,7,'Spiced Tomato Basil Soup','spiced-tomato-basil-soup','Sweet roasted tomatoes slow simmered with Italian sweet basil leaves, roasted garlic, and a hint of smoked paprika.','https://images.unsplash.com/photo-1594756202469-9ff9799b2e4e?w=800&auto=format&fit=crop&q=80','Roma Tomatoes, Fresh Sweet Basil, Garlic, Olive Oil, Vegetable Broth',NULL,'vegan',159.00,130,4.00,20.00,3.00,4.00,5.00,370.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(36,1,8,'Crispy Baked Tofu Bites','crispy-baked-tofu-bites','Oven-crisped organic tofu cubes served with house sweet chili tamari dip and spring onions.','https://images.unsplash.com/photo-1546069901-d5bfd2cbfb1f?w=800&auto=format&fit=crop&q=80','Non-GMO Tofu, Cornstarch, Tamari, Garlic Powder, Sweet Chili Dip','Soy','vegan',179.00,210,16.00,14.00,8.00,3.00,1.00,340.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(37,1,8,'Grilled Chicken Skewers','grilled-chicken-skewers','Tender cubes of chicken breast marinated in yogurt, cilantro, and spices, flame-grilled on skewers.','https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&auto=format&fit=crop&q=80','Chicken Breast, Greek Yogurt, Fresh Cilantro, Cumin, Coriander','Dairy','non_vegetarian',229.00,290,36.00,6.00,8.00,1.00,1.00,410.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(38,1,8,'Edamame with Himalayan Pink Salt','edamame-himalayan-pink-salt','Fresh young soybean pods steamed to tender perfection and sprinkled with coarsely ground rock salt.','/assets/images/foods/edamame-pink-salt.jpg','Green Soybean Pods, Himalayan Pink Rock Salt','Soy','vegan',159.00,160,14.00,11.00,5.00,6.00,2.00,290.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-23 03:17:12'),(39,1,8,'Air-Fried Sweet Potato Fries','air-fried-sweet-potato-fries','Crispy skin-on sweet potato batons air-fried with rosemary salt. Served with hung curd garlic dip.','https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=800&auto=format&fit=crop&q=80','Sweet Potatoes, Olive Oil Spray, Sea Salt, Rosemary, Hung Curd Dip','Dairy','vegetarian',169.00,220,3.00,42.00,4.00,6.00,8.00,310.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(40,1,8,'Spiced Cottage Cheese Skewers','spiced-cottage-cheese-skewers','Firm cottage cheese cubes glazed with mustard oil, fenugreek, and tandoori spices, grilled on bamboo skewers.','/assets/images/foods/spiced-cottage-cheese-skewers.jpg','Paneer, Mustard Oil, Kasuri Methi, Degi Mirch, Bell Peppers','Dairy','vegetarian',209.00,260,20.00,8.00,14.00,2.00,2.00,380.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-23 03:17:12'),(41,1,3,'Green Detox Cold-Pressed Juice','green-detox-cold-pressed-juice','Hydrating, raw cold-pressed mix of crisp cucumber, celery, granny smith apple, and lemon mint.','https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=800&auto=format&fit=crop&q=80','Cucumber, Celery, Green Apple, Baby Spinach, Lemon, Fresh Mint',NULL,'vegan',149.00,95,3.00,20.00,0.00,3.00,11.00,80.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(42,1,3,'Raw Coconut Water with Chia Seeds','raw-coconut-water-chia-seeds','Pure tender coconut water straight from coastal farms, infused with soaked organic chia seeds.','https://images.unsplash.com/photo-1525385133512-2f3bdd039054?w=800&auto=format&fit=crop&q=80','Tender Coconut Water, Organic Chia Seeds',NULL,'vegan',129.00,85,3.00,16.00,1.00,4.00,8.00,90.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(43,1,3,'Berry Antioxidant Blast Shake','berry-antioxidant-blast-shake','Wild blueberries, strawberries, almond milk, and whey protein isolate for post-workout recovery.','/assets/images/foods/berry-antioxidant-blast-shake.jpg','Blueberries, Strawberries, Almond Milk, Whey Isolate, Stevia','Dairy, Tree Nuts','vegetarian',199.00,220,18.00,28.00,3.00,6.00,12.00,120.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-23 03:17:12'),(44,1,4,'Protein Ice Cream (Vanilla/Choco)','protein-ice-cream-vanilla-choco','Artisanal low-fat churned ice cream infused with whey isolate and organic vanilla pods.','https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?w=800&auto=format&fit=crop&q=80','Low Fat Milk, Whey Isolate, Erythritol, Bourbon Vanilla Extract','Dairy','vegetarian',180.00,180,20.00,16.00,5.00,2.00,6.00,90.00,NULL,NULL,1,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(45,1,4,'Chia Seed Pudding with Mango Puree','chia-seed-pudding-mango-puree','Soaked black chia seeds in coconut almond milk layered with fresh seasonal Alphonso mango puree.','https://images.unsplash.com/photo-1511690743698-d9d85f2fbf38?w=800&auto=format&fit=crop&q=80','Chia Seeds, Coconut Milk, Alphonso Mango, Cardamom, Stevia',NULL,'vegan',169.00,230,8.00,26.00,10.00,7.00,9.00,60.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(46,1,4,'Baked Oat & Apple Crumble (No Sugar)','baked-oat-apple-crumble-no-sugar','Warm spiced green apples baked with rolled oats, cinnamon, crushed almonds, and organic maple drops.','https://images.unsplash.com/photo-1568571780765-9276ac8b75a2?w=800&auto=format&fit=crop&q=80','Rolled Oats, Green Apples, Almond Flour, Cinnamon, Nutmeg, Maple Syrup','Tree Nuts, Gluten','vegan',179.00,240,7.00,38.00,6.00,6.00,10.00,80.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(47,1,4,'Greek Yogurt Parfait with Berries','greek-yogurt-parfait-berries','Thick hung Greek yogurt layered with house-toasted buckwheat granola, fresh berries, and organic raw honey.','https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&auto=format&fit=crop&q=80','Greek Yogurt, Buckwheat Granola, Raspberries, Blueberries, Raw Honey','Dairy','vegetarian',169.00,190,16.00,22.00,3.00,3.00,8.00,95.00,NULL,NULL,0,0,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(48,1,5,'Artisan Sourdough Protein Pizza','artisan-sourdough-protein-pizza','Hand-stretched slow fermented sourdough crust topped with San Marzano tomato reduction, light mozzarella, basil, and fresh vegetable medley.','https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&auto=format&fit=crop&q=80','Sourdough Flour, San Marzano Tomatoes, Low-Fat Mozzarella, Bell Peppers, Olives, Fresh Basil, Extra Virgin Olive Oil','Gluten, Dairy','vegetarian',299.00,640,32.00,72.00,16.00,7.00,4.00,480.00,NULL,'350 g (8 inch)',1,1,1,'2026-09-15 09:41:43','2026-09-15 09:41:43'),(49,1,5,'Grilled Lean Protein Burger','grilled-lean-protein-burger','Juicy grilled spiced chicken breast patty on a whole-wheat brioche bun with crisp lettuce, tomato, pickles, and light chipotle Greek yogurt spread.','https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&auto=format&fit=crop&q=80','Whole-Wheat Brioche, Minced Chicken Breast, Crisp Lettuce, Ripe Tomato, Pickles, Greek Yogurt Chipotle Sauce','Gluten, Dairy','non_vegetarian',269.00,490,36.00,42.00,14.00,5.00,5.00,460.00,NULL,'280 g burger',1,1,1,'2026-09-15 09:41:43','2026-09-17 04:14:55');
/*!40000 ALTER TABLE `food_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `food_variants`
--

DROP TABLE IF EXISTS `food_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `food_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `food_item_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `calories_adjustment` int(11) DEFAULT NULL,
  `protein_adjustment` decimal(6,2) DEFAULT NULL,
  `carbs_adjustment` decimal(6,2) DEFAULT NULL,
  `fat_adjustment` decimal(6,2) DEFAULT NULL,
  `fiber_adjustment` decimal(6,2) DEFAULT NULL,
  `sugar_adjustment` decimal(6,2) DEFAULT NULL,
  `sodium_adjustment` decimal(7,2) DEFAULT NULL,
  `caffeine_adjustment` decimal(6,2) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_food_variants_food_item_id` (`food_item_id`),
  KEY `idx_food_variants_available` (`food_item_id`,`is_available`),
  CONSTRAINT `fk_food_variants_food_item` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `food_variants`
--

LOCK TABLES `food_variants` WRITE;
/*!40000 ALTER TABLE `food_variants` DISABLE KEYS */;
INSERT INTO `food_variants` VALUES (5,13,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(6,13,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.80,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(7,14,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(8,14,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(9,15,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(10,15,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(11,16,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(12,16,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(13,17,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(14,17,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.00,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(15,18,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(16,18,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.00,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(17,19,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(18,19,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(19,20,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(20,20,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,2.80,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(21,21,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(22,21,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(23,22,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(24,22,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(25,23,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(26,23,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(27,24,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(28,24,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.00,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:17'),(29,25,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(30,25,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(31,26,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(32,26,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(33,27,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(34,27,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(35,28,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(36,28,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.00,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(37,29,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(38,29,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,4.20,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(39,30,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(40,30,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,2.10,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(43,32,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(44,32,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(45,33,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(46,33,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(47,34,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(48,34,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.00,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(49,35,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(50,35,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,1.80,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(51,36,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(52,36,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(53,37,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(54,37,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.40,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(55,38,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(56,38,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(57,39,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(58,39,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,2.80,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(59,40,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(60,40,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,0.70,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(61,41,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(62,41,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,3.80,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(63,42,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(64,42,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,2.80,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(65,43,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(66,43,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,4.20,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(67,44,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(68,44,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,2.10,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(69,45,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(70,45,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,3.20,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(71,46,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(72,46,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,3.50,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(73,47,'Standard Portion',NULL,0.00,0,0.00,NULL,NULL,NULL,0.00,NULL,NULL,0,1,1,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(74,47,'Large / Power Portion',NULL,50.00,110,10.00,NULL,NULL,NULL,2.80,NULL,NULL,0,1,2,'2026-09-14 19:07:05','2026-09-22 01:05:18'),(75,48,'8-inch Personal (Standard)','',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(76,48,'11-inch Sharing (+₹120)','',120.00,240,16.00,32.00,8.00,NULL,1.40,NULL,NULL,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(77,31,'Regular Bowl (320ml)','',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(78,31,'Large Pot (480ml)','',50.00,80,5.00,12.00,2.00,NULL,1.40,NULL,NULL,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(79,4,'Regular (350ml Single Shot)','',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(80,4,'Grande (450ml Double Shot)','',40.00,50,8.00,3.00,1.00,NULL,0.00,NULL,NULL,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(81,7,'Single Cup (120g)','',0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,1,1,1,'2026-09-15 09:46:53','2026-09-22 01:05:18'),(82,7,'Double Waffle Cup (200g)','',60.00,110,12.00,8.00,3.00,NULL,0.00,NULL,NULL,1,1,2,'2026-09-15 09:46:53','2026-09-22 01:05:18');
/*!40000 ALTER TABLE `food_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_item_customizations`
--

DROP TABLE IF EXISTS `order_item_customizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_item_customizations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `customization_id` bigint(20) unsigned NOT NULL,
  `customization_name_snapshot` varchar(100) NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `price_adjustment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `calories_adjustment` int(11) DEFAULT NULL,
  `protein_adjustment` decimal(6,2) DEFAULT NULL,
  `carbs_adjustment` decimal(6,2) DEFAULT NULL,
  `fat_adjustment` decimal(6,2) DEFAULT NULL,
  `fiber_adjustment` decimal(6,2) DEFAULT NULL,
  `sugar_adjustment` decimal(6,2) DEFAULT NULL,
  `sodium_adjustment` decimal(7,2) DEFAULT NULL,
  `caffeine_adjustment` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_custom_item_id` (`order_item_id`),
  KEY `idx_order_custom_def_id` (`customization_id`),
  CONSTRAINT `fk_order_custom_def` FOREIGN KEY (`customization_id`) REFERENCES `food_customizations` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_order_custom_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_item_customizations`
--

LOCK TABLES `order_item_customizations` WRITE;
/*!40000 ALTER TABLE `order_item_customizations` DISABLE KEYS */;
INSERT INTO `order_item_customizations` VALUES (1,1,1,'Extra Paneer',1,40.00,80,10.00,1.00,6.00,NULL,NULL,NULL,NULL,'2026-09-14 08:57:57'),(2,1,2,'Avocado',1,60.00,80,1.00,4.00,7.00,NULL,NULL,NULL,NULL,'2026-09-14 08:57:57'),(3,2,1,'Extra Paneer',1,40.00,80,10.00,1.00,6.00,NULL,NULL,NULL,NULL,'2026-09-14 09:07:33'),(4,2,2,'Avocado',1,60.00,80,1.00,4.00,7.00,NULL,NULL,NULL,NULL,'2026-09-14 09:07:33'),(5,3,1,'Extra Paneer',1,40.00,80,10.00,1.00,6.00,NULL,NULL,NULL,NULL,'2026-09-14 09:23:45'),(6,4,1,'Extra Paneer',1,40.00,80,10.00,1.00,6.00,NULL,NULL,NULL,NULL,'2026-09-14 09:24:17'),(7,5,1,'Extra Paneer',1,40.00,80,10.00,1.00,6.00,NULL,NULL,NULL,NULL,'2026-09-14 09:25:22'),(8,6,1,'Extra Paneer',1,40.00,80,10.00,1.00,6.00,NULL,NULL,NULL,NULL,'2026-09-14 09:26:26'),(14,14,104,'High-Protein Whole Wheat Crust',1,35.00,20,7.00,-4.00,0.50,NULL,NULL,NULL,NULL,'2026-09-15 10:05:08'),(15,14,106,'Pesto Base with Buffalo Mozzarella',1,45.00,60,4.00,1.00,5.00,NULL,NULL,NULL,NULL,'2026-09-15 10:05:08'),(16,15,82,'Quinoa',1,30.00,40,4.00,6.00,1.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(17,15,84,'Extra paneer · 50 g',1,40.00,90,10.00,1.00,5.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(18,15,88,'Avocado',1,50.00,80,1.00,4.00,7.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(19,16,93,'Steamed Brown Rice',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(20,16,95,'Grilled Chicken Breast · 120 g',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(21,17,113,'Multigrain Seeded Bun',1,20.00,15,3.00,2.00,1.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(22,17,116,'Double Patty (Extra 100g)',1,80.00,150,24.00,0.00,4.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(23,18,103,'Sourdough Artisan Crust',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(24,18,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-15 11:20:49'),(28,21,11,'Regular Chicken · Brown Rice · Extra Vegetables',1,20.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-16 03:28:19'),(29,23,11,'Regular Chicken · Brown Rice · Extra Vegetables',1,20.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-16 03:28:19'),(30,25,11,'Regular Chicken · Brown Rice · Extra Vegetables',1,20.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-16 03:28:40'),(31,27,11,'Regular Chicken · Brown Rice · Extra Vegetables',1,20.00,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-16 03:28:40'),(32,29,103,'Sourdough Artisan Crust',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-16 03:39:04'),(33,29,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-16 03:39:04'),(34,30,103,'Sourdough Artisan Crust',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 00:18:03'),(35,30,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 00:18:03'),(36,30,111,'Extra Paneer Cubes',1,45.00,70,8.00,1.00,4.00,NULL,NULL,NULL,NULL,'2026-09-17 00:18:03'),(37,31,26,'Avocado Slices',1,50.00,80,1.50,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-17 00:47:04'),(38,32,103,'Sourdough Artisan Crust',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 03:35:18'),(39,32,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-17 03:35:18'),(40,32,111,'Extra Paneer Cubes',1,45.00,70,8.00,1.00,4.00,NULL,NULL,NULL,NULL,'2026-09-17 03:35:18'),(41,33,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-22 00:34:38'),(42,33,104,'High-Protein Whole Wheat Crust',1,35.00,20,7.00,-4.00,0.50,NULL,NULL,NULL,NULL,'2026-09-22 00:34:38'),(43,34,37,'Extra Protein Scoop / Serving',1,40.00,90,12.00,NULL,NULL,NULL,NULL,NULL,NULL,'2026-09-22 00:34:38'),(44,35,103,'Sourdough Artisan Crust',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-22 00:51:02'),(45,35,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,NULL,'2026-09-22 00:51:02'),(46,36,82,'Quinoa',1,30.00,40,4.00,6.00,1.00,NULL,0.50,NULL,NULL,'2026-09-22 01:13:36'),(47,36,84,'Extra paneer · 50 g',1,40.00,90,10.00,1.00,5.00,NULL,0.25,NULL,NULL,'2026-09-22 01:13:36'),(48,37,82,'Quinoa',1,30.00,40,4.00,6.00,1.00,NULL,0.50,NULL,NULL,'2026-09-22 01:14:03'),(49,37,84,'Extra paneer · 50 g',1,40.00,90,10.00,1.00,5.00,NULL,0.25,NULL,NULL,'2026-09-22 01:14:03'),(50,38,11,'Extra Protein Scoop / Serving',1,40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,'2026-09-22 01:15:08'),(51,38,12,'Avocado Slices',1,50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,'2026-09-22 01:15:08'),(52,39,11,'Extra Protein Scoop / Serving',1,40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,'2026-09-22 01:15:32'),(53,39,12,'Avocado Slices',1,50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,'2026-09-22 01:15:32'),(54,41,103,'Sourdough Artisan Crust',1,0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,'2026-09-22 02:12:24'),(56,43,11,'Extra Protein Scoop / Serving',1,40.00,90,12.00,NULL,NULL,NULL,0.50,NULL,NULL,'2026-09-22 02:13:31'),(57,43,12,'Avocado Slices',1,50.00,80,1.50,NULL,NULL,NULL,0.30,NULL,NULL,'2026-09-22 02:13:31'),(60,47,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,'2026-09-22 03:29:09'),(61,47,104,'High-Protein Whole Wheat Crust',1,35.00,20,7.00,-4.00,0.50,NULL,0.50,NULL,NULL,'2026-09-22 03:29:09'),(65,51,103,'Sourdough Artisan Crust',1,0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,'2026-10-01 12:18:29'),(66,51,105,'San Marzano Tomato & Low-Fat Mozzarella',1,0.00,0,0.00,0.00,0.00,NULL,0.00,NULL,NULL,'2026-10-01 12:18:29'),(67,51,111,'Extra Paneer Cubes',1,45.00,70,8.00,1.00,4.00,NULL,0.40,NULL,NULL,'2026-10-01 12:18:29'),(68,51,109,'Grilled Mushrooms',1,35.00,35,2.50,3.00,1.00,NULL,0.40,NULL,NULL,'2026-10-01 12:18:29');
/*!40000 ALTER TABLE `order_item_customizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `food_item_id` bigint(20) unsigned NOT NULL,
  `food_name_snapshot` varchar(150) NOT NULL,
  `base_price_snapshot` decimal(10,2) NOT NULL,
  `variant_name_snapshot` varchar(100) DEFAULT NULL,
  `variant_price_snapshot` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein` decimal(6,2) DEFAULT NULL,
  `carbs` decimal(6,2) DEFAULT NULL,
  `fat` decimal(6,2) DEFAULT NULL,
  `fiber` decimal(6,2) DEFAULT NULL,
  `sugar` decimal(6,2) DEFAULT NULL,
  `sodium` decimal(7,2) DEFAULT NULL,
  `caffeine` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `idx_order_items_food_item_id` (`food_item_id`),
  CONSTRAINT `fk_order_items_food` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,'Paneer Protein Bowl',249.00,'Large',50.00,2,399.00,798.00,860,58.00,58.00,37.00,9.00,6.00,420.00,NULL,'2026-09-14 08:57:57'),(2,2,1,'Paneer Protein Bowl',249.00,'Large',50.00,2,399.00,798.00,860,58.00,58.00,37.00,9.00,6.00,420.00,NULL,'2026-09-14 09:07:33'),(3,3,1,'Paneer Protein Bowl',249.00,'Regular',0.00,1,289.00,289.00,600,45.00,39.00,24.00,9.00,6.00,420.00,NULL,'2026-09-14 09:23:45'),(4,4,1,'Paneer Protein Bowl',249.00,'Regular',0.00,1,289.00,289.00,600,45.00,39.00,24.00,9.00,6.00,420.00,NULL,'2026-09-14 09:24:17'),(5,5,1,'Paneer Protein Bowl',249.00,'Regular',0.00,1,289.00,289.00,600,45.00,39.00,24.00,9.00,6.00,420.00,NULL,'2026-09-14 09:25:22'),(6,6,1,'Paneer Protein Bowl',249.00,'Regular',0.00,1,289.00,289.00,600,45.00,39.00,24.00,9.00,6.00,420.00,NULL,'2026-09-14 09:26:26'),(11,11,34,'Broccoli & Almond Protein Soup',189.00,'Standard Portion',0.00,1,189.00,189.00,190,12.00,16.00,8.00,5.00,3.00,320.00,NULL,'2026-09-15 07:35:39'),(13,13,14,'Smoked Salmon with Steamed Asparagus',349.00,'Standard Portion',0.00,1,349.00,349.00,490,42.00,14.00,18.00,4.00,2.00,480.00,NULL,'2026-09-15 08:08:47'),(14,14,48,'Artisan Sourdough Protein Pizza',299.00,'11-inch Sharing (+₹120)',120.00,1,499.00,499.00,960,59.00,101.00,29.50,7.00,4.00,480.00,NULL,'2026-09-15 10:05:08'),(15,15,1,'Paneer Protein Bowl',249.00,NULL,0.00,2,369.00,738.00,730,53.00,59.00,31.00,8.00,5.00,420.00,NULL,'2026-09-15 11:20:49'),(16,15,2,'Chicken Rice Bowl',279.00,NULL,0.00,1,279.00,279.00,560,44.00,50.00,14.00,6.00,4.00,510.00,NULL,'2026-09-15 11:20:49'),(17,15,49,'Grilled Lean Protein Burger',269.00,NULL,0.00,1,369.00,369.00,655,63.00,44.00,19.00,5.00,5.00,460.00,NULL,'2026-09-15 11:20:49'),(18,15,48,'Artisan Sourdough Protein Pizza',299.00,'11-inch Sharing (+₹120)',120.00,1,419.00,419.00,880,48.00,104.00,24.00,7.00,4.00,480.00,NULL,'2026-09-15 11:20:49'),(19,16,2,'Chicken Rice Bowl',279.00,NULL,0.00,1,279.00,279.00,560,44.00,50.00,14.00,NULL,NULL,NULL,NULL,'2026-09-16 03:27:59'),(20,16,1,'Paneer Protein Bowl',249.00,NULL,0.00,1,249.00,249.00,520,38.00,48.00,18.00,NULL,NULL,NULL,NULL,'2026-09-16 03:27:59'),(21,18,2,'Chicken Rice Bowl',279.00,NULL,0.00,1,279.00,279.00,560,44.00,50.00,14.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:19'),(22,18,1,'Paneer Protein Bowl',249.00,NULL,0.00,1,249.00,249.00,520,38.00,48.00,18.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:19'),(23,19,2,'Chicken Rice Bowl',279.00,NULL,0.00,1,279.00,279.00,560,44.00,50.00,14.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:19'),(24,19,1,'Paneer Protein Bowl',249.00,NULL,0.00,1,249.00,249.00,520,38.00,48.00,18.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:19'),(25,24,2,'Chicken Rice Bowl',279.00,NULL,0.00,1,279.00,279.00,560,44.00,50.00,14.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:40'),(26,24,1,'Paneer Protein Bowl',249.00,NULL,0.00,1,249.00,249.00,520,38.00,48.00,18.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:40'),(27,25,2,'Chicken Rice Bowl',279.00,NULL,0.00,1,279.00,279.00,560,44.00,50.00,14.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:40'),(28,25,1,'Paneer Protein Bowl',249.00,NULL,0.00,1,249.00,249.00,520,38.00,48.00,18.00,NULL,NULL,NULL,NULL,'2026-09-16 03:28:40'),(29,26,48,'Artisan Sourdough Protein Pizza',299.00,'11-inch Sharing (+₹120)',120.00,1,419.00,419.00,880,48.00,104.00,24.00,7.00,4.00,480.00,NULL,'2026-09-16 03:39:04'),(30,27,48,'Artisan Sourdough Protein Pizza',299.00,'8-inch Personal (Standard)',0.00,1,344.00,344.00,710,40.00,73.00,20.00,7.00,4.00,480.00,NULL,'2026-09-17 00:18:03'),(31,28,20,'Chicken Teriyaki Bowl',249.00,'Large / Power Portion',50.00,1,349.00,349.00,700,49.50,60.00,10.00,4.00,8.00,520.00,NULL,'2026-09-17 00:47:04'),(32,29,48,'Artisan Sourdough Protein Pizza',299.00,'8-inch Personal (Standard)',0.00,1,344.00,344.00,710,40.00,73.00,20.00,7.00,4.00,480.00,NULL,'2026-09-17 03:35:18'),(33,30,48,'Artisan Sourdough Protein Pizza',299.00,'11-inch Sharing (+₹120)',120.00,1,454.00,454.00,900,55.00,100.00,24.50,7.00,4.00,480.00,NULL,'2026-09-22 00:34:38'),(34,30,26,'Mediterranean Greek Feta Salad',219.00,'Large / Power Portion',50.00,1,309.00,309.00,480,34.00,16.00,18.00,5.00,4.00,460.00,NULL,'2026-09-22 00:34:38'),(35,31,48,'Artisan Sourdough Protein Pizza',299.00,'11-inch Sharing (+₹120)',120.00,1,419.00,419.00,880,48.00,104.00,24.00,7.00,4.00,480.00,NULL,'2026-09-22 00:51:02'),(36,32,1,'Paneer Protein Bowl',249.00,NULL,0.00,3,319.00,957.00,650,52.00,55.00,24.00,8.00,5.75,420.00,NULL,'2026-09-22 01:13:36'),(37,33,1,'Paneer Protein Bowl',249.00,NULL,0.00,3,319.00,957.00,650,52.00,55.00,24.00,8.00,5.75,420.00,NULL,'2026-09-22 01:14:03'),(38,34,13,'Paneer Steak with Quinoa & Greens',269.00,'Large / Power Portion',50.00,3,409.00,1227.00,790,55.50,36.00,20.00,8.00,7.60,410.00,NULL,'2026-09-22 01:15:08'),(39,35,13,'Paneer Steak with Quinoa & Greens',269.00,'Large / Power Portion',50.00,3,409.00,1227.00,790,55.50,36.00,20.00,8.00,7.60,410.00,NULL,'2026-09-22 01:15:32'),(41,37,48,'Artisan Sourdough Protein Pizza',299.00,'8-inch Personal (Standard)',0.00,2,299.00,598.00,640,32.00,72.00,16.00,7.00,4.00,480.00,NULL,'2026-09-22 02:12:24'),(43,39,13,'Paneer Steak with Quinoa & Greens',269.00,'Large / Power Portion',50.00,3,409.00,1227.00,790,55.50,36.00,20.00,8.00,7.60,410.00,NULL,'2026-09-22 02:13:31'),(47,43,48,'Artisan Sourdough Protein Pizza',299.00,'8-inch Personal (Standard)',0.00,1,334.00,334.00,660,39.00,68.00,16.50,7.00,4.50,480.00,NULL,'2026-09-22 03:29:09'),(51,47,48,'Artisan Sourdough Protein Pizza',299.00,'8-inch Personal (Standard)',0.00,1,379.00,379.00,745,42.50,76.00,21.00,7.00,4.80,480.00,NULL,'2026-10-01 12:18:29'),(52,47,47,'Greek Yogurt Parfait with Berries',169.00,NULL,0.00,1,169.00,169.00,190,16.00,22.00,3.00,3.00,8.00,95.00,NULL,'2026-10-01 12:18:29');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(64) NOT NULL,
  `restaurant_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `table_id` bigint(20) unsigned DEFAULT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `order_type` enum('dine_in','takeaway') NOT NULL DEFAULT 'dine_in',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `service_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `order_status` enum('placed','accepted','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'placed',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `fk_orders_table` (`table_id`),
  KEY `idx_orders_order_number` (`order_number`),
  KEY `idx_orders_restaurant_id` (`restaurant_id`),
  KEY `idx_orders_branch_id` (`branch_id`),
  KEY `idx_orders_customer_id` (`customer_id`),
  KEY `idx_orders_order_status` (`order_status`),
  KEY `idx_orders_payment_status` (`payment_status`),
  KEY `idx_orders_created_at` (`created_at`),
  CONSTRAINT `fk_orders_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_table` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'HB-1A09F232DD9-412',1,1,4,2,'dine_in',798.00,39.90,0.00,837.90,'completed','completed','Please make it extra spicy','2026-09-14 08:57:57','2026-09-17 00:19:21'),(2,'HB-1A09F2BF467-645',1,1,4,3,'dine_in',798.00,39.90,0.00,837.90,'completed','completed','Less dressing please','2026-09-14 09:07:33','2026-09-16 04:19:51'),(3,'HB-1A09F3AC9D0-415',1,1,1,5,'dine_in',289.00,14.45,0.00,303.45,'pending','ready','Dashboard workflow test note','2026-09-14 09:23:45','2026-09-14 10:30:21'),(4,'HB-1A09F3B49BC-504',1,1,1,6,'dine_in',289.00,14.45,0.00,303.45,'completed','completed','Dashboard workflow test note','2026-09-14 09:24:17','2026-09-14 09:24:17'),(5,'HB-1A09F3C4777-454',1,1,1,7,'dine_in',289.00,14.45,0.00,303.45,'completed','completed','Dashboard workflow test note','2026-09-14 09:25:22','2026-09-14 09:25:22'),(6,'HB-1A09F3D41A4-399',1,1,1,8,'dine_in',289.00,14.45,0.00,303.45,'completed','completed','Dashboard workflow test note','2026-09-14 09:26:26','2026-09-14 09:26:26'),(11,'HB-1A0A3FE2D2B-244',1,1,4,13,'dine_in',189.00,9.45,0.00,198.45,'completed','ready',NULL,'2026-09-15 07:35:39','2026-09-15 11:06:17'),(13,'HB-1A0A41C85FC-980',1,1,4,15,'dine_in',349.00,17.45,0.00,366.45,'completed','completed',NULL,'2026-09-15 08:08:47','2026-09-15 08:09:31'),(14,'HB-1A0A48708DB-577',1,1,4,16,'dine_in',499.00,24.95,0.00,523.95,'completed','accepted',NULL,'2026-09-15 10:05:08','2026-09-15 10:56:23'),(15,'HB-1A0A4CC55AF-802',1,1,4,17,'dine_in',1805.00,90.25,0.00,1895.25,'completed','accepted',NULL,'2026-09-15 11:20:49','2026-09-15 11:20:49'),(16,'HB-1001',1,1,4,1,'dine_in',502.86,25.14,0.00,528.00,'completed','completed','Special instruction: Less spicy','2026-09-16 04:58:00','2026-09-17 00:02:32'),(18,'HB-1002',1,1,NULL,2,'takeaway',366.67,18.33,0.00,385.00,'completed','preparing','Special instruction: Less spicy','2026-09-16 04:54:00','2026-09-17 00:02:30'),(19,'HB-1003',1,1,11,3,'dine_in',706.67,35.33,0.00,742.00,'completed','preparing','Special instruction: Less spicy','2026-09-16 04:34:00','2026-09-16 03:28:40'),(24,'HB-1004',1,1,15,4,'dine_in',281.90,14.10,0.00,296.00,'completed','ready','Special instruction: Extra dressing on side','2026-09-16 04:22:00','2026-09-16 03:28:40'),(25,'HB-1005',1,1,1,1,'dine_in',502.86,25.14,0.00,528.00,'completed','completed','Special instruction: Less spicy','2026-09-16 04:00:00','2026-09-16 03:28:40'),(26,'HB-1A0A84BF38E-784',1,1,4,18,'dine_in',419.00,20.95,0.00,439.95,'completed','accepted',NULL,'2026-09-16 03:39:04','2026-09-16 03:39:05'),(27,'HB-1A0ACBA4404-506',1,1,4,19,'dine_in',344.00,17.20,0.00,361.20,'completed','accepted',NULL,'2026-09-17 00:18:03','2026-09-17 00:40:38'),(28,'HB-1A0ACD4D543-481',1,1,4,20,'dine_in',349.00,17.45,0.00,366.45,'completed','accepted',NULL,'2026-09-17 00:47:04','2026-09-17 00:47:04'),(29,'HB-1A0AD6ED935-553',1,1,4,21,'dine_in',344.00,17.20,0.00,361.20,'completed','ready',NULL,'2026-09-17 03:35:18','2026-09-22 00:28:52'),(30,'HB-1A0C6893F7A-175',1,1,4,22,'dine_in',763.00,38.15,0.00,801.15,'completed','ready',NULL,'2026-09-22 00:34:38','2026-09-22 00:48:04'),(31,'HB-1A0C6984331-646',1,1,4,23,'dine_in',419.00,20.95,0.00,439.95,'completed','accepted',NULL,'2026-09-22 00:51:02','2026-09-22 00:51:02'),(32,'HB-1A0C6ACEA82-155',1,1,1,24,'dine_in',957.00,47.85,0.00,1004.85,'pending','placed','Testing sugar snapshot persistence','2026-09-22 01:13:36','2026-09-22 01:13:36'),(33,'HB-1A0C6AD56B1-268',1,1,1,25,'dine_in',957.00,47.85,0.00,1004.85,'pending','placed','Testing sugar snapshot persistence','2026-09-22 01:14:03','2026-09-22 01:14:03'),(34,'HB-1A0C6AE5395-368',1,1,1,26,'dine_in',1227.00,61.35,0.00,1288.35,'pending','placed','Testing sugar snapshot persistence','2026-09-22 01:15:08','2026-09-22 01:15:08'),(35,'HB-1A0C6AEAF83-357',1,1,1,27,'dine_in',1227.00,61.35,0.00,1288.35,'pending','placed','Testing sugar snapshot persistence','2026-09-22 01:15:32','2026-09-22 01:15:32'),(36,'HB-1A0C6AEAF8D-711',1,1,1,28,'dine_in',578.00,28.90,0.00,606.90,'pending','placed',NULL,'2026-09-22 01:15:32','2026-09-22 01:15:32'),(37,'HB-1A0C6E2C0C5-321',1,1,1,29,'dine_in',598.00,29.90,0.00,627.90,'pending','placed','Automated QA flow verification','2026-09-22 02:12:24','2026-09-22 02:12:24'),(39,'HB-1A0C6E3C5EE-309',1,1,1,31,'dine_in',1227.00,61.35,0.00,1288.35,'pending','placed','Testing sugar snapshot persistence','2026-09-22 02:13:31','2026-09-22 02:13:31'),(40,'HB-1A0C6E3C5F8-323',1,1,1,32,'dine_in',578.00,28.90,0.00,606.90,'pending','placed',NULL,'2026-09-22 02:13:31','2026-09-22 02:13:31'),(43,'HB-1A0C7290725-778',1,1,1,35,'dine_in',334.00,16.70,0.00,350.70,'completed','completed',NULL,'2026-09-22 03:29:09','2026-09-23 03:34:39'),(47,'HB-1A0F766DF6C-879',1,1,1,39,'dine_in',548.00,27.40,0.00,575.40,'completed','preparing',NULL,'2026-10-01 12:18:29','2026-10-01 12:20:22');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `payment_method` enum('cash','upi','card') NOT NULL,
  `transaction_reference` varchar(128) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `idx_payments_order_id` (`order_id`),
  KEY `idx_payments_status` (`status`),
  CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'upi','TXN-UPI-8CBFCF82D856',837.90,'completed','2026-09-14 03:27:57','2026-09-14 08:57:57'),(2,2,'upi','TXN-UPI-14FDE3566DEC',837.90,'completed','2026-09-14 03:37:33','2026-09-14 09:07:33'),(3,11,'cash','TXN-CASH-7C07CCEAC98E',198.45,'completed','2026-09-15 02:05:39','2026-09-15 07:35:39'),(5,13,'upi','TXN-UPI-BAFB73071E3B',366.45,'completed','2026-09-15 02:38:47','2026-09-15 08:08:47'),(6,14,'cash','TXN-CASH-A9A681CA8BF1',523.95,'completed','2026-09-15 04:35:08','2026-09-15 10:05:08'),(7,15,'upi','TXN-UPI-B5DB951DA9AF',1895.25,'completed','2026-09-15 05:50:49','2026-09-15 11:20:49'),(8,16,'upi',NULL,528.00,'completed','2026-09-16 04:58:00','2026-09-16 03:28:19'),(9,18,'upi',NULL,385.00,'completed','2026-09-16 04:54:00','2026-09-16 03:28:19'),(10,19,'upi',NULL,742.00,'completed','2026-09-16 04:34:00','2026-09-16 03:28:19'),(14,24,'upi',NULL,296.00,'completed','2026-09-16 04:22:00','2026-09-16 03:28:40'),(15,25,'upi',NULL,528.00,'completed','2026-09-16 04:00:00','2026-09-16 03:28:40'),(16,26,'upi','TXN-UPI-FCE00C4E2381',439.95,'completed','2026-09-15 22:09:05','2026-09-16 03:39:05'),(17,27,'cash','TXN-CASH-DFE2A639757E',361.20,'completed','2026-09-16 18:48:03','2026-09-17 00:18:03'),(18,28,'upi','TXN-UPI-11CE1B40E03D',366.45,'completed','2026-09-16 19:17:04','2026-09-17 00:47:04'),(19,29,'cash','TXN-CASH-32A380E3F6EE',361.20,'completed','2026-09-16 22:05:18','2026-09-17 03:35:18'),(20,30,'upi','TXN-UPI-6030E4BF590A',801.15,'completed','2026-09-21 19:04:38','2026-09-22 00:34:38'),(21,31,'upi','TXN-UPI-4A271954FA96',439.95,'completed','2026-09-21 19:21:02','2026-09-22 00:51:02'),(22,43,'upi','TXN-UPI-F50370EBC13E',350.70,'completed','2026-09-21 21:59:09','2026-09-22 03:29:09'),(23,47,'upi','TXN-UPI-226AD687A077',575.40,'completed','2026-10-01 06:48:29','2026-10-01 12:18:29');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qr_tokens`
--

DROP TABLE IF EXISTS `qr_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qr_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `table_id` bigint(20) unsigned NOT NULL,
  `token` varchar(128) NOT NULL,
  `status` enum('active','inactive','expired') NOT NULL DEFAULT 'active',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `fk_qr_restaurant` (`restaurant_id`),
  KEY `fk_qr_branch` (`branch_id`),
  KEY `fk_qr_table` (`table_id`),
  KEY `idx_qr_token` (`token`),
  KEY `idx_qr_status` (`status`),
  CONSTRAINT `fk_qr_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_qr_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_qr_table` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_tokens`
--

LOCK TABLES `qr_tokens` WRITE;
/*!40000 ALTER TABLE `qr_tokens` DISABLE KEYS */;
INSERT INTO `qr_tokens` VALUES (40,1,1,1,'hb_table_1_qr_5e478a0c','active',NULL,'2026-09-16 04:09:14'),(41,1,1,2,'hb_table_2_qr_8933e4b2','active',NULL,'2026-09-16 04:09:14'),(42,1,1,3,'hb_table_3_qr_f8c2b4b0','active',NULL,'2026-09-16 04:09:14'),(43,1,1,11,'hb_table_4_qr_c21a4965','active',NULL,'2026-09-16 04:09:14'),(44,1,1,12,'hb_table_5_qr_7a9059ca','active',NULL,'2026-09-16 04:09:14'),(45,1,1,13,'hb_table_6_qr_2c835598','active',NULL,'2026-09-16 04:09:14'),(46,1,1,14,'hb_table_7_qr_09015241','active',NULL,'2026-09-16 04:09:14'),(47,1,1,15,'hb_table_8_qr_f0de8cb3','active',NULL,'2026-09-16 04:09:14'),(48,1,1,16,'hb_table_9_qr_8808117a','active',NULL,'2026-09-16 04:09:14'),(49,1,1,17,'hb_table_10_qr_8338035c','active',NULL,'2026-09-16 04:09:14'),(50,1,1,18,'hb_table_11_qr_68104510','active',NULL,'2026-09-16 04:09:14'),(51,1,1,4,'hb_table_12_qr_dbdcfe02','active',NULL,'2026-09-16 04:09:14');
/*!40000 ALTER TABLE `qr_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurant_tables`
--

DROP TABLE IF EXISTS `restaurant_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurant_tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned NOT NULL,
  `table_number` varchar(50) NOT NULL,
  `status` enum('available','occupied','cleaning','out_of_service') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_branch_table` (`branch_id`,`table_number`),
  KEY `idx_tables_restaurant_id` (`restaurant_id`),
  KEY `idx_tables_branch_id` (`branch_id`),
  KEY `idx_tables_status` (`status`),
  CONSTRAINT `fk_tables_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_tables_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurant_tables`
--

LOCK TABLES `restaurant_tables` WRITE;
/*!40000 ALTER TABLE `restaurant_tables` DISABLE KEYS */;
INSERT INTO `restaurant_tables` VALUES (1,1,1,'Table 1','available','2026-09-14 08:47:24','2026-09-23 03:34:39'),(2,1,1,'Table 2','available','2026-09-14 08:47:24','2026-09-14 08:47:24'),(3,1,1,'Table 3','available','2026-09-14 08:47:24','2026-09-16 03:27:59'),(4,1,1,'Table 12','available','2026-09-14 08:47:24','2026-09-14 08:47:24'),(11,1,1,'Table 4','available','2026-09-16 03:27:59','2026-09-16 03:27:59'),(12,1,1,'Table 5','occupied','2026-09-16 03:27:59','2026-09-16 03:27:59'),(13,1,1,'Table 6','available','2026-09-16 03:27:59','2026-09-16 03:27:59'),(14,1,1,'Table 7','available','2026-09-16 03:27:59','2026-09-16 03:27:59'),(15,1,1,'Table 8','available','2026-09-16 03:27:59','2026-09-16 03:27:59'),(16,1,1,'Table 9','occupied','2026-09-16 03:27:59','2026-09-16 03:27:59'),(17,1,1,'Table 10','available','2026-09-16 03:27:59','2026-09-16 03:27:59'),(18,1,1,'Table 11','available','2026-09-16 03:27:59','2026-09-16 03:27:59');
/*!40000 ALTER TABLE `restaurant_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restaurants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','suspended') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_restaurants_slug` (`slug`),
  KEY `idx_restaurants_status` (`status`),
  KEY `fk_restaurants_owner_user` (`owner_user_id`),
  CONSTRAINT `fk_restaurants_owner_user` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restaurants`
--

LOCK TABLES `restaurants` WRITE;
/*!40000 ALTER TABLE `restaurants` DISABLE KEYS */;
INSERT INTO `restaurants` VALUES (1,2,'Greenhouse Kitchen','greenhouse-kitchen','greenhouse_logo.png','greenhouse_cover.jpg','Fresh food, clear choices, and protein-rich meals made for everyday eating.','+918025201234','hello@greenhousekitchen.com','100 Feet Road, HAL 2nd Stage, Indiranagar','Bengaluru','Karnataka','approved','2026-09-14 08:47:24','2026-09-14 09:26:27'),(2,5,'Green Earth Bistro','green-earth-bistro',NULL,NULL,NULL,'+91 98200 44551','contact@greenearthbistro.in','Koregaon Park','Pune','Maharashtra','approved','2026-09-16 03:27:59','2026-09-16 03:27:59'),(3,6,'Pure Green Kitchen','pure-green-kitchen',NULL,NULL,NULL,'+91 99876 22110','info@puregreenkitchen.in','Bandra West','Mumbai','Maharashtra','approved','2026-09-16 03:27:59','2026-09-17 00:05:11');
/*!40000 ALTER TABLE `restaurants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `rating` tinyint(3) unsigned NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `restaurant_reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','hidden') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_reviews_order` (`order_id`),
  KEY `idx_reviews_restaurant_id` (`restaurant_id`),
  KEY `idx_reviews_customer_id` (`customer_id`),
  KEY `idx_reviews_status` (`status`),
  CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,1,1,NULL,5,'The food was fresh and delicious.','Thank you so much! We take great pride in our organic greens and balanced macros. (Verified Test: 2026-09-22 00:48:04)','2026-09-22 00:48:04','approved','2026-09-15 05:50:00','2026-09-22 00:48:04'),(2,1,2,NULL,5,'Healthy, filling and delivered right on time.',NULL,NULL,'approved','2026-09-15 05:30:00','2026-09-16 03:27:59'),(3,1,3,NULL,5,'Healthy, filling and delivered right on time.',NULL,NULL,'approved','2026-09-15 05:15:00','2026-09-16 03:27:59'),(4,1,4,NULL,4,'Great macros, perfectly balanced bowl.',NULL,NULL,'approved','2026-09-14 11:00:00','2026-09-16 03:27:59'),(5,1,1,NULL,5,'Amazing avocado and grilled protein bowl!',NULL,NULL,'approved','2026-09-14 06:45:00','2026-09-16 03:27:59'),(6,1,23,31,5,'Wonderful fresh taste!','Thank you so much! We take great pride in our organic greens and balanced macros. (Verified Test: 2026-09-23 03:34:39)','2026-09-23 03:34:39','approved','2026-09-22 00:52:00','2026-09-23 03:34:39'),(7,1,39,47,5,'Verified dining experience.',NULL,NULL,'approved','2026-10-01 12:20:43','2026-10-01 12:20:43');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','super_admin','Platform administrator with full privileges','2026-09-14 08:47:24','2026-09-14 08:47:24'),(2,'Restaurant Owner','restaurant_owner','Owner of a restaurant entity','2026-09-14 08:47:24','2026-09-14 08:47:24'),(3,'Manager','manager','Branch manager supervising table service and orders','2026-09-14 08:47:24','2026-09-14 08:47:24'),(4,'Staff','staff','Waitstaff and kitchen staff','2026-09-14 08:47:24','2026-09-14 08:47:24');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `restaurant_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_role_id` (`role_id`),
  KEY `idx_users_restaurant_id` (`restaurant_id`),
  KEY `idx_users_status` (`status`),
  CONSTRAINT `fk_users_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,NULL,'Mira Shah','mira@healthybite.in','$2y$12$cW0Glto2qEq4w3VSmR89AeDKt9jPXxSKucUYdwMTOanVnzKPfxdza','active','2026-09-14 08:47:24','2026-09-16 03:27:59'),(2,2,1,'Aarav Sharma','aarav@greenhouse.in','$2y$12$cW0Glto2qEq4w3VSmR89AeDKt9jPXxSKucUYdwMTOanVnzKPfxdza','active','2026-09-14 08:47:24','2026-09-23 03:34:40'),(3,3,1,'Meera Nair','meera@greenhouse.in','$2y$12$cW0Glto2qEq4w3VSmR89AeDKt9jPXxSKucUYdwMTOanVnzKPfxdza','active','2026-09-14 08:47:24','2026-10-01 12:21:09'),(4,4,1,'Rohan Patel','rohan@greenhouse.in','$2y$12$vCkdxh4kga6bD8Rctbl/ee8ik1SAz6knTNV29WrDTRWrOy8.KidQW','inactive','2025-06-21 04:30:00','2026-09-16 03:27:59'),(5,2,2,'Neha Kapoor','neha@greenearth.in','$2y$12$vCkdxh4kga6bD8Rctbl/ee8ik1SAz6knTNV29WrDTRWrOy8.KidQW','active','2025-09-14 04:30:00','2026-09-16 03:27:59'),(6,2,3,'Vikram Iyer','vikram@puregreen.in','$2y$12$vCkdxh4kga6bD8Rctbl/ee8ik1SAz6knTNV29WrDTRWrOy8.KidQW','active','2025-09-13 04:30:00','2026-09-16 03:27:59'),(7,1,NULL,'System Admin','admin@healthybite.com','$2y$12$CDus75xaXNXaBw9ixmoym.rikl2woatapB7G7cB/3uJaQvDOKB5z.','active','2026-09-17 00:01:09','2026-09-17 00:06:36'),(8,2,1,'Greenhouse Owner','owner@greenhousekitchen.com','$2y$12$CDus75xaXNXaBw9ixmoym.rikl2woatapB7G7cB/3uJaQvDOKB5z.','active','2026-09-17 00:01:09','2026-09-17 00:06:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-01 18:12:12
