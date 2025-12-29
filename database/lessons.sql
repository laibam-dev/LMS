-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2025 at 12:32 PM
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
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `title`, `video_url`, `created_at`) VALUES
(1, 1, 'Introduction to Deep Learning', 'videos/dl_intro.mp4', '2025-12-27 04:35:51'),
(2, 1, 'Neural Networks Basics', 'videos/neural_networks.mp4', '2025-12-27 04:35:51'),
(3, 1, 'Convolutional Neural Networks', 'videos/cnn.mp4', '2025-12-27 04:35:51'),
(4, 1, 'Recurrent Neural Networks', 'videos/rnn.mp4', '2025-12-27 04:35:51'),
(5, 2, 'Introduction to Parallel Computing', 'videos/parallel_intro.mp4', '2025-12-27 04:36:30'),
(6, 2, 'Multithreading & Multiprocessing', 'videos/multithreading.mp4', '2025-12-27 04:36:30'),
(7, 2, 'Distributed Systems Basics', 'videos/distributed_systems.mp4', '2025-12-27 04:36:30'),
(8, 3, 'Introduction to Marketing', 'videos/marketing_intro.mp4', '2025-12-27 04:37:54'),
(9, 3, 'Consumer Behavior', 'videos/consumer_behavior.mp4', '2025-12-27 04:37:54'),
(10, 3, 'Digital Marketing', 'videos/digital_marketing.mp4', '2025-12-27 04:37:54'),
(11, 4, 'Introduction to Compilers', 'videos/compiler_intro.mp4', '2025-12-27 04:38:33'),
(12, 4, 'Lexical Analysis', 'videos/lexical_analysis.mp4', '2025-12-27 04:38:33'),
(13, 4, 'Syntax Analysis', 'videos/syntax_analysis.mp4', '2025-12-27 04:38:33'),
(14, 4, 'Semantic Analysis', 'videos/semantic_analysis.mp4', '2025-12-27 04:38:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
