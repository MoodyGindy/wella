CREATE DATABASE IF NOT EXISTS `wellaresin`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `wellaresin`;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `display_name` VARCHAR(120) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `featured_image` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_products_category` (`category_id`),
  CONSTRAINT `fk_products_categories`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_product_images_product` (`product_id`),
  CONSTRAINT `fk_product_images_products`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_address` TEXT NOT NULL,
  `customer_phone` VARCHAR(50) NULL,
  `customer_whatsapp` VARCHAR(50) NOT NULL,
  `notes` TEXT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `line_total` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_order_items_order` (`order_id`),
  INDEX `idx_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_orders`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_products`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO `admin_users` (`email`, `display_name`, `password_hash`)
VALUES ('admin@wellaresin.com', 'Administrator', '$2y$10$qkGfz/uWfGV6ZxXUFBO9De.gtxr2bkItS1lnmmVb5nmeAjZ27ZnuW')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- Seed demo catalog data
INSERT INTO `categories` (`name`) VALUES
  ('Statement Clocks'),
  ('Serving Trays'),
  ('Keepsakes')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `products`
  (`category_id`, `name`, `slug`, `description`, `price`, `stock_quantity`, `featured_image`, `is_active`, `created_at`)
VALUES
  (
    (SELECT id FROM categories WHERE name = 'Statement Clocks' LIMIT 1),
    'Emerald Valley Resin Clock',
    'emerald-valley-resin-clock',
    'A handcrafted resin wall clock inspired by lush valleys, with gold leaf details and quartz accents.',
    95.00,
    8,
    'https://images.unsplash.com/photo-1616628182501-3e3e47cdc68f?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80',
    1,
    NOW()
  ),
  (
    (SELECT id FROM categories WHERE name = 'Statement Clocks' LIMIT 1),
    'Marble Whisper Desk Clock',
    'marble-whisper-desk-clock',
    'A minimalist round clock featuring soft marble tones, perfect for curated shelves and desks.',
    68.00,
    15,
    'https://images.unsplash.com/photo-1616627567935-3d6fe7f02d87?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80',
    1,
    NOW()
  ),
  (
    (SELECT id FROM categories WHERE name = 'Serving Trays' LIMIT 1),
    'Botanical Garden Serving Tray',
    'botanical-garden-serving-tray',
    'Large resin tray embedded with pressed botanicals and brushed gold handles for elegant hosting.',
    82.00,
    10,
    'https://images.unsplash.com/photo-1616627453281-190a2e732db9?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80',
    1,
    NOW()
  ),
  (
    (SELECT id FROM categories WHERE name = 'Serving Trays' LIMIT 1),
    'Mocha Swirl Charcuterie Board',
    'mocha-swirl-charcuterie-board',
    'Rich neutral swirls and a high-gloss finish elevate grazing tables and coffee moments.',
    74.00,
    12,
    'https://images.unsplash.com/photo-1616627714539-b630abd3f271?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80',
    1,
    NOW()
  ),
  (
    (SELECT id FROM categories WHERE name = 'Keepsakes' LIMIT 1),
    'Golden Petal Keepsake Box',
    'golden-petal-keepsake-box',
    'A petite resin box preserving dried blooms—ideal for jewelry or treasured notes.',
    46.00,
    20,
    'https://images.unsplash.com/photo-1616627567969-0ef9b8679511?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80',
    1,
    NOW()
  ),
  (
    (SELECT id FROM categories WHERE name = 'Keepsakes' LIMIT 1),
    'Custom Initial Coaster Set',
    'custom-initial-coaster-set',
    'Set of four neutral resin coasters featuring custom monogramming and subtle metallic flecks.',
    38.00,
    25,
    'https://images.unsplash.com/photo-1616627452388-2fba3a0637f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=900&q=80',
    1,
    NOW()
  )
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `price` = VALUES(`price`),
  `stock_quantity` = VALUES(`stock_quantity`),
  `featured_image` = VALUES(`featured_image`),
  `is_active` = VALUES(`is_active`);

