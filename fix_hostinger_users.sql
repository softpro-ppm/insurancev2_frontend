-- SQL Script to fix Hostinger database
-- Run this in phpMyAdmin on Hostinger

-- First, delete the old "Test" user
DELETE FROM users WHERE email = 'abc@gmail.com';

-- Create the proper admin user
INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at) 
VALUES (
    'Admin', 
    'admin@insurance.com', 
    '$2y$12$IyxmNN8ICbf3q6NUIvqQgO/wuoTjzpqTeh9r1DjTAuBM6yV0ykRA.', 
    NOW(), 
    NOW(), 
    NOW()
);

-- Verify the user was created
SELECT * FROM users;
