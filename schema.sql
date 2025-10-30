-- Schema for Student Submission Tracker
CREATE DATABASE IF NOT EXISTS submission_tracker;
USE submission_tracker;

-- Instructors
CREATE TABLE instructors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL
);

-- Courses
CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL,
  title VARCHAR(255) NOT NULL,
  instructor_id INT,
  FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE SET NULL
);

-- Students
CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  roll_no VARCHAR(50) UNIQUE NOT NULL,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(150) UNIQUE,
  phone VARCHAR(30)
);

-- Assignments
CREATE TABLE assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  due_date DATE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Submissions
CREATE TABLE submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL,
  student_id INT NOT NULL,
  submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  file_path VARCHAR(512),
  grade VARCHAR(10),
  remarks TEXT,
  FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  UNIQUE (assignment_id, student_id)
);

-- Sample data
INSERT INTO instructors (name, email) VALUES
('Dr. Priya Sen', 'priya.sen@example.com'),
('Mr. Ravi Patil', 'ravi.patil@example.com');

INSERT INTO courses (code, title, instructor_id) VALUES
('CS101', 'Introduction to Programming', 1),
('DBS201', 'Database Systems', 2);

INSERT INTO students (roll_no, name, email, phone) VALUES
('21CS001', 'Santosh Panke', 'santosh@example.com', '9876543210'),
('21CS002', 'Anita Sharma', 'anita@example.com', '9876501234');

INSERT INTO assignments (course_id, title, description, due_date) VALUES
(1, 'Assignment 1 - Hello World', 'Create a Hello World program', '2025-11-10'),
(2, 'Assignment 1 - ER Diagram', 'Draw ER diagram and normalize', '2025-11-15');

INSERT INTO submissions (assignment_id, student_id, file_path, grade, remarks) VALUES
(1, 1, 'uploads/santosh_hw1.zip', 'A', 'Well implemented'),
(2, 2, NULL, NULL, NULL);
