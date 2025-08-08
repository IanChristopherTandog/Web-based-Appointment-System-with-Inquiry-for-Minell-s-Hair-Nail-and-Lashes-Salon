-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 05, 2024 at 08:39 AM
-- Server version: 10.11.9-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u910139511_salondb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` char(50) DEFAULT NULL,
  `UserName` char(50) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'ADMIN', 'admin', 9761312421, 'minells.salon@gmail.com', '$2y$10$fy.3xrnr.OAOnEmDelwlseZhedu28xOvyOBRI2rHeGuPbH1A5/3YS', '2024-08-25 06:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `tblappointment`
--

CREATE TABLE `tblappointment` (
  `ID` int(10) NOT NULL,
  `AptNumber` varchar(80) DEFAULT NULL,
  `Name` varchar(120) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `PhoneNumber` bigint(11) DEFAULT NULL,
  `AptDate` varchar(120) DEFAULT NULL,
  `AptTime` varchar(120) DEFAULT NULL,
  `Services` varchar(120) DEFAULT NULL,
  `ApplyDate` timestamp NULL DEFAULT current_timestamp(),
  `Remark` varchar(250) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `RemarkDate` timestamp NULL DEFAULT NULL,
  `Stylist` varchar(100) DEFAULT NULL,
  `Type` enum('Online','Walk-in') NOT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Branch` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblappointment`
--

INSERT INTO `tblappointment` (`ID`, `AptNumber`, `Name`, `Email`, `PhoneNumber`, `AptDate`, `AptTime`, `Services`, `ApplyDate`, `Remark`, `Status`, `RemarkDate`, `Stylist`, `Type`, `Price`, `Branch`) VALUES
(117, '768668458', 'Arman Dizon', 'armandizon33@gmail.com', 0, '2024-10-14', '09:00 am', 'Hair Cut (Men)', '2024-10-14 15:47:31', 'asdas', '3', NULL, 'Kyan Patric  Ferrer', 'Online', 100.00, 'Langkaan'),
(118, '874174044', 'Arman Dizon', 'armandizon33@gmail.com', 0, '2024-10-15', '09:00 am', 'Hair Cut (Men)', '2024-10-14 15:48:57', 'test', '3', NULL, 'ian Ferrer', 'Walk-in', 100.00, 'Langkaan'),
(119, '836362669', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', 0, '2024-10-17', '09:00 AM', 'Hair Spa', '2024-10-17 06:17:08', 'Done', '3', NULL, 'Ian', 'Online', 299.00, 'Manggahan'),
(126, '36280538810', '', '', 0, '2024-10-18', '06:46', 'Brazilian Botox', '2024-10-17 22:46:30', '', '3', NULL, 'Test', 'Walk-in', 999.00, 'Manggahan'),
(127, '779896671', 'Arman Dizon', 'armandizon33@gmail.com', NULL, '2024-10-21', '09:00 AM', 'Hair Cut (Men)', '2024-10-21 08:03:38', '', '3', NULL, 'arman', 'Online', 100.00, 'Manggahan'),
(128, '542873864', 'RMN DZN', 'dizonarman91@gmail.com', NULL, '2024-10-21', '09:30 AM', 'Hair Spa', '2024-10-21 11:00:39', '', '3', NULL, 'Test', 'Online', 299.00, 'Langkaan'),
(129, '582160538', 'Arman Dizon', 'armandizon33@gmail.com', NULL, '2024-10-21', '11:30 AM', 'Cellophane', '2024-10-21 11:05:32', '', '3', NULL, 'Test', 'Online', 499.00, 'Manggahan'),
(130, '750440393', 'Arman Dizon', 'armandizon33@gmail.com', 0, '2024-10-22', '09:00 AM', 'Hair Spa', '2024-10-22 15:58:58', 'trsting', '2', NULL, 'Test', 'Online', 299.00, 'Langkaan'),
(132, '26671131704', 'test', 'test@gmail.com', 1311, '2024-11-14', '10:27', 'Eyelash Extension', '2024-11-14 12:28:17', '', '3', NULL, 'Ian', 'Walk-in', 499.00, 'Manggahan'),
(133, '08311607', 'eight', 'eghit@bmail.com', 131, '2024-11-06', '20:32', 'Hair Color + Brazilian', '2024-11-14 12:29:23', '', '3', NULL, 'kyan', 'Walk-in', 1299.00, 'Manggahan'),
(134, '03645128', '', 'tester1@gmail.com', 0, '2024-11-15', '13:01', '(M) Gel Polish', '2024-11-15 14:18:33', 'asd', '3', NULL, 'Ian', 'Walk-in', 350.00, 'Manggahan'),
(135, '678028975', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-27', '09:00 AM', 'Balayage', '2024-11-16 08:30:44', '', '3', NULL, 'Ian', 'Online', 2199.00, 'Manggahan'),
(136, '459801563', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-27', '09:30 AM', 'Hair Cut (Men)', '2024-11-16 08:31:09', '', '3', NULL, 'Ian', 'Online', 100.00, 'Langkaan'),
(137, '317572151', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-17', '09:00 AM', 'Brazilian Botox', '2024-11-16 08:31:30', '', '3', NULL, 'Test', 'Online', 999.00, 'Manggahan'),
(138, '609408951', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-17', '09:30 AM', 'Hair Spa', '2024-11-16 08:32:07', '', '3', NULL, 'Test', 'Online', 299.00, 'Manggahan'),
(139, '756927330', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', 0, '2024-11-19', '09:00 AM', 'Color Highlights', '2024-11-16 08:47:31', 'test branch', '3', NULL, 'Ian', 'Online', 1499.00, 'Langkaan'),
(140, '621128916', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-17', '10:00 AM', 'Manicure', '2024-11-16 13:35:01', '', '3', NULL, 'Ian', 'Online', 100.00, 'Langkaan'),
(141, '00663643', 'TESTER', 'tester1@gmail.com', 0, '2024-11-06', '13:01', 'Nail Art', '2024-11-16 13:42:24', '', '3', NULL, 'Ian', 'Walk-in', 170.00, 'Langkaan'),
(142, '549885961', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', 0, '2024-11-18', '09:00 AM', 'Hair Color + Brazilian', '2024-11-16 14:55:26', 'kwanlang', '2', NULL, 'kyan', 'Online', 1299.00, 'Langkaan'),
(143, '638250409', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-18', '09:00 AM', 'Manicure', '2024-11-17 04:53:57', '', '3', NULL, 'Ian', 'Online', 100.00, 'Langkaan'),
(144, '661536896', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-18', '09:30 AM', 'Hair Spa', '2024-11-17 04:54:49', '', '3', NULL, 'Test', 'Online', 299.00, 'Manggahan'),
(145, '273793807', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-18', '10:00 AM', 'Hair Cut (Men)', '2024-11-17 04:55:51', '', '3', NULL, 'arman', 'Online', 100.00, 'Manggahan'),
(146, '760512436', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-18', '10:00 AM', 'Hair Spa', '2024-11-17 05:10:49', '', '3', NULL, 'Ian', 'Online', 299.00, 'Langkaan'),
(147, '167841991', 'TEAM CLUSTER', 'cogteamcluster2@gmail.com', NULL, '2024-11-22', '09:00 AM', 'Hair Spa', '2024-11-18 12:40:32', '', '3', NULL, 'Test', 'Online', 299.00, 'Manggahan'),
(148, '514649377', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-11-19', '12:30 PM', 'Hair Spa', '2024-11-18 12:55:24', '', '3', NULL, 'Test', 'Online', 299.00, 'Manggahan'),
(149, '722926527', 'Arman Dizon', 'armandizon33@gmail.com', NULL, '2024-11-19', '09:00 AM', 'Hair Spa', '2024-11-18 13:01:36', '', '3', NULL, 'Test', 'Online', 299.00, 'Manggahan'),
(150, '311700814', 'Cenn', 'cenn921@gmail.com', NULL, '2024-11-29', '11:00 AM', 'Hair Color - Men', '2024-11-18 13:37:49', '', '3', NULL, 'arman', 'Online', 499.00, 'Manggahan'),
(151, '452367081', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', 0, '2024-11-21', '09:00 AM', 'Regular Rebond', '2024-11-20 12:35:04', '451', '2', NULL, 'Ian', 'Online', NULL, NULL),
(152, '14104561229', 'kwan', 'kwan@gmail.com', 0, '0213-03-12', '13:21', '(P) Gel Polish', '2024-11-20 12:38:52', '', '3', NULL, 'Ian', 'Walk-in', NULL, NULL),
(153, '847926064', 'kirito', 'kiritorank2@gmail.com', NULL, '2024-11-26', '09:00 AM', 'Pedicure', '2024-11-24 06:09:40', '', '3', NULL, 'kwan', 'Online', NULL, NULL),
(154, '591478210', 'kirito', 'kiritorank2@gmail.com', NULL, '2024-11-29', '04:30 PM', 'Pedicure', '2024-11-25 09:03:45', '', '2', NULL, 'kwan', 'Online', NULL, NULL),
(155, '371779778', 'Arman Dizon', 'armandizon33@gmail.com', 0, '2024-11-29', '09:00 AM', 'Manicure', '2024-11-27 09:42:38', 'dito', '3', NULL, 'Ian', 'Online', NULL, NULL),
(156, '260885242', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', 0, '2024-11-29', '09:30 AM', 'Hair Cut (Men)', '2024-11-28 01:36:29', 'go', '3', NULL, 'Ian', 'Online', NULL, NULL),
(157, '631952313', 'Arman Dizon', 'armandizon33@gmail.com', 9935003383, '2024-11-29', '10:00 AM', 'Manicure', '2024-11-28 12:03:46', '', '3', NULL, 'Ian', 'Online', 0.00, NULL),
(158, '914756156', 'Arman Dizon', 'armandizon33@gmail.com', 9935003383, '2024-11-29', '10:30 AM', 'Hair Cut (Men)', '2024-11-28 12:20:22', '', '3', NULL, 'Ian', 'Online', 0.00, NULL),
(159, '562591527', 'Arman Dizon', 'armandizon33@gmail.com', 9935003383, '2024-11-29', '11:30 AM', 'Hair Cut (Men)', '2024-11-28 12:21:55', '', '3', NULL, 'Ian', 'Online', 0.00, NULL),
(160, '361543233', 'Arman Dizon', 'armandizon33@gmail.com', 9935003383, '2024-11-29', '12:00 PM', 'Manicure', '2024-11-28 12:44:14', '', '3', NULL, 'Ian', 'Online', 0.00, NULL),
(161, '132865271', 'Arman Dizon', 'armandizon33@gmail.com', 9935003383, '2024-11-29', '12:30 PM', 'Hair Spa', '2024-11-28 12:44:31', '', '3', NULL, 'Test', 'Online', 0.00, NULL),
(162, '720264904', 'Arman Dizon', 'armandizon33@gmail.com', 9935003383, '2024-11-29', '01:00 PM', 'Hair Spa', '2024-11-28 12:48:49', '', '3', NULL, 'Test', 'Online', 0.00, NULL),
(163, '218728725', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-12-01', '09:00 AM', 'Manicure', '2024-11-30 15:29:09', '', '1', NULL, 'kwan', 'Online', 0.00, NULL),
(164, '88462306873', 'Aira', 'aira@gmail.com', 0, '2024-12-01', '15:30', '(P) Gel Polish', '2024-11-30 15:32:05', '', '3', NULL, 'kyan', 'Walk-in', NULL, NULL),
(165, '93361246769', 'Dianne', 'obanadiannechristine@gmail.com', 0, '2024-11-30', '10:30', 'Pedicure', '2024-11-30 15:37:00', '', '3', NULL, 'Ian', 'Walk-in', NULL, NULL),
(166, '864741550', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2024-12-31', '09:00 AM', 'Hair Color - Men', '2024-11-30 15:48:25', '', '0', NULL, 'Ian', 'Online', 0.00, NULL),
(167, '912045383', 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', NULL, '2025-02-01', '09:00 AM', 'Balayage', '2024-12-01 01:42:12', '', '0', NULL, 'Ian', 'Online', 0.00, NULL),
(168, '853472268', 'Juan Dela Cruz', 'cogteamcluster2@gmail.com', 0, '2024-12-02', '09:00 AM', 'Hair Cut (Men)', '2024-12-01 02:56:47', 'No show ', '4', NULL, 'Ian', 'Online', 0.00, NULL),
(169, '11355488584', 'Pedro', 'pedro@gmail.com', 0, '2024-12-02', '11:00', 'Pedicure', '2024-12-01 03:18:43', '', '3', NULL, 'arman', 'Walk-in', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `ID` int(11) NOT NULL,
  `CategoryName` varchar(255) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`ID`, `CategoryName`) VALUES
(1, 'Nail Services'),
(2, 'Treatments'),
(3, 'Hair Services'),
(4, 'Lash Services'),
(13, 'Hair Packages');

-- --------------------------------------------------------

--
-- Table structure for table `tbldayoffs`
--

CREATE TABLE `tbldayoffs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbldayoffs`
--

INSERT INTO `tbldayoffs` (`id`, `title`, `start_date`, `end_date`, `created_at`) VALUES
(19, 'Owner\'s Birthday', '2024-10-28', '2024-10-29', '2024-10-28 13:23:30'),
(20, 'TEST', '2024-10-30', '2024-10-30', '2024-10-28 13:49:36'),
(21, 'ONE DAY', '2024-11-01', '0000-00-00', '2024-10-28 14:06:53'),
(22, 'Holiday', '2024-10-25', '2024-10-26', '2024-10-28 14:22:48'),
(23, 'HOLIDAY ', '2024-10-08', '0000-00-00', '2024-10-28 14:39:28'),
(24, 'HOLIDAY', '2024-11-30', '0000-00-00', '2024-11-13 20:57:49'),
(25, 'HOLIDAY', '2024-11-23', '0000-00-00', '2024-11-13 21:01:36'),
(26, 'HOLIDAY', '2024-11-28', '0000-00-00', '2024-11-18 13:40:11'),
(27, 'Christmas Day', '2024-12-25', '0000-00-00', '2024-11-30 10:55:21'),
(28, 'Rizal Day', '2024-12-30', '0000-00-00', '2024-11-30 11:25:57'),
(29, 'Rizal Day', '2024-12-30', '0000-00-00', '2024-11-30 14:34:09'),
(30, 'Not Available', '2024-12-06', '0000-00-00', '2024-12-01 03:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `tblfaqs`
--

CREATE TABLE `tblfaqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblfaqs`
--

INSERT INTO `tblfaqs` (`id`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(1, 'What services do you offer at your salon?', 'We offer a wide range of services including haircuts, coloring, styling, facials, manicures, pedicures, and various hair treatments. For a full list of services, please visit our services page.', '2024-10-04 07:35:54', '2024-10-04 07:35:54'),
(2, 'How do I book an appointment?', 'You can book an appointment online through our website or by calling our salon directly. We also accept walk-in clients based on availability.', '2024-10-04 07:43:37', '2024-10-04 07:43:37'),
(3, 'What are your salon hours?', 'Our salon is open from Monday to Sunday, 9:00 AM to 7:00 PM', '2024-10-04 07:45:49', '2024-11-28 12:41:38'),
(4, 'Do you offer any discounts or promotions?', 'Yes, we frequently have special promotions and discounts. Please check our website or follow us on social media to stay updated on our latest offers.', '2024-10-04 07:45:49', '2024-10-04 07:45:49'),
(5, 'What should I expect during my first visit?', 'During your first visit, you will receive a consultation with one of our stylists to discuss your preferences and any specific needs. We want to ensure you have the best experience tailored just for you.', '2024-10-04 07:45:49', '2024-10-04 07:45:49'),
(6, 'What products do you use in your salon?', 'We use high-quality products from trusted brands that are known for their effectiveness and safety. If you have any specific allergies or preferences, please let your stylist know.', '2024-10-04 07:45:49', '2024-10-04 07:45:49'),
(7, 'Can I cancel or reschedule my appointment?', 'Yes, we understand that plans can change. Please contact us at least 24 hours in advance to cancel or reschedule your appointment.', '2024-10-04 07:45:49', '2024-11-30 15:11:22'),
(8, 'Do you cater to special events like weddings?', 'Absolutely! We offer specialized packages for weddings and other special events. Please contact us for more information on our bridal services and group bookings.', '2024-10-04 07:45:49', '2024-10-04 07:45:49'),
(9, 'Is your salon wheelchair accessible?', 'Yes, our salon is wheelchair accessible, and we strive to accommodate all clients comfortably. If you have specific needs, please let us know in advance.', '2024-10-04 07:45:49', '2024-10-04 07:45:49'),
(10, 'What safety measures do you have in place?', 'We prioritize the health and safety of our clients and staff. We follow strict sanitation protocols, and all tools and equipment are thoroughly cleaned between appointments. Masks and hand sanitizers are also available for use.', '2024-10-04 07:45:49', '2024-10-04 07:45:49');

-- --------------------------------------------------------

--
-- Table structure for table `tblimages`
--

CREATE TABLE `tblimages` (
  `id` int(11) NOT NULL,
  `ImagePath` varchar(255) NOT NULL,
  `PageType` enum('aboutus','contactus') NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblimages`
--

INSERT INTO `tblimages` (`id`, `ImagePath`, `PageType`, `CreatedAt`, `UpdatedAt`) VALUES
(4, 'uploads/about_us/bg.jpg', 'aboutus', '2024-10-11 14:51:08', '2024-10-11 14:51:12');

-- --------------------------------------------------------

--
-- Table structure for table `tblinquiry`
--

CREATE TABLE `tblinquiry` (
  `ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submit_date` datetime DEFAULT current_timestamp(),
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `user_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblinquiry`
--

INSERT INTO `tblinquiry` (`ID`, `name`, `email`, `subject`, `message`, `submit_date`, `status`, `user_type`) VALUES
(10, 'Arman Dizon', 'dizonarman91@gmail.com', 'asdasdjsagdjagsdsag', 'jsadgasdagshdgsajdgsajdjad', '2024-10-11 15:59:42', 'read', 'REGISTERED'),
(11, 'Arman Dizon', 'dizonarman91@gmail.com', 'asdas', 'ddddd', '2024-10-11 16:02:11', 'read', 'REGISTERED'),
(12, 'Arman Dizon', 'armandizon33@gmail.com', 'asdhaskdjhasjkdhk', 'jasdasd', '2024-10-11 16:10:06', 'read', 'REGISTERED'),
(13, 'Ian', 'ictandog37@gmail.com', 'TEST', 'Testing', '2024-10-18 14:08:28', 'read', 'REGISTERED'),
(14, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:50:53', 'unread', 'REGISTERED'),
(15, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:51:01', 'unread', 'REGISTERED'),
(16, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:51:44', 'unread', 'REGISTERED'),
(17, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:51:56', 'unread', 'REGISTERED'),
(18, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:52:46', 'unread', 'REGISTERED'),
(19, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:53:05', 'unread', 'REGISTERED'),
(20, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:53:19', 'unread', 'REGISTERED'),
(21, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:53:35', 'unread', 'REGISTERED'),
(22, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:54:38', 'unread', 'REGISTERED'),
(23, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:56:49', 'unread', 'REGISTERED'),
(24, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 13:57:45', 'unread', 'REGISTERED'),
(25, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 14:01:26', 'unread', 'REGISTERED'),
(26, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 14:02:47', 'unread', 'REGISTERED'),
(27, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 14:03:41', 'unread', 'REGISTERED'),
(28, 'Arman Dizon', 'armandizon33@gmail.com', 'Arman', 'Arman', '2024-10-22 14:03:59', 'unread', 'REGISTERED'),
(29, 'Arman Dizon', 'armandizon33@gmail.com', 'asdjsadkasdkjsahdkj', 'khasdkjasdashdaskjdkas', '2024-10-22 15:15:56', 'unread', 'REGISTERED'),
(30, 'Arman Dizon', 'dizonarman91@gmail.com', 'sadasdasdasd', 'sadsaasd', '2024-10-24 13:47:05', 'read', 'GUEST'),
(31, 'Arman Dizon', 'armandizon33@gmail.com', 'sadasdas', 'adsadasdasdsa', '2024-10-24 07:47:56', 'unread', 'REGISTERED'),
(32, 'Arman Dizon', 'dizonarman91@gmail.com', 'test', 'tessstttt', '2024-10-24 13:56:22', 'read', 'GUEST'),
(33, 'Arman Dizon', 'armandizon33@gmail.com', 'test message', 'ASFSAFSAFSAFSAFSAFASF', '2024-11-18 12:36:41', 'unread', 'GUEST'),
(34, 'Arman Dizon', 'armandizon33@gmail.com', 'test message', 'ASFSAFSAFSAFSAFSAFASF', '2024-11-18 12:37:47', 'unread', 'GUEST'),
(35, 'Arman Dizon', 'armandizon33@gmail.com', 'test message', 'test', '2024-11-20 12:06:18', 'unread', 'REGISTERED'),
(36, 'Arman Dizon', 'armandizon33@gmail.com', 'test', 'test', '2024-11-20 12:11:40', 'unread', 'REGISTERED'),
(37, 'Arman Dizon', 'armandizon33@gmail.com', 'test message', 'test message', '2024-11-20 12:17:17', 'unread', 'REGISTERED'),
(38, 'axl', 'jayr31@gmail.com', 'testing', 'testing', '2024-11-27 07:35:54', 'unread', 'GUEST'),
(39, 'Arman Dizon', 'dizonarman91@gmail.com', 'test message for4', 'test message for4', '2024-11-27 09:48:57', 'unread', 'GUEST'),
(40, 'Ian test', 'ictandog37@gmail.com', 'testing', 'ASDSAFASA', '2024-11-28 15:09:39', 'unread', 'GUEST'),
(41, 'test', 'test@gmail.com', 'test', 'test to', '2024-11-28 17:10:02', 'unread', 'GUEST'),
(42, 'Dianne', 'obanadiannechristine@gmail.com', 'test', 'testing', '2024-11-30 15:51:50', 'read', 'GUEST'),
(43, 'Dianne', 'obanadiannechristine@gmail.com', 'test', 'testing', '2024-11-30 15:57:08', 'unread', 'REGISTERED'),
(44, 'Marie Dela Cruz', 'kyanpatricferrer@gmail.com', 'Test Inquiry', 'Test Inquiry', '2024-12-01 03:11:10', 'read', 'GUEST'),
(45, 'Juan Dela Cruz', 'cogteamcluster2@gmail.com', 'Test message', 'test message', '2024-12-01 03:13:10', 'read', 'REGISTERED');

-- --------------------------------------------------------

--
-- Table structure for table `tblinvoice`
--

CREATE TABLE `tblinvoice` (
  `id` int(11) NOT NULL,
  `Userid` int(11) DEFAULT NULL,
  `ServiceId` int(11) DEFAULT NULL,
  `BillingId` int(11) DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblinvoice`
--

INSERT INTO `tblinvoice` (`id`, `Userid`, `ServiceId`, `BillingId`, `PostingDate`) VALUES
(1, 2, 2, 621839533, '2024-07-30 15:33:22');

-- --------------------------------------------------------

--
-- Table structure for table `tblpage`
--

CREATE TABLE `tblpage` (
  `ID` int(10) NOT NULL,
  `PageType` varchar(200) DEFAULT NULL,
  `PageTitle` mediumtext DEFAULT NULL,
  `PageDescription` mediumtext DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `UpdationDate` date DEFAULT NULL,
  `Timing` varchar(200) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `ImagePath` varchar(255) DEFAULT NULL,
  `Mission` mediumtext DEFAULT NULL,
  `Vision` mediumtext DEFAULT NULL,
  `History` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `MobileNumber`, `UpdationDate`, `Timing`, `address`, `ImagePath`, `Mission`, `Vision`, `History`) VALUES
(1, 'aboutus', 'About Us', '<p>We aim to deliver high-quality salon services at affordable prices, ensuring that every client experiences exceptional care, expert styling, and personalized attention, all while maintaining accessibility and value for our diverse clientele..</p>\r\n\r\n<p>&nbsp;</p>\r\n', NULL, NULL, NULL, '', NULL, NULL, '<p>At Minlle&#39;s Salon, our mission is to empower every client with confidence and beauty through personalized hair and beauty services. We strive to provide a welcoming environment where creativity, innovation, and professionalism blend to offer exceptional salon experiences.</p>\r\n', '<p>Our vision is to be the leading salon in the industry, known for setting trends in beauty and hair care, while maintaining a commitment to sustainability, client satisfaction, and continuous growth in our craft. We aim to inspire self-expression and confidence in all who visit us.</p>\r\n', '<p>Established in 2019, Minlle&#39;s Salon started as a small local salon with a passion for beauty and customer care. Over the years, we&#39;ve grown into a trusted brand with a loyal customer base, providing a wide range of beauty services. Our dedication to quality and personalized service has been at the heart of our journey.</p>\r\n'),
(2, 'contactus', 'Contact-Us', '<p>We&rsquo;re here to assist you! Whether you have questions about our services, need assistance with appointments, or simply want to share your feedback, feel free to reach out. Our dedicated team is ready to provide you with the information and support you need. Contact us today, and let us help you achieve your beauty goals!</p>\r\n', 'minells.salon@gmail.com', 9683622371, NULL, '9:00 am to 7:00 pm', 'Langkaan, Dasmarinas City, Cavite', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblreplies`
--

CREATE TABLE `tblreplies` (
  `ID` int(11) NOT NULL,
  `inquiry_id` int(11) NOT NULL,
  `reply_text` text NOT NULL,
  `reply_by` varchar(255) NOT NULL,
  `reply_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblreplies`
--

INSERT INTO `tblreplies` (`ID`, `inquiry_id`, `reply_text`, `reply_by`, `reply_date`) VALUES
(24, 13, 'reply', 'ADMIN', '2024-10-20 21:03:43'),
(25, 30, 'asdsad', 'ADMIN', '2024-10-24 13:54:42'),
(26, 32, 'test reply', 'ADMIN', '2024-10-24 13:57:04'),
(27, 42, 'inbox', 'ADMIN', '2024-11-30 15:52:22'),
(28, 44, 'test reply', 'ADMIN', '2024-12-01 03:11:58'),
(29, 45, 'test reply for juan', 'ADMIN', '2024-12-01 03:13:37');

-- --------------------------------------------------------

--
-- Table structure for table `tblservices`
--

CREATE TABLE `tblservices` (
  `ID` int(10) NOT NULL,
  `ServiceName` varchar(200) DEFAULT NULL,
  `Cost` int(10) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `CategoryName` varchar(255) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `ServiceDescription` text DEFAULT NULL,
  `MaxAppointmentsPerDay` int(11) DEFAULT 0,
  `Duration` int(11) NOT NULL,
  `ImagePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblservices`
--

INSERT INTO `tblservices` (`ID`, `ServiceName`, `Cost`, `CreationDate`, `CategoryName`, `ServiceDescription`, `MaxAppointmentsPerDay`, `Duration`, `ImagePath`) VALUES
(21, 'Hair Cut (Men)', 100, '2024-09-24 11:54:40', 'Hair Services', 'Personalized styles, from classic cuts to modern fades, ensuring precision and a sharp finish. ', 6, 30, '../uploads/67174d998ee09.jpg'),
(22, 'Hair Cut (Women)', 120, '2024-09-24 11:54:40', 'Hair Services', 'Revamp your style with our Women\'s Haircut service. Experience a rejuvenating transformation!', 6, 30, '../uploads/67174dbfe8b08.jpg'),
(27, 'Regular Hair Color (Women)', 499, '2024-09-24 11:54:40', 'Hair Services', 'Basic hair coloring to refresh your natural shade or cover grays.', 6, 30, '../uploads/67174d165ce81.jpg'),
(31, 'Regular Rebond', 999, '2024-09-24 11:54:40', 'Hair Services', 'Smooth, straight hair with our classic rebonding treatment.', 4, 360, '../uploads/671751ab7c7a8.jpg'),
(33, 'Color Highlights', 1499, '2024-09-24 11:54:40', 'Hair Services', 'Add dimension and depth to your hair with expertly applied highlights.', 6, 120, '../uploads/67174824d6a4c.jpg'),
(34, 'Balayage', 2199, '2024-09-24 11:54:40', 'Hair Services', 'A soft, natural-looking gradient color for a sun-kissed effect.', 1, 480, '../uploads/6717462861e5b.jpg'),
(36, 'Hair Spa', 299, '2024-09-24 11:54:40', 'Treatments', 'A nourishing treatment to revitalize dry and damaged hair.', 6, 30, '../uploads/67174c8cddd90.jpg'),
(37, 'Brazilian Botox', 999, '2024-09-24 11:54:40', 'Treatments', 'A deep conditioning treatment to smooth and strengthen hair.', 6, 90, '../uploads/671746e92b8ef.jpg'),
(41, 'Hair Color + Brazilian', 1299, '2024-09-24 11:54:40', 'Hair Packages', 'Color your hair and treat it with Brazilian smoothing for extra shine and health.', 4, 180, '../uploads/67174c5455102.jpg'),
(45, 'Manicure', 100, '2024-09-24 11:54:40', 'Nail Services', 'Basic nail grooming to keep your nails neat and tidy.', 6, 30, '../uploads/6717500c0eb84.jpg'),
(46, 'Pedicure', 120, '2024-09-24 11:54:40', 'Nail Services', 'Foot care with a professional trim and shaping for healthy nails.', 6, 30, '../uploads/67175090b5ba1.jpg'),
(47, '(M) Gel Polish', 350, '2024-09-24 11:54:40', 'Nail Services', 'Long-lasting gel polish for a shiny, chip-resistant manicure.', 6, 30, '../uploads/67174afe390a3.jpg'),
(48, '(P) Gel Polish', 400, '2024-09-24 11:54:40', 'Nail Services', 'A perfect gel polish pedicure for a lasting, flawless finish.', 6, 30, '../uploads/67174b111acf4.jpg'),
(50, 'Nail Art', 170, '2024-09-24 11:54:40', 'Nail Services', 'Add a creative and stylish design to your nails.', 6, 30, '../uploads/6713ceec10299.jpg'),
(51, 'Soft Gel Extension', 600, '2024-09-24 11:54:40', 'Nail Services', 'Enhance your nails with durable, natural-looking soft gel extensions.', 6, 60, '../uploads/671754a15ea3d.jpg'),
(58, 'Eyelash Extension', 499, '2024-09-24 11:54:40', 'Lash Services', 'Get fuller, longer lashes with professional extensions.', 6, 60, '../uploads/67174851323c4.jpg'),
(61, 'Hair and Make-up', 800, '2024-09-24 11:54:40', 'Lash Services', 'Professional hair styling and makeup for any occasion.', 6, 90, '../uploads/67174b879cdd2.jpg'),
(72, 'Brazilian Treatment', 499, '2024-10-22 06:36:03', 'Hair Services', 'Enjoy sleek, manageable locks and say goodbye to bad hair days!', 8, 60, '../uploads/Brazilian Treatment.jpg'),
(73, 'Hair Color - Men', 499, '2024-10-22 06:59:31', 'Hair Services', 'Basic hair coloring to refresh your natural shade or cover grays.', 6, 30, '../uploads/haircolor men.jpg'),
(74, 'Perm', 999, '2024-10-22 07:16:18', 'Hair Services', 'Enjoy long-lasting, bouncy curls that elevate your style and bring versatility to your look!', 7, 60, '../uploads/Perm.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tblsettings`
--

CREATE TABLE `tblsettings` (
  `id` int(11) NOT NULL,
  `maintenance_mode` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblsettings`
--

INSERT INTO `tblsettings` (`id`, `maintenance_mode`) VALUES
(1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblstylist`
--

CREATE TABLE `tblstylist` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `commission_rate` decimal(5,2) DEFAULT 0.00,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `specialty` varchar(255) DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblstylist`
--

INSERT INTO `tblstylist` (`id`, `name`, `email`, `password`, `commission_rate`, `is_verified`, `created_at`, `specialty`, `availability`, `branch`) VALUES
(4, 'Test', 'test@gmail.com', '$2y$10$kCbl.xgkQQ3u3SE6Y8WrYe0dd.2ySwZwJGeyxFkmXsZxqWoJZpn.y', 1.00, 0, '2024-10-07 01:24:22', 'Treatments,Lash Services', 'Mon-Fri 9 AM - 5 PM', 'Manggahan'),
(5, 'Ian', 'ian@gmail.com', '$2y$10$gp18MJrw01ASUFjGFhIcCuZE55ut8eF6cjpQ91jG.w.RowytBWdKW', 5.00, 0, '2024-10-17 06:06:31', 'Nail Services,Treatments,Hair Services,Lash Services', 'Monday,Thursday', 'Langkaan'),
(6, 'arman', 'arman@gmail.com', '$2y$10$yWDCVQEYSW9ZTkBY/oI1WeTka/O8a0ZUYR9o8RF84gMPVKb5bonFS', 5.00, 0, '2024-10-17 06:06:53', 'Hair Services,Lash Services', 'Sat 10 AM - 4 PM,Sun 12 PM - 6 PM', 'Manggahan'),
(7, 'kyan', 'kyan@gmail.com', '$2y$10$YTv/p6QANfhfvRvsJoANWua9dzuoExYdgn427IKhNT8QWW.Z27oL6', 5.00, 0, '2024-10-17 06:08:12', 'Waxing,Hair Packages', 'Sat 10 AM - 4 PM,Sun 12 PM - 6 PM', 'Manggahan'),
(8, 'darwin', 'darwin@gmail.com', '$2y$10$Fzbq1Da.7X46pljo0qG.6OQoVqLfR3mpKEOiEb1A.X.UdMqJLg4um', 5.00, 0, '2024-10-17 06:08:35', 'Waxing,Hair Packages,Nail Packages', 'Mon-Fri 9 AM - 5 PM,Sat 10 AM - 4 PM', 'Langkaan'),
(12, 'testin', 'tests@gmail.com', '$2y$10$M8DFzChQ3vIkWTx8RSwrH.MAY9wTES6.2qjteAiuQMR5N637e4YIS', 0.00, 0, '2024-11-13 18:45:21', 'Hair Services,Waxing,Nail Packages', NULL, 'Manggahan'),
(13, 'Another', 'other@gmail.com', '$2y$10$zVRwNhjy76JqiaClKslNmuxO8w4qQFdogbrivTFFil8CwJch.P6Y6', 0.00, 0, '2024-11-16 14:05:23', 'Treatments,Lash Services,Nail Packages', NULL, 'Langkaan'),
(14, 'kwan', 'kwan@gmail.com', '$2y$10$uZU9PczK2rCVxkn30MEP2.vm.QyzCvf3ScS7r6X5YrXKyGVazu7gW', 0.00, 0, '2024-11-20 12:39:46', 'Nail Services,Treatments,Hair Services,Lash Services,Waxing', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblstylists`
--

CREATE TABLE `tblstylists` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `specialty` varchar(255) NOT NULL,
  `experience_years` int(2) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `availability` text NOT NULL,
  `ratings` decimal(3,1) DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `tblstylists`
--

INSERT INTO `tblstylists` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `specialty`, `experience_years`, `profile_picture`, `availability`, `ratings`, `status`) VALUES
(1, 'ian', 'Tandog', 'ian05@gmail.com', '0912321541', '1', 1, 'ada', 'Monday-Friday', 4.5, 0),
(2, 'Arman Christopher', 'Dizon', 'arman@gmail.com', '0932141', '3', 0, NULL, 'Monday, Tuesday, Thursday, Friday, Saturday', NULL, 0),
(3, 'Kyan Patric ', 'Ferrer', 'kyan@gmail.com', '09123214', '0', 0, NULL, 'Monday, Tuesday, Wednesday, Friday, Saturday', NULL, 0),
(4, 'sdassada', 'asdsa', 'asd@gmail.om', '014122', '0', 0, NULL, '', NULL, 0),
(6, 'b', 'b', 'b@gas', '012412`', 'Hair Services', 0, NULL, 'Tuesday, Friday', NULL, 0),
(7, 'Sem', 'Cabarles', 'sem@rmail.com', '0932141', 'Array', 0, NULL, 'Monday, Thursday, Friday, Saturday', NULL, 0),
(8, 'Kyan Patric ', 'Ferrer', 'kyan@gmail.com', '09412412', 'Hair Services, Treatments, Hair Packages, Lash', 0, NULL, '', NULL, 0),
(9, 'ian', 'Ferrer', 'ictandog05@gmail.com', '12312', 'Hair Services, Treatments, Hair Packages', 0, NULL, '', NULL, 0),
(11, 'Kobe', 'Villegas', 'kobe@gmail.com', '09216545', 'Waxing, Nail Services, Nail Services', 0, NULL, 'Monday, Tuesday, Wednesday, Thursday', NULL, 0),
(12, 'Lance', 'Pagdato', 'lancepagdatothegreatest@gmail.com', '094552', 'Treatments, Hair Packages, Lash, Waxing', 0, NULL, 'Monday, Tuesday, Wednesday, Thursday, Friday', NULL, 0),
(13, 'Ian Handog Hotdog Talbog Alindog Hanggang Alas Ots', 'Tandog', 'taldog@gmail.com', '524976315', 'Hair Packages', 0, NULL, 'Tuesday', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(75) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `reg_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`id`, `name`, `email`, `password`, `otp`, `is_verified`, `reg_date`) VALUES
(17, 'Ian Christopher A. Tandog', 'ictandog37@gmail.com', '$2y$10$BjLHAzHqT8wzGDC7hLj0Te550r.UMM00IXt/gPwloUaq5zMTL4S8S', NULL, 1, '2024-10-20 21:02:28'),
(22, 'kobe villegas', 'chiyuki241@gmail.com', '$2y$10$XXJoEWn/bl55oTaSdGHSV.kEgRAI37rCJ1xtG6D0QxWlOglPnPxBi', '815977', 1, '2024-10-20 21:02:28'),
(23, 'Arman Dizon', 'armandizon33@gmail.com', '$2y$10$ObcEqhX5DrHetqKLB8guiOTXTkJjt2PkuHFFNl6yd6Hvk/B6WF/.S', '528674', 1, '2024-10-20 21:02:28'),
(27, 'Arman Dizon', 'dizonarman91@gmail.com', '$2y$10$BjBFA2iUEaJEyOyE4gquOu2pV49er544q1a0z2eAq7syOz4gwiLgy', '779395', 0, '2024-10-25 17:10:51'),
(28, 'test', 'tester1@gmail.com', '$2y$10$15tLgUFblBQFYgPRV2RMmehLx.sjLrWSNmVruhtYVOuYSgXRNTC.K', '952971', 0, '2024-11-16 05:24:59'),
(30, 'IdsDSA', 'ian@gmail.com', '$2y$10$SyDxib1FY6/lR6dmA1xugeBJqu.xvFafUvcb3os2CrgFqmTElFADu', '205447', 0, '2024-11-16 06:05:07'),
(38, 'Cenn', 'cenn921@gmail.com', '$2y$10$34oPZVJxWWkQT4u5CAO4/OGjQGt4YiFP1BK.Qz8ROqxSMyd4.rGIi', '494802', 1, '2024-11-18 13:35:03'),
(39, 'kirito', 'kiritorank2@gmail.com', '$2y$10$KlWsNomfNA/oDxqqQYCFquvqOQIAzYqtUF1j1xHB8k1uPz4WXyEX2', '451531', 1, '2024-11-19 15:41:49'),
(40, 'Kyan Patric Ferrer', 'kyanpatricferrer@gmail.com', '$2y$10$IEkhmBwoxnHB/2SNNa4zI.gQYH61M9BTtRXmwiLY4/yMA63VzwubS', '362275', 1, '2024-11-25 10:19:23'),
(41, 'Dianne', 'obanadiannechristine@gmail.com', '$2y$10$NmIRj/JZ8FM2EGEPzPe8Fe4n54jdOdCoQg46STv5vr.vz3mteEj26', '894024', 1, '2024-11-30 15:53:36'),
(42, 'Juan Dela Cruz', 'cogteamcluster2@gmail.com', '$2y$10$qkmhEix0HsoqxVzA/cGVHeA59JNmgn0IpAJxkdJ1MgeHgRtn/o6sK', '227546', 1, '2024-12-01 02:55:40'),
(43, 'axl', 'jayr31@gmail.com', '$2y$10$j/9Ch4Ur6yZr3jvA5kxAquRLmJZMIHn8Fo31na69pQjYONj6EDVJi', '796086', 1, '2024-12-02 03:49:05'),
(44, 'test', 'test2@gmail.com', '$2y$10$YmMGFGdL40Ntex5qjQZktuuoW/SLsdu6iuEGVqEeQgCA7LQH955Xe', NULL, 0, '2024-12-04 08:22:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblappointment`
--
ALTER TABLE `tblappointment`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbldayoffs`
--
ALTER TABLE `tbldayoffs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblfaqs`
--
ALTER TABLE `tblfaqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblimages`
--
ALTER TABLE `tblimages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblinquiry`
--
ALTER TABLE `tblinquiry`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblinvoice`
--
ALTER TABLE `tblinvoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `tblpage`
--
ALTER TABLE `tblpage`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblreplies`
--
ALTER TABLE `tblreplies`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `inquiry_id` (`inquiry_id`);

--
-- Indexes for table `tblservices`
--
ALTER TABLE `tblservices`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblsettings`
--
ALTER TABLE `tblsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblstylist`
--
ALTER TABLE `tblstylist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tblstylists`
--
ALTER TABLE `tblstylists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblappointment`
--
ALTER TABLE `tblappointment`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbldayoffs`
--
ALTER TABLE `tbldayoffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tblfaqs`
--
ALTER TABLE `tblfaqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblimages`
--
ALTER TABLE `tblimages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblinquiry`
--
ALTER TABLE `tblinquiry`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tblinvoice`
--
ALTER TABLE `tblinvoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tblpage`
--
ALTER TABLE `tblpage`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblreplies`
--
ALTER TABLE `tblreplies`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tblservices`
--
ALTER TABLE `tblservices`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tblsettings`
--
ALTER TABLE `tblsettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblstylist`
--
ALTER TABLE `tblstylist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tblstylists`
--
ALTER TABLE `tblstylists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblreplies`
--
ALTER TABLE `tblreplies`
  ADD CONSTRAINT `tblreplies_ibfk_1` FOREIGN KEY (`inquiry_id`) REFERENCES `tblinquiry` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
