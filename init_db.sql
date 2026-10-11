CREATE DATABASE IF NOT EXISTS jodasam_academy;
USE jodasam_academy;

CREATE TABLE students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(20) UNIQUE NOT NULL,
  name VARCHAR(100) NOT NULL,
  class VARCHAR(20) NOT NULL,
  age INT,
  contact VARCHAR(20),
  password VARCHAR(100) DEFAULT '1234'
);

CREATE TABLE grades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_db_id INT,
  subject VARCHAR(50),
  score INT,
  term VARCHAR(20),
  FOREIGN KEY (student_db_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100),
  message TEXT,
  date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Demo data
INSERT INTO students (student_id, name, class, age, contact, password) VALUES
('JODA/001', 'Samuel Elamosi', 'SSS2', 16, '08030000001', '1234'),
('JODA/002', 'Aisha Bello', 'JSS3', 14, '08030000002', '1234');