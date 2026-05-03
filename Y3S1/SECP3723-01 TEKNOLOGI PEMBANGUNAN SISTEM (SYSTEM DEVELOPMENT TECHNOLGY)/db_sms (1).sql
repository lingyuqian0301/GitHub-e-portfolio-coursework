-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2026 at 06:20 AM
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
-- Database: `db_sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_faculty`
--

CREATE TABLE `tb_faculty` (
  `f_id` varchar(10) NOT NULL,
  `f_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_faculty`
--

INSERT INTO `tb_faculty` (`f_id`, `f_name`) VALUES
('J00', 'Admin Department'),
('J28', 'Faculty of Computing'),
('J30', 'Faculty of Artificial Intelligence');

-- --------------------------------------------------------

--
-- Table structure for table `tb_programme`
--

CREATE TABLE `tb_programme` (
  `p_id` varchar(10) NOT NULL,
  `p_name` varchar(100) NOT NULL,
  `p_fac` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_programme`
--

INSERT INTO `tb_programme` (`p_id`, `p_name`, `p_fac`) VALUES
('FAICH', 'Bachelor of Artificial Intelligence', 'J30'),
('P00', 'Not Related (Staff)', 'J00'),
('SECJH', 'Bachelor of Computer Science (Software Engineering)', 'J28'),
('SECPH', 'Bachelor of Computer Science (Data Engineering)', 'J28');

-- --------------------------------------------------------

--
-- Table structure for table `tb_residential`
--

CREATE TABLE `tb_residential` (
  `r_id` varchar(10) NOT NULL,
  `r_name` varchar(50) NOT NULL,
  `r_address` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_residential`
--

INSERT INTO `tb_residential` (`r_id`, `r_name`, `r_address`) VALUES
('R00', 'Outside UTM', ''),
('R01', 'KTDI', 'Jalan UTM'),
('R02', 'KTF', 'Lorong UTM');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `u_id` int(10) NOT NULL,
  `u_pwd` varchar(255) NOT NULL,
  `u_name` varchar(100) NOT NULL,
  `u_phoneperator` varchar(10) NOT NULL,
  `u_email` varchar(50) NOT NULL,
  `u_gender` varchar(1) NOT NULL,
  `u_programme` varchar(10) NOT NULL,
  `u_residential` varchar(10) NOT NULL,
  `u_type` varchar(11) NOT NULL,
  `u_phone` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`u_id`, `u_pwd`, `u_name`, `u_phoneperator`, `u_email`, `u_gender`, `u_programme`, `u_residential`, `u_type`, `u_phone`) VALUES
(1, '$2y$10$jIx466AY7Ek5TMKYzgkXE.1m2X./WIr5R6e34GEYrnCrEq8tTInWG', 'Test', '11', 'test@gmail.com', 'F', 'SECPH', 'R00', '03', 557643521),
(4, '$2y$10$jIx466AY7Ek5TMKYzgkXE.1m2X./WIr5R6e34GEYrnCrEq8tTInWG', 'Staff', '1', 'staff@utm.my', 'M', 'P00', 'R00', '01', 123456789),
(5, '$2y$10$jIx466AY7Ek5TMKYzgkXE.1m2X./WIr5R6e34GEYrnCrEq8tTInWG', 'Tan Yu Yu', '11', 'yuyu@gmail.com', 'M', 'FAICH', 'R00', '03', 34567890),
(6, '$2y$10$jIx466AY7Ek5TMKYzgkXE.1m2X./WIr5R6e34GEYrnCrEq8tTInWG', 'Ling Yu An', '11', '000yuan@gmail.com', 'F', 'SECPH', 'R02', '03', 55834039),
(7, '$2y$10$R/gK37VRFqCiFgSQnHf5FuZQUheJKgwRajJLPsOyqWvwpoYvfLuha', 'Dr. Tan', '12', 'lecturer@sms.utm.my', 'M', 'P00', 'R00', '02', 123456789),
(8, '$2y$10$Q5ADfmGchvdc8n2WqsrIXOrD1KWsKuPOSimjq.2N.75/bRFVTdugK', 'Dr. Ali', '12', 'ali@utm.my', 'F', 'P00', 'R00', '02', 121111111),
(9, '$2y$10$7lZhH/xiN6pob/3jiU9PZeXa8gUGQHqoqH64y7FZsqKz7s7tj0K4O', 'Prof. Sarah', '12', 'sarah@utm.my', 'F', 'P00', 'R00', '02', 122222222),
(14, '$2y$10$7itmdzfrdG405v44YlejE.JACcgAhYHlDqnN7w91XDFGuQJKbqg.m', 'Tan Yu Qian', '011', 'yqling29@gmail.com', 'F', 'FAICH', 'R01', '03', 55934038);

-- --------------------------------------------------------

--
-- Table structure for table `tb_utype`
--

CREATE TABLE `tb_utype` (
  `ut_id` varchar(10) NOT NULL,
  `ut_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_utype`
--

INSERT INTO `tb_utype` (`ut_id`, `ut_name`) VALUES
('01', 'Staff'),
('02', 'Lecturer'),
('03', 'Student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_faculty`
--
ALTER TABLE `tb_faculty`
  ADD PRIMARY KEY (`f_id`);

--
-- Indexes for table `tb_programme`
--
ALTER TABLE `tb_programme`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `p_fac` (`p_fac`);

--
-- Indexes for table `tb_residential`
--
ALTER TABLE `tb_residential`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`u_id`),
  ADD KEY `u_programme` (`u_programme`),
  ADD KEY `u_residential` (`u_residential`),
  ADD KEY `u_type` (`u_type`);

--
-- Indexes for table `tb_utype`
--
ALTER TABLE `tb_utype`
  ADD PRIMARY KEY (`ut_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `u_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_programme`
--
ALTER TABLE `tb_programme`
  ADD CONSTRAINT `tb_programme_ibfk_1` FOREIGN KEY (`p_fac`) REFERENCES `tb_faculty` (`f_id`);

--
-- Constraints for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD CONSTRAINT `tb_user_ibfk_1` FOREIGN KEY (`u_programme`) REFERENCES `tb_programme` (`p_id`),
  ADD CONSTRAINT `tb_user_ibfk_2` FOREIGN KEY (`u_residential`) REFERENCES `tb_residential` (`r_id`),
  ADD CONSTRAINT `tb_user_ibfk_3` FOREIGN KEY (`u_type`) REFERENCES `tb_utype` (`ut_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
