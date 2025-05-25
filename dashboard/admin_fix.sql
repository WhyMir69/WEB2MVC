-- This SQL script will create a fresh admin user
-- Run this in phpMyAdmin or MySQL command line

-- First, remove any existing admin user to avoid duplicates
DELETE FROM `users` WHERE `username` = 'admin';

-- Create a new admin user with a known working password hash
-- This is for the password 'admin123'
INSERT INTO `users` (`username`, `email`, `password`) 
VALUES ('admin', 'admin@example.com', '$2y$10$PYDzqoFjE7V03KySgquimu1cHmZBZRXDxr5qnhg9h64PDXL0UyIVK');
