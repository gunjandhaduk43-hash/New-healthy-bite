-- ============================================================
-- HEALTHY BITE — MySQL Database Schema
-- Database Name: healthy_bite
-- Storage Engine: InnoDB
-- Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS `healthy_bite`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `healthy_bite`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `order_item_customizations`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `qr_tokens`;
DROP TABLE IF EXISTS `restaurant_tables`;
DROP TABLE IF EXISTS `food_customizations`;
DROP TABLE IF EXISTS `food_variants`;
DROP TABLE IF EXISTS `food_items`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `branches`;
DROP TABLE IF EXISTS `restaurants`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. ROLES
CREATE TABLE `roles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. RESTAURANTS
CREATE TABLE `restaurants` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `owner_user_id` BIGINT UNSIGNED NULL,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `logo` VARCHAR(255) NULL,
    `cover_image` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `phone` VARCHAR(30) NULL,
    `email` VARCHAR(191) NULL,
    `address` TEXT NULL,
    `city` VARCHAR(100) NULL,
    `state` VARCHAR(100) NULL,
    `status` ENUM('pending', 'approved', 'suspended') NOT NULL DEFAULT 'approved',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_restaurants_slug` (`slug`),
    INDEX `idx_restaurants_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. USERS
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `role_id` BIGINT UNSIGNED NOT NULL,
    `restaurant_id` BIGINT UNSIGNED NULL,
    `name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_users_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_users_role_id` (`role_id`),
    INDEX `idx_users_restaurant_id` (`restaurant_id`),
    INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Link owner to user safely
ALTER TABLE `restaurants`
    ADD CONSTRAINT `fk_restaurants_owner_user`
    FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 4. BRANCHES
CREATE TABLE `branches` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `address` TEXT NULL,
    `city` VARCHAR(100) NULL,
    `state` VARCHAR(100) NULL,
    `phone` VARCHAR(30) NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_branches_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_branches_restaurant_id` (`restaurant_id`),
    INDEX `idx_branches_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. CATEGORIES
CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_categories_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_categories_restaurant_id` (`restaurant_id`),
    INDEX `idx_categories_slug` (`slug`),
    INDEX `idx_categories_restaurant_sort` (`restaurant_id`, `status`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. FOOD ITEMS
CREATE TABLE `food_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `ingredients` TEXT NULL,
    `allergens` VARCHAR(255) NULL,
    `food_type` ENUM('vegetarian', 'non_vegetarian', 'vegan', 'jain', 'other') NOT NULL DEFAULT 'vegetarian',
    `base_price` DECIMAL(10,2) NOT NULL,
    `calories` INT UNSIGNED NULL DEFAULT NULL,
    `protein` DECIMAL(6,2) NULL DEFAULT NULL,
    `carbs` DECIMAL(6,2) NULL DEFAULT NULL,
    `fat` DECIMAL(6,2) NULL DEFAULT NULL,
    `fiber` DECIMAL(6,2) NULL DEFAULT NULL,
    `sugar` DECIMAL(6,2) NULL DEFAULT NULL,
    `sodium` DECIMAL(7,2) NULL DEFAULT NULL,
    `caffeine` DECIMAL(6,2) NULL DEFAULT NULL,
    `serving_size` VARCHAR(100) NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `is_popular` TINYINT(1) NOT NULL DEFAULT 0,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_food_items_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_food_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_food_items_restaurant_id` (`restaurant_id`),
    INDEX `idx_food_items_category_id` (`category_id`),
    INDEX `idx_food_items_slug` (`slug`),
    INDEX `idx_food_items_available` (`is_available`),
    INDEX `idx_food_items_featured` (`is_featured`),
    INDEX `idx_food_items_popular` (`is_popular`),
    INDEX `idx_food_items_scoping` (`restaurant_id`, `category_id`, `is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. FOOD VARIANTS
CREATE TABLE `food_variants` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `food_item_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `price_adjustment` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `calories_adjustment` INT NULL DEFAULT NULL,
    `protein_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `carbs_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `fat_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `fiber_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `sugar_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `sodium_adjustment` DECIMAL(7,2) NULL DEFAULT NULL,
    `caffeine_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_food_variants_food_item` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_food_variants_food_item_id` (`food_item_id`),
    INDEX `idx_food_variants_available` (`food_item_id`, `is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. FOOD CUSTOMIZATIONS
CREATE TABLE `food_customizations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `food_item_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `group_name` VARCHAR(100) NOT NULL,
    `price_adjustment` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `calories_adjustment` INT NULL DEFAULT NULL,
    `protein_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `carbs_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `fat_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `fiber_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `sugar_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `sodium_adjustment` DECIMAL(7,2) NULL DEFAULT NULL,
    `caffeine_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `is_required` TINYINT(1) NOT NULL DEFAULT 0,
    `min_quantity` INT UNSIGNED NOT NULL DEFAULT 0,
    `max_quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_food_customizations_food_item` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_food_customizations_food_item_id` (`food_item_id`),
    INDEX `idx_food_customizations_group` (`food_item_id`, `group_name`),
    INDEX `idx_food_customizations_available` (`is_available`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. RESTAURANT TABLES
CREATE TABLE `restaurant_tables` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `table_number` VARCHAR(50) NOT NULL,
    `status` ENUM('available', 'occupied', 'cleaning', 'out_of_service') NOT NULL DEFAULT 'available',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `uq_branch_table` UNIQUE (`branch_id`, `table_number`),
    CONSTRAINT `fk_tables_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_tables_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_tables_restaurant_id` (`restaurant_id`),
    INDEX `idx_tables_branch_id` (`branch_id`),
    INDEX `idx_tables_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. QR TOKENS
CREATE TABLE `qr_tokens` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `table_id` BIGINT UNSIGNED NOT NULL,
    `token` VARCHAR(128) NOT NULL UNIQUE,
    `status` ENUM('active', 'inactive', 'expired') NOT NULL DEFAULT 'active',
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_qr_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_qr_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_qr_table` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_qr_token` (`token`),
    INDEX `idx_qr_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. CUSTOMERS
CREATE TABLE `customers` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(120) NOT NULL,
    `mobile` VARCHAR(30) NULL,
    `email` VARCHAR(191) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_customers_mobile` (`mobile`),
    INDEX `idx_customers_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. ORDERS
CREATE TABLE `orders` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(64) NOT NULL UNIQUE,
    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    `branch_id` BIGINT UNSIGNED NOT NULL,
    `table_id` BIGINT UNSIGNED NULL,
    `customer_id` BIGINT UNSIGNED NOT NULL,
    `order_type` ENUM('dine_in', 'takeaway') NOT NULL DEFAULT 'dine_in',
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tax` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `service_charge` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_status` ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    `order_status` ENUM('placed', 'accepted', 'preparing', 'ready', 'completed', 'cancelled') NOT NULL DEFAULT 'placed',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_table` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_orders_order_number` (`order_number`),
    INDEX `idx_orders_restaurant_id` (`restaurant_id`),
    INDEX `idx_orders_branch_id` (`branch_id`),
    INDEX `idx_orders_customer_id` (`customer_id`),
    INDEX `idx_orders_order_status` (`order_status`),
    INDEX `idx_orders_payment_status` (`payment_status`),
    INDEX `idx_orders_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. ORDER ITEMS
CREATE TABLE `order_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `food_item_id` BIGINT UNSIGNED NOT NULL,
    `food_name_snapshot` VARCHAR(150) NOT NULL,
    `base_price_snapshot` DECIMAL(10,2) NOT NULL,
    `variant_name_snapshot` VARCHAR(100) NULL,
    `variant_price_snapshot` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL,
    `calories` INT NULL DEFAULT NULL,
    `protein` DECIMAL(6,2) NULL DEFAULT NULL,
    `carbs` DECIMAL(6,2) NULL DEFAULT NULL,
    `fat` DECIMAL(6,2) NULL DEFAULT NULL,
    `fiber` DECIMAL(6,2) NULL DEFAULT NULL,
    `sugar` DECIMAL(6,2) NULL DEFAULT NULL,
    `sodium` DECIMAL(7,2) NULL DEFAULT NULL,
    `caffeine` DECIMAL(6,2) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_order_items_food` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_order_items_order_id` (`order_id`),
    INDEX `idx_order_items_food_item_id` (`food_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. ORDER ITEM CUSTOMIZATIONS
CREATE TABLE `order_item_customizations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_item_id` BIGINT UNSIGNED NOT NULL,
    `customization_id` BIGINT UNSIGNED NOT NULL,
    `customization_name_snapshot` VARCHAR(100) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `price_adjustment` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `calories_adjustment` INT NULL DEFAULT NULL,
    `protein_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `carbs_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `fat_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `fiber_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `sugar_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `sodium_adjustment` DECIMAL(7,2) NULL DEFAULT NULL,
    `caffeine_adjustment` DECIMAL(6,2) NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_order_custom_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_order_custom_def` FOREIGN KEY (`customization_id`) REFERENCES `food_customizations` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_order_custom_item_id` (`order_item_id`),
    INDEX `idx_order_custom_def_id` (`customization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. PAYMENTS
CREATE TABLE `payments` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `order_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `payment_method` ENUM('cash', 'upi', 'card') NOT NULL,
    `transaction_reference` VARCHAR(128) NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    `paid_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_payments_order_id` (`order_id`),
    INDEX `idx_payments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. REVIEWS
CREATE TABLE `reviews` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurant_id` BIGINT UNSIGNED NOT NULL,
    `customer_id` BIGINT UNSIGNED NOT NULL,
    `order_id` BIGINT UNSIGNED NULL,
    `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `comment` TEXT NULL,
    `status` ENUM('pending', 'approved', 'hidden') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_reviews_restaurant_id` (`restaurant_id`),
    INDEX `idx_reviews_customer_id` (`customer_id`),
    INDEX `idx_reviews_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
