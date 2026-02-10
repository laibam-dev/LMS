-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2026 at 08:17 AM
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
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target_role` enum('all','student','instructor') DEFAULT 'all',
  `admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `target_role`, `admin_id`, `created_at`) VALUES
(1, '🛠️ Scheduled System Maintenance', 'Dear Users, our portal will be down for scheduled maintenance tonight from 12:00 AM to 2:00 AM. Please save your work. Sorry for the inconvenience.', 'all', 1, '2026-02-08 14:38:20'),
(2, ' Important Notice - [Semester/Term] Final Examination Schedule', 'Dear Students,\r\nThis is to inform you that the final examinations for the Fall- 2025 semester will be held from 20-3-2026 to .14-4-2026.\r\nPlease find the detailed date sheet attached to this email/posted on the student portal.\r\nKey Details:\r\nAdmit Cards: Must be brought to every exam.\r\nArrival Time: Please be in the examination hall 15 minutes before the scheduled time.\r\nRules: No electronic devices (phones, smartwatches) are permitted.\r\nPlease prepare accordingly. Best of luck!\r\n[Aqsa Nazir/PolyMathPath Inatitue ]', 'student', 1, '2026-02-09 02:10:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
