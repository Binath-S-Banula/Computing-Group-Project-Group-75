-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 04:59 PM
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
-- Database: `nlink_cp`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `lecturer` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `student_id`, `student_name`, `batch_number`, `faculty`, `lecturer`, `reason`) VALUES
(1, '30681', 'G D A NISAL', '23.1', 'Faculty of Computing', 'RTX - Tuesday 9.00AM to 10.00AM - Room: Myroom', 'daasa'),
(2, '30681', 'G D A NISAL', '23.1', 'Faculty of Computing', 'RTX - Tuesday 9.00AM to 10.00AM - Room: Myroom', 'daasa'),
(5, '30681', 'rsdg', '23.1', 'Faculty of Design', 'Ginthi - Monday 9.00AM to 10.00AM - Room: classroom', 'sgdgsdg'),
(6, 'reh', 'regerg', 'gwrg', 'Faculty of Computing', 'Ginthi - Monday 9.00AM to 10.00AM - Room: classroom', 'gsdgsd'),
(7, 'asf', 'saf', 'saf', 'Faculty of Business', 'Ginthi - Monday 9.00AM to 10.00AM - Room: classroom', 'sfsa'),
(8, 'asf', 'saf', 'saf', 'Faculty of Business', 'Ginthi - Monday 9.00AM to 10.00AM - Room: classroom', 'sfsa');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_appointments`
--

CREATE TABLE `doctor_appointments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `faculty` varchar(100) NOT NULL,
  `doctor` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_appointments`
--

INSERT INTO `doctor_appointments` (`id`, `student_id`, `student_name`, `batch_number`, `faculty`, `doctor`, `reason`, `submitted_at`) VALUES
(1, '30681', 'G D A NISAL', 'gwrg', 'Faculty of Business', '', 'sad', '2025-04-06 16:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `id` int(11) NOT NULL,
  `doctor_name` varchar(255) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `time_range` varchar(50) NOT NULL,
  `room` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedule`
--

INSERT INTO `doctor_schedule` (`id`, `doctor_name`, `day_of_week`, `time_range`, `room`, `created_at`) VALUES
(1, 'Ginthi', 'Monday', '9.00AM to 10.00AM', 'classroom', '2025-04-06 16:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_schedule`
--

CREATE TABLE `lecturer_schedule` (
  `id` int(11) NOT NULL,
  `lecturer_name` varchar(255) NOT NULL,
  `day_of_week` varchar(50) NOT NULL,
  `time_range` varchar(50) NOT NULL,
  `room` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_schedule`
--

INSERT INTO `lecturer_schedule` (`id`, `lecturer_name`, `day_of_week`, `time_range`, `room`) VALUES
(1, 'RTX', 'Tuesday', '9.00AM to 10.00AM', 'Myroom');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_appointments`
--
ALTER TABLE `doctor_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecturer_schedule`
--
ALTER TABLE `lecturer_schedule`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `doctor_appointments`
--
ALTER TABLE `doctor_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lecturer_schedule`
--
ALTER TABLE `lecturer_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
