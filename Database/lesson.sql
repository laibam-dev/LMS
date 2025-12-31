SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Purana table khatam karein agar pehle se hai
DROP TABLE IF EXISTS `lessons`;

-- Naya table jo aapke current LMS database ke sath chale ga
CREATE TABLE `lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(10) UNSIGNED NOT NULL, -- Courses table ki ID ke mutabiq
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL, -- Aapke PHP code ke liye zaroori column
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  -- Instructor jab course delete kare toh lessons khud delete ho jayein
  CONSTRAINT `fk_lessons_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample Data (Aapki IDs ke mutabiq)
INSERT INTO `lessons` (`id`, `course_id`, `title`, `content`, `pdf_file`, `video_url`, `created_at`) VALUES
(1, 1, 'Introduction to Computer', 'Basic concepts', 'intro.pdf', 'https://www.youtube.com/watch?v=example1', '2025-12-31 10:00:00'),
(2, 2, 'Calculus Basics', 'Derivatives intro', 'calculus.pdf', 'https://www.youtube.com/watch?v=example2', '2025-12-31 10:05:00'),
(3, 4, 'English Grammar', 'Tenses and Verbs', 'grammar.pdf', 'https://www.youtube.com/watch?v=example3', '2025-12-31 10:10:00');

COMMIT;