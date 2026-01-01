-- Quiz ke sawalaat ka table banayein jo 'assessments' table se link ho
CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL, -- Ye assessment table ki ID hai
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Computer Basics Quiz (Assessment ID 1) ke liye sample sawal
INSERT INTO `quiz_questions` (`assessment_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1, 'What is the brain of a computer?', 'RAM', 'CPU', 'Hard Disk', 'Monitor', 'B'),
(1, 'Which of these is an Output device?', 'Keyboard', 'Mouse', 'Printer', 'Scanner', 'C');