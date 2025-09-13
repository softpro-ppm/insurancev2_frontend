-- Create Agent User for Hostinger Database
-- Run this SQL in phpMyAdmin to create the agent user

-- First, check if agents table exists and has the right structure
-- If not, create it:
CREATE TABLE IF NOT EXISTS `agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'Active',
  `policies_count` int(11) DEFAULT 0,
  `performance` decimal(8,2) DEFAULT 0.00,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agents_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Delete existing agent if any
DELETE FROM agents WHERE email = 'chbalaram321@gmail.com';

-- Insert the agent user
INSERT INTO agents (name, phone, email, user_id, status, policies_count, performance, address, password, created_at, updated_at) VALUES (
    'Chinta Balaram Naidu',
    '+919876543210',
    'chbalaram321@gmail.com',
    'AG001',
    'Active',
    0,
    0.00,
    'Hyderabad, Telangana',
    '$2y$12$IyxmNN8ICbf3q6NUIvqQgO/wuoTjzpqTeh9r1DjTAuBM6yV0ykRA.',
    NOW(),
    NOW()
);

-- Verify the agent was created
SELECT * FROM agents WHERE email = 'chbalaram321@gmail.com';
