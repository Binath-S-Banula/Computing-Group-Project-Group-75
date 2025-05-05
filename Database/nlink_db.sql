-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 10:59 AM
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
-- Database: `nlink_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin30172', '$2y$10$tzuQJ3tNOVA6yf842BfoKe7lsVAPfE2soOVda1KxvBm/1pfVIJ9Vy'),
(2, 'adminp', '$2y$10$uPN4eaChjALAvjZ5KPAAYeYYLzttr3lzgUiHKduYXWBifg3JDMza.');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `subject_allocation_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `lecturer_id`, `subject_allocation_id`, `message`, `created_at`) VALUES
(1, 1, 12, 'lecture cancelled', '2025-05-05 03:14:19'),
(2, 1, 1, 'lecture cancelled', '2025-05-05 03:14:19'),
(3, 1, 11, 'lecture cancelled', '2025-05-05 03:14:19');

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` int(11) NOT NULL,
  `batch_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`id`, `batch_name`) VALUES
(1, '2024.1'),
(2, '2024.2'),
(3, '2025.1'),
(4, '2025.2'),
(10, '2023.2');

-- --------------------------------------------------------

--
-- Table structure for table `career_admins`
--

CREATE TABLE `career_admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `career_admins`
--

INSERT INTO `career_admins` (`id`, `username`, `email`, `password`) VALUES
(1, 'careeradmin001', 'careeradmin@email.com', '$2y$10$TZzNfEuLdgn9BAuDaVqIz.VJGberq.tsqd.I81Ds7QT0WgiWwTzdm'),
(2, 'cadmin', 'careeradmin@email.com', '$2y$10$vq/Tio9clB5G/t9iX2jIlOlkWlksCvNuG8QdmJ3pQxd3gAofSCur2');

-- --------------------------------------------------------

--
-- Table structure for table `career_events`
--

CREATE TABLE `career_events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `career_events`
--

INSERT INTO `career_events` (`id`, `event_name`, `description`, `event_date`, `start_time`, `end_time`, `location`, `image`, `created_at`) VALUES
(3, 'Career Fair / Job Expo', 'An event where companies set up booths to connect with students or job seekers, share information about career opportunities, and collect resumes.', '2025-05-28', '09:00:00', '15:00:00', 'foc', '3.jpg', '2025-05-03 22:49:26'),
(4, 'Industry Expert Talks / Guest Lectures', 'Professionals from various fields are invited to share insights about their career paths, industry trends, and advice for breaking into specific careers.', '2025-05-04', '10:00:00', '12:00:00', 'fob', '4.jpg', '2025-05-03 22:50:09'),
(5, 'Resume Building & Interview Skills Workshop', 'A hands-on session helping participants craft professional resumes, write cover letters, and practice interview techniques.\r\n', '2025-05-25', '12:00:00', '17:00:00', 'auditorium', '5.jpg', '2025-05-03 22:51:19');

-- --------------------------------------------------------

--
-- Table structure for table `club_admins`
--

CREATE TABLE `club_admins` (
  `id` int(11) NOT NULL,
  `club_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_admins`
--

INSERT INTO `club_admins` (`id`, `club_name`, `username`, `email`, `description`, `password`, `is_approved`, `is_active`) VALUES
(1, 'foss', 'foss30172', 'foss@email.com', '', '$2y$10$cfgvjc77O0X91sMROBqUVO0Pl8iawItm.zJ9s68ec69eRrqVtb.0i', 1, 1),
(2, 'cricket', 'cricket001', 'cricket@gmail.com', '', '$2y$10$BrzkoVg.issl/mThoZhPD.7CS2OZ0TiGxh0OmZAKNTy4dg12dEBaC', 1, 1),
(3, 'ieee', 'ieee001', 'ieee@email.com', '', '$2y$10$M7eIFcG4BcdvEjn7pL7OtepFVos9GSm7rILjF3ZyKosayoyevzfo2', 1, 1),
(4, 'dancing', 'dancing001', 'dancing@email.com', '', '$2y$10$EZk5AaRi47Vd4gVqyC7IROhXZFWGaFVISsrJ210GKNxvgus.UMjRG', 0, 0),
(5, 'chess', 'chess001', 'chess@email.com', '', '$2y$10$PCX0rfsZBQI.zw91ucXnb.FFrNx/a7CfPNiUJfN1VVE3iKF.N7J8W', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `club_events`
--

CREATE TABLE `club_events` (
  `id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `access_type` enum('members','public') NOT NULL DEFAULT 'public',
  `max_attendees` int(11) DEFAULT NULL,
  `status` enum('upcoming','cancelled','ended') NOT NULL DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_events`
--

INSERT INTO `club_events` (`id`, `club_id`, `title`, `description`, `event_date`, `start_time`, `end_time`, `location`, `created_at`, `access_type`, `max_attendees`, `status`) VALUES
(10, 1, 'workshop', 'hackathon workshop', '2025-05-20', '19:35:00', '19:34:00', '33333', '2025-04-29 14:01:20', 'members', 50, 'upcoming'),
(11, 1, 'meeting', 'meeting to welcome new members', '2025-06-01', '09:00:00', '11:00:00', 'foc L-101', '2025-04-29 14:19:35', 'members', 10, 'upcoming'),
(12, 1, 'field trip', 'field trip to virtusa', '2025-06-05', '09:00:00', '17:00:00', 'sssss', '2025-04-29 18:46:09', 'public', 25, 'upcoming'),
(13, 1, 'Q n A', 'get to know about our club', '2025-04-29', '12:26:00', '00:28:00', 'zzzzzzz', '2025-04-29 18:56:11', 'public', 100, 'ended'),
(16, 1, 'vv', 'vvvvvvvv', '2025-05-09', '09:00:00', '13:30:00', 'vvvvvv', '2025-05-02 22:13:51', 'public', 77, 'cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `club_event_registrations`
--

CREATE TABLE `club_event_registrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_event_registrations`
--

INSERT INTO `club_event_registrations` (`id`, `student_id`, `event_id`, `registered_at`) VALUES
(5, 3, 11, '2025-04-29 20:55:55'),
(11, 4, 11, '2025-04-29 21:50:42'),
(14, 1, 11, '2025-05-02 22:18:45');

-- --------------------------------------------------------

--
-- Table structure for table `club_members`
--

CREATE TABLE `club_members` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `join_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive','blacklisted') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `club_members`
--

INSERT INTO `club_members` (`id`, `student_id`, `club_id`, `join_date`, `status`) VALUES
(23, 3, 2, '2025-04-28 20:30:24', 'active'),
(24, 3, 1, '2025-04-28 20:35:45', 'active'),
(27, 4, 2, '2025-04-29 04:38:56', 'active'),
(31, 1, 1, '2025-04-30 01:07:29', 'active'),
(32, 5, 1, '2025-04-30 02:29:31', 'active'),
(33, 4, 1, '2025-04-30 03:12:43', 'active'),
(35, 1, 2, '2025-05-03 15:54:26', 'active'),
(36, 6, 1, '2025-05-05 13:58:51', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `degrees`
--

CREATE TABLE `degrees` (
  `id` int(11) NOT NULL,
  `degree_name` varchar(255) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `degrees`
--

INSERT INTO `degrees` (`id`, `degree_name`, `faculty_id`) VALUES
(1, 'Computer Science', 1),
(2, 'Software Engineering', 1),
(3, 'Digital Marketing', 2),
(4, 'Electrical Engineering', 3),
(5, 'multimedia', 3),
(15, 'Business Management', 2);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `created_at`, `is_active`) VALUES
(2, 'Lalith Ferdo', 'doctor', '2025-04-25 22:04:58', 1),
(3, 'Saman Perera', 'Dentist', '2025-04-25 22:05:20', 1),
(4, 'Nicola Muller', 'counsellor', '2025-04-25 22:05:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `slot_start` time DEFAULT NULL,
  `slot_end` time DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_availability`
--

INSERT INTO `doctor_availability` (`id`, `doctor_id`, `day_of_week`, `slot_start`, `slot_end`, `is_available`, `student_id`) VALUES
(1, 2, 'Monday', '10:00:00', '10:30:00', 1, NULL),
(2, 2, 'Friday', '10:00:00', '10:30:00', 1, 3),
(3, 2, 'Tuesday', '10:00:00', '10:30:00', 1, NULL),
(6, 2, 'Monday', '10:30:00', '11:00:00', 1, NULL),
(7, 2, 'Tuesday', '10:30:00', '11:00:00', 1, NULL),
(8, 2, 'Wednesday', '10:30:00', '11:00:00', 1, NULL),
(9, 2, 'Thursday', '10:30:00', '11:00:00', 1, NULL),
(10, 2, 'Friday', '10:30:00', '11:00:00', 1, NULL),
(11, 2, 'Monday', '11:00:00', '11:30:00', 1, NULL),
(12, 2, 'Tuesday', '11:00:00', '11:30:00', 1, NULL),
(13, 2, 'Wednesday', '11:00:00', '11:30:00', 1, NULL),
(14, 2, 'Thursday', '11:00:00', '11:30:00', 1, NULL),
(15, 2, 'Friday', '11:00:00', '11:30:00', 1, NULL),
(16, 2, 'Monday', '11:30:00', '12:00:00', 1, NULL),
(17, 2, 'Tuesday', '11:30:00', '12:00:00', 1, NULL),
(18, 2, 'Wednesday', '11:30:00', '12:00:00', 1, NULL),
(19, 2, 'Thursday', '11:30:00', '12:00:00', 1, NULL),
(20, 2, 'Friday', '11:30:00', '12:00:00', 1, NULL),
(31, 2, 'Thursday', '10:00:00', '10:30:00', 1, 1),
(32, 2, 'Wednesday', '10:00:00', '10:30:00', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_notifications`
--

CREATE TABLE `event_notifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` int(11) NOT NULL,
  `faculty_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `faculty_name`) VALUES
(1, 'Computing'),
(2, 'Business'),
(3, 'Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `favorite_events`
--

CREATE TABLE `favorite_events` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorite_events`
--

INSERT INTO `favorite_events` (`id`, `student_id`, `event_id`, `created_at`) VALUES
(6, 1, 4, '2025-05-04 07:27:10'),
(7, 3, 4, '2025-05-04 09:31:17'),
(8, 3, 5, '2025-05-04 09:31:27'),
(9, 4, 4, '2025-05-04 09:31:37'),
(10, 4, 3, '2025-05-04 09:31:39'),
(11, 4, 5, '2025-05-04 09:31:41'),
(12, 5, 3, '2025-05-04 09:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `lecturer_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `sheet_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `lecturer_id`, `name`, `password`, `faculty_id`, `sheet_id`) VALUES
(1, '30172', 'james jakob', '$2y$10$P.eAPvlUlKR9/ZQC/Dkv3eZgM5Rhs/NKYilVpDP8amGW8oiPprqi6', 1, '15LCqs6jIiOk0pKBQaZxmRTeE3SI_8Kit'),
(2, '3001', 'mohomad faruz', '$2y$10$iz9/2hnfKitS5XtumyfGl.dudR8LY9bDACjBQbcenCiLIIneDDRq6', 2, '16hkIqFTAXT5I2ttHYZX4Hk-6smDokS4q'),
(3, '31111', 'bella ferdo', '$2y$10$G1X0HvdvGiER7NwfOwTDHub861tOBwstu3R.BcDriSuXoO/WfjLcm', 1, '1Gh8-JHPa8hAoFdJRBiARVqWjIFttB8nm'),
(4, '30115', 'iris elisebeth', '$2y$10$dbFqVLYqYM0xCfc5va8vgOnOmROauZW2SYFgy7btP9xIfjobOWIbe', 2, ''),
(5, '30000', 'rajitha iresh', '$2y$10$KBWXffEA2dw.B5UqcVuFHeKHBHvGOtQ/kDytUx.9NxPhuSCT3yVdG', 3, NULL),
(6, '50000', 'alex fredo', '$2y$10$bs3vlXqElvJicII0WhJXseSTZPu84QiLGrfXiVi97kQeyAC49Kt2i', 2, NULL),
(7, 'lecturer', 'sam perera', '$2y$10$fgunSm9Che0dLJz8rCW2teX/8ykPVp1zkwWu6GBfji/kSnBKYDEFy', 1, '15LCqs6jIiOk0pKBQaZxmRTeE3SI_8Kit');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_notes`
--

CREATE TABLE `lecturer_notes` (
  `lecturer_id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_notes`
--

INSERT INTO `lecturer_notes` (`lecturer_id`, `note`, `created_at`, `updated_at`) VALUES
(1, 'i have a special meeting', '2025-05-05 01:37:12', '2025-05-05 01:46:25');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_notifications`
--

CREATE TABLE `lecturer_notifications` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_notifications`
--

INSERT INTO `lecturer_notifications` (`id`, `lecturer_id`, `proposal_id`, `status`, `created_at`) VALUES
(1, 1, 2, 'pending', '2025-04-22 05:05:33'),
(2, 3, 1, 'pending', '2025-04-22 06:11:53'),
(3, 3, 4, 'pending', '2025-04-24 20:47:29'),
(4, 1, 6, 'pending', '2025-04-24 20:59:34');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_reminders`
--

CREATE TABLE `lecturer_reminders` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `reminder_date` date NOT NULL,
  `reminder_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_reminders`
--

INSERT INTO `lecturer_reminders` (`id`, `lecturer_id`, `note`, `reminder_date`, `reminder_time`, `created_at`) VALUES
(1, 1, 'i have to update lecturer notes', '2025-05-19', '12:00:00', '2025-05-04 20:49:13'),
(2, 1, 'i have a special meeting', '2025-05-05', '12:00:00', '2025-05-04 20:51:38'),
(3, 1, 'i have a meeting foss', '2025-05-05', '14:00:00', '2025-05-04 20:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `medical_admins`
--

CREATE TABLE `medical_admins` (
  `id` int(11) NOT NULL,
  `admin_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_admins`
--

INSERT INTO `medical_admins` (`id`, `admin_id`, `password`) VALUES
(1, 'medical30172', '$2y$10$tzuQJ3tNOVA6yf842BfoKe7lsVAPfE2soOVda1KxvBm/1pfVIJ9Vy'),
(2, 'medicalc', '$2y$10$ZF4K0kXYrQMCTBpjv05iwuRYDz5lZugZvCoP2Ff2Jn.wekh88ZM8S');

-- --------------------------------------------------------

--
-- Table structure for table `medical_appointments`
--

CREATE TABLE `medical_appointments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `doctor_availability_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_time_range` varchar(50) DEFAULT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','disabled','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_appointments`
--

INSERT INTO `medical_appointments` (`id`, `student_id`, `doctor_availability_id`, `doctor_id`, `appointment_date`, `appointment_time_range`, `reason`, `created_at`, `status`) VALUES
(1, 1, 1, 2, '2025-05-05', '10:00:00 - 10:30:00', 'head ache', '2025-05-03 05:12:13', 'rejected'),
(2, 1, 3, 2, '2025-05-06', '10:00:00 - 10:30:00', 'stomach ache', '2025-05-03 06:11:44', 'cancelled'),
(3, 1, 4, 2, '2025-05-07', '10:00:00 - 10:30:00', 'rash', '2025-05-03 06:23:56', 'disabled'),
(4, 1, 31, 2, '2025-05-08', '10:00:00 - 10:30:00', 'broken leg', '2025-05-03 06:24:46', 'approved'),
(5, 3, 2, 2, '2025-05-09', '10:00:00 - 10:30:00', 'hair loss', '2025-05-03 06:25:33', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `degree_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `name`, `email`, `faculty_id`, `degree_id`, `batch_id`, `password`) VALUES
(1, '1111', 'sandun fernando', 'sandun@email.com', 1, 1, 3, '$2y$10$2L5810YNTuzZDFRLJ8B7huzSpEqC5hzZdXFFvPrhowKGkk5tvcEaS'),
(3, '2222', 'iris elisebeth', 'ant@email.com', 2, 3, 3, '$2y$10$oo5OF4V7Kp6Sn9KiMnEQnu5HtZgH3vo.VAeQV0USYxYcL5v/mhux2'),
(4, '3333', 'john smith', 'john@email.com', 3, 4, 3, '$2y$10$jcpFBn6KwwUBns.HPbSUVulOadGEHol4aS1w61m1C15gcjdoqJwFe'),
(5, '4444', 'nethmi dias', 'nethmi@email.com', 2, 3, 3, '$2y$10$ohFjOdv6EIhfQe2ZLFEO2.jXTW7NLlmM4me9x52c72/UvkBNNzSaS'),
(6, 'student', 'kavindu gunathilaka', 'kavindu@email.com', 1, 1, 3, '$2y$10$15eIQvcWiRnP5R.3lHXagOVlLKJLRfsmdxIfPjg1fABrcfUwyStcq');

-- --------------------------------------------------------

--
-- Table structure for table `student_timetables`
--

CREATE TABLE `student_timetables` (
  `id` int(11) NOT NULL,
  `degree_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `sheet_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_timetables`
--

INSERT INTO `student_timetables` (`id`, `degree_id`, `batch_id`, `sheet_id`) VALUES
(1, 1, 3, '18siEEkINT6hP2ryT7WaAl-EVg3aB1WtP'),
(2, 1, 1, '1yIez1p6C1AvsvuWpypKOWpLIDBuml8a1'),
(3, 4, 3, '1_9u8BDts0R1ZWZ56BSecwSPQs4iJlwtB'),
(5, 3, 3, '15LCqs6jIiOk0pKBQaZxmRTeE3SI_8Kit');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `faculty_id`) VALUES
(1, 'Introduction to Computer Science', 'CS101', 1),
(2, 'Data Structures and Algorithms', 'CS201', 1),
(3, 'Digital Marketing 101', 'DM101', 2),
(4, 'Principles of Marketing', 'MKT101', 2),
(5, 'Basic Electrical Engineering', 'EE101', 3),
(10, 'Electronics I', 'EE202', 3),
(12, 'python', 'SE101', 1),
(13, 'java', 'SE102', 1),
(14, 'statistics', 'CS103', 1),
(15, 'Web Development', 'SE103', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject_allocations`
--

CREATE TABLE `subject_allocations` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `degree_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_allocations`
--

INSERT INTO `subject_allocations` (`id`, `subject_id`, `lecturer_id`, `degree_id`, `batch_id`) VALUES
(1, 1, 1, 1, 3),
(2, 2, 3, 1, 3),
(3, 3, 2, 3, 3),
(4, 4, 4, 3, 3),
(5, 5, 5, 4, 3),
(6, 10, 5, 4, 3),
(7, 10, 5, 4, 4),
(10, 12, 1, 2, 3),
(11, 13, 1, 2, 4),
(12, 14, 1, 1, 2),
(13, 15, 7, 1, 1),
(14, 1, 7, 1, 1),
(15, 2, 7, 1, 1),
(16, 12, 7, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `timetable_proposals`
--

CREATE TABLE `timetable_proposals` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `sheet_id` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `day` varchar(20) DEFAULT NULL,
  `start_time` varchar(10) DEFAULT NULL,
  `end_time` varchar(10) DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `new_lecturer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable_proposals`
--

INSERT INTO `timetable_proposals` (`id`, `lecturer_id`, `subject_id`, `sheet_id`, `date`, `day`, `start_time`, `end_time`, `reason`, `status`, `created_at`, `new_lecturer`) VALUES
(4, 1, 1, '15LCqs6jIiOk0pKBQaZxmRTeE3SI_8Kit', '2025-05-07', 'Wednesday', '10:00 AM', '12:00 PM', 'vacation', 'pending', '2025-04-22 22:23:00', 3),
(5, 1, 2, '15LCqs6jIiOk0pKBQaZxmRTeE3SI_8Kit', '2025-04-30', 'Wednesday', '11:00 AM', '2:00 PM', 'important meeting', 'pending', '2025-04-24 20:53:59', NULL),
(6, 3, 2, '1Gh8-JHPa8hAoFdJRBiARVqWjIFttB8nm', '2025-04-30', 'Wednesday', '3:00 PM', '5:00 PM', 'workshop', 'pending', '2025-04-24 20:59:04', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_allocation_id` (`subject_allocation_id`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `career_admins`
--
ALTER TABLE `career_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `career_events`
--
ALTER TABLE `career_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `club_admins`
--
ALTER TABLE `club_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- Indexes for table `club_events`
--
ALTER TABLE `club_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `club_event_registrations`
--
ALTER TABLE `club_event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `club_members`
--
ALTER TABLE `club_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `degrees`
--
ALTER TABLE `degrees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_notifications`
--
ALTER TABLE `event_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorite_events`
--
ALTER TABLE `favorite_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `lecturer_notes`
--
ALTER TABLE `lecturer_notes`
  ADD PRIMARY KEY (`lecturer_id`);

--
-- Indexes for table `lecturer_notifications`
--
ALTER TABLE `lecturer_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `proposal_id` (`proposal_id`);

--
-- Indexes for table `lecturer_reminders`
--
ALTER TABLE `lecturer_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `medical_admins`
--
ALTER TABLE `medical_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Indexes for table `medical_appointments`
--
ALTER TABLE `medical_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `student_timetables`
--
ALTER TABLE `student_timetables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `degree_id` (`degree_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `subject_allocations`
--
ALTER TABLE `subject_allocations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `degree_id` (`degree_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `timetable_proposals`
--
ALTER TABLE `timetable_proposals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `career_admins`
--
ALTER TABLE `career_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `career_events`
--
ALTER TABLE `career_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `club_admins`
--
ALTER TABLE `club_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `club_events`
--
ALTER TABLE `club_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `club_event_registrations`
--
ALTER TABLE `club_event_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `club_members`
--
ALTER TABLE `club_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `degrees`
--
ALTER TABLE `degrees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `event_notifications`
--
ALTER TABLE `event_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `favorite_events`
--
ALTER TABLE `favorite_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lecturer_notifications`
--
ALTER TABLE `lecturer_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lecturer_reminders`
--
ALTER TABLE `lecturer_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `medical_admins`
--
ALTER TABLE `medical_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medical_appointments`
--
ALTER TABLE `medical_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_timetables`
--
ALTER TABLE `student_timetables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `subject_allocations`
--
ALTER TABLE `subject_allocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `timetable_proposals`
--
ALTER TABLE `timetable_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`subject_allocation_id`) REFERENCES `subject_allocations` (`id`);

--
-- Constraints for table `club_events`
--
ALTER TABLE `club_events`
  ADD CONSTRAINT `club_events_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `club_admins` (`id`);

--
-- Constraints for table `club_event_registrations`
--
ALTER TABLE `club_event_registrations`
  ADD CONSTRAINT `club_event_registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_event_registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `club_events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `club_members`
--
ALTER TABLE `club_members`
  ADD CONSTRAINT `club_members_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_members_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `club_admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `lecturers_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`);

--
-- Constraints for table `lecturer_notes`
--
ALTER TABLE `lecturer_notes`
  ADD CONSTRAINT `lecturer_notes_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturer_notifications`
--
ALTER TABLE `lecturer_notifications`
  ADD CONSTRAINT `lecturer_notifications_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`),
  ADD CONSTRAINT `lecturer_notifications_ibfk_2` FOREIGN KEY (`proposal_id`) REFERENCES `timetable_proposals` (`id`);

--
-- Constraints for table `lecturer_reminders`
--
ALTER TABLE `lecturer_reminders`
  ADD CONSTRAINT `lecturer_reminders_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_timetables`
--
ALTER TABLE `student_timetables`
  ADD CONSTRAINT `student_timetables_ibfk_1` FOREIGN KEY (`degree_id`) REFERENCES `degrees` (`id`),
  ADD CONSTRAINT `student_timetables_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subject_allocations`
--
ALTER TABLE `subject_allocations`
  ADD CONSTRAINT `subject_allocations_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_allocations_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_allocations_ibfk_3` FOREIGN KEY (`degree_id`) REFERENCES `degrees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_allocations_ibfk_4` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
