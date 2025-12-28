-- Courses extra columns (if not exist)
ALTER TABLE courses
  ADD COLUMN IF NOT EXISTS subject VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS description TEXT NULL,
  ADD COLUMN IF NOT EXISTS created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP;

-- Assignments
CREATE TABLE IF NOT EXISTS assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  instructions TEXT NULL,
  due_date DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Enrollments
CREATE TABLE IF NOT EXISTS enrollments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  student_id INT NOT NULL,
  enrolled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_course_student (course_id, student_id),
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
