-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2025 at 12:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `type` enum('quiz','exam') NOT NULL,
  `title` varchar(255) NOT NULL,
  `total_marks` int(11) DEFAULT 100,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`id`, `course_id`, `type`, `title`, `total_marks`, `created_at`) VALUES
(1, 1, 'quiz', 'Quiz 1: Deep Learning Basics', 100, '2025-12-27 04:42:48'),
(2, 1, 'exam', 'Final Exam: Deep Learning', 100, '2025-12-27 04:42:48'),
(3, 2, 'quiz', 'Quiz 1: Parallel Computing', 100, '2025-12-27 04:42:48'),
(4, 2, 'exam', 'Final Exam: Parallel & Distributed Computing', 100, '2025-12-27 04:42:48'),
(5, 3, 'quiz', 'Quiz 1: Marketing Basics', 100, '2025-12-27 04:42:49'),
(6, 3, 'exam', 'Final Exam: Marketing', 100, '2025-12-27 04:42:49'),
(7, 4, 'quiz', 'Quiz 1: Compiler Basics', 100, '2025-12-27 04:42:49'),
(8, 4, 'exam', 'Final Exam: Compiler Construction', 100, '2025-12-27 04:42:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
