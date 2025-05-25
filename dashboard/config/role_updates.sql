-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('admin', 'instructor', 'staff') NOT NULL DEFAULT 'staff' AFTER email;

-- Update existing admin user to have admin role
UPDATE users SET role = 'admin' WHERE username = 'admin';

-- Update test user to have staff role
UPDATE users SET role = 'staff' WHERE username = 'test';
