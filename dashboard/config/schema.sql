-- Database Schema for Student-Course Dashboard

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student-Course enrollments (for future expansion)
CREATE TABLE enrollments (
    student_id INT,
    course_id INT,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Insert a default admin user (password: admin123)
INSERT INTO users (username, email, password) VALUES 
('admin', 'admin@example.com', '$2y$10$8uRbD/KKmET0L0bhyHFHdOJFyS8Oq71FGQtpBEyWzKgJHEWpYlK9C');

-- Insert a test user (password: password123)
INSERT INTO users (username, password, email) 
VALUES ('test', '$2y$10$8QYUNoQNyPrTGWNQW3sA8OVbvNn3u5cSZgREjF98bE2zrJVkPeT5m', 'test@example.com');

-- Insert sample students
INSERT INTO students (first_name, last_name, email, phone, date_of_birth) VALUES
('John', 'Doe', 'john.doe@example.com', '123-456-7890', '2000-01-15'),
('Jane', 'Smith', 'jane.smith@example.com', '987-654-3210', '2001-05-20'),
('Michael', 'Johnson', 'michael.j@example.com', '555-123-4567', '1999-11-08');

-- Insert sample courses
INSERT INTO courses (course_code, title, description, credits) VALUES
('CS101', 'Introduction to Computer Science', 'Fundamentals of computer science including programming basics.', 3),
('MATH201', 'Calculus I', 'Introduction to differential and integral calculus.', 4),
('ENG105', 'English Composition', 'Developing writing skills for academic and professional contexts.', 3);
