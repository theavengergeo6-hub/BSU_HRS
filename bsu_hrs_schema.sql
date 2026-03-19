-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2026 at 01:00 AM
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
-- Database: `bsu_hrs_schema`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 7, 'Reservation approve', 'Reservation ID: 9', '::1', '2026-02-26 03:18:04'),
(2, 7, 'Reservation approve', 'Reservation ID: 17', '::1', '2026-02-27 02:13:06'),
(3, 7, 'Reservation approve', 'Reservation ID: 15', '::1', '2026-02-27 06:26:11'),
(4, 7, 'Reservation deny', 'Reservation ID: 18', '::1', '2026-03-01 23:42:26'),
(5, 7, 'Reservation deny', 'Reservation ID: 20', '::1', '2026-03-01 23:42:30'),
(6, 7, 'Reservation pencil', 'Reservation ID: 22', '::1', '2026-03-02 06:42:25');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `booking_no` varchar(255) NOT NULL,
  `source_table` enum('reservations','room_reservation','guest_room_reservations','function_room_reservations') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `message`, `is_read`, `created_at`, `booking_no`, `source_table`) VALUES
(21, 'New reservation submitted with Booking No. GM RES-20250125-142.', 1, '2025-01-25 12:19:14', 'GM RES-20250125-142', 'room_reservation'),
(22, 'New reservation submitted with Booking No. GM RES-20250125-143.', 1, '2025-01-25 12:48:07', 'GM RES-20250125-143', 'room_reservation'),
(23, 'New reservation submitted with Booking No. GM RES-20250126-144.', 1, '2025-01-26 12:27:47', 'GM RES-20250126-144', 'room_reservation'),
(24, 'New reservation submitted with Booking No. GM RES-20250127-145.', 1, '2025-01-27 08:05:00', 'GM RES-20250127-145', 'room_reservation'),
(25, 'New reservation submitted with Booking No. EV RES-20250127-1.', 1, '2025-01-27 12:34:23', 'EV RES-20250127-1', 'reservations'),
(26, 'New reservation submitted with Booking No. GM RES-20250128-146.', 1, '2025-01-28 04:57:04', 'GM RES-20250128-146', 'room_reservation'),
(27, 'New reservation submitted with Booking No. EV RES-20250128-81.', 1, '2025-01-28 04:58:34', 'EV RES-20250128-81', 'reservations'),
(28, 'New reservation submitted with Booking No. EV RES-20250128-82.', 1, '2025-01-28 04:59:10', 'EV RES-20250128-82', 'reservations'),
(29, 'New reservation submitted with Booking No. GM RES-20250128-147.', 1, '2025-01-28 05:05:02', 'GM RES-20250128-147', 'room_reservation'),
(30, 'New reservation submitted with Booking No. EV RES-20250128-83.', 1, '2025-01-28 09:42:18', 'EV RES-20250128-83', 'reservations'),
(31, 'New reservation submitted with Booking No. EV RES-20250128-84.', 1, '2025-01-28 11:23:25', 'EV RES-20250128-84', 'reservations'),
(32, 'New reservation submitted with Booking No. GM RES-20250128-148.', 1, '2025-01-28 11:32:33', 'GM RES-20250128-148', 'room_reservation'),
(33, 'New reservation submitted with Booking No. EV RES-20250128-85.', 1, '2025-01-28 11:35:15', 'EV RES-20250128-85', 'reservations'),
(34, 'New reservation submitted with Booking No. GM RES-20250128-149.', 1, '2025-01-28 11:36:47', 'GM RES-20250128-149', 'room_reservation'),
(35, 'New reservation submitted with Booking No. GM RES-20250128-150.', 1, '2025-01-28 11:46:36', 'GM RES-20250128-150', 'room_reservation'),
(36, 'New reservation submitted with Booking No. GM RES-20250128-151.', 1, '2025-01-28 12:17:00', 'GM RES-20250128-151', 'room_reservation'),
(37, 'New reservation submitted with Booking No. EV RES-20250128-84.', 1, '2025-01-28 12:17:03', 'EV RES-20250128-84', 'reservations'),
(38, 'New reservation submitted with Booking No. EV RES-20250128-87.', 1, '2025-01-28 12:28:42', 'EV RES-20250128-87', 'reservations'),
(39, 'New reservation submitted with Booking No. EV RES-20250128-88.', 1, '2025-01-28 13:18:59', 'EV RES-20250128-88', 'reservations'),
(40, 'New reservation submitted with Booking No. EV RES-20250129-89.', 1, '2025-01-29 00:55:16', 'EV RES-20250129-89', 'reservations'),
(41, 'New reservation submitted with Booking No. EV RES-20250130-90.', 1, '2025-01-29 19:05:20', 'EV RES-20250130-90', 'reservations'),
(42, 'New reservation submitted with Booking No. GM RES-20250130-152.', 1, '2025-01-29 23:47:52', 'GM RES-20250130-152', 'room_reservation'),
(43, 'New reservation submitted with Booking No. EV RES-20250130-90.', 1, '2025-01-30 14:25:29', 'EV RES-20250130-90', 'reservations'),
(44, 'New reservation submitted with Booking No. GM RES-20250130-153.', 1, '2025-01-30 14:27:58', 'GM RES-20250130-153', 'room_reservation'),
(45, 'New reservation submitted with Booking No. GM RES-20250131-154.', 1, '2025-01-31 04:08:54', 'GM RES-20250131-154', 'room_reservation'),
(46, 'New reservation submitted with Booking No. EV RES-20250131-92.', 1, '2025-01-31 06:31:14', 'EV RES-20250131-92', 'reservations'),
(47, 'New reservation submitted with Booking No. GM RES-20250131-155.', 1, '2025-01-31 06:33:22', 'GM RES-20250131-155', 'room_reservation'),
(48, 'New reservation submitted with Booking No. EV RES-20250131-93.', 1, '2025-01-31 08:39:01', 'EV RES-20250131-93', 'reservations'),
(49, 'New reservation submitted with Booking No. GM RES-20250131-156.', 1, '2025-01-31 09:00:29', 'GM RES-20250131-156', 'room_reservation'),
(50, 'New reservation submitted with Booking No. EV RES-20250203-94.', 1, '2025-02-03 11:39:52', 'EV RES-20250203-94', 'reservations'),
(51, 'New reservation submitted with Booking No. EV RES-20250203-95.', 1, '2025-02-03 11:41:57', 'EV RES-20250203-95', 'reservations'),
(52, 'New reservation submitted with Booking No. GM RES-20250212-157.', 1, '2025-02-12 00:20:55', 'GM RES-20250212-157', 'room_reservation'),
(53, 'New reservation submitted with Booking No. GM RES-20250220-158.', 1, '2025-02-19 23:36:23', 'GM RES-20250220-158', 'room_reservation'),
(54, 'New reservation submitted with Booking No. GM RES-20250220-159.', 1, '2025-02-19 23:56:04', 'GM RES-20250220-159', 'room_reservation'),
(55, 'New reservation submitted with Booking No. EV RES-20250224-0001.', 1, '2025-02-24 07:25:26', 'EV RES-20250224-0001', 'reservations'),
(56, 'New reservation submitted with Booking No. GM RES-20250224-160.', 1, '2025-02-24 07:27:29', 'GM RES-20250224-160', 'room_reservation'),
(57, 'New reservation submitted with Booking No. GM RES-20250224-161.', 0, '2025-02-24 07:41:11', 'GM RES-20250224-161', 'room_reservation'),
(58, 'New reservation submitted with Booking No. EV RES-20250224-0002.', 1, '2025-02-24 07:43:06', 'EV RES-20250224-0002', 'reservations'),
(60, 'New reservation submitted with Booking No. GM RES-20250224-163.', 0, '2025-02-24 08:33:41', 'GM RES-20250224-163', 'room_reservation'),
(61, 'New reservation submitted with Booking No. EV RES-20250224-0003.', 0, '2025-02-24 08:36:28', 'EV RES-20250224-0003', 'reservations'),
(62, 'New reservation submitted with Booking No. GM RES-20250224-164.', 0, '2025-02-24 09:18:42', 'GM RES-20250224-164', 'room_reservation'),
(63, 'New reservation submitted with Booking No. GM RES-20250224-165.', 0, '2025-02-24 09:31:30', 'GM RES-20250224-165', 'room_reservation'),
(64, 'New reservation submitted with Booking No. GM RES-20250224-166.', 0, '2025-02-24 09:32:52', 'GM RES-20250224-166', 'room_reservation'),
(65, 'New reservation submitted with Booking No. GM RES-20250224-167.', 0, '2025-02-24 09:34:11', 'GM RES-20250224-167', 'room_reservation'),
(66, 'New reservation submitted with Booking No. GM RES-20250224-168.', 0, '2025-02-24 09:35:56', 'GM RES-20250224-168', 'room_reservation'),
(67, 'New reservation submitted with Booking No. GM RES-20250224-169.', 0, '2025-02-24 09:36:37', 'GM RES-20250224-169', 'room_reservation'),
(68, 'New reservation submitted with Booking No. GM RES-20250224-170.', 0, '2025-02-24 09:37:38', 'GM RES-20250224-170', 'room_reservation'),
(69, 'New reservation submitted with Booking No. EV RES-20250224-0004.', 0, '2025-02-24 09:38:49', 'EV RES-20250224-0004', 'reservations'),
(70, 'New reservation submitted with Booking No. EV RES-20250224-0005.', 0, '2025-02-24 09:39:55', 'EV RES-20250224-0005', 'reservations'),
(71, 'New reservation submitted with Booking No. EV RES-20250224-0006.', 0, '2025-02-24 09:40:58', 'EV RES-20250224-0006', 'reservations'),
(72, 'New reservation submitted with Booking No. EV RES-20250224-0007.', 0, '2025-02-24 10:17:16', 'EV RES-20250224-0007', 'reservations'),
(73, 'New reservation submitted with Booking No. EV RES-20250224-0008.', 1, '2025-02-24 10:18:03', 'EV RES-20250224-0008', 'reservations'),
(74, 'New reservation submitted with Booking No. EV RES-20250224-0009.', 1, '2025-02-24 10:18:47', 'EV RES-20250224-0009', 'reservations'),
(75, 'New reservation submitted with Booking No. EV RES-20250225-0010.', 0, '2025-02-25 00:10:16', 'EV RES-20250225-0010', 'reservations'),
(76, 'New reservation submitted with Booking No. EV RES-20250225-0010.', 0, '2025-02-25 00:10:17', 'EV RES-20250225-0010', 'reservations'),
(77, 'New reservation submitted with Booking No. EV RES-20250225-0011.', 0, '2025-02-25 01:13:15', 'EV RES-20250225-0011', 'reservations'),
(78, 'New reservation submitted with Booking No. EV RES-20250227-0012.', 0, '2025-02-27 03:50:03', 'EV RES-20250227-0012', 'reservations'),
(79, 'New reservation submitted with Booking No. EV RES-20250227-0013.', 0, '2025-02-27 03:51:24', 'EV RES-20250227-0013', 'reservations'),
(80, 'New reservation submitted with Booking No. GM RES-20250227-171.', 1, '2025-02-27 03:55:58', 'GM RES-20250227-171', 'room_reservation'),
(81, 'New reservation submitted with Booking No. GM RES-20250227-172.', 1, '2025-02-27 04:22:15', 'GM RES-20250227-172', 'room_reservation'),
(82, 'New reservation submitted with Booking No. EV RES-20250227-0014.', 0, '2025-02-27 05:08:52', 'EV RES-20250227-0014', 'reservations'),
(83, 'New reservation submitted with Booking No. GM RES-20250227-173.', 0, '2025-02-27 05:10:08', 'GM RES-20250227-173', 'room_reservation'),
(84, 'New reservation submitted with Booking No. EV RES-20250227-0015.', 0, '2025-02-27 05:29:14', 'EV RES-20250227-0015', 'reservations'),
(85, 'New reservation submitted with Booking No. GM RES-20250227-174.', 0, '2025-02-27 05:32:09', 'GM RES-20250227-174', 'room_reservation'),
(86, 'New reservation submitted with Booking No. GM RES-20250227-175.', 0, '2025-02-27 05:32:59', 'GM RES-20250227-175', 'room_reservation'),
(87, 'New reservation submitted with Booking No. EV RES-20250227-0016.', 0, '2025-02-27 05:36:06', 'EV RES-20250227-0016', 'reservations'),
(88, 'New reservation submitted with Booking No. EV RES-20250227-0017.', 0, '2025-02-27 06:07:04', 'EV RES-20250227-0017', 'reservations'),
(89, 'New reservation submitted with Booking No. EV RES-20250227-0018.', 0, '2025-02-27 06:26:40', 'EV RES-20250227-0018', 'reservations'),
(90, 'New reservation submitted with Booking No. EV RES-20250227-0019.', 1, '2025-02-27 08:08:57', 'EV RES-20250227-0019', 'reservations'),
(91, 'New reservation submitted with Booking No. GM RES-20250227-176.', 1, '2025-02-27 08:09:36', 'GM RES-20250227-176', 'room_reservation'),
(92, 'New reservation submitted with Booking No. GM RES-20250303-177.', 0, '2025-03-03 06:54:26', 'GM RES-20250303-177', 'room_reservation'),
(93, 'New reservation submitted with Booking No. EV RES-20250303-0020.', 0, '2025-03-03 06:58:33', 'EV RES-20250303-0020', 'reservations'),
(94, 'New reservation submitted with Booking No. EV RES-20250303-0021.', 0, '2025-03-03 07:00:42', 'EV RES-20250303-0021', 'reservations'),
(95, 'New reservation submitted with Booking No. EV RES-20250303-0022.', 0, '2025-03-03 07:19:45', 'EV RES-20250303-0022', 'reservations'),
(96, 'New reservation submitted with Booking No. EV RES-20250304-0023.', 0, '2025-03-04 03:34:07', 'EV RES-20250304-0023', 'reservations'),
(97, 'New reservation submitted with Booking No. GM RES-20250309-178.', 0, '2025-03-09 10:12:59', 'GM RES-20250309-178', 'room_reservation'),
(98, 'New reservation submitted with Booking No. GM RES-20250309-179.', 0, '2025-03-09 10:19:40', 'GM RES-20250309-179', 'room_reservation'),
(99, 'New reservation submitted with Booking No. EV RES-20250309-0024.', 0, '2025-03-09 11:58:21', 'EV RES-20250309-0024', 'reservations'),
(100, 'New reservation submitted with Booking No. EV RES-20250309-0024.', 0, '2025-03-09 11:58:48', 'EV RES-20250309-0024', 'reservations'),
(101, 'New reservation submitted with Booking No. GM RES-20250309-180.', 0, '2025-03-09 12:20:11', 'GM RES-20250309-180', 'room_reservation'),
(102, 'New reservation submitted with Booking No. EV RES-20250314-0025.', 1, '2025-03-14 08:12:06', 'EV RES-20250314-0025', 'reservations'),
(103, 'New reservation submitted with Booking No. GM RES-20250314-181.', 1, '2025-03-14 08:13:51', 'GM RES-20250314-181', 'room_reservation');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','admin','viewer') DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `role`, `last_login`, `created_at`) VALUES
(7, 'admin', '$2y$10$bBebV2Cv8MqUlKXuspWljOhAwaHlggSg3EmlKBVJNbMAsQ/WhWx3G', 'admin@bsu.edu.ph', 'super_admin', '2026-03-19 07:32:39', '2026-02-25 03:44:02');

-- --------------------------------------------------------

--
-- Table structure for table `banquet`
--

CREATE TABLE `banquet` (
  `id` int(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banquet`
--

INSERT INTO `banquet` (`id`, `image`, `name`, `description`, `is_active`) VALUES
(3, 'IMG_70840.png', 'THEATER TYPE', 'In this setup, rows of rectangular meeting or banquet tables, covered with skirting and paired with banquet chairs, are arranged facing a stage or screen.\r\n\r\nThis layout suits events where guests remain seated and watch the program for a long time.\r\n', 1),
(9, 'IMG_69154.png', 'BOARDROOM TYPE', 'It is called a boardroom setup because it resembles the typical layout used in corporate boardrooms and conference rooms.\r\n\r\nIf space allows, setting up a boardroom arrangement in a smaller banquet hall works well as a dedicated meeting area.\r\n', 1),
(10, 'IMG_99683.png', 'U-SHAPE TYPE', 'A U-shaped floor plan arranges tables and chairs in the shape of a “U,” facing the front where the speaker leads the discussion.\r\n\r\nThis layout works best for small groups and fits well in rectangular rooms.\r\n', 1),
(11, 'IMG_90306.png', 'WEDDING STYLE', 'A banquet is a formal dining event organized for a large number of guests.\r\n\r\nIt is usually held for special occasions such as wedding receptions, award ceremonies, or major conferences.\r\n\r\nThere are different banquet styles, including buffet, reception, and cafeteria style setups.\r\n', 1),
(12, 'IMG_93068.png', 'HERRING BONE TYPE', 'This layout is similar to the classroom setup.\r\n\r\nIn a herringbone arrangement, each row of tables and chairs is slightly angled inward toward the front.\r\n\r\nAdvantages include better visibility of the podium since all seats are directed inward.\r\n\r\nParticipants also face forward, making it easier to focus on the speaker.\r\n', 1),
(13, 'IMG_87503.png', 'HOLLOW SQUARE TYPE', 'The hollow square layout is similar to the u-shape floor plan but simply closes off the fourth side to form a closed square or rectangle. It also has an open space in the middle of the table.', 1),
(14, 'IMG_23903.png', 'CLASSROOM TYPE', 'The classroom setup is a simple and easy setup with rows of rectangular tables and chairs on either side. This setup works for a banquet event with a speaker. That said, it can also be used for a banquet that is devoted to the full course meal and socializing with the other guests.', 1),
(15, 'IMG_64307.png', 'T-SHAPE TYPE', 'This type of seating is followed in conferences, where the top table is laid down and there is one spring attached with the top table.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `booking_sequences`
--

CREATE TABLE `booking_sequences` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'guest, function',
  `year` int(4) NOT NULL,
  `month` int(2) NOT NULL,
  `last_number` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_sequences`
--

INSERT INTO `booking_sequences` (`id`, `type`, `year`, `month`, `last_number`, `created_at`, `updated_at`) VALUES
(1, 'guest', 2026, 3, 3, '2026-03-06 07:17:27', '2026-03-16 07:53:50'),
(2, 'function', 2026, 3, 0, '2026-03-06 07:17:27', '2026-03-06 07:17:27');

-- --------------------------------------------------------

--
-- Table structure for table `carousel_slides`
--

CREATE TABLE `carousel_slides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `button_text` varchar(100) DEFAULT 'View Rooms',
  `button_url` varchar(255) DEFAULT 'rooms.php',
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousel_slides`
--

INSERT INTO `carousel_slides` (`id`, `title`, `subtitle`, `button_text`, `button_url`, `image_path`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Welcome to BSU Hostel', 'The perfect venue for your events. Spacious function rooms and comfortable guest rooms for meetings, celebrations, and group stays. Reserve your space today.', 'View Rooms', 'rooms_showcase.php', 'hostel/hostel2.png', 1, 1, '2026-02-19 05:14:52'),
(2, 'Book Your Function or Guest Room', 'Check availability and reserve your stay in minutes.', 'Check Availability', 'calendar.php', 'rooms/IMG_19689.jpg', 2, 1, '2026-02-19 05:14:52'),
(4, 'Stay With Us', 'Ideal for students, groups, and travelers visiting BSU.', 'Get in Touch', 'contact.php', 'hostel/hostel2.png', 4, 1, '2026-02-19 05:14:52');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender`, `message`, `image_url`, `timestamp`, `user_id`, `is_read`) VALUES
(76, 'user', '', 'images/chat/add.JPG', '2025-01-25 01:34:54', 29, 1),
(77, 'admin', 'okay', NULL, '2025-01-25 01:35:11', 29, 1),
(78, 'user', 'hi', NULL, '2025-01-25 17:20:19', 29, 1),
(79, 'admin', 'hello', NULL, '2025-01-25 17:20:28', 29, 1),
(80, 'user', 'hi', NULL, '2025-01-26 12:12:34', 30, 1),
(81, 'user', '', 'images/chat/add.JPG', '2025-01-26 12:13:24', 30, 1),
(82, 'user', 'hi', NULL, '2025-01-26 22:48:44', 29, 1),
(83, 'user', 'Hi', NULL, '2025-01-27 01:44:39', 29, 1),
(84, 'user', 'Hello po', NULL, '2025-01-27 08:05:45', 29, 1),
(85, 'user', 'Hi', NULL, '2025-01-28 04:59:45', 34, 1),
(86, 'admin', 'ollrH', NULL, '2025-01-28 05:04:07', 34, 1),
(87, 'user', 'hi', NULL, '2025-01-30 22:48:26', 29, 1),
(88, 'admin', 'hello', NULL, '2025-01-30 22:48:36', 29, 1),
(89, 'user', '', 'images/chat/IMG_2235.jpeg', '2025-02-24 07:43:32', 34, 1),
(90, 'user', '', 'images/chat/hostel.JPG', '2025-02-24 11:43:59', 30, 1),
(91, 'user', 'HIgh', NULL, '2025-02-28 05:23:53', 44, 1),
(92, 'user', 'Hi! I booked EV RES-20250315-0027 for March 30, 8:00 AM - 10:00 AM. Can it be approved ASAP? It\'s a rush event. Thanks!', NULL, '2025-03-15 10:27:54', 29, 1),
(93, 'admin', 'Hi! I\'ll check your request now. Please wait a moment.', NULL, '2025-03-15 10:28:19', 29, 1),
(94, 'admin', 'It\'s under review. If all is good, we’ll approve it shortly.', NULL, '2025-03-15 10:28:28', 29, 1),
(95, 'user', 'Thanks!', NULL, '2025-03-15 10:28:40', 29, 1),
(96, 'admin', 'No problem! I’ll update you soon. 😊', NULL, '2025-03-15 10:28:56', 29, 0),
(97, 'user', 'okay', NULL, '2025-03-15 10:29:05', 29, 1),
(98, 'user', 'hi can someone assist me?', NULL, '2025-03-15 10:29:45', 43, 1),
(99, 'user', 'hi can someone assist me?', NULL, '2025-03-15 10:30:30', 34, 1),
(100, 'user', 'Good day Admin', NULL, '2025-03-16 09:22:07', 44, 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

CREATE TABLE `contact_details` (
  `sr_no` int(11) NOT NULL,
  `address` varchar(50) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` bigint(20) NOT NULL,
  `pn2` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `iframe` varchar(300) NOT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `phone2` varchar(50) DEFAULT NULL,
  `email2` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_details`
--

INSERT INTO `contact_details` (`sr_no`, `address`, `gmap`, `pn1`, `pn2`, `email`, `fb`, `iframe`, `facebook_url`, `phone2`, `email2`) VALUES
(1, '3J8G MFC, Nasugbu, Batangas', 'https://maps.app.goo.gl/kdGkPGPSZvNGX2ur9', 908754767, '4355566', 'hostel.nasugbu@g.batstate-u.edu.ph', 'https://web.facebook.com', 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d247691.37575470193!2d120.626131!3d14.066679!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd96a403666c7d:0x397173d7eb8f7cf9!2sBatangas State University, Nasugbu Campus!5e0!3m2!1sen!2sph!4v1725632299713!5m2!1sen!2sph', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_types`
--

CREATE TABLE `event_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'bi-calendar-event',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_types`
--

INSERT INTO `event_types` (`id`, `name`, `description`, `icon`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Meeting and Conference', 'Business meetings, conferences, board meetings', 'bi-people', 1, 1, '2026-02-23 02:29:18'),
(2, 'Seminar and Lecture', 'Educational seminars, lectures, workshops', 'bi-mic', 2, 1, '2026-02-23 02:29:18'),
(3, 'Buffet and Celebrations', 'Birthday parties, anniversaries, buffet events', 'bi-cake', 3, 1, '2026-02-23 02:29:18'),
(4, 'Orientation and Presentation', 'Student orientations, product presentations', 'bi-easel', 4, 1, '2026-02-23 02:29:18'),
(5, 'Programs and Special Events', 'Special programs, cultural events, ceremonies', 'bi-star', 5, 1, '2026-02-23 02:29:18');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(550) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `icon`, `name`, `description`) VALUES
(12, 'IMG_73410.svg', 'TV', 'Enjoy your favorite shows, movies, and entertainment in the comfort of your room or the shared lounge area. Our televisions are equipped with cable channels and clear displays, ensuring a relaxing and enjoyable viewing experience for all our guests. Perfect for unwinding after a busy day or catching up on the latest news and events.'),
(13, 'IMG_43730.svg', 'WIFI', 'Stay connected during your stay with our reliable, high-speed WiFi. Whether you\'re catching up on work, streaming your favorite shows, or staying in touch with loved ones, our internet connection ensures a seamless online experience. Available in all rooms and common areas for your convenience, free of charge!'),
(20, 'IMG_27868.svg', 'AIRCON', 'Experience ultimate comfort with our air-conditioned rooms, designed to keep you cool and relaxed throughout your stay. Whether you\'re escaping the summer heat or just want a refreshing atmosphere, our air conditioning ensures a pleasant and cozy environment for every guest.'),
(25, 'hygiene-products.png', 'Amenity Kit', 'Complimentary toiletries provided for your convenience and comfort throughout your stay.');

-- --------------------------------------------------------

--
-- Table structure for table `facility_reservations`
--

CREATE TABLE `facility_reservations` (
  `id` int(11) NOT NULL,
  `booking_no` varchar(50) NOT NULL,
  `reservation_no` varchar(50) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_initial` varchar(10) DEFAULT NULL,
  `office_type_id` int(11) NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `external_office_name` varchar(255) DEFAULT NULL,
  `activity_name` varchar(255) NOT NULL,
  `event_type_id` int(11) DEFAULT NULL,
  `venue_id` int(11) NOT NULL,
  `venue_setup_id` int(11) NOT NULL,
  `banquet_style_id` int(11) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `participants_count` int(11) NOT NULL CHECK (`participants_count` <= 200),
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `miscellaneous_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`miscellaneous_items`)),
  `additional_instruction` text DEFAULT NULL,
  `status` enum('pending','pencil_booked','approved','denied','cancelled','completed') NOT NULL DEFAULT 'pending',
  `admin_remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_reservations`
--

INSERT INTO `facility_reservations` (`id`, `booking_no`, `reservation_no`, `last_name`, `first_name`, `middle_initial`, `office_type_id`, `office_id`, `external_office_name`, `activity_name`, `event_type_id`, `venue_id`, `venue_setup_id`, `banquet_style_id`, `start_datetime`, `end_datetime`, `participants_count`, `email`, `contact_number`, `miscellaneous_items`, `additional_instruction`, `status`, `admin_remarks`, `created_at`, `updated_at`) VALUES
(15, 'FAC-20260226-638', 'RES-20260226-329', 'Jackson', 'Michael', 'J', 2, 32, '', 'OVCDEA Party', 3, 4, 1, 3, '2026-03-02 07:00:00', '2026-03-02 17:00:00', 80, 'theavengergeo6@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":1},\"view_board\":{\"quantity\":2}}', '', 'approved', NULL, '2026-02-26 08:28:30', '2026-02-27 06:26:11'),
(16, 'FAC-20260227-123', 'RES-20260227-850', 'Curry', 'Stephen', 'C', 2, 20, '', 'Head Birthday', 3, 2, 1, 14, '2026-03-03 10:00:00', '2026-03-03 17:00:00', 40, 'geomarc789@gmail.com', '09087547440', '{\"banquet_chairs\":{\"quantity\":40},\"rectangular_table\":{\"quantity\":7}}', '', '', '\n--- 2026-03-02 13:55:21 (Pencil booked) ---\nSigned by Sir Ems', '2026-02-27 01:46:14', '2026-03-02 05:55:21'),
(17, 'FAC-20260227-639', 'RES-20260227-357', 'Santos', 'Vilma', 'R', 4, NULL, 'VSR', 'Christmas Party', 3, 1, 1, 11, '2026-03-04 07:00:00', '2026-03-04 17:00:00', 200, 'vilmasantos@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"round_table\":{\"quantity\":10},\"banquet_chairs\":{\"quantity\":200}}', '', 'approved', NULL, '2026-02-27 02:08:02', '2026-02-27 02:13:06'),
(18, 'FAC-20260227-605', 'RES-20260227-903', 'Johnson', 'Magic', 'E', 1, 5, '', 'NBA Championship', 3, 1, 1, 14, '2026-02-27 07:00:00', '2026-02-27 17:00:00', 60, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"round_table\":{\"quantity\":7},\"banquet_chairs\":{\"quantity\":50}}', '', 'denied', NULL, '2026-02-27 07:08:47', '2026-03-01 23:42:26'),
(19, 'FAC-20260227-756', 'RES-20260227-360', 'Hackerman', 'Gene', 'A', 2, 25, '', 'Libreng Pakain', 3, 4, 1, 9, '2026-03-01 07:00:00', '2026-03-01 12:00:00', 80, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":1,\"mic\":1},\"view_board\":{\"quantity\":1},\"rectangular_table\":{\"quantity\":5}}', '', 'approved', '\n--- 2026-02-27 15:19:59 (Approved) ---\nApproved', '2026-02-27 07:11:19', '2026-02-27 07:19:59'),
(20, 'FAC-20260227-952', 'RES-20260227-224', 'dela vega', 'irish', 'r.', 1, 1, '', 'collaborative meeting', 1, 1, 1, NULL, '2026-03-02 07:00:00', '2026-03-02 13:00:00', 20, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":1,\"mic\":1}}', '', 'denied', NULL, '2026-02-27 08:06:13', '2026-03-01 23:42:30'),
(21, 'FAC-20260302-760', 'RES-20260302-793', 'Graham', 'Aubery', 'D', 2, 22, '', 'CHS Buffet', 3, 1, 1, 9, '2026-03-02 07:00:00', '2026-03-02 17:00:00', 70, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"round_table\":{\"quantity\":0}}', '', '', '\n--- 2026-03-02 13:39:35 (Pencil booked) ---\nPencil Booked', '2026-03-02 05:09:33', '2026-03-02 05:39:35'),
(22, 'FAC-20260302-808', 'RES-20260302-597', 'Pantheress', 'Pink', 'E', 4, NULL, 'BFP', 'BFP Fire Prevention Month', 1, 1, 1, 3, '2026-03-02 07:00:00', '2026-03-02 17:00:00', 120, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"view_board\":{\"quantity\":2},\"rectangular_table\":{\"quantity\":2}}', '', 'approved', '\n--- 2026-03-02 16:40:13 (Approved) ---\nSubmitted All the REquirements', '2026-03-02 06:19:17', '2026-03-02 08:40:13'),
(23, 'FAC-20260302-645', 'RES-20260302-235', 'Drake', 'Drizzy', 'D', 3, 69, '', 'Kunwari Lang', 2, 1, 1, 14, '2026-03-02 21:00:00', '2026-03-02 23:00:00', 60, 'michaelblackson0975@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"round_table\":{\"quantity\":16},\"banquet_chairs\":{\"quantity\":190},\"view_board\":{\"requested\":true},\"rectangular_table\":{\"quantity\":10}}', '', 'cancelled', '\n--- 2026-03-02 16:04:42 (Pencil booked) ---\nSubmitted Reservation form with Signatures\n--- 2026-03-03 08:20:19 (Cancelled) ---\nCancelled, di nakapag pasa ng forms', '2026-03-02 08:03:45', '2026-03-03 00:20:19'),
(24, 'FAC-20260303-316', 'RES-20260303-887', 'Curry', 'Marco', 'C', 1, 2, '', 'CTE Assembly', 2, 1, 1, 9, '2026-03-05 07:00:00', '2026-03-05 12:00:00', 60, 'gomari13@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"banquet_chairs\":{\"quantity\":2},\"view_board\":{\"requested\":true}}', '', 'approved', '\n--- 2026-03-03 14:23:58 (Pencil booked) ---\nSubmitted a paper signed by me\n--- 2026-03-05 08:20:49 (Approved) ---\nSubmitted signed papers', '2026-03-03 06:22:31', '2026-03-05 00:20:49'),
(25, 'FAC-20260305-925', 'RES-20260305-161', 'Jordan', 'Michael', 'K', 1, 5, '', 'Technoprenuership Pitch', 5, 1, 1, 3, '2026-03-06 07:00:00', '2026-03-06 17:00:00', 150, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"banquet_chairs\":{\"quantity\":150},\"view_board\":{\"requested\":true},\"rectangular_table\":{\"quantity\":1}}', '', 'approved', '\n--- 2026-03-05 08:26:21 (Pencil booked) ---\nSigned by me\n--- 2026-03-05 08:30:57 (Approved) ---\nSubmitted signed paper by Sir Marvin', '2026-03-05 00:23:48', '2026-03-05 00:30:57'),
(26, 'FAC-20260309-588', 'RES-20260309-123', 'Tesfaye', 'Abel', 'C', 4, NULL, 'Los Angeles Lakers', 'Lakers Film Study', 2, 4, 1, 14, '2026-03-11 07:00:00', '2026-03-11 13:00:00', 40, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":1,\"mic\":1},\"banquet_chairs\":{\"quantity\":40},\"view_board\":{\"requested\":true},\"rectangular_table\":{\"quantity\":5}}', '', 'approved', '\n--- 2026-03-09 10:28:52 (Pencil booked) ---\nSubmitted signed by me document\n--- 2026-03-09 10:29:51 (Approved) ---\nSubmitted signed papers by Dean Marvin', '2026-03-09 02:25:17', '2026-03-09 02:29:51'),
(27, 'FAC-20260309-186', 'RES-20260309-960', 'Azarcon', 'Jeremie', 'R', 1, 6, '', 'Quiz Bee', 5, 4, 1, 12, '2026-03-11 14:00:00', '2026-03-11 17:00:00', 40, 'jeremieazarcon@gmail.com', '09626970801', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":0},\"banquet_chairs\":{\"quantity\":40},\"view_board\":{\"requested\":true},\"rectangular_table\":{\"quantity\":4}}', 'Parteady ng lapis and ballpen', 'approved', '\n--- 2026-03-09 18:34:19 (Approved) ---\nSubhmitted all the requirements', '2026-03-09 10:33:27', '2026-03-09 10:34:19'),
(28, 'FAC-20260310-977', 'RES-20260310-673', 'Dela Vega', 'Emerish Jem', 'R', 1, 2, '', 'CAS Meeting', 1, 3, 1, 14, '2026-03-16 07:00:00', '2026-03-16 12:00:00', 50, 'emsdelavega@gmail.com', '09626970801', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":0},\"banquet_chairs\":{\"quantity\":50}}', '', 'approved', '\n--- 2026-03-10 17:06:54 (Approved) ---\nsubmitted signed requirements', '2026-03-10 02:51:31', '2026-03-10 09:06:54'),
(29, 'FAC-20260318-081', 'RES-20260318-490', 'De Guzman', 'Geo Mar', 'C', 1, 7, '', 'Grade 9 Quiz Bee', 5, 1, 1, 14, '2026-03-20 07:00:00', '2026-03-20 12:00:00', 60, 'geomarc789@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":1,\"mic\":1},\"banquet_chairs\":{\"quantity\":60},\"view_board\":{\"requested\":true},\"_terms_agreed_by\":\"De Guzman, Geo Mar C.\",\"_terms_position\":\"Student\",\"_terms_date\":\"March 18, 2026\"}', '', 'approved', '\n--- 2026-03-18 09:20:14 (Pencil booked) ---\nsigned by me\n--- 2026-03-18 09:20:51 (Approved) ---\nall signatories are signed', '2026-03-18 00:45:20', '2026-03-18 01:20:51'),
(30, 'FAC-20260318-600', 'RES-20260318-245', 'Weeknd', 'The', '', 4, NULL, 'Golden State Warriors', 'Warriors Championship Ceremony', 5, 1, 1, 3, '2026-03-20 13:00:00', '2026-03-20 22:00:00', 80, 'theavengergeo6@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"banquet_chairs\":{\"quantity\":80},\"rectangular_table\":{\"quantity\":1},\"_price_breakdown\":[{\"venue_id\":1,\"date\":\"2026-03-20\",\"start\":\"13:00\",\"end\":\"22:00\",\"hours\":9,\"rate_type\":\"Whole Day + 1h Extension\",\"cost\":3400},{\"venue_id\":2,\"date\":\"2026-03-20\",\"start\":\"13:00\",\"end\":\"22:00\",\"hours\":9,\"rate_type\":\"Whole Day + 1h Extension\",\"cost\":3400},{\"rate_type\":\"Sound System\",\"cost\":1500}],\"_estimated_total\":8300,\"_client_type\":\"External\",\"_terms_agreed_by\":\"The Weeknd\",\"_terms_position\":\"Event Coordinator\",\"_terms_date\":\"March 18, 2026\"}', '', 'approved', '\n--- 2026-03-19 07:57:37 (Approved) ---\nsubmitted signed papers', '2026-03-18 01:22:42', '2026-03-18 23:57:37'),
(31, 'FAC-20260318-089', 'RES-20260318-213', 'De Guzman', 'Gomari', 'C', 3, 135, '', '4th year OJT Orientation', 4, 1, 1, 3, '2026-03-19 13:00:00', '2026-03-19 18:00:00', 80, 'theavengergeo6@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"round_table\":{\"quantity\":2},\"banquet_chairs\":{\"quantity\":80},\"view_board\":{\"requested\":true},\"rectangular_table\":{\"quantity\":2},\"mono_block_chairs\":{\"quantity\":2},\"_terms_agreed_by\":\"De Guzman, Gomari C.\",\"_terms_position\":\"Event Coordinator\",\"_terms_date\":\"March 18, 2026\"}', 'oiqpdipwoqdipqidpoqidpoqidpoqidpoqidopqidopqidpoqiwdopqidpoqidpidpqoipowiqpoidopqipodiwpoidqpidqpodiqpowdipoqwipqoidpqowidpowq', 'approved', '\n--- 2026-03-18 15:58:21 (Approved) ---\nsubmitted signed', '2026-03-18 05:25:18', '2026-03-18 07:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`, `sort_order`, `created_at`) VALUES
(1, 'Who may request to use the Hostel Function Rooms?', 'The Hostel may be requested for use by BatStateU units, extension project teams, partner agencies, LGUs, NGOs, and other approved organizations, subject to availability and compliance with university policies.', 1, '2026-02-23 00:34:37'),
(2, 'Are Extension Services activities prioritized in the schedule?', 'Yes. Extension Services activities and university-sanctioned programs are given priority in scheduling, subject to advance booking and approval.', 2, '2026-02-23 00:34:37'),
(3, 'How can we submit a request or make inquiries?', 'You may submit a request or inquiry through the Reservation page, by contacting the office directly, or via the contact details provided on this website.', 3, '2026-02-23 00:34:37'),
(4, 'What general rules must users observe during facility use?', 'Users must comply with university policies, maintain cleanliness, and use the facility only for the approved purpose and within the reserved time slot.', 4, '2026-02-23 00:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `function_calendar_config`
--

CREATE TABLE `function_calendar_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `function_calendar_config`
--

INSERT INTO `function_calendar_config` (`id`, `config_key`, `config_value`, `description`, `updated_at`) VALUES
(1, 'min_advance_days', '1', 'Minimum days in advance for booking', '2026-03-06 07:07:06'),
(2, 'max_advance_days', '180', 'Maximum days in advance for booking', '2026-03-06 07:07:06'),
(3, 'min_duration_hours', '1', 'Minimum event duration in hours', '2026-03-06 07:07:06'),
(4, 'max_duration_hours', '12', 'Maximum event duration in hours', '2026-03-06 07:07:06'),
(5, 'operating_hours_start', '07:00:00', 'Start of operating hours', '2026-03-06 07:07:06'),
(6, 'operating_hours_end', '23:00:00', 'End of operating hours', '2026-03-06 07:07:06'),
(7, 'buffer_before_minutes', '30', 'Buffer time before events in minutes', '2026-03-06 07:07:06'),
(8, 'buffer_after_minutes', '60', 'Buffer time after events in minutes', '2026-03-06 07:07:06'),
(9, 'weekly_holidays', '[]', 'Days of week when bookings are not allowed', '2026-03-06 07:07:06'),
(10, 'special_dates', '[]', 'Special dates with custom rules', '2026-03-06 07:07:06');

-- --------------------------------------------------------

--
-- Table structure for table `function_rooms`
--

CREATE TABLE `function_rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `floor` varchar(20) DEFAULT NULL,
  `capacity_min` int(11) DEFAULT 0,
  `capacity_max` int(11) NOT NULL,
  `rate_per_hour` decimal(10,2) DEFAULT NULL,
  `rate_per_day` decimal(10,2) NOT NULL,
  `has_sound_system` tinyint(1) NOT NULL DEFAULT 0,
  `has_projector` tinyint(1) NOT NULL DEFAULT 0,
  `has_wifi` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `function_rooms`
--

INSERT INTO `function_rooms` (`id`, `room_name`, `floor`, `capacity_min`, `capacity_max`, `rate_per_hour`, `rate_per_day`, `has_sound_system`, `has_projector`, `has_wifi`, `description`, `amenities`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Function Room A', 'Ground Floor', 20, 40, 500.00, 4000.00, 1, 1, 1, 'Spacious function room perfect for meetings, seminars, and small events.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-06 07:07:05'),
(2, 'Function Room B', 'Ground Floor', 25, 50, 600.00, 4500.00, 1, 1, 1, 'Ideal for workshops, training sessions, and medium-sized gatherings.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-06 07:07:05'),
(3, 'Function Room C', 'Ground Floor', 30, 60, 700.00, 5000.00, 1, 1, 1, 'Largest function room with complete AV equipment, suitable for conferences and events.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-06 07:07:05'),
(4, 'Function Room D', 'Ground Floor', 15, 30, 400.00, 3000.00, 1, 0, 1, 'Intimate space for small meetings, interviews, and private discussions.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-06 07:07:05'),
(5, 'Function Room E', 'Ground Floor', 20, 45, 550.00, 4200.00, 1, 1, 1, 'Versatile space for training, seminars, and corporate events.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-06 07:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `function_room_availability`
--

CREATE TABLE `function_room_availability` (
  `id` int(11) NOT NULL,
  `function_room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_slot` varchar(20) NOT NULL COMMENT 'e.g., 08:00-10:00',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `reservation_id` int(11) DEFAULT NULL,
  `status` enum('available','booked','blocked','maintenance') NOT NULL DEFAULT 'available',
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `function_room_blocked_dates`
--

CREATE TABLE `function_room_blocked_dates` (
  `id` int(11) NOT NULL,
  `function_room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `is_full_day` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `function_room_images`
--

CREATE TABLE `function_room_images` (
  `id` int(11) NOT NULL,
  `function_room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `function_room_reservations`
--

CREATE TABLE `function_room_reservations` (
  `id` int(11) NOT NULL,
  `booking_no` varchar(50) NOT NULL,
  `reservation_no` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_initial` varchar(10) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `office_type_id` int(11) NOT NULL,
  `office_id` int(11) DEFAULT NULL,
  `external_office_name` varchar(255) DEFAULT NULL,
  `activity_name` varchar(255) NOT NULL,
  `event_type_id` int(11) DEFAULT NULL,
  `participants_count` int(11) NOT NULL,
  `banquet_style_id` int(11) DEFAULT NULL,
  `function_room_id` int(11) NOT NULL,
  `venue_setup_id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `setup_time` time DEFAULT NULL,
  `cleanup_time` time DEFAULT NULL,
  `total_hours` decimal(5,2) GENERATED ALWAYS AS (timestampdiff(HOUR,concat(`event_date`,' ',`start_time`),concat(`event_date`,' ',`end_time`))) STORED,
  `rate_per_hour` decimal(10,2) NOT NULL,
  `total_rental_cost` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `miscellaneous_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`miscellaneous_items`)),
  `additional_instruction` text DEFAULT NULL,
  `status` enum('pending','pencil_booked','approved','denied','cancelled','completed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `admin_remarks` text DEFAULT NULL,
  `terms_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `terms_accepted_by` varchar(255) DEFAULT NULL,
  `terms_accepted_at` datetime DEFAULT NULL,
  `terms_position` varchar(100) DEFAULT NULL,
  `digital_signature` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `function_room_reservations`
--
DELIMITER $$
CREATE TRIGGER `trg_function_reservation_after_insert` AFTER INSERT ON `function_room_reservations` FOR EACH ROW BEGIN
  INSERT INTO function_room_availability 
    (function_room_id, date, time_slot, start_time, end_time, is_available, reservation_id, status)
  VALUES 
    (NEW.function_room_id, NEW.event_date, 
     CONCAT(NEW.start_time, '-', NEW.end_time),
     NEW.start_time, NEW.end_time, 0, NEW.id, 'booked');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_function_reservation_after_update` AFTER UPDATE ON `function_room_reservations` FOR EACH ROW BEGIN
  IF NEW.status = 'cancelled' AND OLD.status != 'cancelled' THEN
    DELETE FROM function_room_availability 
    WHERE reservation_id = OLD.id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `guest_calendar_config`
--

CREATE TABLE `guest_calendar_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_calendar_config`
--

INSERT INTO `guest_calendar_config` (`id`, `config_key`, `config_value`, `description`, `updated_at`) VALUES
(1, 'min_advance_days', '1', 'Minimum days in advance for booking', '2026-03-06 07:07:06'),
(2, 'max_advance_days', '90', 'Maximum days in advance for booking', '2026-03-06 07:07:06'),
(3, 'min_stay_nights', '1', 'Minimum number of nights for stay', '2026-03-06 07:07:06'),
(4, 'max_stay_nights', '30', 'Maximum number of nights for stay', '2026-03-06 07:07:06'),
(5, 'check_in_time', '14:00:00', 'Default check-in time', '2026-03-06 07:07:06'),
(6, 'check_out_time', '12:00:00', 'Default check-out time', '2026-03-06 07:07:06'),
(7, 'buffer_days', '0', 'Buffer days between bookings', '2026-03-06 07:07:06'),
(8, 'weekly_holidays', '[]', 'Days of week when bookings are not allowed', '2026-03-06 07:07:06'),
(9, 'special_dates', '[]', 'Special dates with custom rules', '2026-03-06 07:07:06');

-- --------------------------------------------------------

--
-- Table structure for table `guest_details`
--

CREATE TABLE `guest_details` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest_rooms`
--

CREATE TABLE `guest_rooms` (
  `id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `room_type` enum('standard','deluxe','family','dormitory') NOT NULL DEFAULT 'standard',
  `capacity_adults` int(11) NOT NULL DEFAULT 2,
  `capacity_children` int(11) NOT NULL DEFAULT 0,
  `max_guests` int(11) NOT NULL DEFAULT 2,
  `bed_configuration` varchar(100) DEFAULT NULL,
  `floor` varchar(20) DEFAULT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `extra_bed_available` tinyint(1) NOT NULL DEFAULT 0,
  `extra_bed_price` decimal(10,2) DEFAULT 500.00,
  `description` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_rooms`
--

INSERT INTO `guest_rooms` (`id`, `room_number`, `room_name`, `room_type`, `capacity_adults`, `capacity_children`, `max_guests`, `bed_configuration`, `floor`, `price_per_night`, `extra_bed_available`, `extra_bed_price`, `description`, `amenities`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '101', 'Guest Room 1', 'standard', 2, 1, 4, '1 Queen Bed', '2nd Floor', 5000.00, 1, 500.00, 'Comfortable guest room with queen bed.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-10 01:21:19'),
(2, '102', 'Guest Room 2', 'standard', 2, 1, 5, '2 Twin Beds', '2nd Floor', 2500.00, 1, 500.00, 'Guest room with city view.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-10 01:21:19'),
(3, '103', 'Guest Room 3', 'family', 3, 2, 5, '1 Queen + 1 Single', '2nd Floor', 2500.00, 1, 500.00, 'Spacious guest room for small families.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-10 01:21:19'),
(4, '104', 'Guest Room 4', 'deluxe', 2, 2, 8, '1 King Bed + Sofa Bed', '2nd Floor', 2500.00, 1, 500.00, 'Cozy room for couples or solo travelers.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-10 01:21:19'),
(5, 'D001', 'Dormitory', 'dormitory', 20, 4, 24, 'Bunk Beds (12 beds)', 'Ground Floor', 8000.00, 0, 0.00, 'Spacious dormitory accommodating up to 24 guests. Ideal for student delegations, sports teams, and group accommodations.', NULL, 1, 0, '2026-03-06 07:07:05', '2026-03-10 01:21:19');

-- --------------------------------------------------------

--
-- Table structure for table `guest_room_availability`
--

CREATE TABLE `guest_room_availability` (
  `id` int(11) NOT NULL,
  `guest_room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `available_quantity` int(11) NOT NULL DEFAULT 1,
  `booked_quantity` int(11) NOT NULL DEFAULT 0,
  `blocked_quantity` int(11) NOT NULL DEFAULT 0,
  `notes` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_room_availability`
--

INSERT INTO `guest_room_availability` (`id`, `guest_room_id`, `date`, `is_available`, `available_quantity`, `booked_quantity`, `blocked_quantity`, `notes`, `updated_at`) VALUES
(1, 1, '2026-03-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(2, 2, '2026-03-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(3, 4, '2026-03-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(4, 3, '2026-03-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(5, 5, '2026-03-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(6, 1, '2026-03-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(7, 2, '2026-03-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(8, 4, '2026-03-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(9, 3, '2026-03-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(10, 5, '2026-03-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(11, 1, '2026-03-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(12, 2, '2026-03-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(13, 4, '2026-03-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(14, 3, '2026-03-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(15, 5, '2026-03-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(16, 1, '2026-03-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(17, 2, '2026-03-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(18, 4, '2026-03-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(19, 3, '2026-03-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(20, 5, '2026-03-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(21, 1, '2026-03-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(22, 2, '2026-03-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(23, 4, '2026-03-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(24, 3, '2026-03-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(25, 5, '2026-03-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(26, 1, '2026-03-11', 0, 1, 1, 0, NULL, '2026-03-10 01:37:14'),
(27, 2, '2026-03-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(28, 4, '2026-03-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(29, 3, '2026-03-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(30, 5, '2026-03-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(31, 1, '2026-03-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(32, 2, '2026-03-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(33, 4, '2026-03-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(34, 3, '2026-03-12', 0, 1, 2, 0, NULL, '2026-03-10 08:29:19'),
(35, 5, '2026-03-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(36, 1, '2026-03-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(37, 2, '2026-03-13', 0, 1, 2, 0, NULL, '2026-03-10 07:30:38'),
(38, 4, '2026-03-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(39, 3, '2026-03-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(40, 5, '2026-03-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(41, 1, '2026-03-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(42, 2, '2026-03-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(43, 4, '2026-03-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(44, 3, '2026-03-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(45, 5, '2026-03-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(46, 1, '2026-03-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(47, 2, '2026-03-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(48, 4, '2026-03-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(49, 3, '2026-03-15', 0, 1, 2, 0, NULL, '2026-03-10 08:30:55'),
(50, 5, '2026-03-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(51, 1, '2026-03-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(52, 2, '2026-03-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(53, 4, '2026-03-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(54, 3, '2026-03-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(55, 5, '2026-03-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(56, 1, '2026-03-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(57, 2, '2026-03-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(58, 4, '2026-03-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(59, 3, '2026-03-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(60, 5, '2026-03-17', 0, 1, 2, 0, NULL, '2026-03-16 06:29:02'),
(61, 1, '2026-03-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(62, 2, '2026-03-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(63, 4, '2026-03-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(64, 3, '2026-03-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(65, 5, '2026-03-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(66, 1, '2026-03-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(67, 2, '2026-03-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(68, 4, '2026-03-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(69, 3, '2026-03-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(70, 5, '2026-03-19', 0, 1, 1, 0, NULL, '2026-03-16 07:53:50'),
(71, 1, '2026-03-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(72, 2, '2026-03-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(73, 4, '2026-03-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(74, 3, '2026-03-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(75, 5, '2026-03-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(76, 1, '2026-03-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(77, 2, '2026-03-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(78, 4, '2026-03-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(79, 3, '2026-03-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(80, 5, '2026-03-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(81, 1, '2026-03-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(82, 2, '2026-03-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(83, 4, '2026-03-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(84, 3, '2026-03-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(85, 5, '2026-03-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(86, 1, '2026-03-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(87, 2, '2026-03-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(88, 4, '2026-03-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(89, 3, '2026-03-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(90, 5, '2026-03-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(91, 1, '2026-03-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(92, 2, '2026-03-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(93, 4, '2026-03-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(94, 3, '2026-03-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(95, 5, '2026-03-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(96, 1, '2026-03-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(97, 2, '2026-03-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(98, 4, '2026-03-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(99, 3, '2026-03-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(100, 5, '2026-03-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(101, 1, '2026-03-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(102, 2, '2026-03-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(103, 4, '2026-03-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(104, 3, '2026-03-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(105, 5, '2026-03-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(106, 1, '2026-03-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(107, 2, '2026-03-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(108, 4, '2026-03-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(109, 3, '2026-03-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(110, 5, '2026-03-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(111, 1, '2026-03-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(112, 2, '2026-03-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(113, 4, '2026-03-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(114, 3, '2026-03-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(115, 5, '2026-03-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(116, 1, '2026-03-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(117, 2, '2026-03-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(118, 4, '2026-03-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(119, 3, '2026-03-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(120, 5, '2026-03-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(121, 1, '2026-03-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(122, 2, '2026-03-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(123, 4, '2026-03-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(124, 3, '2026-03-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(125, 5, '2026-03-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(126, 1, '2026-03-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(127, 2, '2026-03-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(128, 4, '2026-03-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(129, 3, '2026-03-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(130, 5, '2026-03-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(131, 1, '2026-04-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(132, 2, '2026-04-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(133, 4, '2026-04-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(134, 3, '2026-04-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(135, 5, '2026-04-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(136, 1, '2026-04-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(137, 2, '2026-04-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(138, 4, '2026-04-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(139, 3, '2026-04-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(140, 5, '2026-04-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(141, 1, '2026-04-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(142, 2, '2026-04-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(143, 4, '2026-04-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(144, 3, '2026-04-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(145, 5, '2026-04-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(146, 1, '2026-04-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(147, 2, '2026-04-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(148, 4, '2026-04-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(149, 3, '2026-04-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(150, 5, '2026-04-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(151, 1, '2026-04-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(152, 2, '2026-04-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(153, 4, '2026-04-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(154, 3, '2026-04-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(155, 5, '2026-04-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(156, 1, '2026-04-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(157, 2, '2026-04-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(158, 4, '2026-04-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(159, 3, '2026-04-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(160, 5, '2026-04-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(161, 1, '2026-04-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(162, 2, '2026-04-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(163, 4, '2026-04-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(164, 3, '2026-04-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(165, 5, '2026-04-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(166, 1, '2026-04-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(167, 2, '2026-04-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(168, 4, '2026-04-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(169, 3, '2026-04-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(170, 5, '2026-04-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(171, 1, '2026-04-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(172, 2, '2026-04-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(173, 4, '2026-04-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(174, 3, '2026-04-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(175, 5, '2026-04-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(176, 1, '2026-04-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(177, 2, '2026-04-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(178, 4, '2026-04-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(179, 3, '2026-04-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(180, 5, '2026-04-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(181, 1, '2026-04-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(182, 2, '2026-04-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(183, 4, '2026-04-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(184, 3, '2026-04-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(185, 5, '2026-04-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(186, 1, '2026-04-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(187, 2, '2026-04-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(188, 4, '2026-04-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(189, 3, '2026-04-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(190, 5, '2026-04-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(191, 1, '2026-04-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(192, 2, '2026-04-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(193, 4, '2026-04-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(194, 3, '2026-04-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(195, 5, '2026-04-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(196, 1, '2026-04-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(197, 2, '2026-04-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(198, 4, '2026-04-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(199, 3, '2026-04-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(200, 5, '2026-04-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(201, 1, '2026-04-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(202, 2, '2026-04-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(203, 4, '2026-04-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(204, 3, '2026-04-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(205, 5, '2026-04-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(206, 1, '2026-04-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(207, 2, '2026-04-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(208, 4, '2026-04-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(209, 3, '2026-04-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(210, 5, '2026-04-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(211, 1, '2026-04-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(212, 2, '2026-04-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(213, 4, '2026-04-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(214, 3, '2026-04-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(215, 5, '2026-04-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(216, 1, '2026-04-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(217, 2, '2026-04-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(218, 4, '2026-04-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(219, 3, '2026-04-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(220, 5, '2026-04-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(221, 1, '2026-04-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(222, 2, '2026-04-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(223, 4, '2026-04-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(224, 3, '2026-04-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(225, 5, '2026-04-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(226, 1, '2026-04-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(227, 2, '2026-04-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(228, 4, '2026-04-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(229, 3, '2026-04-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(230, 5, '2026-04-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(231, 1, '2026-04-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(232, 2, '2026-04-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(233, 4, '2026-04-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(234, 3, '2026-04-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(235, 5, '2026-04-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(236, 1, '2026-04-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(237, 2, '2026-04-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(238, 4, '2026-04-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(239, 3, '2026-04-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(240, 5, '2026-04-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(241, 1, '2026-04-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(242, 2, '2026-04-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(243, 4, '2026-04-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(244, 3, '2026-04-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(245, 5, '2026-04-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(246, 1, '2026-04-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(247, 2, '2026-04-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(248, 4, '2026-04-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(249, 3, '2026-04-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(250, 5, '2026-04-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(251, 1, '2026-04-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(252, 2, '2026-04-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(253, 4, '2026-04-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(254, 3, '2026-04-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(255, 5, '2026-04-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(256, 1, '2026-04-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(257, 2, '2026-04-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(258, 4, '2026-04-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(259, 3, '2026-04-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(260, 5, '2026-04-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(261, 1, '2026-04-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(262, 2, '2026-04-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(263, 4, '2026-04-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(264, 3, '2026-04-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(265, 5, '2026-04-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(266, 1, '2026-04-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(267, 2, '2026-04-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(268, 4, '2026-04-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(269, 3, '2026-04-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(270, 5, '2026-04-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(271, 1, '2026-04-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(272, 2, '2026-04-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(273, 4, '2026-04-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(274, 3, '2026-04-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(275, 5, '2026-04-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(276, 1, '2026-04-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(277, 2, '2026-04-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(278, 4, '2026-04-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(279, 3, '2026-04-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(280, 5, '2026-04-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(281, 1, '2026-05-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(282, 2, '2026-05-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(283, 4, '2026-05-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(284, 3, '2026-05-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(285, 5, '2026-05-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(286, 1, '2026-05-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(287, 2, '2026-05-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(288, 4, '2026-05-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(289, 3, '2026-05-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(290, 5, '2026-05-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(291, 1, '2026-05-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(292, 2, '2026-05-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(293, 4, '2026-05-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(294, 3, '2026-05-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(295, 5, '2026-05-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(296, 1, '2026-05-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(297, 2, '2026-05-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(298, 4, '2026-05-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(299, 3, '2026-05-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(300, 5, '2026-05-04', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(301, 1, '2026-05-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(302, 2, '2026-05-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(303, 4, '2026-05-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(304, 3, '2026-05-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(305, 5, '2026-05-05', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(306, 1, '2026-05-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(307, 2, '2026-05-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(308, 4, '2026-05-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(309, 3, '2026-05-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(310, 5, '2026-05-06', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(311, 1, '2026-05-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(312, 2, '2026-05-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(313, 4, '2026-05-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(314, 3, '2026-05-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(315, 5, '2026-05-07', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(316, 1, '2026-05-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(317, 2, '2026-05-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(318, 4, '2026-05-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(319, 3, '2026-05-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(320, 5, '2026-05-08', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(321, 1, '2026-05-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(322, 2, '2026-05-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(323, 4, '2026-05-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(324, 3, '2026-05-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(325, 5, '2026-05-09', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(326, 1, '2026-05-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(327, 2, '2026-05-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(328, 4, '2026-05-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(329, 3, '2026-05-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(330, 5, '2026-05-10', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(331, 1, '2026-05-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(332, 2, '2026-05-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(333, 4, '2026-05-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(334, 3, '2026-05-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(335, 5, '2026-05-11', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(336, 1, '2026-05-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(337, 2, '2026-05-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(338, 4, '2026-05-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(339, 3, '2026-05-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(340, 5, '2026-05-12', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(341, 1, '2026-05-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(342, 2, '2026-05-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(343, 4, '2026-05-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(344, 3, '2026-05-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(345, 5, '2026-05-13', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(346, 1, '2026-05-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(347, 2, '2026-05-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(348, 4, '2026-05-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(349, 3, '2026-05-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(350, 5, '2026-05-14', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(351, 1, '2026-05-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(352, 2, '2026-05-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(353, 4, '2026-05-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(354, 3, '2026-05-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(355, 5, '2026-05-15', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(356, 1, '2026-05-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(357, 2, '2026-05-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(358, 4, '2026-05-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(359, 3, '2026-05-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(360, 5, '2026-05-16', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(361, 1, '2026-05-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(362, 2, '2026-05-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(363, 4, '2026-05-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(364, 3, '2026-05-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(365, 5, '2026-05-17', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(366, 1, '2026-05-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(367, 2, '2026-05-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(368, 4, '2026-05-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(369, 3, '2026-05-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(370, 5, '2026-05-18', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(371, 1, '2026-05-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(372, 2, '2026-05-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(373, 4, '2026-05-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(374, 3, '2026-05-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(375, 5, '2026-05-19', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(376, 1, '2026-05-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(377, 2, '2026-05-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(378, 4, '2026-05-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(379, 3, '2026-05-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(380, 5, '2026-05-20', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(381, 1, '2026-05-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(382, 2, '2026-05-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(383, 4, '2026-05-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(384, 3, '2026-05-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(385, 5, '2026-05-21', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(386, 1, '2026-05-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(387, 2, '2026-05-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(388, 4, '2026-05-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(389, 3, '2026-05-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(390, 5, '2026-05-22', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(391, 1, '2026-05-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(392, 2, '2026-05-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(393, 4, '2026-05-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(394, 3, '2026-05-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(395, 5, '2026-05-23', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(396, 1, '2026-05-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(397, 2, '2026-05-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(398, 4, '2026-05-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(399, 3, '2026-05-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(400, 5, '2026-05-24', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(401, 1, '2026-05-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(402, 2, '2026-05-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(403, 4, '2026-05-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(404, 3, '2026-05-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(405, 5, '2026-05-25', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(406, 1, '2026-05-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(407, 2, '2026-05-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(408, 4, '2026-05-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(409, 3, '2026-05-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(410, 5, '2026-05-26', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(411, 1, '2026-05-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(412, 2, '2026-05-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(413, 4, '2026-05-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(414, 3, '2026-05-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(415, 5, '2026-05-27', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(416, 1, '2026-05-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(417, 2, '2026-05-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(418, 4, '2026-05-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(419, 3, '2026-05-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(420, 5, '2026-05-28', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(421, 1, '2026-05-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(422, 2, '2026-05-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(423, 4, '2026-05-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(424, 3, '2026-05-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(425, 5, '2026-05-29', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(426, 1, '2026-05-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(427, 2, '2026-05-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(428, 4, '2026-05-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(429, 3, '2026-05-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(430, 5, '2026-05-30', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(431, 1, '2026-05-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(432, 2, '2026-05-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(433, 4, '2026-05-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(434, 3, '2026-05-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(435, 5, '2026-05-31', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(436, 1, '2026-06-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(437, 2, '2026-06-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(438, 4, '2026-06-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(439, 3, '2026-06-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(440, 5, '2026-06-01', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(441, 1, '2026-06-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(442, 2, '2026-06-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(443, 4, '2026-06-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(444, 3, '2026-06-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(445, 5, '2026-06-02', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(446, 1, '2026-06-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(447, 2, '2026-06-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(448, 4, '2026-06-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(449, 3, '2026-06-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05'),
(450, 5, '2026-06-03', 1, 1, 0, 0, NULL, '2026-03-06 07:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `guest_room_images`
--

CREATE TABLE `guest_room_images` (
  `id` int(11) NOT NULL,
  `guest_room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guest_room_reservations`
--

CREATE TABLE `guest_room_reservations` (
  `id` int(11) NOT NULL,
  `booking_no` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(255) NOT NULL,
  `guest_email` varchar(255) NOT NULL,
  `guest_contact` varchar(50) NOT NULL,
  `guest_address` text DEFAULT NULL,
  `guest_dob` date DEFAULT NULL,
  `guest_id_type` varchar(50) DEFAULT NULL,
  `guest_id_number` varchar(100) DEFAULT NULL,
  `purpose_of_stay` varchar(255) DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `check_in_time` time NOT NULL,
  `check_out_time` time NOT NULL,
  `number_of_nights` int(11) GENERATED ALWAYS AS (to_days(`check_out_date`) - to_days(`check_in_date`)) STORED,
  `adults_count` int(11) NOT NULL DEFAULT 1,
  `children_count` int(11) NOT NULL DEFAULT 0,
  `total_guests` int(11) NOT NULL,
  `guest_room_id` int(11) NOT NULL,
  `extra_bed_requested` tinyint(1) NOT NULL DEFAULT 0,
  `extra_beds_count` int(11) NOT NULL DEFAULT 0,
  `room_price_per_night` decimal(10,2) NOT NULL,
  `extra_bed_price_per_night` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `other_guests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`other_guests`)),
  `status` enum('pending','pencil_booked','confirmed','checked_in','checked_out','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `special_requests` text DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `terms_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `terms_accepted_by` varchar(255) DEFAULT NULL,
  `terms_accepted_at` datetime DEFAULT NULL,
  `digital_signature` text DEFAULT NULL,
  `data_privacy_consent` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guest_room_reservations`
--

INSERT INTO `guest_room_reservations` (`id`, `booking_no`, `user_id`, `guest_name`, `guest_email`, `guest_contact`, `guest_address`, `guest_dob`, `guest_id_type`, `guest_id_number`, `purpose_of_stay`, `check_in_date`, `check_out_date`, `check_in_time`, `check_out_time`, `adults_count`, `children_count`, `total_guests`, `guest_room_id`, `extra_bed_requested`, `extra_beds_count`, `room_price_per_night`, `extra_bed_price_per_night`, `subtotal`, `discount_amount`, `total_amount`, `other_guests`, `status`, `payment_status`, `payment_method`, `payment_date`, `amount_paid`, `special_requests`, `admin_remarks`, `terms_accepted`, `terms_accepted_by`, `terms_accepted_at`, `digital_signature`, `data_privacy_consent`, `created_by`, `created_at`, `updated_at`, `deleted`) VALUES
(4, 'GBK-20260310-2834', NULL, 'Geo Mar C. De Guzman', 'geomarc789@gmail.com', '09626970801', 'Brgy.Caybunga, Balayan, Batangas', '2004-06-02', NULL, NULL, NULL, '2026-03-13', '2026-03-14', '11:00:00', '12:00:00', 4, 1, 5, 2, 0, 0, 2500.00, 0.00, 2500.00, 0.00, 2500.00, '[{\"name\":\"Gomari C. De Guzman\",\"dob\":\"1995-12-13\",\"age\":\"30\"},{\"name\":\"Vichelle P. Laruta\",\"dob\":\"1995-05-18\",\"age\":\"30\"},{\"name\":\"Gomer Adrian C. De Guzman\",\"dob\":\"2009-10-08\",\"age\":\"16\"},{\"name\":\"Anna Marie C. De Guzman\",\"dob\":\"1974-11-24\",\"age\":\"51\"}]', 'confirmed', 'unpaid', NULL, NULL, 0.00, '', '\n--- 2026-03-10 13:34:49 (Pencil booked) ---\nsubmitted signed by me\n--- 2026-03-10 14:37:42 (Pencil booked) ---\nsbmitted signed papers\n--- 2026-03-10 14:38:16 (Pencil booked) ---\nsubmitted pencil booked\n--- 2026-03-10 14:45:18 (Pencil booked) ---\nsubmitted signed requirements\n--- 2026-03-10 14:47:10 (Pencil booked) ---\nsubmitted signed requirements\n--- 2026-03-10 15:30:38 (Confirmed) ---\nsubmitted signed papers', 0, 'Geo Mar C. De Guzman', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAV4AAADICAYAAACgY4nwAAAQAElEQVR4AeydB7hUxRmG/3ONRkXEICUKKLHGxIABpAYVgoICEVAMVUEkKIICSreEGhEDWGICBAmGICAGMEKQopIQsUTEEqrSRBSRoEFELBjewVn3Nu7u3t29Wz6eZ86ZM6fNvHv5dvaff/7J+Vr/REAEREAEkkogx/RPBERABEQgqQQkvEnFrZeJgAikLIEkVkzCm0TYepUIiIAIQEDCCwUlERABEUgiAQlvEmHrVSIgAtESyMzrJbyZ+bmqVSIgAilMQMKbwh+OqiYCIpCZBCS8mfm5qlUikEgCenYxCUh4iwlQt4uACIhAtAQkvNES0/X5CNSsWdPKlStnFSpUsAkTJuQ7rwIREIHcBCS8uXnoKEoCK1assM2bN9vBgwftyy+/tClTpkT5BF0eLwJ6TvoQkPCmz2eVkjXt2bNnrnrl5OhPKhcQHYhAAQT0v6QAKCqKjMCSJUts+/btuS7OK8S5TupABETAEZDwOgzaREtgzZo11qtXr1y3Va5c2Xr06JGrLOsPBEAECiAg4S0AiooKJ3DgwAGrW7eu/exnP7Ndu3blunDs2LG5jnUgAiJQMAEJb8FcVFoAgc8//9zq1KljGzZsyHcWE0PTpk3zlatABEQgPwEJb34mKimAAKJbu3Zt27ZtW76zJ554ot155535ylO3QDUTgZIlIOEtWf5p8XZEt127dgWKbhAENn78ePvud7+bFm1RJUUgFQhIeFPhU0jhOiC62HSfe+65fLVEbOfPn2+tW7fOd04FIiAChROQ8BbOJuvPILr0dLds2eJYBEHg9mwQ3ccff9wNsnEch6RHiEDWEJDwZs1HHV1DEdvq1atbeE/366+/dg855phjbPbs2RJdR0MbEYiegIQ3emYZfQfiOnHiRKtXr57t3LnTtTUIvu3pMjONnm7Dhg3dOW1EQASiJyDhjZ5Zxt5BrIUOHTrY4MGDDX9dGkpCjNkjur/73e9MogsNJRGInYCEN3Z2GXfnxx9/bE8//XSoXccee2woX7FiRVu5cqX98pe/DJUpIwIiEBsBCW9s3DLyrpNPPtm6du1qxx13nJUuXdo+++wz184zzjjDli5dameffbY71kYERKB4BCS8xeNXMncn8K3XXHONHXXUUbZ37173lmrVqtnixYutUqVK7lgbERCB4hOQ8BafYcY8ATNDq1at7JNPPnFtwpa7cOFCK1u2rDvWRgREID4EJLzx4Zj2T3nsscesY8eOoUG1Nm3a2Jw5c+z4449P+7apASKQagQkvHH7RNL3QePGjbObb77ZrSJBK7p3726TJ0+2o48+mkMlERCBOBOQ8MYZaDo9DjexgQMH2siRI0PVJtjNmDFjLAi+9d0NnVRGBEQgLgQkvHHBmH4P+eKLL1xcXXq21D4IAsNHt2/fvhwqiYAIJJBApgtvAtGl76MRXbwXNm7c6BrBxIi//OUv1r59e3esjQiIQGIJSHgTyzflno7odurUyZYvX+7qFgSBPfjgg9asWTN3rI0IiEDiCUh4E884Zd6A6DLzjEUqqRQRxgjrqJ4uNJREIHkESkR4k9c8vckT8KLro40hugS7Ye00f432IiACySEg4U0O5xJ9i0S3RPHr5SKQj0BKCi8BuOmJEZhlxowZ+SqtgsgJSHQjZ6UrRSBZBL4V3mS9sYj3IBQXXnihrVmzxsj37t3bpkyZUsRdOl0QAfhh05V5oSA6KhOBkiOQUsKLUODm9M4774SI4OTvfU1DhcoUSQCWxRFdvvguuOACK1++vP30pz+1Z599tsh36gIREIHICKSM8CIUed2cfBPOPPNMn9U+AgKwjFV0Edxrr73WBTvftm2bffXVV7Z161YXtyGCV+sSEcgEAglvQ0oI79tvv230rsLdnCZNmhRq/EknnRTKK3NkArGK7qZNm4w11rCtP/XUU8YvDd5EiMjTTz/drr76ag6VREAE4kCgRIV3//79NmLECKtfv7699957rjn8R2chRey8rkCbiAnEIrrcM3bsWPcZhJt4GNj8zW9+Y9u3b7dXX33VGjVqFHE9dKEIiMCRCZSY8OK4j7iOHz/eDaJRTaauEi+AOLAcK0VOAAGN1rzwwgsvuJWCEVg8SXhbEARGvIbVq1dbjx49DH9fypVEoKQJZNL7ky68LCdTt25dt8TMjh07QiyJBbtu3TpjcC1UqExEBKIV3Y8++shuueUWa968ufl4DbyoZcuWzpuECGUSXIgoiUBiCCRVeLEjNmnSxDZs2BBqzY9//GO3nhfxAsqVKxcqVyYyAtGKLmac2rVr2/Tp00N23FNPPdVmzpxp06ZNM0wMkb1ZV4mACMRKIGnCO3XqVPezllFzKotZYdCgQS5YS40aNSgqNO3bt6/Qc9l84ssvv7QGDRqY99M95phjDGFlgCwvF3q2P/rRj+zGG2+0Dz/80J3mM8BP+qWXXrLLLrvMlWkjAlER0MUxEUi48H788cfWoUMHu+222wwzA7XEXYmBnAEDBhj/+SnLm15//fVQEaIROlDGEcBcgKngrbfecsdwJPZCXvs41/EFxwDm+++/765lg2/uP/7xDxs2bJiW9wGIkggkkUBChff555+3OnXq2KJFi1yTypQpY0wBnjBhgh133HGurLANAz/+XOPGjX1W+0MEENuLL77YXn755UNH5lYF/sMf/uB8b13BoQ3+t4888ojVrFnTcM3j+FCxW1mCVSeWLl1q9IApUxIBEUgugYQILz+BW7RoYaQPPvjAtYie2IsvvmiRxn2l98aNuJchFOSVzP71r3/Zz3/+c+MXAzzOPvtsW716dS4/2+XLlzv3sNtvv9327NnDZfad73zHeSmsX7/esKtj3sGmft555xk+1FWqVLHvf//7xpeiu0GbNCagqqc6gbgLLz9t+QlMb5fG8xP4rrvusnnz5lmFChUoKjL9+9//tl27drnrLrnkEjvhhBNcPts3rBLRqlUr27t3r0NBr/eZZ56xSpUquWMmouAV0rp161zeCgg1LmO7d+82BBdTD7PRDh48aDt37jRmqGFHx6WMXrJ7mDYiIAIJIxBX4aU3hRj4n8D0shg979Onj/uJG2krEGl/LULj89m6RyCHDBliDIR5k8FNN91kTzzxhJUqVcr+97//2eDBg61evXrOQ8RzwluBXxorV660/v37u2m/n3zyiT9tDMblXUmYL8rQBcqIgAgkhEDchJeft7iK+Z/A/IRlgCxS00J468LNDFdeeWX4qazL0xO96qqrDBsujcf0guvdqFGjnDsY9lsGyiZOnGiYePw1QRAYftL//Oc/7dNPP6XYpfPPP9/uuOMOW7Bggf3whz8MTV7hJF+UfEmSV4o7AT1QBEIE4iK8v//97w1xQCR48qWXXup6Xt8/ZDPkOJokM8O3tN59911jYJEvNUpLly7tTDZMNqEMTwU8FrwdNwgCLnOBbXysBQp+8pOfOLFdfcgWjCcDg2rt2rUzvhg5Ty+XHjFfml26dKFISQREIIEEiiW8OO/jMzp06FDj5zD1xG0MZ/yivBa4tqAkM8NhKsRHwGzjXelOO+00F5oR8wHCmdeOy13hYosZAXPEK6+84nyl+/XrZ9zLZ4V7n7cTX3755cbEFkwVmq0GRSURSDyBmIUX0WUgJ3xCBD97+Y8dBId7XrFU3wc9pxeWrWYG4lggiP/9738dQmaaEQ8XFzB6ueH+uO6CbzYww7SDqYagQ5gjfvCDH7izBLvhHL9OKECYGXBjwO7EE0+kKCuTGi0CJUEgJuFFdPPGziW4TXFDBz700EN24MABx6Fs2bJZ6c2AGHbt2tXwMABE27Zt7eGHHzYC4GBW8Hw459PJJ59st956q7322mvOTxovhiD49svv73//u5s1uGrVKndL5cqVbfHixc69zBVoIwIikFQCUQsvoosIhMfOpYdGWXFqzs/dkSNHhh5BEJfQQRZkEFS+zAjRSHPpvQ4bNsyIhUsUN2zflIcnXMPowb755pt29913h9zK/DW4iTGBApswng+U41u9YsUKq1atGodKIiACJUAgKuH1outjA2AT5Gctdt7i1p2Rdt/LwzbZq1ev4j4ybe7HZ7lp06a2cOFCV2fs4/fcc48hqvfdd5+F225x/0JIGVzD9MAXHp+DuzFsM2vWLBfHYfPmza4UIcf08Oijj1rKmxZcjbURgcwlELHwJlJ0//jHP4amFZ977rkW3vPNXPSHW7Z27VpjEM17GOAJgj8us/Xy2nK7devmIrvhToanwuEn5N7i4cAECb68vJdJEATOXEFZ7qt1JAIiUBIEIhZeBnh8T5eBmcKiYEXbCEwM9Hb9fYhuEHxrn/TlmbjHXIPvsxdYPBeY+cdstPBeLhHI6LlihiDeRWEs6AUj2izd46/BpED0MQZCfZn2IiACJUsgIuFl0IspplSVn6yYF5gRxXFxE6IbbmJgYKi4z0yH++m1tm/f3lj+iPoyLRqbrI/gRtlJJ51kTz75pP3tb3+zIwku9mF6yLiY+dgYfE6sJIE5Ij6LhVIjJREQgXgQKFJ4ceK/9957Q+/C5hgv0c1GEwMmm4suusgNhnnfZ+CGT+VFNLt3727EXijKfo6JgmsmT57MY1wi4A32YlaSYDaaK9RGBEQgZQgcUXjpieL76UWhZ8+eFq+ZTdloYmDqbps2bQwvhML+AqpWrWp4MIwZM+aI8S0Q7XHjxhmzBBFo/zwmRxCgCNOQL9NeBEQgtQgcUXhr1apl9HipMiPh9KDIxyNlm4mByRCsaUZYx4L40TPFfYyZZohvQdf4MoIREWdh5MiRoVgLfD54LGAWInCOv1Z7ERCB1CNQqPDibM9sJ19lelcFuS3589HsM9XEsGXLFmPCA/Fu/Yw+uBB6sVGjRsY0YI7zpurVq7teLtHHgiDIezp0zMAbkygwLfjleziJVwQDaPjocqwkAiKQ2gQKFV7MDL7qp5xyivET2R8XZ5+JJgY8EIgOhkfBsmXLjOm6f/7znx0mzAB169YNBS53hd9s8MnFfo4XAx4N3xTn2xEKkji5TIZgWjbHXBQEgRHsfO7cuRHHOuY+JREQgZIlUKjwElDFV42ftT5f3D3xF7yo41ea7l4MhGIkaA1BZvAugA+/DJjkwJJHiDHrzlEenrDBErf4hhtuOKItFxcx4jMgsPjo8gzMEj169DBczIjTS5mSCIhA+hAoVHjpgeHuxNIy8ZpFxswpbzP+3ve+lxETJQhegynBf+SsMcd6cfR4GehCmP059kFwuJdK/AQYU1ZQoqeM7y0uYj5CGdcxmEZgc2I6YNelTEkERCC9CBQqvDSDwDesk4Z9kuPiJMQJ265/Bj24ICjcnumvS/X9vHnzQlUk1gIBy+nVh7t3cUEQBHbdddcZC1XSSw2CgttOTAV6z/SU8cHlXhJfgJgUmAosv1yIKIlA+hI4ovDGs1ks5e5/cvsprfF8fpHPSsAFiORf//pX92QihBGCETssEyFc4TcbVn3AZDB+/HijJwaOKQAAEABJREFUp/9Nca4ddlvst3lXk+B6XMtwEWMQLddNOhABEUhLAkkR3qefftrNvoJQ+fLlbfjw4WTTPhHw3dt1MSmMGDEiFBCexjG1mkA3rPqA+FJWUEKUsePisZDXjosnBJMpWPKnoHtVJgIikH4EEi689ArDQzw+8MADGRMdC5u1/8h9b94fY+vFg4PoYb4s737dunVGsJu8dlwmrciOm5eWjkUgcwgkXHiZdEHYQ5AxgYDwh+QPp/TdssSRXz4nvBVBEBiCzODZ8ccfH34qlMfejc8uwW/8YCMnvR13xowZJjsuRJREIDMJJFR4sUsyug86RuCJ80A+XRPTdLHpYjaYOnVqvmbg5jVnzhzDTS7fyUMFTL1GlC+44AJjyR38fw8VO3cyBtzgJTsuRJREILMJJEx48dUltoPHR7hH7Lv+OJ32xLVl4At/Zvxud+zYka/6RBcjilhBHiAExuF+Bs5++9vfhpY3IuA5PWciv+HlITtuPqwqEIGMJFCQ8MalofiZ+tF9Bo5wtYrLg5P4EASWmBIEZ2fgK3yabng1+EJhsgR23fBy8ribMXON+3fv3k2RIbCdO3e21atX29ChQ7NybTkHQhsRyFICCRFeBo0I1gJTRvZZrJF8uiS8EBBRTArUnahihdWdSRAENGf2Wvg1+D8zK+/66693M8z8OQbOCJRz//33G4Lty7UXARHIHgJxF178UW+88UZjD0Z6dIgT+VROuHHxZYEfbqtWrSx8tpivdxDknvRw3nnnGaIb3j5mnCHYl19+ea6gONh1WRmCgbNzzjnHP1J7ERCBVCaQoLrFXXiZ7UZwburLsjPhdl7KUi1hi2ZA66yzzrK77rorV+/U1xUxZoUHTAS+rFKlSoZ/su+1YsdlaR7MKpgo/HVnnHGGMRBHIBzO+XLtRUAEspdAXIUXm+7o0aMdTUSK6bPsXUGKbi688EJ74403zHsY+GoGQeAisjFtFz9kwmIySYLzQRAY688xoMYxsRkI1YhdGwGmLAgCY+kd/HGZQkyZkgiIgAhAIK7Ci/2SHiQPxk8VLwDyqZqYFfbOO+/kqh5iSi8dMSa2BKtFdOnSJRRwnItZCZgpwsTHRZSbN2+eyzSBvzLxePFhJvQj9yiJgAjEi0D6PyduwsvU2Pfff98RwWeXn+buIIU3uHfx5RAEgfM0YCBs7dq1LmoaYTGJkdCnT598vWGEd/bs2UZox+nTp4fOcw/TiKdNm2YVK1Y0/RMBERCBggjERXhxs8K+6V9ALAZi0vrjVN4zaQE3L2bXMcGDZXMwOyC4CC91D4Ig16QIbNgMINJuzrM4JT18VoG47LLLKFISAREQgUIJxEV48XXlZzdvITYB0cfIp2vCXsv6ZdQfGzUuZZgPOCZ5jw3y9JpxP2O9tMKmCHOdkghkOAE1LwoCxRbeFStWGD+7eWeZMmVcnALy6ZgI6IMbGOYG6k9PFlMCXyZ54zJwji8YBt/y+vByr5IIiIAIFEagWMJLSER+YvuHMy24bNmy/jCt9ixU2aRJE2PiAxWnp8vgmg/qw8oPVapUMQbLmDaMmWHChAkuzgLXK4mACIhApASKJbzEHdi6dat7F25ZrDPmDtJs88orr1jjxo3d6hBUnchgeDwwkYJjn1577TUjshgLVPoy7UUglQmobqlJIGbhZQkbenw0i6hcTJwgn26JwDa4g3kbNXEVli1bZpUrV063pqi+IiACaUIgZuH9xS9+YX5CQb9+/YyZX2nS5lA16bHjo+t9j9u1a2fz58/PmEDtpn8iIAIpSSAm4c3rs8sMrZRsXSGVYnYZdlpi4+I6FgSBixKG9wI23EJuU7EIxIeAnpL1BKIWXlZMYGqsJ3fPPfdYuvjsUmc8F5jCS0Bzjome9qc//cmIi8uxkgiIgAgkmkDUwnvzzTcbKylQMVZa4Oc5+XRIW7ZsMUI1EluB+jLtd8GCBRbuo0u5kgiIgAgkkkBUwssyPkwWoEIsZU40L/LpkLznAmEbqS+eC88++6wReYxjpWwnoPaLQPIIRCy8xGEYPHiwq1kQBIaPa7qYGOS54D42bURABFKEQMTCS2wCvxIDeabKpkgbjlgNeS4cEY9OioAIlACBiIQ3HU0M8lwogb+m+L9STxSBjCRQpPCmo4lBngsZ+beqRolAxhAoUniZSutNDHgxlISJgam6NWrUsHLlyhmxcCtUqGC4gBX0KchzoSAqKhMBEUglAkcU3sWLFxs9XipMcHPCP5JPZGI23Pr1690MMuLhVq9e3Ro1amQI6sGDB41ZZlzDskJ56yHPhbxEEnOsp4qACBSPwBGFd9asWaGn49Fw7LHHho7jmcFFjQUn6cmecsopVq9ePevatashvHmX5uG9QRBYjx49yIaSPBdCKJQRARFIcQKFCi8rMiBm1L9q1ar2q1/9imzc0p49e+yhhx4yopoRBYw1zujJhgcZ52VBELALpTp16hiRwxBmXyjPBU9CexEQgXQgUKjwTp48ORQEp3v37nGLO0vvtkuXLnbuuee65dT9hAZg4RdM0HGWEWKhSBaeJJYC54IgsP79+9vChQvttNNOo8gtQKmYCw6FmXYiIAJpQ6BA4SXA+ZQpU1wjjjvuOOvUqZPLx7rJ27t98sknQ6JOrIQ2bdoYZe+9954RgIdld0aMGBGamtyiRQvbvHmzYe4IgsM9YHkuxPpp6D4REIGSJlCg8BJABrGkch06dLDSpUuTjSphMliyZImz1zI9l+nF4b3b008/3X7961/bf/7zHzcLjnXOeEG3bt0MswP5IAjcUkIIMYN7lJEYaFPMBUgoiYAIpCOBfMKLnRVB9I0hKI7PR7JnBd6hQ4ca65BhNsBDwd8X3rvFTnvLLbcYgWr8eWbEzZ071x/a6NGjc63uy4n08lygxkoiIAIikJtALuHdvn27i97FwBqXsQoDA2vkj5R27NhhrEaBN8Ill1zizAX+GdyH7RaRzdu75ZxPiK5fNJOFJMlH6rmAOeSxxx4zfIzLly9vlSpVMvb0lP3ztRcBERCBVCEQEt5FixZZgwYNQj/zEb/wuLt5K7xv3z6bMWOG4ZFQrVo1Gz58uIX3bnENY8LFc889Z9huf33IrBDeuw1/Xl7RnTlzprHwZPg1eT0XWHyyYcOGhn2YgTp65qz/holj//79xp5YweHPUF4EREAEUoFADhMSsJdiy/VLmDOYtWnTJmMtsvBKMoHhmWeecQKNZ0GvXr0MLwXKuY6ebdu2be3xxx93tltWeECUrZB/mDXq168fWh4esc8rutSPLwSeFe7hgP0Y74dVq1aZfz+voQ4k8kz+eOKJJ8iGJ+VFQAREoEQJ5CC42FupBcKHwPETPXwwCxHGywARvfrqq23t2rXmRZB7LrroIueTywKYEydOdOaKo446ikcWmjBrMKC2bt06d00QBFaQ6LZr1869z130zca/m0N60Yg97924caPrXZPnHCldF+Gk7koiIAKZSSCHHixNQ0Bx5cI8wDHuWkQl4yd9rVq1bPz48YYtl3MkepW9e/c2BtPmzZtnCHipUqU4VWTyZo0NGza4a3n3Aw88kMu8wCoXTZs2NUwV7qKwDasaX3/99UavF/MGQov4IsJcRi+ZPYneMnslERABEUgVAs7Gi5DhQoZ4IWQscc4KE7feeqvhReArSy8Yd6+lS5e6nuWwYcPs1FNP9aeL3K9Zs8aZKRDpvGaNjh07Gv7DLMXDpAjeT3Cc8IdWrFjRsDszjfi+++5zq0cg2uHXkF+9ejU7l5o1a+b22oiACIhAqhBwwktMBEStc+fOhs2Vnqj/OY/JgIGuRx55xCjHrkqksGgagKAi5pgWMFNwL4KJWYOeNGLLQBkiTh34EmBwjOtI1GHgwIGGoOLpQG+b8sJSuGBHW9fCnqlyERABEYgXgRxstsuWLXMTHRBAL7j0gjE7vPnmm27wC+8F/HBjeXHt2rWdaPt7EVIG8Bj48h4JuJr5d/vrjj76aJs0aZLhmobwFiW4/j6/mCVuZbia+XLtRUAERCAVCOTMmTPHxT/wlcH/1f+cHzVqlNET9udi2TNwh2nA3xsEgXP1YopwXo8EBNlfh2DiHcEXgy+LZM9acL63HKnNOZLn6hoREAERiBcBZ2rgYQgsgstgWSQ/57knkoSZIfy68F4tg2HYlfE8wDPCCyaiy4QIysLvLSr/4Ycfutlu/rrbbrvNZ7UXAREQgZQhkEMvc8CAARHbT6OtObZdBsowG2CqwFxwzTXXhDwSHnzwQcOmiz8wz45VdLmXQO0fffQRWWO6MqLuDrQRAREQgeIRiOvdOdhPBw0aZAhiXJ8c9jA8I3bu3GlMaGAWG6tH1KxZ00Uow5sBGzOXF0d0V6xY4WzRPKdMmTIuuA55JREQARFINQIhU0OyK4avbbxEF3MGPsW+DSNHjrSyZcv6Q+1FQAREIKUIlIjwxlN0oUkcB+I0kGdFCwSdvJIIiEBmE0jX1iVdeOMtukxTnjBhguOPCxwDde5AGxEQARFIUQJJFd54iy5M8S8m2A75fv362VlnnUVWSQREQARSlkDShDcRonv//feH4kcwnblv374pC1oVE4GsIaCGFkkgKcKbCNHF3zg83u64ceMS6plRJEldIAIiIAIREki48CZCdIlcdu2117qgOrSTgDkERCevJAIiIAKpTiChwpsI0QUoK1Zs27aNrF1xxRVGiEh3oI0IiEAhBFScSgQSJryJEt0pU6aEYkuwCgaTMVIJqOoiAiIgAkURSIjwJkp0iZQ2ZMgQ1yamIE+fPt1OOOEEd6yNCIiACKQLgbgLb6JEF7tup06d7IsvvnBsCehz/vnnu7w2IpCmBFTtLCUQV+FNlOgithdffLF5u+6VV14pu26W/sGq2SKQCQTiJryJFF0ijW3evNnxLl26tBHRzB1oIwIiIAJpSCAuwptI0cW84Be8DILArWYsu24a/qWlUZVVVRFINIFiC28iRZee7pIlSxwDwlbOnz/fWrZs6Y61EQEREIF0JVAs4U206PqeLqLLMkAslpmuoFVvERABEfAEYhZeia5HqH1SCOglIpBBBGIW3oYNG5pfOeLYY4+1WNZIy8sR7wXMC+rp5iWjYxEQgUwiEJPwEpBm48aNjgMxcGfOnGnRLkzpbg7bILp16tQxL7qszzZ79myTeSEMkrIiIAIZQSBq4WVts9GjR7vGB0FgBB6Ph+iyAOaWLVvcc1mAE5suvWpXoE2KElC1REAEYiEQlfC+++67dt1119nBgwctCAKbNWuWtW3bNpb3hu6hp4vL2PLly11ZEARGj1qi63BoIwIikIEEIhbezz77zOiV7tmzx2EYOHCgNWnSxOVj3SC62HTzuox17tw51kfqPhEQARFIeQIRC2/37t1t7dq1rkHNmze3AQMGuHysGy+63qYrl7FYSea7TwUiIAIpTiAi4R0/frwtWLDANYU1zSZPnuzysW4kurGS030iIAKZQKBI4V26dBeCU+wAAAX/SURBVKmNHDnStZV1zfA0wH3MFcSwYQCtWrVq8l6IgZ1uEQERyAwCRxReRLJr16729ddfW05Ojk2bNs2qVq0aU8t5xsSJE61evXq2c+dO94xs8l5wDdZGBERABA4RKFR49+3b5wbT2B+6zoYPH26EZiQfbSKy2DnnnGODBw8OrZMWBIFhwpD3QrQ0db0IiEC6EyhQeOmd0tN96623XPtat25tPXv2dPloN7ic4ee7e/fu0K1MlHj11VcNN7JQoTIiIAIikCUEChTeUaNGGbZdGGCPffjhh8lGlXA7YyXgm266yXyvmQf07t3brZnGemkcl2jSy0VABESgBAjkE168F5jAQF3KlSvnJkng6sVxpGn58uXOlvvUU0+FbkHAX375ZRs2bJibfBE6oYwIiIAIZBmBXMKLny7+ujBgMUliMFSsWJHDiNKBAweMiRWYJj744AN3D4Nyffv2dT3oM88805VpIwIiIALZTCAkvJgGmJnGDDWATJgwwWrUqEE2ovT666+7gDbhPr5VqlRxZoU777zTCKYT0YNMV4mACIhAZhNwwvvVV19Zx44djVgMNLdr167Wvn17skUm4jZgmrj00kvt7bffDl3foUMHe/7556127dqhMmVEQAREQATMnPDecccd9sILLzge9HLvvfdely9qs3//ftcrZoIFs9G4nkkWjz76qFsbrVSpUhQpiYAIiIAIhBHIIYA5Exsow56LXZeJDRyHpXzZ7du3W7NmzWzbtm2hc/j5vvTSS9aiRYtQmTIiIAIiIAK5CeT06dPHleC5gM8tngyu4AibRYsWWYMGDeyNN95wVwVBYP3797e5c+dahQoVXJk2IiACIiACBRPI8SYCfHVx+Sr4ssOlW7dutVq1ahn2271797pCerfMTGNWmivQRgREQAQymUAc2uZsvExqwAWssOcxk23SpElWt25d27Rpk7sMNzEmWmDPxa7rCrURAREQAREokkBO48aN7e677y70Qnq59GoHDRqUK84CdmFmpRV6o06IgAiIgAgUSCBnypQpLvJY3rO+l4std+XKlaHTPs7CVVddFSpTRgREQARKlkB6vT2nTJky+WqMp8IVV1xh9HI//fRTd/7444+3MWPGuAkRirPgkGgjAiIgAjERcDZef6fv5davX99efPFFX2z0cpkMwXTiIAhC5cqIgAiIgAhETyAkvEwVrlmzZq5eLi5mTKZYuHChqZcbPVzdIQJZTkDNL4SAE95du3ZZ8+bNbcuWLaHL6OXS673hhhsUTSxERRkREAERKD6BnFWrVhmrQBCYnMcFQWC33367bLnAUBIBERCBBBDIYRDNh3BkcsT69ettyJAh6uUmALYeKQKpQEB1KHkCOZ9//rmrRbdu3VwvN5Ipw+4GbURABERABGIikHPon4skNnbsWMXMjQmhbhIBERCB6AjkTJ061cVeiO42XS0CIhBXAnpYVhHIadmyZVY1WI0VAREQgZIm4NzJSroSer8IiIAIZBMBCW82fdpqa5QEdLkIJIaAhDcxXPVUERABESiUgIS3UDQ6IQIiIAKJISDhTQxXPTVxBPRkEUh7AhLetP8I1QAREIF0IyDhTbdPTPUVARFIewIS3rT/CFOjAaqFCIhA5AQkvJGz0pUiIAIiEBcCEt64YNRDREAERCByAhLeyFml35WqsQiIQEoSkPCm5MeiSomACGQyAQlvJn+6apsIiEBKEpDwJv1j0QtFQASynYCEN9v/AtR+ERCBpBOQ8CYduV4oAiKQ7QQkvIf/ArQVAREQgaQRkPAmDbVeJAIiIAKHCUh4D3PQVgREQASSRiClhTdpFPQiERABEUgiAQlvEmHrVSIgAiIAAQkvFJREQAREIIkEohfeJFZOrxIBERCBTCQg4c3ET1VtEgERSGkCEt6U/nhUOREQgRQmEHPVJLwxo9ONIiACIhAbAQlvbNx0lwiIgAjETEDCGzM63SgCIpCKBNKhThLedPiUVEcREIGMIiDhzaiPU40RARFIBwIS3nT4lFRHEUh3Aqp/LgIS3lw4dCACIiACiSfwfwAAAP//TQQXqQAAAAZJREFUAwDb5OI2Si4pfQAAAABJRU5ErkJggg==', 1, NULL, '2026-03-10 03:21:43', '2026-03-10 07:30:38', 0),
(5, 'GBK-20260310-3216', NULL, 'Bryan C. Johnson', 'geomarc789@gmail.com', '09087547440', 'Caybunga, Balayan, Batangas', '1974-01-01', NULL, NULL, NULL, '2026-03-15', '2026-03-16', '11:00:00', '11:00:00', 1, 0, 1, 3, 0, 0, 2500.00, 0.00, 2500.00, 0.00, 2500.00, '[]', 'confirmed', 'unpaid', NULL, NULL, 0.00, '', '\n--- 2026-03-10 14:41:07 (Pencil booked) ---\nsubmitted signed forms\n--- 2026-03-10 16:30:55 (Confirmed) ---\nsubmitted signed requirements', 0, 'Geo Mar C. De Guzman', NULL, 'data:image/png;base64,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JRqB0+813fKaa65x37NDAAEEgiJAOxBAAIHsEiCAyy5p7oNAkgt8+OGHbj02MYRCITdtUtMee/XqZefPn9dptxUtWtRlmNS0So20aZqkgr5ixYq579khgAACCCCQYAJ0B4GrEiCAuyouLkYAgasVUHA2YsQIe/zxx/3pkG3btrWFCxfaU089ZefOnXNV6l04LSuQkpJio0aNsubNm1u+fPncd+wQQAABBBBAAAEEfhJIHcD9dI49AgggEBGBr7/+2k1/HDp0qAveQqGQKVBTkpKRI0e6eyiT5DPPPGPjxo1zWSiVjVKjbgcOHHDTKXWtu5AdAggggAACCCCAgBHA8ZcgYgJUhIAncPToUXv22WetevXqtmbNGu+0C+IUoH322Wf+OY3Q6d23qlWrWv369a1JkybuU8deQpOJEyf611NAAAEEEEAAAQSSWYAALpmfPn1HIMIC+/btMy0RULlyZXvrrbdcwJbBW1z2sueee87efffdy17DlwgggAACCCCAQDIIEMAlw1Omjwhkg4BGyTQ9csaMGXb27NlUd8ybN2+q4zx58pjWd2vXrp1VqlTJbV5Zi3DrnN6TK168uPud6uvataspC6U7wQ6BVAIcIIAAAgggkDwCBHDJ86zpKQJRE/jjH/9o/fr1u6T+u+++22WUPHXqlP/dHXfcYbt377a1a9faq6++aitWrHCbV961a5c7fu2112zDhg1WsWJF/7c9evSwOXPm+McUEEAAgSwLUAECCCAQZwIEcHH2wGguAkES2Lp1q/3qV7+y119/PdV0ySpVqtjGjRvtpptusqlTp/rfKaDT2m/XX399hrqRO3du+/TTT0316QcXLlywTp06mbJa6pgNAQQQQACBWApwbwRiIUAAFwt17olAAgjMmjXLFJBt3rzZ7821115r8+bNc2u8aQqk996aplBqXbcPPvjAZZr0f5CBgoK4ZcuWucQm3uXKarly5UrvkE8EEEAAAQQQQCDeBDLdXgK4TNPxQwSSW2DBggWpABSkDRo0yI3G6V04TYXUBSVKlDCNumldNx1ndps9e7YVKVLE//nTTz/tryHnn6SAAAIIIIAAAggkuAABXII/4Ax1j4sQyITAxdMgT548af3797e5c+eapjqqSi0FsHz58lTvsel8ZjatF6dpmV4Qt3//fqZSZgaS3yCAAAIIIIBAXAsQwMX146Px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', 1, NULL, '2026-03-10 06:31:31', '2026-03-10 08:30:55', 0);
INSERT INTO `guest_room_reservations` (`id`, `booking_no`, `user_id`, `guest_name`, `guest_email`, `guest_contact`, `guest_address`, `guest_dob`, `guest_id_type`, `guest_id_number`, `purpose_of_stay`, `check_in_date`, `check_out_date`, `check_in_time`, `check_out_time`, `adults_count`, `children_count`, `total_guests`, `guest_room_id`, `extra_bed_requested`, `extra_beds_count`, `room_price_per_night`, `extra_bed_price_per_night`, `subtotal`, `discount_amount`, `total_amount`, `other_guests`, `status`, `payment_status`, `payment_method`, `payment_date`, `amount_paid`, `special_requests`, `admin_remarks`, `terms_accepted`, `terms_accepted_by`, `terms_accepted_at`, `digital_signature`, `data_privacy_consent`, `created_by`, `created_at`, `updated_at`, `deleted`) VALUES
(6, 'GBK-202603-0001', NULL, 'Michael J. Jackson', 'geomarc789@gmail.com', '09626970801', 'Brgy. Caybunga, Balayan, Batangas', '2004-06-02', NULL, NULL, NULL, '2026-03-12', '2026-03-13', '11:00:00', '12:00:00', 2, 0, 2, 3, 0, 0, 2500.00, 0.00, 2500.00, 0.00, 2500.00, '[{\"name\":\"Lisa Marie Presley\",\"dob\":\"2004-06-08\",\"age\":\"21\"}]', 'confirmed', 'unpaid', NULL, NULL, 0.00, '', '\n--- 2026-03-10 16:29:19 (Confirmed) ---\nsubmitted signed requirements', 0, '', NULL, 'data:image/png;base64,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88wzwf6ff/45KFNAAAEEEEAgigIEcFF8a9QZAQSKJKDxTwsXLrR33nnHKleuXKRr43ayeiHVJk1Urp4rlaOyKPi67bbbrHHjxqapIKZNmxZ8Eut5nh166KF2yy23mMa+zZkzxzT3nwL4Zs2aBROzf/TRRxlr7ltvvWWqz+677+7G5mWyLhlDSM6DuQsCCCCQ1QIEcFn9+mk8AtkjoB647bffPnsanEdLFQB9/PHH7siBBx5oW20Vjf8LUODTr18/U6/akCFDTGPaXCM2rjTWTZlFlyxZYo8++qjrldt11103Htn0X12rPZ9++qmtXr1axbQs6hU89thjrXbt2nb44Yfbm2++afqMU2Pzmjdv7oJRtem1116zuI1JTAswD0GgyAJcgED0BaLx/97Rd6YFCCCAQMYFXnrppaAOLVu2DMphLPz111/2wAMPWOvWrV3gM3HiRPv7779dVUuXLm0Kip5++mlT4DNw4EDbcccd3bH8VnvvvXdwKF1j/3744QdT75/cf/rpp+D5iYWlS5eaehWPOuooF+Sdc845pnap/YnnUUYAAQQQCIFASKpAABeSF0E1EEAAgVQL6LNC/xlhDeA++eQTu/TSS009a+eee64tWrTIr7KbAuKyyy5zn0jeddddpl7E4GABhTVr1gRnPPXUU0E5VQX1sGnaAvX4+c/QJ5433nijKaC7//777ZRTTrHE3kJNd6Hxmr1797aaNWva8ccf74JYZU/178EvAggggAACBHDR/DNArRFAAIEiC/gBnMa/hSmZi4KrKVOmWKdOnaxJkyY2fvx4W7lypWuf53nWpk0bl4hm4cKFdsEFF1ilSpXcscKu9GniG2+8EZzevn37oJzsgp51/fXX29FHH20rVqxwt9c8dOop1CeeJ598su23336mHrcbbrjBBaNKwnLxxRe7T0TdBRtX6m189tlnTUHsnnvuaVr69u1raoeObTyFfxFAAAEEslSAAC5LXzzNzmYB2p6NAt9++62FbfzbF198YZrmQUk9FNj4AabeT8WKFU2fEypomzx5sikRTXHH7D322GO2fPly3daNkWvYsKErJ3ulTyYVuF177bVuPJvqq/F5CsTUo5jf8/bff3/X66j2q71XX321HXTQQbb11lsHl6gX7oknnjAFn7vttpsLav/973+b2vb5558H51FAAAEEEIi/AAFc/N8xLUQA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sbpNmjSxiy66KHFXvmX1UrZp08aWLl3qztljjz3sjTfesE6dOrltVggggAACCIRZIAsCuDDzUzcEEEAgfwEFNE2bNjU/YYkCGQVoZ555po0YMcJ+/fVXd3GpUqWsd+/epvngxowZYxoD5w5EZKV2aoyb3+OWGLidddZZdt9999nzzz9vyqaZV5PKly9vGu/neV5ehzfZd+utt9pxxx1n+nxSB9q3b2+vvPIK0wQIgwUBBBBAIBICBHCReE1UMmMCPBiBDAhoTjMFYuph++yzz1wNND5LAZyCGT8xifZp6oD58+fbqFGjrEqVKu7cqKwKCtwUkP7f//2fKSPk559/nm+zrrvuOqtWrVq+x3VAvXbKMjl06FBT4hLP82zgwIH24IMPWrly5XQKCwIIIIAAApEQIICLxGuikgggEEWB4tT5pZdeMqXJVybEP//8M7iFMi3+8ssvblvjtTp37uyyLd5+++2x63F75513bMCAAXb66ae77JAKaD3PM7Xbcv1z6KGHugAv1+5NNr/99ls74ogjTPO86YACNn2GOmjQIG2yIIAAAgggECkBArhIvS4qiwACcRX47LPPrGfPni75yKeffppnMz3Ps44dO9qrr77qPhnU2K08TwzpzoJ63BS4XX311e5T0JYtW7pPG9WUnXbayTQ9gHrOtO0vFStWtHHjxvmbef6qF09jARctWuSOV61a1WbOnGn6dNLtCO+KmiGAAAIIIJCnAAFcnizsRAABBNIj8Mcff9hVV11lzZo1sxkzZuT7UM1V9uKLL7rxYHvvvXe+54XxQGEDtx122MGUSVLj/H788UfXlNatW7usk36iFrfzf6vRo0ebgrv/bW72o7nujjzySNPzdVCJTtTDWadOHW2yIBBjAZqGAAJxFiCAi/PbpW0IIBBqgUceecQaN27sxq/5UwDkrrB6j1544QWbNGmSaT603MfDvK3AaUvJSfweN81Lp7T+hx9+uI0fP941SVMFDBs2zN5//3376quv3L7EVdeuXTfpRdNnlsuXLzfNd/foo49ahw4d7IwzzjDf9aSTTrKnn37adtxxx8TbUEYAAQQQyC3AdugFCOBC/4qoIAIIxE1Ac48pWFGGRY3Pyqt96pF75plnTHO57b///nmdEtp9RQnc1IiHHnrIFKjKRdu1a9c29Tbqs0fdS/uUaVO//qK0/0puojFw6pHc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', 1, NULL, '2026-03-10 07:50:47', '2026-03-10 08:29:19', 0),
(7, 'GBK-202603-0002', NULL, 'Geo C. De Guzman', 'theavengergeo6@gmail.com', '09087547440', 'Caybunga, Balayan, Batangas', '2004-06-02', NULL, NULL, NULL, '2026-03-17', '2026-03-18', '11:00:00', '12:00:00', 10, 0, 10, 5, 0, 0, 8000.00, 0.00, 8000.00, 0.00, 8000.00, '[{\"name\":\"Jeremie Azarcon\",\"dob\":\"2000-01-01\",\"age\":\"26\"},{\"name\":\"Gomari C. De Guzman\",\"dob\":\"1995-12-13\",\"age\":\"30\"},{\"name\":\"Vichelle P. Laruta\",\"dob\":\"1995-05-18\",\"age\":\"30\"},{\"name\":\"Gomer Adrian C. De Guzman\",\"dob\":\"2009-09-08\",\"age\":\"16\"},{\"name\":\"Ysabella Ellao\",\"dob\":\"2009-09-18\",\"age\":\"16\"},{\"name\":\"Jowsel Marquez\",\"dob\":\"2000-02-28\",\"age\":\"26\"},{\"name\":\"Flinky Rose Igaya\",\"dob\":\"1995-12-18\",\"age\":\"30\"},{\"name\":\"Ferlyn Rose Igaya\",\"dob\":\"2003-11-13\",\"age\":\"22\"},{\"name\":\"Fiella Rose Igaya\",\"dob\":\"2001-11-24\",\"age\":\"24\"},{\"name\":\"Roux Flynn Igaya\",\"dob\":\"2017-04-01\",\"age\":\"8\"},{\"name\":\"Wensel Maquez\",\"dob\":\"2004-09-13\",\"age\":\"21\"},{\"name\":\"Gomer T. De Guzman\",\"dob\":\"1976-09-12\",\"age\":\"49\"},{\"name\":\"Anna Marie C. De Guzman\",\"dob\":\"1974-11-24\",\"age\":\"51\"},{\"name\":\"Imelda C. Caguimbal\",\"dob\":\"1953-01-05\",\"age\":\"73\"},{\"name\":\"Dionisa De Guzman\",\"dob\":\"1959-02-05\",\"age\":\"67\"},{\"name\":\"May Angela D. Vergara\",\"dob\":\"1997-01-05\",\"age\":\"29\"},{\"name\":\"Keian Adriel D. Vergara\",\"dob\":\"2012-09-02\",\"age\":\"13\"},{\"name\":\"Whena Marquez\",\"dob\":\"1970-02-05\",\"age\":\"56\"},{\"name\":\"Fe Igaya\",\"dob\":\"1972-02-06\",\"age\":\"54\"},{\"name\":\"Nariyah Isabella D. Vergara\",\"dob\":\"2019-01-05\",\"age\":\"7\"},{\"name\":\"Michael Jackson\",\"dob\":\"1958-08-25\",\"age\":\"67\"},{\"name\":\"Stephen Curry\",\"dob\":\"1988-03-14\",\"age\":\"38\"},{\"name\":\"Kyrie Irving\",\"dob\":\"1991-09-08\",\"age\":\"34\"}]', 'confirmed', 'unpaid', NULL, NULL, 0.00, '4 more fans', '\n--- 2026-03-16 14:28:40 (Pencil booked) ---\nsubmitted signed docus\n--- 2026-03-16 14:29:02 (Confirmed) ---\nsigned by the dean', 0, '', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA3AAAADICAYAAACpmwNSAAAQAElEQVR4AezdebxX877H8c+qk07SuYZMFRENNCcRRSVHylBCQtlRJOXKVESqQzjSraiujkvdQ5HKEEcqDWjQQOXskiJDkpLbiUTC7f1lrfPb2967PfyGtX6/l4f12981f7/P5Q+fx/f7/XxL/cI/CCCAAAIIIIAAAggggAACkRAoZfyDQLEFuBEBBBBAAAEEEEAAAQSSKUAAl0xt3oUAAv8WoIQAAggggAACCCBQZAECuCKTcQMCCCCAQKoFeD8CCCCAAAKZKkAAl6lfnnYjgAACCCCQmQK0GgEEEIi0AAFcpD8flUcAAQQQQAABBBBIngBvQiD1AgRwqf8G1AABBBBAAAEEEEAAAQTSXSBO7SOAixMkj0EAAQQQQAABBBBAAAEEEi1AAJdo4XA+n1ohgAACCCCAAAIIIIBABAUI4CL40agyAqkV4O0IIIAAAggggAACqRIggEuVPO9FAAEEMlGANiOAAAIIIIBAiQQI4ErEx80IIIAAAgggkCwB3oMAAgggYEYAx38FCCCAAAIIIIAAAukuQPsQSBsBAri0+ZQ0BAEEEEAAAQQQQAABBOIvEK4nEsCF63tQGwQQQAABBBBAAAEEEEAgXwECuHxpwnmCWiGAAAIIIIAAAggggEDmChDAZe63p+WZJ0CLEUAAAQQQQAABBCIuQAAX8Q9I9RFAAIHkCPAWBBBAAAEEEAiDAAFcGL4CdUAAAQQQQCCdBWgbAggggEDcBAjg4kbJgxBAAAEEEEAAAQTiLcDzEEAgpwABXE4P9hBAAAEEEEAAAQQQQCA9BNKyFQRwaflZaRQCCCCAAAIIIIAAAgikowABXLK+Ku9BAAEEEEAAAQQQQAABBEooQABXQkBuRyAZArwDAQQQQAABBBBAAAEJEMBJgQ0BBBBIXwFahgACCCCAAAJpJEAAl0Yfk6YggAACCCAQXwGehgACCCAQNgECuLB9EeqDAAIIIIAAAgikgwBtQACBhAgQwCWElYcigAACCCCAAAIIIIBAcQW4L38BArj8bTiDAAIIIIAAAggggAACCIRKgABun5+DCxBAAAEEEEAAAQQQQACBcAgQwIXjO1CLdBWgXQgggAACCCCAAAIIxFGAAC6OmDwKAQQQiKcAz0IAAQQQQAABBHILEMDlFmEfAQQQQACB6AvQAgQQQACBNBUggEvTD0uzEEAAAQQQQACB4glwFwIIhFmAAC7MX4e6IYAAAggggAACCCAQJQHqmnABAriEE/MCBBBAAAEEEEAAAQQQQCA+AukcwMVHiKcggAACCCCAAAIIIIAAAiERIIALyYegGmEToD4IIIAAAggggAACCIRPgAAufN+EGiGAQNQFqD8CCCCAAAIIIJAgAQK4BMHyWAQQQAABBIojwD0IIIAAAggUJEAAV5AO5xBAAAEEEEAAgegIUFMEEMgAAQK4DPjINBEBBBBAAAEEEEAAgYIFOBsVAQK4qHwp6okAAggggAACCCCAAAIZLxDKAC7jvwoACCCAAAIIIIAAAggggEAeAgRweaBwKNICVB4BBBBAAAEEEEAAgbQVIIBL209LwxBAoOgC3IEAAggggAACCIRbgAAu3N+H2iGAAAIIREWAeiKAAAIIIJAEAQK4JCDzCgQQQAABBBBAoCABziGAAAKFFSCAK6wU1yGAAAIIIIAAAgggED4BapRhAgRwGfbBaS4CCCCAAAIIIIAAAghEVyC+AVx0Hag5AggggAACCCCAAAIIIBB6AQK40H+izKkgLUUAAQQQQAABBBBAAIGCBQjgCvbhLAIIREOAWiKAAAIIIIAAAhkhQACXEZ+ZRiKAAAII5C/AGQQQQAABBKIjQAAXnW9FTRFAAAEEEEAgbALUBwEEEEiyAAFcksF5HQIIIIAAAggggAACEmBDoDgCBHDFUeMeBBBAAAEEEEAAAQQQQCAFAr8FcCl4M69EAAEEEEAAAQQQQAABBBAokgABXJG4uDhPAQ4igAACCCCAAAIIIIBAUgQI4JLCzEsQQCA/AY4jgAACCCCAAAIIFF6AAK7wVlyJAAIIIBAuAWqDAAIIIIBAxgkQwGXcJ6fBCCCAAAIIIGCGAQIIIBBNAQK4aH43al1MgSFDhlizZs3cpnIxH8NtCCCAAAIIIJDJArQdgRQKEMClEJ9XJ1dgypQpNmLECFu9erXbVNax5NaCtyGAAAIIIIAAAghkskBJ204AV1JB7o+MQPXq1a1MmTJBfVXWseAABQQQQAABBBBAAAEEQi5AABfyD5TY6mXW0+vXr2/r16+3BQsWuE1lHcssBVqLAAIIIIAAAgggEGUBArgofz3qXmSBChUq2AknnOA2lYv8AG74twAlBBBAAAEEEEAAgaQLEMAlnZwXIoAAAggggAACCCCAAALFEyCAK54bdyGAAAIIIIBAagR4KwIIIJDRAgRwGf35aTwCCCCAAAIIIJBJArQVgegLEMBF/xvSAgQQyCXwxRdfWNu2ba1JkyZ2ww03uM1f/8//26tXLzvzzDOtXbt2tnv37lxPYBcBBBBAAAEEEMglEJJdAriQfAiqgQACJRP46aefbPr06XbppZdanTp1bPHixS7r6KRJk0ybv/6f//eZZ56x9957zxYtWmQNGjSwH374oWQV4G4EEEAAAQQQQCAJAgRwSUBOwCt4JAII/Cag3rb777/f6tWrZ1dddZXNnj3bfvnll9/OFu7P5s2brVGjRgRxhePiKgQQQAABBBBIoQABXArxeTUCqRGI/lvV2/bqq6/aZZddZnXr1rWHHnrIFMj5LdP6fkOHDnW9aytWrLAVezd//T//r4498MAD5nmeu0335zWccsOGDW6YZbVq1axGjRpu0zBMDc0cMmSIu5cfBBBAAAEEEEAgWQIEcMmS5j0IpInAOeeckyOIad68uV100UX2888/J7yFW7ZssQ4dOtjRRx9tV1xxhc2cOTN47x//+EfLysqy119/3ebOnWs9e/a0mjVrumt1vb/+n/9Xx6699lobOXJkEMS98847dtJJJ7k5c23atHH3a1/DLLdv325fffWV2zQMU8MyR4wYYVOmTEl4u0P1AiqDAAIIIIAAAikVIIBLKT8vRyBaArfddpstXbo0RxCTnZ1t8+bNs9NOO80dT0SL1q9fb71797batWvb/PnzbdeuXcFrFJANHz7c1q1bZ/rbsGHD4FxhCldeeaU9/fTTVqZMGXf5559/7ubMLVmyxLZu3eqO5fez3377WfXq1fM7zXEEEMglwC4CCCCAQMkFCOBKbsgTEMgYAfVA5dfYDz74wE4//XTTEMX8rinqcQVrnTp1ctkkJ06caBo6qWd4nmft27d38930vqysLCtfvrxOFWtTb5uGU+Z1s3ocH3/8cVOQqh4/bXqnhmAqaNRwzbzu4xgCCCCAQFwFeBgCCPwmQAD3GwR/EECgYIEff/zRDU30r1Jvl4IYDVlU75uOq8fq/PPPd8Mci5uaX0Ha1KlTrWXLlu45s2bN0qPdduihh7rhjcoe+cQTT7jEI+5EHH66devmslL6wdkrr7xiCko1VFJDRJUkpXHjxqZNvX4aglmhQoU4vJlHIIAAAggggEBiBdLr6QRw6fU9aQ0CCRO49dZbbceOHe75SuKhXi8FMRqy+NJLL1m/fv2CuWTqObvggguKtL7azp07bezYsabn9ejRw1auXOnepR8NU9RcNQVuf/nLX6xSpUo6HPetcuXK5gdnTZs2tYoVK8b9HTwQAQQQQAABBBAoiQABXEn0inEvtyAQRYG1a9e6eWGqu+d5dt9996kYbKVKlXIB3N/+9rcgiNMcMgV6Ba2v9umnn7pEJOrhOvbYY23AgAG2cePG4LkKoiZMmODWdOvSpYtpzllwspgFP6uketJ69erlevSUUVJ19Tc/26T+KhFKXtkpi/l6bkMAAQQQQAABBEokQABXIj5uRiCpAil5mYY0Klvjnj173PtvueUWl7rf7eT6USD27LPPBglBlHykTp06LkhSsKRASEHSWWedZVWqVLEGDRq4pQA0v8x/vud5LqulhmZqGKOGZHrer6n+c72u0LsK2h5++GE788wzXZZJZZX86KOP7JlnnnGBqYZJKrOkv2mun79pWKiuV8+chpEW+qVciAACCCCAAAIIJECAAC4BqDwSgXQS8Icuqk1ac01DJVXOb2vdurWNGTMm6Inbtm2bC5IULCkQUpD07rvv2nfffZfjEZ7nuWBO55Q0REMpc1xQhB0FnVoSQL12xx13nAva1GuoIZhFeEyOS//v//7P/vrXv+Y4Fq0daosAAggggAAC6SBAAJcOX5E2IJAgAQ2d9IMWDV8cN26clS5dep9v69ixo2muWn4X1qpVyy6//HIbPHiwTZ482WbMmGFr1qxxgZ/m1eV3X0HHN2/ebJdeeqnr1dNwTAWS6sFT4OXft//++1vnzp1NAaKWQ1ASFn9T8hJ/U6ZJf+vTp49/u3399ddBmQICGSVAYxFAAAEEQiNAABeaT0FFEAiXgOaunXfeeUEiEvW8aT5YYWupIZPq8VJQ5AdJc+bMsQ8//NAWLlxojz76qCk4UqDVpEkTO+ywwwr76BzXaYijUv1rqObs2bNN8+q+/fbb4BrP86xt27ZurTcNmxw9erQboqmeOQWL/qYhkv6m+XH+1r9//+BZWjYg2KGAAAIIIFAoAS5CAIH4ChDAxdeTpyGQNgJ9+/Y1DX9UgxTk3HjjjSoWaYvN6qhnNGjQwA466KAiPSO/izVHTT14J554oqk37eeff3aXep7n1qMbNGiQPfXUU/bZZ5+5v+eee26xkqCUK1fOFOzp4cuXL9cfNgQQQAABBBBIjgBvyUOAAC4PFA4hkOkCn3/+uT3//PMBw4gRIwo1dDK4IYEFv8dNwzA1P0/z3fzXnX322W4tt+nTp5sCTvW8adikf764fxV46t5du3a5HkSV2RBAAAEEEEAAgVQIEMAVVp3rIi2g4YCLFy82ZTQ86aSTTMP7lDo+96YMidryOq6U8jqne5XNUBkVd+/eHWmX/Cp/5513msx0/sorr7QWLVqomNJNgZuWGahdu3aOHrcyZcrYddddZ2+99ZYpA2Yi1ojTMgl+48ePH+8X+YsAAggggAACCCRdgAAu6eS8MBkCmgc1depUUyCilPVHHXWUmwel+VhKKa+MiEodn3tThkRteR3XkD2d072a26WMiuqZ8QOdgtoVpXPz58839WCpzocccogNHTpUxZRs6l3TsEUF3hoqOXbsWPOXG1CFrrrqKsvOzrb777/fdF7HErHpO/vPLe5cPf9+/iKAAAIIIIAAAiURIIAriR73hkbgyy+/dCno1bumoXX6H+4ePXrYf//3f5vS0sf+T388K63Mh+nUE6d1zjT3zTcaPHiwHXDAAf5uUv5q7bgrrrjClJRE2SQ1LFKBt4I5VaBs2bKm8+px+6//+i+rWLGiDid0O/DAA4PnJ+N9wct+LfCLAAIIIIAAAggEAgRweKDUxQAAEABJREFUAQWFKAqoV0zD6urVq2dK+67etS1btgRN8TzPjj/+eJeyXv+z/9prr7mAzs+KmPuvAgVteR3X8/1zDzzwQLDOmdYb09DKdOiJe+SRR+zjjz92flqHTan+3U6CfzQUVe9SFkhlpHz11Vdt06ZNljubpK5ZtWqVqZ6J7HFLcHN5PAJJFOBVCCCAAALpJkAAl25fNEPa4wdudevWNQ2rU8+Rmu55np188sl22223uflQSlm/ZMkSl7Jew+10rmrVqqaMiHltCiC05T6nY0orr786d+2117q09Jp/pfeq10i9fn4vkY5FbVPikocffthV2/M8GzVqlCsn+kfBm5Yr0Fpw6kn13+d5np1xxhmmbJJKqKLATUsPHHroof4l/EUAAQQQSKQAz0YAgVAKEMCF8rNQqfwEfvnlF2vTpo0pkFLg5vd6aVhdt27d3Hwo9bLdcccdpqF3sUPf8ntmcY+rHmPGjAl64hR8KPNhcZ+X6vs0X1BZFlWP7t27m5KFqJzITd9PvZfLli0LXtO+fXsXlGse4wsvvOCySSppjJYkCC5KckH/3SX5lbwOAQQQQACBSAtQ+cQJEMAlzpYnJ0BAwyXVo+b3dClwU1ZI9c6o9+iII45IwFvzf2THjh1Nqez9K5577jlbu3atvxuZv7kTl9x9990Jr7t6TZXmX72Xepl6MydPnmxPPPGEderUycqXL6/Dodg0fNavyLp16/wifxFAAAEEEEAAgaQLZEAAl3RTXphAge3btwdPVy+cArd7773XUjmsTmn2b731VlcvJUvRcEA/wHQHQ/6jIYxdu3YNaqnAKdGJSxS8aUirEszoxZ7n2bhx46x169baDdWmbKN+Vk5V7PTTT9cfNgQQQAABBBBAICUCBHApYeelxRXYuXNncKuSkyQ8cAveVnChX79+5q8/tm3bNtN8uILvCM/ZIUOG2DfffBNUSEMX1asZHIhzQUlmmjZtaprzpkf/8Y9/tIkTJ9qFF16o3VBtX3/9tWVlZZk/hHL06NGmZSlCVUkqgwACCCCAAAIZJUAAl1GfO/qNjZ2XFVtOdctKly5tAwcODKqhhCBaDDw4EOLCrFmzflc7BVi/O1jCA8oQqiGT6jn96KOP3NM0bFKLb59zzjluP0w/Cto0r1ILiKte6jHs3LmzioXeuBABBBBAAAEEEIi3AAFcvEV5XkIF9D/6++23n2lTOaEvK+LDL730UvvrX/8a3PXyyy/bzz//HOyHsfDmm29a7jldI0aMsC5dusSlusoW2qFDB6tZs6Zpjb7FixcHvVl6geY0Nm/eXMXQbVp2Qj6qmOqvpSNUZkMgSQK8BgEEEEAAgTwFCODyZOFgWAXq16/vAg4FHSqHrZ7K3qh1zFQvDUu8//77VQztFpuARZWUaex8OB0r7jZt2jTT0gtKkOL3YulZnue5AHH58uUuw6SOhW17++23bejQoa5a5cqVc0tGKGGOO8APAgggEHoBKogAAuksQACXzl83TdtWoUIF0xbW5tWpUyeomj9UMDgQooKGNM6ZMydHjeIRcCopitZvUzC7Y8eO4Pnnn3++DR8+3FauXOkydx577LHBuTAVNO9NQazfe6p5b9WqVQtTFakLAggggAACiRPgyaEXIIAL/SeiglETOPnkk4Mqb9q0KSiHraAgJbZO6nE69dRTYw8Vuawhk1qz7Z///Gdwr4IfzbObMGGCSwhSpUqV4FzYCgraWrVqZX6Poea9aV26sNWT+iCAAAIIIIBA5gqEOYDL3K9CyyMtoDXM/OyYWrPu22+/DV17vvjiC8vOzg7q1aZNG+vZs2ewX5zCggULTCn2Y9fBu+6660xp+DX/rTjPTPY9N954oykLp9579NFHG/PeJMGGAAIIIIAAAmESIIAL09egLnEUSO2jNJdMNVAmw2HDhqkYmk29TLEBlTJBPvnkk8Wun3rdtPadhkj6PVfKYjl79mzTkEw9v9gPT+KNCkAnTZrk3uh5nj322GPGvDfHwQ8CCCCAAAIIhEiAAC5EH4OqpI+AAhi/NYcffrhfDMVfZX78/vvvg7qMGjWqWIGKAjc9S8s5LFy40D3P8zzr37+/aeHrRo0auWNR+FHgqeGSCrhV3zFjxtgpp5yiIhsCCCCAAAIIIBAqAQK4UH0OKpMuAkcccUTQlAMPPDAoh6GwZs2aoBoHHXSQachncKAQBT9wq1u3ro0dO9Z+/PFHd1epUqXc/u23324qu4MR+NmzZ49deeWVpuQlqq4WMS+qie5jQ8AX4C8CCCCAAAKJFCCAS6Quz0YgZAIKtpYuXRrU6t577w3K+yr89NNP1rFjRzvhhBNcoPbDDz+4WzTMMCsry2WX1Fp47mCEfu655x7zTZSAZtCgQRGqPVVFAIE0E6A5CCCAwD4FCOD2ScQFCBRdwA8IdGdsWfup3DQnbdeuXa4Kp512mnXu3NmV9/WjXrfmzZvb3LlzTYGcrlfgpt6qVatWueUBKleurMOR2mbMmOGCUVX64IMPtqeeesr+8Ic/aJcNAQQQQACBiAlQ3UwRIIDLlC9NO5MqEDvvLbac1Erk8bLXX389ODpw4MCgXFBByT2UXfL99993l3meZ5dffrkpcFMPnp9x052M0I/W6OvRo4ersYZ8jh8/3qLaFtcIfhBAAAEEEEAgIwQSEsBlhByNRKAAAaWg90/Hlv1jqfi7evVqe++999yry5UrZzfffLPVqFHDzjnnHHcs94963XJnl1Qg98Ybb9ijjz4a6WBHwz8VhO7cudM1W8lYmjVr5sr8IIAAAggggAACYRYggAvz18nMutHqBAk88cQTwZM1jFIBnYI0DfG87bbbgnM6NnjwYDvxxBMtd3bJF1980ZR1Mrg4ooX//M//tA8++MDVvmXLlta3b19X5gcBBBBAAAEEEAi7QKmwV5D6IRB1AT81fSrboWUDClrrbcuWLbZhwwZr27at1apVy0aOHGnKzqg6e55nSuwRjeySqnHB24QJE2zy5MnuoqOOOspiA1t3kB8EEEAAAQQQQCDEAgRwIf44VC26AsuXLw8qn52dHZRTVWjXrp35gaTneTZu3Dhr0aJFUJ2ZM2eaFvdevHixaaFvndAC3Jojprluffr00aHIb/oWCkTVELVv4sSJ9h//8R/aZUPADAMEEEAAAQQiIEAAF4GPRBWjJaAAaNasWUGlW7VqFZRTVTjssMOCV2se28UXX2zly5cPjmlOmL/jeZ5dc801pmDnwQcftChml/TbEvv3X//6l0u+oqUUdPyhhx5Ki+GgagsbAgikXoAaIIAAAskSIIBLljTvyRiBqVOn2meffebaqzT9Z511liun8ueiiy4KXt+lSxfXy/bmm28Gx1Ro3769Ww5g5cqVpuCmYsWKOpw2m5ZB8L+L1qvr2rVr2rSNhiCAAAIIRFqAyiNQJAECuCJxcTECBQuo980foqf1xO64446Cb0jS2UsuucT1qKlXTeWePXvajh07grdr7pvmgmVlZVmVKlWC4+lS6NChg23cuNE1R72RmuPndvhBAAEEEEAAAQQiJpAzgItY5akuAmET0NBEDdVTvRo1ahSqYOjII480be+8845NmTJFVQy2bt26BeV0KyhYnT9/ftCs++67z8qWLRvsU0AAAQQQQAABBKIkQAAXpa8V8rpmevUUKMybNy9gUKr6YCckBS0RcOWVV+aojYZ4astxME129E38jJOlSpWyxx9/3Dp27JgmraMZCCCAAAIIIJCJAgRwmfjVaXPcBU455ZQgNb0CBQ1HPPfcc+P+npI8cNq0adawYUPbvHlzjsdcf/31OfZTtBP31+YO3p555hmLnQsY9xfyQAQQQAABBBBAIAkCBHBJQOYV6S2g3qt169a5RnqeZwoUlBDEHQjBz/r1661p06bWvXt327lzZ44aVapUycKQJTNHpeKwk1fw1rp16zg8mUeEU4BaIYAAAgggkDkCBHCZ861paQIEFCi8++67wZMHDRpkYQkUFERWr17dmjRpYmvXrg3qWK5cuaB8/PHHB+V0KeibxA6bVEAdlm+SLsa0A4G0EqAxCCCAQMQECOAi9sGobjgEvvjiC2vWrFmOYZOjR4+2Pn36hKKCCt7eeOMN27ZtW476KKDbtWtXcCz3fLjgREQLBG8R/XBUGwEEEIioANVGIBUCBHCpUOedkRT46aefbPr06aY1xOrUqWOrV6927fC8X4dNas03dyDFPwpiFLz51dAQTwWXVatWNX+op+bp6ZiyZvrXRf2v2k3PW9S/IvVHAAEEEEAgYwSK3VACuGLTcWOmCChzo3q0jjnmGLvqqqts9uzZ9ssvvwTNf+SRR0IxbFK9gs2bN8/RK/j000/bc88957IvfvLJJ67OnheugNNVqoQ/BG8lBOR2BBBAAAEEEIiMAAFcZD5VAivKo/MUUOA2YMAAq127tqlHKzYByAknnGB33XWXLVy40C6//PI870/Gwdy9gtnZ2e61nvdrkKZMmApuYufpDR48OBQBp6toCX9+/vlnl4SFnrcSQnI7AggggAACCERGgAAuMp+KiiZLwA/c6tata2PHjrUff/zRvdrzPFNPnHrgFixYYDfffLPVqlXLnUv2j+qk9cyqVatWYK+ggrfY4ObRRx+13r17x7W6qXrY119/bVq+YcWKFa4Knvdr0ErCEsfBDwIIIIAAAgikqQABXJp+WJpVdIGtW7faOeecY+pdU+D2ww8/uIeULVvWunTpYgoUtL5bo0aN3PFU/Sh4a9u2rc2dO9e++eaboBqqt98reNlll1nLli1zDKdUNsZU9hYGFY1DYdWqVXbmmWfahx9+GDxtzJgxadOzGDQq/Qu0EAEEEEAAAQSKKEAAV0QwLk8/AfW4aVjhiSeeaEuXLjUNS1QrFbj16tXLFCyMHDnSjjrqKB1O6abgTfPwli9fHtTjkksucfPy/F7BypUr26mnnmorV65013heevVMPfvssy7Q/vzzz1379N3UK9qpUye3zw8CCGSKAO1EAAEEMlOAAC4zvzut3iugwE1z3BQAKEDzA7e9p6xChQqmrI3z5s2zDh062A033OCWDdDSAbFbjRo1guO5y1pjrU2bNqaePT2zpNuWLVvcmm4zZsxwj1KAqWDmscceM79XcMOGDaask+vXr3fX6Ofxxx9Pi56p3bt3u2Gr119/vfm9o1lZWTZnzpyg/WovGwIIIIAAAvsU4AIEIixQKsJ1p+oIFFlg48aNph6rI4880hRwaajknj17fvccDU384IMP3FIBWi5g0qRJQVn7/qYgML+y5mgtWbLEDcnU0MzYzJW/e+E+DkybNs1OPvlk++S3TJL77befGx559tlnB3eqBy42eDv66KPtH//4hwtAg4siWti0aZMpGB4/frxrgYJXfbvhw4ebLNxBfhBAAAEEEEAAgSQIpPoVBHCp/gK8P+EC6lnT+m3t27e3evXq2euvvx704CT85XtfoEyJGpp5zz337N0r2nY5EusAABAASURBVL8KLk866STr3r17MN/N8zzTGm5aMsB/mpYLUPu2b9/uDml+2JtvvumGUroDEf5ZtGiRnXHGGW4OoppRqVIlmzVrljFkUhpsCCCAAAIIIJBpAgRwkf7iVL4gAfWOXXjhhaaeKM0b01IAua8/7rjjbNiwYW4OmVLtr1ixwgUK6s2K3VbsPR6775dnzpxp+ZUVNCrw8N9ZpUoVv1jov6q/hkX6N1SvXt1eeeUVUwZKHdu2bZtLVtKnT59g7p4yT06dOtUNA9U1Ud6UNfOCCy4w9WaqHfLUd6xTp4522RBAAAEEEEAAgYwTIIDLuE+eGQ3+85//bDVr1jT1Qu3atStHo0uXLu3WblPgpZ6xq6++2s2h0pw3BXvalNExdsvrmM43btzYDZHMq3z66afn6CXSvLocFdnHjgIx9T75lymz5FtvveV61ZTMZNSoUa5H0U9WUqpUKRsxYoQNHTrUVPbvy/dviE989913bnmEgQMHusDU8zzr27evaSjpwQcfbPyDAAIIIIAAAghkqgABXKZ++TRutzJHLlu2zPKac9atWzdbs2aNqWdHQVeiGRQg+u+ILfvH8vqrnkMFf7Hrtz3yyCOmNPllypSxl19+2a1/NmjQIPODU8/zbMKECda1a9e8HhmpY+pxbNGihakHUxVX4Dtx4kS7++67Ix+Yqj3pstEOBBBAAAEEEEiNAAFcatx5awIFlIAk9+OrVatmzz//vD388MNWsWLF3KcTtn/44YcHz44tBwdjCgrctJyBAksFmTrleZ5p/bYrrrjC3n//fTv//PNdkPbxxx/rtNs03+3tt9+2du3auf0o/7z22mum4M3PoqlMnvPmzXPLBkS5XdQdAQRyCLCDAAIIIFACAQK4EuBxazgFNGcqtmZdunQxDUVUoBN7PBnlL7/8MnhNbDk4uLegwE3ZI2vVqmW5lzO488473ZDJFnt7pNQrp2Gfe29x/9auXdv1UikwVaDjDkb0RwYa9tq5c+cgWYuC1blz59qxxx4b0VZRbQQQQACB+AvwRAQQIIDjv4G0E9AyAdnZ2S640Rw4BUUaepiKhsb2usWW/bpoTlfDhg1NCVSUrVLHVVcN9dT6ZjfffLNbBmDVqlXBkFD1IGpIpZJ5KKjTPVHdtJ6bekXr169vGvaqdnieZ0OGDHFDQsuXL69DbAgggAACCCCAQMkF0uQJBHBp8iFpRk4BrfOm4Ea9VDnPJHdPyU/8N8aWY5cH2Llzp3+JKXBT8KmgpkGDBi4hyfLly4PzWVlZLkumhlR6nhccj1pB8xM1NFRLJNx3333BXD4lXxk/frz17t07ak2ivggggAACCCCAQFIECOCSwvy7l3AgwwVyLw+gOXrqjVPgph428bz44otuzp7K2vr162fDhw+3/fffX7uR3TRfr1mzZqZkM5s2bXLt8DzPze/TOQ2ddAf5QQABBBBAAAEEEPidAAHc70g4gED8BGIzT/rlvJYH0By9Fi1aBC/WkEpd5x/QPD4FcL/uR/P3ww8/NC2FcO6557pMoH4r1As3b948N2RS6/L5x/mLAAIIIIAAAggg8HsBArjfm3AEgbgJxM57U7lp06aW3/IA/kuV7KRTp06m+WE6dsopp7ieN5WjuClByS233OKSsWjhc78NCtb+/ve/26xZs6xu3br+Yf4mWoDnI4AAAggggECkBQjgIv35qHzYBRSM+XUcNWqUrV271u163r+XB3AHfvvZuHGjnXHGGaagR4eqVq1qkyZNstKlS2s3UpsCUA0JbdSokT355JNuQW414JBDDrEHH3zQZQZNh6UP1CY2BDJFgHYigAACCKRegAAu9d+AGqSxgHrd/Obt2rXLL9pdd91lrVu3DvYXL15sV199tSlxydatW91xzXWbMmWKHXjggW4/Kj+5E5R8++23ruply5a1G2+80WXc7NGjh/3hD39wx/lBAAEEEMgIARqJAAJxEiCAixMkj0EgLwH1nsUe11p0L730kvXt29cNkXz66adNx9q2bWsvvPCC+UsJ6J5hw4aZhhmqHJVNSUjySlBy8cUXu2UCBg0aZAcccEBUmkM9EUAAAQQQQCAUAlQiVoAALlaDMgJxEtAQSC1j8OmnnwZPbNWqlalHTT1rCmg0PLJPnz723nvvBddUqVLFBg4caJorpoQfwYmQF/aVoGTcuHFWuXLlkLeC6iGAAAIIIIAAAuEXIIAr4jficgQkoABNQyCVlESBmnqd/K1SpUpWo0aNHJkWdc+yZcvs2GOPdUMn58yZY7t379Zht6kXTr1xK1eutJtuuskaN27sjof954svvnDtadKkiQs6/foec8wxbt4bCUp8Ef4igAACCCCAAALxESCAi48jT8kQAQ1xVG9S/fr17Z133nFJSdasWWOrV68Otu+//z5PjR07dpg/H0wXeJ5n6onT/Lfnn3/elF7f8zydCv2mOmvOnrJHykHz3lTpgw46yCUoWbJkiWmtOx1jQwABBBBAAAEEEIifAAFc/Cx5UpoLKChRT1n//v0tNiFJUZqtJQE0D0y9bRs2bDAFg+qtK8ozUnWtskqq3jLIa85e165dTT2IJChJ1BfiuQgggAACCCCAgBkBHP8VILAPgc2bN1uLFi2sTZs2lp2dHVzdvHlzmz59ug0ePNjKlSsXHPc8z+655x7TUEr/oAK19evX26uvvuoyMaq37U9/+pN/OtR/t2zZYvfdd59bqy33nD0Nlbz99tvdkgAjRowgQUmovySVy2gBGo8AAgggkDYCBHBp8ylpSLwFlM5/wIABpuGSq1atCh6vwGzChAmu9+zmm292wVpsj9yjjz7qgrdNmza5e5SMREMlDz74YLcflR9llNQwyTp16pjWc9O8P7/u6oWbOHGiLV++3NQjWbNmTf8UfxFAAAEE0kyA5iCAQLgECODC9T2oTQgEfvrpJ7vooousdu3aNnbsWPvxxx+DWmVlZdlrr71mmgNWr149W7duXXCuWrVq9uyzz7p5bf369XPHtdbZnXfe6cpR+NEwSQVmLVu2dHPytLTBnj17XNWVPVMB3dKlS01z9tQj6XmeO8cPAggggAACCCCQhwCHEiBAAJcAVB4ZXQH1Mmlo5Lx588wPXMqWLWtXXXWVTZs2zQ2VVIbI3IHdNddc44YRnn322Xbrrbfa9u3bTf8oM6WWBlA5zJva3a5dO9OQyN69e7u5bH59Vf97773XZdWM4tp0fjv4iwACCCCAAAIIpINA5gRw6fC1aENCBRYsWGBaEuD999937/E8z7p06WJK+a+12i655BLXI6deKl2gwE49cq+//ro99NBDVqZMGZeVctKkSTptnue5+XFuJ6Q/aouGR6o3cdGiRaZ9v6oaJqmkJUpM0qtXL6tQoYJ/ir8IIIAAAggggAACKRIolaL38loEQiOgpQEuvfRSO//8803z3lQxBXJvvPGGKYjRUEGlytfQSp1T4KaARvPihg8fbg0bNtRht67bBRdcEPTc3XLLLS7xhzsZsh+l/VegedJJJ7kEJf7SB57nWefOnd0QUQ2TVLIVz2OYZMg+H9VBAAEEEEAAgQwWIIDL4I9P083+9a9/WatWrWz27NmOw/M8l5RjypQppiCte/fu9s0337hz+lFCEgVuGlJ46KGH6lCw3XTTTUEAWLVqVfPnwQUXhKSg5CQa2nnDDTeYn2jF8zzT0gCa2zd69Gi3EHlIqpsO1aANCCCAAAIIIIBA3AQI4OJGyYOiJrBmzRpTD5sCMtW9VKlS9j//8z+mYZE6rmGTOq7t+OOPt6lTp9qYMWMsd+Cm82vXrjUFfSprUybK0qVLqxia7cMPPzQFoOpVU9v9iqkXTnP+nnrqKatevbp/mL8IIBAKASqBAAIIIIBATgECuJwe7GWIgNZvO+uss+zTTz91La5Vq5Ypu6LmsZ166qn2wQcfuOP6uf76603z45SZUfu5Nw0/bN++fTB0UksLaAhm7utSta913Fq0aGFNmjSxmTNnBtU45phj7Mknn7RZs2aFdqhnUFkKCCCAAAJFF+AOBBBISwECuLT8rDSqIAENFVRWSQVeuk69Uq+++qqNGDHCJS3xM0gqYJs/f76bI6bATtfm3rTEgObOffnll+6UgqI77rjDlVP9o7l948aNc3P01MuoeW+q00EHHWQPPvigLVmyxC688EIdYkMAAQQQQAABBHIIsBNeAQK48H4bapYAgRtvvNEl6NCjPc+zoUOHWs+ePU3B2t///ncdNq13NnLkSDdksm7duu5YXj8K3hQILl++PDgdlqGTCs40DLR///62a9euoH7dunVzSwT06NHDtEZdcIICAggggAACCCCAQCQEIhDARcKRSkZAYO7cuaa0+Kqq53mmYOvll192wdvHH3+sw24oobJPavkAdyCfH/XeaZjkjBkz3BXKTPm///u/dtppp7n9VP0oKYmGSypzZnZ2dlAN1esf//iHacmAAw44IDhOAQEEEEAAAQQQQCBaAgRw0fpe1LaoAr9dv2HDBlPvk4YRKllJnz59TEMdFy5caDqmy9Q7pzXdqlWrpt18t927d1u7du1s/fr17pr99tvPJk+ebOedd57bT8WP6tK7d2/Tem4aLunXoVKlSjZhwgRToKq5ff5x/iKAAAIIIIAAAghEU4AALprfjVoXQUBDCDXPbceOHe4uZVocNWqU+fue57nsk4MGDdrnsEKtBaeet3fffdc9y/M8e+yxx6x58+ZuP9k/8+fPt06dOrkEJRMnTjTNe/PrkJWVZRreqTl6/jH+Fk2AqxFAAAEEEEAAgbAJEMCF7YtQn7gKKOBq2rSprVu3zj1XvWVK+e929v5oeQBlYezQocPevX3/q7XTlI5fV+pZGpKZ7EQgapOWNGjZsqWp3qq/6qNNSxyoJ/HNN980LTKuoZ06zoYAAkkX4IUIIIAAAggkRIAALiGsPDQsAkOGDAmWClCdNPxRf5XA47bbbnPLAzRq1EiH9rktWrTInnvuOXed53k2duxY01wzdyAJPzt37nTvbNiwoSkJycqVK4O3qldRiVe0dp16EmvXrh2co4AAAgggEDUB6osAAgjkL0AAl78NZ9JA4MUXX/xdK7Tm25w5c9wcuPyWB8h909dff20akujPl1MCFPV+5b4uEftfffWVnXvuuaa5eQMGDLCNGzcGr1Hvoua4LV682C2BoF7B4CQFBBBAAAEEEMg8AVqc9gIEcGn/iTOzgeppa9CgQY7et9KlS9utt95qmjdWp06dQsP88MMPdtZZZ9nWrVvdPVo6oHPnzq6cyB8tU6C5evXr17e3337btK/3eZ5nF110kSnhyiuvvGKa4+Z5nk6xIYAAAggggAACCKS5QCIDuDSno3lhFujVq1eO4O2II44w9brdeeedVtheN7VPgaACpE8++US7VrlyZXvggQdcOZE/yhp5yimnmIZDKgmL/64rrrjClEDl8ccfNw2l9I/zFwEEEEAAAQQQQCAzBAjgMuN10Pu/AAAIPUlEQVQ7R7CVxa+yApzYoZMaVrh06VK3xltRnqqMjk2aNLFly5a52zzPs7/97W+WyMQg77//vutR69q1q/lr0+nlrVu3dgHoI488YkcffbQOsSGAAAIIIIAAAghkoAABXAZ+9HRu8nfffWdXX321KVOj385mzZpZ+fLl/d1C/73nnnuCXjwFgePHj7dTTz210PcX5UIFa2effbZbCHzBggXBrUpGMn36dLfOXIMGDYLjFPYhwGkEEEAAAQQQQCBNBQjg0vTDZmqz+vXrZ/5wR9+gRYsWfrHQfz/66CPTEgH+Dco4qaGU/n48/mpo5DPPPGNahkCZMLVmm//cihUrmnrb3njjDdO6c/5x/iKAQOIFeAMCCCCAAAJhFiCAC/PXoW5FElBPlR90/elPfwruPeSQQ4Lyvgrt27c3peRv3Lixbd++3V3ur7fmduLwox62Pn36uPdorp7WbPMf63me60FcsWKFab6b55GcxLfhLwIIIBABAaqIAAIIJFyAAC7hxLwgGQKff/653XDDDe5VnufZNddc48pF+dGyAOrx2rZtW3Cb1ovTUMrgQBEKe/bscb2BCxcutClTppgyVx511FFujpsCTQ339B+nYZ5Dhw512SaHDRtm+++/v3+KvwgggAACCCCQEQI0EoHCCRDAFc6Jq0IsoLXZlOb/22+/dbVUIHf88ce7cmF/evbs6ZYX8K9X0hCt9fbPf/7T6tWr5x/O8VfLC6xZs8aUMfK6664zZY28/PLLrVWrVlazZk077LDDXKbI8847z6699lp77bXXbOfOncEzqlatav3797dVq1bZSy+9ZKpDUesdPIwCAggggAACCCCAQEYI5BnAZUTLaWTaCLRv3962bNni2nP44YfbXXfdZco66Q7s/Ykt793N8e9XX31lTZs2dUlCdKJUqVJu7tvkyZNNwZiCsE8//dRmzpxp3bt3Nw2t1Fw4rSN35JFHuvlpyhj53HPP2bp162zGjBmm4Y/+mnF6Zuym52sNNwV9ypZ5++23W5UqVWIvoYwAAggggAACCCCAQL4CBHD50nCimAJJva3n3p6z2Dlkf/nLX0wZI7Xum1+R2LJ/TEsEjBs3zvWurV271h32PM/69u1rCsaUVEQ9aQqulP3xsssus2nTppmSm2gO26ZNm9w9sT+e55l61c444ww3XPKWW26x4cOHmxKVqI7z58+3zz77zLSG22mnnRZ7K2UEEEAAAQQQQAABBAolQABXKCYuCqOAgjf1lKlu6tnSGm0XX3yxdk1zzVxh74+GQtaoUcM0z0zDKzt16uTWUtPwxe+//37vFb/+q6GYDz/8sL3wwgtuDTb1pMXOU9NVnue54ZEXXHCB3XTTTS5T5CuvvOKGR6o3T71qun/06NE2YMAAy8rKsj//+c+m5QDq1q1r5cqV02PY8hXgBAIIIIAAAggggEBBAgRwBelwLrQCSq0fG7ypl6tjx45BfWOHTWpunIKr1atX26RJk2zWrFmWOzALbvyt4HmeW/hbwygHDx7shlhq2KOGai5atMjGjx9vAwcOdJkiNQTz5JNPNs8jY+RvfPxBIDUCvBUBBBBAAIEMECCAy4CPnG5NVOp9JQ9RuzzPc0MUlXRE+/6moMovF/RXwytz96a99dZb9uWXX7qkJuq9U8p/PV/DHkuXLl3Q4ziHAAIIIBBRAaqNAAIIREWAAC4qX4p6BgJKHuLvKJhTcOXv+38vueQSy87OthEjRli1atX8w8Hftm3bmoJA9crl7k078cQTTcsHBBdTQAABBBBAAAEE8hfgDAJJFSCASyo3L4uHQGyqfQVbeT1TPWiaD3fTTTe5xCP+NRp6qaGQTz31lCljpX+cvwgggAACCCCAAAIIJF+g6G8kgCu6GXeEXEDZIjUnTT1sflUrVapkEyZMsOnTp5uGQvrH+YsAAggggAACCCCAQJQECOCi9LUSXNeoPD42QUlsWdkejznmGLdemxKX+O25+uqrbfny5ab12/xj/EUAAQQQQAABBBBAIIoCBHBR/GoZXmclHvEJlMJfywOoh23ZsmW2Y8cO/5Qdd9xxLnvksGHDrGzZssFxCgkR4KEIIIAAAggggAACSRAggEsCMq+Ir8DmzZuDB27dutWUiCR2PTed7NKliy1cuNDySnCi82wIIBAmAeqCAAIIIIAAAoUVIIArrBTXhUZgX0sE3H333TZy5EgrU6ZMaOpMRRBAAAEEEiTAYxFAAIEMEyCAy7APng7N9ZcImDlzpi1YsMBtKmvT0gF9+/ZNh2bSBgQQQAABBBBIsACPRyCKAgRwUfxq1Nm0Flzjxo3thBNOcJvK2nQcHgQQQAABBBBAAAEEEiyQsscTwKWMnhcjgAACCCCAAAIIIIAAAkUTIIArmlc4r6ZWCCCAAAIIIIAAAgggkBECBHAZ8ZlpJAL5C3AGAQQQQAABBBBAIDoCBHDR+VbUFAEEEAibAPVBAAEEEEAAgSQLEMAlGZzXIYAAAggggIAE2BBAAAEEiiNAAFccNe5BAAEEEEAAAQQQSJ0Ab0YggwUI4DL449N0BBBAAAEEEEAAAQQyTSDq7SWAi/oXpP4IIIAAAggggAACCCCQMQIEcCn91LwcAQQQQAABBBBAAAEEECi8AAFc4a24EoFwCVAbBBBAAAEEEEAAgYwTIIDLuE9OgxFAAAEzDBBAAAEEEEAgmgIEcNH8btQaAQQQQACBVAnwXgQQQACBFAoQwKUQn1cjgAACCCCAAAKZJUBrEUCgpAIEcCUV5H4EEEAAAQQQQAABBBBIvABvcAIEcI6BHwQQQAABBBBAAAEEEEAg/AL/DwAA///qqdBnAAAABklEQVQDALAQLJPnAS/UAAAAAElFTkSuQmCC', 1, NULL, '2026-03-16 00:49:18', '2026-03-16 06:29:02', 0);
INSERT INTO `guest_room_reservations` (`id`, `booking_no`, `user_id`, `guest_name`, `guest_email`, `guest_contact`, `guest_address`, `guest_dob`, `guest_id_type`, `guest_id_number`, `purpose_of_stay`, `check_in_date`, `check_out_date`, `check_in_time`, `check_out_time`, `adults_count`, `children_count`, `total_guests`, `guest_room_id`, `extra_bed_requested`, `extra_beds_count`, `room_price_per_night`, `extra_bed_price_per_night`, `subtotal`, `discount_amount`, `total_amount`, `other_guests`, `status`, `payment_status`, `payment_method`, `payment_date`, `amount_paid`, `special_requests`, `admin_remarks`, `terms_accepted`, `terms_accepted_by`, `terms_accepted_at`, `digital_signature`, `data_privacy_consent`, `created_by`, `created_at`, `updated_at`, `deleted`) VALUES
(8, 'GBK-202603-0003', NULL, 'Geo Mar C. De Guzman', 'geomarc789@gmail.com', '0908754470', 'Caybunga, Balayan, Batangas', '2004-06-02', NULL, NULL, NULL, '2026-03-19', '2026-03-20', '11:00:00', '12:00:00', 9, 0, 9, 5, 0, 0, 8000.00, 0.00, 8000.00, 0.00, 8000.00, '[{\"name\":\"Emerish jem Dela Vega\",\"dob\":\"1999-02-02\",\"age\":\"27\"},{\"name\":\"Greg Tomco\",\"dob\":\"2003-09-08\",\"age\":\"22\"},{\"name\":\"Mark Jerome Cabral\",\"dob\":\"2003-02-01\",\"age\":\"23\"},{\"name\":\"Gomer Adrian De Guzman\",\"dob\":\"2003-03-01\",\"age\":\"23\"},{\"name\":\"Jeremie Azarcon\",\"dob\":\"2004-09-02\",\"age\":\"21\"},{\"name\":\"Anna Marie C. De Guzman\",\"dob\":\"1974-09-10\",\"age\":\"51\"},{\"name\":\"Gomer T. De Guzman\",\"dob\":\"2004-03-17\",\"age\":\"21\"},{\"name\":\"Louse Vitton\",\"dob\":\"2001-08-05\",\"age\":\"24\"}]', 'pending', 'unpaid', NULL, NULL, 0.00, '', NULL, 0, '', NULL, 'data:image/png;base64,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4fP5rFu3bla6dGkrW7asLVu2zJVrpda4a6+91o4cOaJdI1mJY2CFAAIIIIBAyAqEZAAXslpUDAEEEAhxgYMHD9qDDz5okydPdjXVdAFNmzY1jXt766237O+//3bll112mbVp08a1wv3yyy+ujGQljoEVAggggAACIS1AABfSn4fKJUOASxCIWoEdO3ZYxYoV7csvv3QGF110kd14440ueNu7d68rS58+val1rkuXLjZixAg7cOCAKydZiWNghQACCCCAQMgLEMCF/CeiggggEDyB8H3SN998YxUqVLCtW7e6l1Dw9ueff9qmTZvcvlbFixe3xYsXm4I4TRngjY0jWYl0WBBAAAEEEAgPAQK48PhO1BIBBBBIUGDOnDlWrVo127dvnztHAZrmc9Pk3Sq46qqrbOTIka5bZffu3d22yklWIoUALtwKAQQQQACBIAgQwAUBmUcggAACqSXw3nvvWcOGDe2vv/7yP8JrWdO0ABrnpom677zzTtdCpxY4nZgjRw6bMWOGNWrUSLssCCCQxgI8HgEEEEisAAFcYqU4DwEEEAghAc3V9txzz1m7du1M23GrpoyTy5cvt169etmaNWusfPnytm3bNncayUocAysEEEAgUgR4jygTIICLsg/O6yKAQNoL/PTTT6ZWsZ07dyarMmpte/jhh01ZJePe4Prrr7dx48bZ9OnTLXfu3DZ+/HirWbOmkawkrhT7CCCAAAIIhKdAYAO48DSg1ggggEDQBMaMGWNFihSxypUrW/78+W3SpElJerbGuWkut7lz58a6LlOmTKbEJCtWrLAaNWqYxr9p7re2bdua16WSZCWxyNhBAAEEEEAgLAUI4MLys0VmpXkrBCJdYN26daYkIjHfc8GCBTF3z7m9dOlSK1iwoG3ZsiXWeUpgosCta9eupkBOCUweeughkpXEUmIHAQQQQACByBAggIuM78hbIBDtAiH//uo2Wbdu3VjJRlRpzd2m37iLxrUpUPv4449NgVmBAgXsgQcesGPHjvlPVXfJTz/91HWT1LYO6DmaTmDx4sXatRwkK3EOrBBAAAEEEIgUAQK4SPmSvAcCCISsgMaf1a5d2/bs2ePqqCQi2bNnd9vLli1ziUjUgqZgTS10999/v+XKlctKlChhzZs3t1GjRtmvv/7qztcqXbp0LqhbtWqVaQJulWnRvUhWIomkLpyPAAII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F1RuHhatArz32QKvvfaaNWjQwKXi19Hq1aubxqZde+212k3UcuLECatdu7Yp06S247tI49Q0Hm7q1KmmpCN6hneeN7ectx/zV0GbgjcFcSrX/HMLFy60pk2bapcFAQQQQAABBBBIEwECuDRh56EIRK+AWr0eeeQR69u3rymA8vl81r17d9NcbVmyZEk0jLo/3rdW+FMAABAASURBVHjjjbZo0aJ4r9G0AHrOunXrTN0fNYm3Jt7OnTu3ec/xJt6OeYMDBw64wFLdJtV9UsfUnVPBm4I47Qd54XEIIIAAAggggIBfgADOT8EGAgiktsD69eutfPnypuBLz9J8aRqX1qFDB+0mavn999/dHHEKzhQMxr3I5/NZrVq17KuvvrI33njDrr766rinWLFixVzZli1bYiU7WbJkid11113mJTdRN0nVTwlV1H3SXcQKgbASoLIIIIAAApEmQAAXaV+U90EgBAWUcv/55593qf2VtERVVFbIBQsWWOXKlbWbqEUZH2+55RZTq1p8F2j+uOXLl5u6P950003xneLK1HLnNs6sZs+ebapfr169XOC3e/fuM6XmslQqUcm9997r9lkhgAACUSfACyOAQEgKEMCF5GehUghEjoDmYlMQ9Oabb/pfSmn81SUxT548/rJzbaxcudJuvvlmN83AyZMnzzpV2SDnz59vH374oSnAO+uEOAXquukVff3116b6DR482DQXnaYL6Nmzp+t2qakCvPP4RQABBBBAAIHEC3Bm6gkQwKWeLXdGIKoFFCRpXjeNPYs51swL3rJnz35enyNHjrhWMbWsqetk3AuUeVJTDqhLZtGiReMeTnD/8OHD/mNjx441r3433HCDzZkzx9q1a2c+n89/DhsIIIAAAggggECoCERBABcq1NQDgegR+Pnnn61GjRqmLo9edsiLLrrIhg0bZp9//rkpwcj5NEaNGmXq6ujNDxfzfE03oKQnmoagXLlyMQ8lanvVqlX+8xQkaqdevXq2bNkyK1KkiHZZEEAAAQQQQACBkBQggAvJz0KlQkaAiiRZYPz48VamTBnTWDTv4lKlStnSpUutYcOGXlGCv+pymT9/fuvatasbmxbzxOuuu84U2OlemnYg5rHEbisg/PXXX/2nK5HKmDFj7K233jJt+w+wgQACCCCAAAIIhKAAAVwIfhSqhEA4Cuzdu9cFaG3btrVD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I/DOO++4ZCFKWqIaFCtWzNRlsnTp0tpN1WX69Ok2fvx49wxlm/QSprgCVggggAACCCCAQIQLEMBF+Afm9RBIUCAZB9R1sUmTJta5c2d/98VWrVrZ7NmzTUlLknHLJF3yyy+/2FNPPeWu8fl8NmbMGNO8b66AFQIIIIAAAgggEAUCBHBR8JF5RQQCIbB161ZTcpJp06a522laALWEvfzyy5YhQwZXlporjbFT8Hj48GH3mGeeecZKlSrltlkFX4AnIoAAAggggEDaCBDApY07T0UgrAQUtCl4UxCniufNm9eNfatWrZp2g7Io0+XXX3/tnqXslpos3O2wQgCBcBOgvggggAACKRAggEsBHpciEOkCyvKopCRq+VL3Sb1vnTp1bNGiRaZ517QfjEWB22uvveYepZa/cePGBaXVzz2QFQIIIIBACAlQFQQQIIDj7wACCMQroAQlyvA4evRod1xzrA0aNMhGjRplmTNndmXBWKnLpAJIdaHU815//XXLlSuXNlkQQAABBBBAAIHEC0TImQRwEfIheQ0EAimwePFi0xQBmipA973uuutszpw5prnXtB/MRUlLlLxEz1TrX/369bXJggACCCCAAAIIRKUAAVzafHaeikBICqiVq1+/fla7dm3bv3+/q2OlSpXcFAEad+YKgrh6++23TdMG6JFqdRs8eLA2WRBAAAEEEEAAgagVIICL2k/Pi4evQOrUXAHbgw8+aAMGDLDTp09b+vTprWfPnvbRRx9Z9uzZU+eh57irEqd07drVnaG6qCunxr+5AlYIIIAAAggggECUChDARemH57URiCmwevVq12Vy2bJlrvjyyy+3KVOmWLt27dx+sFeTJk2ypk2bukBSz3744YdNk4VrmyWFAlyOAAIIIIAAAmEtQAAX1p+PyiOQcoERI0aYpgNQ0hLdrUSJEq7LZOnSpbUb9EXZJlu0aGEnT550zy5cuLANGTLEbbNCAIG0FeDpCCCAAAJpL0AAl/bfgBogkCYCmhagUaNG1r17dztx4oSrQ5s2bezf//63XXnllW4/mCsFbG3btrW+ffu6x/p8Pnv++edt4cKF5vP5XBkrBBBAAIGwFaDiCCAQIAECuABBchsEwklg06ZNVq5cOZs1a5ardrZs2dxYt169ermxb64wiKujR4+aMkyOHz/ePfWCCy6wMWPG2LPPPuv2WSGAAAII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q5b98ccf7phS+M+cOdPUfdIVpGClsXNqcevZs6cdPHjQ3SljxozWunVr++abb6xly5aWIUMGV84KAQQQQAABBBBAILIF0kX26/F2SRHg3OQJfPjhh9aoUSNT1kndQYlKPv744xQnD1mzZo1VrFjRnnjiCdu+fbtu7Rbdf8WKFdanTx+76KKLXBkrBBBAAAEEEEAAgegQIICLju/MW6aSwMCBA+2pp54yb841zfk2duxYu+CCC5L9RI2jU4ISBW8K4rwbaWybxtONGzfOAtEt07tvgH65DQIIIIAAAggggEAQBAjggoDMIyJPQHOvdenSxbWCeW/Xo0cPNw+bz+fzipL0u2XLzV685AAABVdJREFUFmvcuLGpu2TMBCXXXnutjR492hYtWuTmekvSTTkZgbAQoJIIIIAAAgggkFgBArjESnEeAv8TOHHihDVp0sQlE1FRunTpbPjw4da+fXvtJnmZP3++1alTx0qUKGHTp0/3X58lSxZTULhq1So3vs7nS15g6L8hGwgggEAkCvBOCCCAQJQJEMBF2QfndVMmcOTIERdseYFW5syZbfz48fbwww8n6cYaL6dskrfffrvVrVvXFixY4L8+U6ZMrqVt3bp1LijUvv8gGwgggAACCCAQMAFuhEA4ChDAheNXo85pIrB3716rXr26LVmyxD3/4osvthkzZliVKlXcfmJWu3btMmWTvO2229x8bt58cbo2X7589sYbb9iPP/7oph+49NJLVcyCAAIIIIAAAgggEHoCaVYjArg0o+fB4STw008/WeXKlW3t2rWu2ldddZXNnTvXihUr5vbPt9I8bRrfpkQkms/Nm25A3S+rVatmmipg6dKl9sgjjxgtbufT5DgCCCCAAAIIIBC9AgRwkfDteYdUFVi+fLlVqlTJFMTpQXny5LF58+aZfrWf0HL8+HGbMGGCVahQwe677z43vu3kyZPudKX/1/xtyjKpLph33323K2eFAAIIIIAAAggggMC5BAjgzqXDsagXWLlypWnetd9++81ZFC9e3JQhUpkhXUE8q3379rlslGpt02Tb3377rf+sf/3rX9a3b1/buHGj+73++uv9x9Jqg+cigAACCCCAAAIIhI8AAVz4fCtqGmSBo0ePWtOmTU1TBujR6i45bdo0u+SSS7R71rJhwwZTwJY/f34XwGnMnHeSWtg++OADU0bJli1bpniSb+++/CKQxgI8HgEEEEAAAQSCLEAAF2RwHhc+Agq0duzY4SqspCPqNqnU/q7gf6tTp065rpHqIlmmTBnXZVJdJ3VYY9keffRRW7FihRvjVrVqVfP5mApANiwIIICAGQYIIIAAAskRIIBLjhrXRLzAoEGDXGCmF9VYNwVv2vaWPXv2WJ8+faxo0aKm5CRKUuIdu/rqq+355583tcgNGTLEbr75Zu8QvwgggAACCCAQCAHugUAUCxDARfHH59XjF1i8eLG9/PLL7mD27Nldq5pa3tTapmONGjUytcgNHDjQfv75Z3eeVhofN3r0aNOYt2effTbBrpY6lwUBBBBAAAEEEEAgbQTC/akEcOH+Bal/QAV++OEH16KmYE0p/seNG2d//fWXvfDCC6akJLVq1bJZs2aZNy5O59SsWdPmz5/v5m7T8QwZMgS0TtwMAQQQQAABBBBAAAFPgADOk0iTXx4aSgJKWtKgQQM7ePCgq1bFihWtZ8+eVqpUKRs6dKjt3LnTlWullrmyZcva+vXrbcyYMa4rpcpZEEAAAQQQQAABBBBITQECuNTU5d5hJaBJtDdv3uzq7PP5XIvaunXr3L5Wl112mTVr1syV//jjjzZ16lTLmTOnDqXNwlMRQAABBBBAAAEEok6AAC7qPjkvHJ/A448/bgsXLvQf8rpIZsyY0c0DpykAlJSkf//+prFu/hPZQCBMBag2AggggAACCISnAAFceH43ah1AAWWcVGtazFuWLFnSlEFy06ZNpnFwmgLgggsuiHkK2wgggEC0CvDeCCCAAAJpKEAAl4b4PDrtBZSQpHfv3v6K5MqVy9asWWMzZ840zeGmsW7+g2wggAACCCCAQAoFuBwBBFIqQACXUkGuD2uB2bNn++tfqFAhF7wpiPMXsoEAAggggAACCCAQGgLUwgkQwDkGVtEqoG6Sd911l91+++22aNGiaGXgvRFAAAEEEEAAAQTCROD/AQAA//8f4YhnAAAABklEQVQDABLjw4IEt/wCAAAAAElFTkSuQmCC', 1, NULL, '2026-03-16 07:53:50', '2026-03-16 07:53:50', 0);

--
-- Triggers `guest_room_reservations`
--
DELIMITER $$
CREATE TRIGGER `trg_guest_reservation_after_insert` AFTER INSERT ON `guest_room_reservations` FOR EACH ROW BEGIN
  DECLARE curr_date DATE;
  SET curr_date = NEW.check_in_date;
  
  WHILE curr_date < NEW.check_out_date DO
    UPDATE guest_room_availability 
    SET booked_quantity = booked_quantity + 1,
        is_available = CASE 
          WHEN (available_quantity - (booked_quantity + 1)) > 0 THEN 1 
          ELSE 0 
        END
    WHERE guest_room_id = NEW.guest_room_id 
      AND date = curr_date;
    
    SET curr_date = DATE_ADD(curr_date, INTERVAL 1 DAY);
  END WHILE;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `hidden_users`
--

CREATE TABLE `hidden_users` (
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hidden_users`
--

INSERT INTO `hidden_users` (`user_id`) VALUES
(25),
(29);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL,
  `incoming_msg_id` int(255) NOT NULL,
  `outgoing_msg_id` int(255) NOT NULL,
  `msg` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `incoming_msg_id`, `outgoing_msg_id`, `msg`) VALUES
(2, 22, 22, 'test'),
(3, 23, 22, 'test'),
(4, 23, 22, 'asdfg'),
(5, 0, 24, 'fff');

-- --------------------------------------------------------

--
-- Table structure for table `miscellaneous_items`
--

CREATE TABLE `miscellaneous_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('equipment','furniture','supplies') DEFAULT 'equipment',
  `has_quantity` tinyint(1) DEFAULT 1,
  `has_specs` tinyint(1) DEFAULT 0,
  `specs_label` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `miscellaneous_items`
--

INSERT INTO `miscellaneous_items` (`id`, `name`, `category`, `has_quantity`, `has_specs`, `specs_label`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'Basic Sound System', 'equipment', 1, 1, 'Speaker and Mic', 1, 1, '2026-02-23 01:25:18'),
(2, 'Round Table', 'furniture', 1, 0, NULL, 1, 2, '2026-02-23 01:25:18'),
(3, 'Banquet Chairs', 'furniture', 1, 0, NULL, 1, 3, '2026-02-23 01:25:18'),
(4, 'View Board', 'equipment', 1, 0, NULL, 1, 4, '2026-02-23 01:25:18'),
(5, 'Projector', 'equipment', 1, 0, NULL, 1, 5, '2026-02-23 01:25:18'),
(6, 'Projector Screen', 'equipment', 1, 0, NULL, 1, 6, '2026-02-23 01:25:18'),
(7, 'Student Chairs', 'furniture', 1, 0, NULL, 1, 7, '2026-02-23 01:25:18'),
(8, 'Student Tables', 'furniture', 1, 0, NULL, 1, 8, '2026-02-23 01:25:18'),
(9, 'Water Dispenser', 'equipment', 1, 0, NULL, 1, 9, '2026-02-23 01:25:18'),
(10, 'Cup and Saucer', 'supplies', 1, 0, NULL, 1, 10, '2026-02-23 01:25:18'),
(11, 'Percolator', 'equipment', 1, 0, NULL, 1, 11, '2026-02-23 01:25:18'),
(12, 'Basic Sound System', 'equipment', 1, 1, 'Speaker and Mic', 1, 1, '2026-02-23 01:39:40'),
(13, 'Round Table', 'furniture', 1, 0, NULL, 1, 2, '2026-02-23 01:39:40'),
(14, 'Banquet Chairs', 'furniture', 1, 0, NULL, 1, 3, '2026-02-23 01:39:40'),
(15, 'View Board', 'equipment', 1, 0, NULL, 1, 4, '2026-02-23 01:39:40'),
(16, 'Projector', 'equipment', 1, 0, NULL, 1, 5, '2026-02-23 01:39:40'),
(17, 'Projector Screen', 'equipment', 1, 0, NULL, 1, 6, '2026-02-23 01:39:40'),
(18, 'Student Chairs', 'furniture', 1, 0, NULL, 1, 7, '2026-02-23 01:39:40'),
(19, 'Student Tables', 'furniture', 1, 0, NULL, 1, 8, '2026-02-23 01:39:40'),
(20, 'Water Dispenser', 'equipment', 1, 0, NULL, 1, 9, '2026-02-23 01:39:40'),
(21, 'Cup and Saucer', 'supplies', 1, 0, NULL, 1, 10, '2026-02-23 01:39:40'),
(22, 'Percolator', 'equipment', 1, 0, NULL, 1, 11, '2026-02-23 01:39:40');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(230, 29, 'Your reservation with Booking No. GM RES-20250125-140 has been submitted successfully.', 1, '2025-01-25 01:33:54'),
(231, 29, 'Your reservation with Booking No. GM RES-20250125-140 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-25 01:34:19'),
(232, 29, 'Your reservation with Booking No. GM RES-20250125-141 has been submitted successfully.', 1, '2025-01-25 02:43:14'),
(233, 29, 'Your reservation with Booking No. GM RES-20250125-142 has been submitted successfully.', 1, '2025-01-25 12:19:14'),
(234, 29, 'Your reservation with Booking No. GM RES-20250125-142 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-25 12:19:36'),
(235, 29, 'Your reservation with Booking No. GM RES-20250125-143 has been submitted successfully.', 1, '2025-01-25 12:48:07'),
(236, 29, 'Your reservation with Booking No. GM RES-20250125-143 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-25 12:48:23'),
(237, 29, 'Your reservation with Booking No. GM RES-20250125-140 has been updated. Status changed to: <span style=\'color: red;\'>Denied</span>', 1, '2025-01-25 17:23:51'),
(238, 29, 'Your reservation with Booking No. GM RES-20250125-140 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-25 17:32:28'),
(239, 29, 'You have a new liability. Click here to view details.', 1, '2025-01-26 12:15:00'),
(240, 29, 'Your reservation with Booking No. GM RES-20250126-144 has been submitted successfully.', 1, '2025-01-26 12:27:47'),
(241, 29, 'You have a new liability. Click here to view details.', 1, '2025-01-26 22:45:15'),
(242, 29, 'Your reservation with Booking No. GM RES-20250127-145 has been submitted successfully.', 1, '2025-01-27 08:05:00'),
(243, 29, 'Your reservation with Booking No. EV RES-20250127-1 has been submitted successfully.', 1, '2025-01-27 12:34:23'),
(244, 29, 'Your reservation with Booking No. EV RES-20250127-1 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-27 12:35:27'),
(245, 29, 'Your reservation with Booking No. EV RES-20250127-1 has been updated. Payment status updated to: <span style=\'color: green;\'>PAID</span>', 1, '2025-01-27 12:35:27'),
(246, 29, 'Your reservation with Booking No. GM RES-20250128-146 has been submitted successfully.', 1, '2025-01-28 04:57:04'),
(247, 34, 'Your reservation with Booking No. EV RES-20250128-81 has been submitted successfully.', 1, '2025-01-28 04:58:34'),
(248, 29, 'Your reservation with Booking No. EV RES-20250128-82 has been submitted successfully.', 1, '2025-01-28 04:59:10'),
(249, 34, 'Your reservation with Booking No. GM RES-20250128-147 has been submitted successfully.', 1, '2025-01-28 05:05:02'),
(250, 34, 'Your reservation with Booking No. GM RES-20250128-147 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-28 05:06:11'),
(251, 34, 'Your reservation with Booking No. GM RES-20250128-147 has been updated. Payment status updated to: <span style=\'color: green;\'>PAID</span>', 1, '2025-01-28 05:06:11'),
(252, 32, 'Your reservation with Booking No. EV RES-20250128-83 has been submitted successfully.', 0, '2025-01-28 09:42:18'),
(253, 30, 'Your reservation with Booking No. EV RES-20250128-84 has been submitted successfully.', 1, '2025-01-28 11:23:25'),
(254, 30, 'Your reservation with Booking No. GM RES-20250128-148 has been submitted successfully.', 1, '2025-01-28 11:32:33'),
(255, 29, 'Your reservation with Booking No. EV RES-20250128-85 has been submitted successfully.', 1, '2025-01-28 11:35:15'),
(256, 29, 'Your reservation with Booking No. GM RES-20250128-149 has been submitted successfully.', 1, '2025-01-28 11:36:47'),
(257, 32, 'Your reservation with Booking No. EV RES-20250128-83 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 0, '2025-01-28 11:39:40'),
(258, 34, 'Your reservation with Booking No. EV RES-20250128-81 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-28 11:44:29'),
(259, 32, 'Your reservation with Booking No. GM RES-20250128-150 has been submitted successfully.', 0, '2025-01-28 11:46:36'),
(260, 32, 'Your reservation with Booking No. GM RES-20250128-150 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 0, '2025-01-28 11:47:21'),
(261, 30, 'Your reservation with Booking No. GM RES-20250128-151 has been submitted successfully.', 1, '2025-01-28 12:17:00'),
(262, 35, 'Your reservation with Booking No. EV RES-20250128-84 has been submitted successfully.', 0, '2025-01-28 12:17:03'),
(263, 35, 'Your reservation with Booking No. EV RES-20250128-84 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 0, '2025-01-28 12:18:47'),
(264, 35, 'Your reservation with Booking No. EV RES-20250128-84 has been updated. Payment status updated to: <span style=\'color: green;\'>PAID</span>', 0, '2025-01-28 12:18:47'),
(265, 30, 'Your reservation with Booking No. EV RES-20250128-87 has been submitted successfully.', 1, '2025-01-28 12:28:42'),
(266, 30, 'Your reservation with Booking No. EV RES-20250128-88 has been submitted successfully.', 1, '2025-01-28 13:18:59'),
(267, 34, 'Your reservation with Booking No. EV RES-20250129-89 has been submitted successfully.', 1, '2025-01-29 00:55:16'),
(268, 34, 'Your reservation with Booking No. EV RES-20250129-89 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 0, '2025-01-29 03:00:44'),
(269, 34, 'You have a new liability. Click here to view details.', 0, '2025-01-29 16:17:29'),
(270, 29, 'Your reservation with Booking No. EV RES-20250130-90 has been submitted successfully.', 1, '2025-01-29 19:05:20'),
(271, 29, 'Your reservation with Booking No. GM RES-20250125-141 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-29 19:23:59'),
(273, 30, 'Your reservation with Booking No. GM RES-20250128-151 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-29 20:29:53'),
(274, 30, 'Your reservation with Booking No. GM RES-20250128-151 has been updated. Payment status updated to: <span style=\'color: green;\'>PAID</span>', 1, '2025-01-29 20:29:53'),
(275, 29, 'Your reservation with Booking No. GM RES-20250130-152 has been submitted successfully.', 1, '2025-01-29 23:47:52'),
(276, 29, 'You have a new liability. Click here to view details.', 1, '2025-01-29 23:49:39'),
(277, 30, 'Your reservation with Booking No. EV RES-20250128-87 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-01-30 12:02:44'),
(278, 30, 'Your reservation with Booking No. EV RES-20250130-90 has been submitted successfully.', 1, '2025-01-30 14:25:29'),
(279, 30, 'Your reservation with Booking No. GM RES-20250130-153 has been submitted successfully.', 1, '2025-01-30 14:27:58'),
(280, 30, 'Your reservation with Booking No. GM RES-20250131-154 has been submitted successfully.', 1, '2025-01-31 04:08:54'),
(281, 34, 'Your reservation with Booking No. EV RES-20250131-92 has been submitted successfully.', 0, '2025-01-31 06:31:14'),
(282, 34, 'Your reservation with Booking No. EV RES-20250131-92 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 0, '2025-01-31 06:32:09'),
(283, 34, 'Your reservation with Booking No. EV RES-20250131-92 has been updated. Payment status updated to: <span style=\'color: green;\'>PAID</span>', 0, '2025-01-31 06:32:09'),
(284, 34, 'Your reservation with Booking No. GM RES-20250131-155 has been submitted successfully.', 0, '2025-01-31 06:33:22'),
(285, 34, 'Your reservation with Booking No. GM RES-20250131-155 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 0, '2025-01-31 06:33:45'),
(286, 34, 'Your reservation with Booking No. GM RES-20250131-155 has been updated. Payment status updated to: <span style=\'color: green;\'>PAID</span>', 0, '2025-01-31 06:33:45'),
(287, 30, 'Your reservation with Booking No. EV RES-20250131-93 has been submitted successfully.', 1, '2025-01-31 08:39:01'),
(288, 30, 'Your reservation with Booking No. GM RES-20250131-156 has been submitted successfully.', 1, '2025-01-31 09:00:29'),
(289, 30, 'Your reservation with Booking No. EV RES-20250131-93 has been updated. Status changed to: <span style=\'color: gray;\'>Canceled</span>', 1, '2025-01-31 09:14:24'),
(290, 29, 'Your reservation with Booking No. EV RES-20250203-94 has been submitted successfully.', 1, '2025-02-03 11:39:52'),
(291, 30, 'Your reservation with Booking No. EV RES-20250203-95 has been submitted successfully.', 1, '2025-02-03 11:41:57'),
(292, 30, 'Your reservation with Booking No. EV RES-20250130-90 has been updated. Status changed to: <span style=\'color: green;\'>Approved</span>', 1, '2025-02-03 15:49:08'),
(293, 29, 'You have a new liability. Click here to view details.', 1, '2025-02-08 00:01:15'),
(294, 34, 'Your reservation with Booking No. GM RES-20250212-157 has been submitted successfully.', 1, '2025-02-12 00:20:55'),
(295, 29, 'Your reservation with Booking No. GM RES-20250220-158 has been submitted successfully.', 1, '2025-02-19 23:36:23'),
(296, 29, 'Your reservation with Booking No. GM RES-20250220-159 has been submitted successfully.', 0, '2025-02-19 23:56:04'),
(297, 30, 'Your reservation with Booking No. EV RES-20250224-0001 has been submitted successfully.', 1, '2025-02-24 07:25:26'),
(298, 30, 'Your reservation with Booking No. GM RES-20250224-160 has been submitted successfully.', 1, '2025-02-24 07:27:29'),
(299, 34, 'Your reservation with Booking No. GM RES-20250224-161 has been submitted successfully.', 0, '2025-02-24 07:41:11'),
(300, 34, 'Your reservation with Booking No. EV RES-20250224-0002 has been submitted successfully.', 0, '2025-02-24 07:43:06'),
(301, 34, 'Your reservation with Booking No. GM RES-20250224-162 has been submitted successfully.', 0, '2025-02-24 07:50:47'),
(302, 30, 'Your reservation with Booking No. GM RES-20250224-163 has been submitted successfully.', 1, '2025-02-24 08:33:41'),
(303, 30, 'Your reservation with Booking No. EV RES-20250224-0003 has been submitted successfully.', 1, '2025-02-24 08:36:28'),
(304, 30, 'Your reservation with Booking No. GM RES-20250224-164 has been submitted successfully.', 1, '2025-02-24 09:18:42'),
(305, 34, 'Your reservation with Booking No. GM RES-20250224-165 has been submitted successfully.', 0, '2025-02-24 09:31:30'),
(306, 34, 'Your reservation with Booking No. GM RES-20250224-166 has been submitted successfully.', 0, '2025-02-24 09:32:52'),
(307, 34, 'Your reservation with Booking No. GM RES-20250224-167 has been submitted successfully.', 0, '2025-02-24 09:34:11'),
(308, 34, 'Your reservation with Booking No. GM RES-20250224-168 has been submitted successfully.', 0, '2025-02-24 09:35:56'),
(309, 34, 'Your reservation with Booking No. GM RES-20250224-169 has been submitted successfully.', 0, '2025-02-24 09:36:37'),
(310, 34, 'Your reservation with Booking No. GM RES-20250224-170 has been submitted successfully.', 0, '2025-02-24 09:37:38'),
(311, 34, 'Your reservation with Booking No. EV RES-20250224-0004 has been submitted successfully.', 0, '2025-02-24 09:38:49'),
(312, 34, 'Your reservation with Booking No. EV RES-20250224-0005 has been submitted successfully.', 0, '2025-02-24 09:39:55'),
(313, 34, 'Your reservation with Booking No. EV RES-20250224-0006 has been submitted successfully.', 0, '2025-02-24 09:40:58'),
(314, 34, 'Your reservation with Booking No. EV RES-20250224-0007 has been submitted successfully.', 1, '2025-02-24 10:17:16'),
(315, 34, 'Your reservation with Booking No. EV RES-20250224-0008 has been submitted successfully.', 1, '2025-02-24 10:18:03'),
(316, 34, 'Your reservation with Booking No. EV RES-20250224-0009 has been submitted successfully.', 1, '2025-02-24 10:18:47'),
(317, 29, 'Your reservation with Booking No. EV RES-20250225-0010 has been submitted successfully.', 0, '2025-02-25 00:10:16'),
(318, 29, 'Your reservation with Booking No. EV RES-20250225-0010 has been submitted successfully.', 0, '2025-02-25 00:10:17'),
(319, 29, 'Your reservation with Booking No. EV RES-20250225-0011 has been submitted successfully.', 0, '2025-02-25 01:13:15'),
(320, 30, 'Your reservation with Booking No. EV RES-20250227-0012 has been submitted successfully.', 1, '2025-02-27 03:50:03'),
(321, 34, 'Your reservation with Booking No. EV RES-20250227-0013 has been submitted successfully.', 1, '2025-02-27 03:51:24'),
(322, 30, 'Your reservation with Booking No. GM RES-20250227-171 has been submitted successfully.', 1, '2025-02-27 03:55:58'),
(323, 34, 'Your reservation with Booking No. GM RES-20250227-172 has been submitted successfully.', 1, '2025-02-27 04:22:15'),
(324, 43, 'Your reservation with Booking No. EV RES-20250227-0014 has been submitted successfully.', 1, '2025-02-27 05:08:52'),
(325, 43, 'Your reservation with Booking No. GM RES-20250227-173 has been submitted successfully.', 1, '2025-02-27 05:10:08'),
(326, 43, 'Your reservation with Booking No. EV RES-20250227-0015 has been submitted successfully.', 0, '2025-02-27 05:29:14'),
(327, 44, 'Your reservation with Booking No. GM RES-20250227-174 has been submitted successfully.', 0, '2025-02-27 05:32:09'),
(328, 44, 'Your reservation with Booking No. GM RES-20250227-175 has been submitted successfully.', 0, '2025-02-27 05:32:59'),
(329, 44, 'Your reservation with Booking No. EV RES-20250227-0016 has been submitted successfully.', 0, '2025-02-27 05:36:06'),
(330, 44, 'Your reservation with Booking No. EV RES-20250227-0017 has been submitted successfully.', 0, '2025-02-27 06:07:04'),
(331, 44, 'Your reservation with Booking No. EV RES-20250227-0018 has been submitted successfully.', 0, '2025-02-27 06:26:40'),
(332, 30, 'Your reservation with Booking No. EV RES-20250227-0019 has been submitted successfully.', 1, '2025-02-27 08:08:57'),
(333, 30, 'Your reservation with Booking No. GM RES-20250227-176 has been submitted successfully.', 1, '2025-02-27 08:09:36'),
(334, 44, 'Your reservation with Booking No. GM RES-20250303-177 has been submitted successfully.', 0, '2025-03-03 06:54:26'),
(335, 44, 'Your reservation with Booking No. EV RES-20250303-0020 has been submitted successfully.', 0, '2025-03-03 06:58:33'),
(336, 30, 'Your reservation with Booking No. EV RES-20250303-0021 has been submitted successfully.', 1, '2025-03-03 07:00:42'),
(337, 44, 'Your reservation with Booking No. EV RES-20250303-0022 has been submitted successfully.', 1, '2025-03-03 07:19:45'),
(338, 30, 'Your reservation with Booking No. EV RES-20250304-0023 has been submitted successfully.', 1, '2025-03-04 03:34:07'),
(339, 29, 'Your reservation with Booking No. GM RES-20250309-178 has been submitted successfully.', 0, '2025-03-09 10:12:59'),
(340, 29, 'Your reservation with Booking No. GM RES-20250309-179 has been submitted successfully.', 0, '2025-03-09 10:19:40'),
(341, 29, 'Your reservation with Booking No. EV RES-20250309-0024 has been submitted successfully.', 0, '2025-03-09 11:58:21'),
(342, 30, 'Your reservation with Booking No. EV RES-20250309-0024 has been submitted successfully.', 1, '2025-03-09 11:58:48'),
(343, 29, 'Your reservation with Booking No. GM RES-20250309-180 has been submitted successfully.', 0, '2025-03-09 12:20:11'),
(344, 30, 'Your reservation with Booking No. EV RES-20250314-0025 has been submitted successfully.', 1, '2025-03-14 08:12:06'),
(345, 30, 'Your reservation with Booking No. GM RES-20250314-181 has been submitted successfully.', 1, '2025-03-14 08:13:51'),
(346, 43, 'Your reservation with Booking No. EV RES-20250315-0026 has been submitted successfully.', 0, '2025-03-15 08:01:43'),
(347, 43, 'Your reservation with Booking No. GM RES-20250315-182 has been submitted successfully.', 1, '2025-03-15 08:03:29'),
(348, 34, 'Your reservation with Booking No. GM RES-20250315-183 has been submitted successfully.', 0, '2025-03-15 08:05:16'),
(349, 34, 'Your reservation with Booking No. GM RES-20250315-184 has been submitted successfully.', 0, '2025-03-15 08:08:19'),
(350, 29, 'Your reservation with Booking No. EV RES-20250315-0027 has been submitted successfully.', 0, '2025-03-15 08:10:45'),
(351, 30, 'You have a new liability. Click here to view details.', 1, '2025-03-15 08:56:13'),
(352, 44, 'You have a new liability. Click here to view details.', 0, '2025-03-15 08:57:18'),
(353, 30, 'You have a new liability. Click here to view details.', 1, '2025-03-15 08:57:46'),
(354, 44, 'Your reservation with Booking No. EV RES-20250316-0028 has been submitted successfully.', 0, '2025-03-16 08:34:20');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` int(11) NOT NULL,
  `office_type_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `office_type_id`, `name`, `created_at`) VALUES
(1, 1, 'College of Accountancy, Business, Economics and International Hospitality Management', '2026-02-23 01:17:54'),
(2, 1, 'College of Arts and Sciences', '2026-02-23 01:17:54'),
(3, 1, 'College of Criminal Justice Education', '2026-02-23 01:17:54'),
(4, 1, 'College of Health and Sciences', '2026-02-23 01:17:54'),
(5, 1, 'College of Informatics and Computing Sciences', '2026-02-23 01:17:54'),
(6, 1, 'College of Teacher Education', '2026-02-23 01:17:54'),
(7, 1, 'Laboratory School', '2026-02-23 01:17:54'),
(8, 2, 'Accounting', '2026-02-23 01:17:54'),
(9, 2, 'Bids and Awards Committee', '2026-02-23 01:17:54'),
(10, 2, 'Budget', '2026-02-23 01:17:54'),
(11, 2, 'Cashiering', '2026-02-23 01:17:54'),
(12, 2, 'Commission on Audit', '2026-02-23 01:17:54'),
(13, 2, 'Culture and Arts', '2026-02-23 01:17:54'),
(14, 2, 'Environmental Management Unit', '2026-02-23 01:17:54'),
(15, 2, 'ETEEAP', '2026-02-23 01:17:54'),
(16, 2, 'Extension Services', '2026-02-23 01:17:54'),
(17, 2, 'External Affairs', '2026-02-23 01:17:54'),
(18, 2, 'Gender and Development', '2026-02-23 01:17:54'),
(19, 2, 'General Education', '2026-02-23 01:17:54'),
(20, 2, 'General Services', '2026-02-23 01:17:54'),
(21, 2, 'Guidance and Counseling', '2026-02-23 01:17:54'),
(22, 2, 'Health Services', '2026-02-23 01:17:54'),
(23, 2, 'Human Resource Management', '2026-02-23 01:17:54'),
(24, 2, 'ICT Services', '2026-02-23 01:17:54'),
(25, 2, 'Internal Audit', '2026-02-23 01:17:54'),
(26, 2, 'Job Placement / Public Employment Service Office', '2026-02-23 01:17:54'),
(27, 2, 'Library Services', '2026-02-23 01:17:54'),
(28, 2, 'NSTP', '2026-02-23 01:17:54'),
(29, 2, 'Office of the Chancellor', '2026-02-23 01:17:54'),
(30, 2, 'Office of the Vice Chancellor for Academic Affairs', '2026-02-23 01:17:54'),
(31, 2, 'Office of the Vice Chancellor for Administration and Finance', '2026-02-23 01:17:54'),
(32, 2, 'Office of the Vice Chancellor for Development and External Affairs', '2026-02-23 01:17:54'),
(33, 2, 'Office of the Vice Chancellor for Research, Development and Extension Services', '2026-02-23 01:17:54'),
(34, 2, 'On-the-Job Training', '2026-02-23 01:17:54'),
(35, 2, 'Planning and Development', '2026-02-23 01:17:54'),
(36, 2, 'Procurement', '2026-02-23 01:17:54'),
(37, 2, 'Project and Facility Management', '2026-02-23 01:17:54'),
(38, 2, 'Property and Supply', '2026-02-23 01:17:54'),
(39, 2, 'Quality Assurance Management', '2026-02-23 01:17:54'),
(40, 2, 'Records Management', '2026-02-23 01:17:54'),
(41, 2, 'Registration Services', '2026-02-23 01:17:54'),
(42, 2, 'Research', '2026-02-23 01:17:54'),
(43, 2, 'Resource Generation', '2026-02-23 01:17:54'),
(44, 2, 'Scholarship and Financial Assistance', '2026-02-23 01:17:54'),
(45, 2, 'Security Services', '2026-02-23 01:17:54'),
(46, 2, 'Sports Development Program', '2026-02-23 01:17:54'),
(47, 2, 'Student Discipline', '2026-02-23 01:17:54'),
(48, 2, 'Student Organization', '2026-02-23 01:17:54'),
(49, 2, 'Sustainable Development', '2026-02-23 01:17:54'),
(50, 2, 'Testing and Admission', '2026-02-23 01:17:54'),
(51, 2, 'VIP-Corals', '2026-02-23 01:17:54'),
(52, 3, 'Academic League of Filipino and English Majors (ALFEM)', '2026-02-23 01:17:54'),
(53, 3, 'Alliance of Integrated Mathematics and Sciences Students (AIMSS)', '2026-02-23 01:17:54'),
(54, 3, 'Alliance of Students\' Collegiate Excellence in Nutrition and Dietetics (ASCEND)', '2026-02-23 01:17:54'),
(55, 3, 'Association of Psychology Students (APS)', '2026-02-23 01:17:54'),
(56, 3, 'BatStateU The NEU – Nasugbu Red Cross Youth Council', '2026-02-23 01:17:54'),
(57, 3, 'Christian Youth for the City (CYC)', '2026-02-23 01:17:54'),
(58, 3, 'College of Accountancy, Business, Economics, and International Hospitality Management Student Council (CABEIHM-SC)', '2026-02-23 01:17:54'),
(59, 3, 'College of Arts and Sciences Student Council (CAS SC)', '2026-02-23 01:17:54'),
(60, 3, 'College of Health Sciences Student Council (CHS)', '2026-02-23 01:17:54'),
(61, 3, 'College of Health Sciences – Honor Society', '2026-02-23 01:17:54'),
(63, 3, 'College of Teacher Education Council (CTEC)', '2026-02-23 01:17:54'),
(64, 3, 'Communication Arts Society (CARS)', '2026-02-23 01:17:54'),
(65, 3, 'Criminology Student Organization (CSO)', '2026-02-23 01:17:54'),
(66, 3, 'Fisheries and Aquatic Environmentalist Organization (FAEO)', '2026-02-23 01:17:54'),
(67, 3, 'Hospitality Management Society (HMS)', '2026-02-23 01:17:54'),
(68, 3, 'Junior Financial Executives ARASOF-Nasugbu (JFINEX)', '2026-02-23 01:17:54'),
(69, 3, 'Junior Marketing Association ARASOF-Nasugbu Chapter (JMAANC)', '2026-02-23 01:17:54'),
(70, 3, 'Junior People Management Association of the Philippines of BatStateU ARASOF-Nasugbu (JPMAP)', '2026-02-23 01:17:54'),
(71, 3, 'Junior Philippine Association of Management Accountants (JPAMA)', '2026-02-23 01:17:54'),
(72, 3, 'Junior Philippine Institute of Accountants BatStateU ARASOF-Nasugbu Local Chapter (JPIA)', '2026-02-23 01:17:54'),
(73, 3, 'LabSchool Robotics Club', '2026-02-23 01:17:54'),
(74, 3, 'Leaders of Elementary Aspiring Pedagogues (LEAP)', '2026-02-23 01:17:54'),
(75, 3, 'Peer Councilors\' Group (PCG)', '2026-02-23 01:17:54'),
(76, 3, 'Philippine Association of Food Technologists Inc. NU – Chapter (PAFTI)', '2026-02-23 01:17:54'),
(77, 3, 'Sining at Kultura ng Lahing Batangan (SIKLAB)', '2026-02-23 01:17:54'),
(78, 3, 'Social Science Council (SocSci)', '2026-02-23 01:17:54'),
(79, 3, 'Society of Physical Education and Social Studies Students (SPESSS)', '2026-02-23 01:17:54'),
(80, 3, 'Student Body Organization Elementary (SBO-Elementary)', '2026-02-23 01:17:54'),
(81, 3, 'Student Body Organization High School (SBO-High School)', '2026-02-23 01:17:54'),
(82, 3, 'Supreme Student Council (SSC)', '2026-02-23 01:17:54'),
(83, 3, 'Tourism Management Society (TMS)', '2026-02-23 01:17:54'),
(135, 3, 'College of Informatics and Computer Sciences Student Council (CICS-SC)', '2026-02-23 01:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `office_types`
--

CREATE TABLE `office_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `office_types`
--

INSERT INTO `office_types` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'College', 'Academic colleges and departments', '2026-02-23 01:17:54'),
(2, 'Office', 'University administrative offices', '2026-02-23 01:17:54'),
(3, 'Student Organization', 'Accredited student organizations', '2026-02-23 01:17:54'),
(4, 'External', 'External organizations, companies, or individuals', '2026-02-23 01:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`id`, `name`) VALUES
(1, 'Rectangular Table'),
(2, 'Round Table'),
(3, 'Basic Sound System'),
(4, 'Mono Block Chair'),
(5, 'Projector Screen');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_venues`
--

CREATE TABLE `reservation_venues` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_venues`
--

INSERT INTO `reservation_venues` (`id`, `reservation_id`, `venue_id`, `start_datetime`, `end_datetime`, `created_at`) VALUES
(4, 17, 1, '2026-03-04 07:00:00', '2026-03-04 17:00:00', '2026-02-27 02:08:02'),
(5, 17, 2, '2026-03-04 07:00:00', '2026-03-04 17:00:00', '2026-02-27 02:08:02'),
(6, 17, 3, '2026-03-04 07:00:00', '2026-03-04 17:00:00', '2026-02-27 02:08:02'),
(7, 17, 4, '2026-02-27 07:00:00', '2026-02-27 17:00:00', '2026-02-27 02:08:02'),
(8, 17, 5, '2026-02-27 07:00:00', '2026-02-27 17:00:00', '2026-02-27 02:08:02'),
(9, 18, 1, '2026-02-27 07:00:00', '2026-02-27 17:00:00', '2026-02-27 07:08:47'),
(10, 19, 4, '2026-03-01 07:00:00', '2026-03-01 12:00:00', '2026-02-27 07:11:19'),
(11, 19, 5, '2026-03-01 07:00:00', '2026-03-01 12:00:00', '2026-02-27 07:11:19'),
(12, 20, 1, '2026-03-02 07:00:00', '2026-03-02 13:00:00', '2026-02-27 08:06:13'),
(13, 20, 2, '2026-03-02 07:00:00', '2026-03-02 13:00:00', '2026-02-27 08:06:13'),
(14, 21, 1, '2026-03-02 07:00:00', '2026-03-02 17:00:00', '2026-03-02 05:09:33'),
(15, 21, 2, '2026-03-02 07:00:00', '2026-03-02 17:00:00', '2026-03-02 05:09:33'),
(16, 22, 1, '2026-03-02 07:00:00', '2026-03-02 17:00:00', '2026-03-02 06:19:17'),
(17, 22, 3, '2026-03-02 07:00:00', '2026-03-02 17:00:00', '2026-03-02 06:19:17'),
(18, 22, 5, '2026-03-02 07:00:00', '2026-03-02 17:00:00', '2026-03-02 06:19:17'),
(19, 23, 1, '2026-03-02 21:00:00', '2026-03-02 23:00:00', '2026-03-02 08:03:45'),
(20, 24, 1, '2026-03-05 07:00:00', '2026-03-05 12:00:00', '2026-03-03 06:22:31'),
(21, 24, 2, '2026-03-05 12:00:00', '2026-03-05 16:00:00', '2026-03-03 06:22:31'),
(22, 24, 3, '2026-03-05 16:00:00', '2026-03-05 18:00:00', '2026-03-03 06:22:31'),
(23, 25, 1, '2026-03-06 07:00:00', '2026-03-06 17:00:00', '2026-03-05 00:23:48'),
(24, 25, 2, '2026-03-06 07:00:00', '2026-03-06 17:00:00', '2026-03-05 00:23:48'),
(25, 25, 3, '2026-03-06 07:00:00', '2026-03-06 17:00:00', '2026-03-05 00:23:48'),
(26, 26, 4, '2026-03-11 07:00:00', '2026-03-11 13:00:00', '2026-03-09 02:25:17'),
(27, 26, 5, '2026-03-11 07:00:00', '2026-03-11 13:00:00', '2026-03-09 02:25:17'),
(28, 27, 4, '2026-03-11 14:00:00', '2026-03-11 17:00:00', '2026-03-09 10:33:27'),
(29, 28, 3, '2026-03-16 07:00:00', '2026-03-16 12:00:00', '2026-03-10 02:51:31'),
(30, 29, 1, '2026-03-20 07:00:00', '2026-03-20 12:00:00', '2026-03-18 00:45:20'),
(31, 29, 2, '2026-03-20 07:00:00', '2026-03-20 12:00:00', '2026-03-18 00:45:20'),
(32, 30, 1, '2026-03-20 13:00:00', '2026-03-20 22:00:00', '2026-03-18 01:22:42'),
(33, 30, 2, '2026-03-20 13:00:00', '2026-03-20 22:00:00', '2026-03-18 01:22:42'),
(34, 31, 1, '2026-03-19 13:00:00', '2026-03-19 18:00:00', '2026-03-18 05:25:18'),
(35, 31, 2, '2026-03-19 13:00:00', '2026-03-19 18:00:00', '2026-03-18 05:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL,
  `site_title` varchar(50) NOT NULL,
  `site_about` varchar(550) NOT NULL,
  `shutdown` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`sr_no`, `site_title`, `site_about`, `shutdown`) VALUES
(1, 'BatStateU HOSTEL', 'The BatStateU ARASOF Nasugbu Hostel Reservation System is a web-based platform designed to simplify and streamline the reservation process for the university’s hostel facilities. It caters to both internal clients, such as BatStateU students and faculty, and external clients looking to book accommodations for events. This system ensures a user-friendly experience by automating bookings, providing real-time updates, and promoting efficient hostel management.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `team_details`
--

CREATE TABLE `team_details` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `picture` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_details`
--

INSERT INTO `team_details` (`sr_no`, `name`, `picture`) VALUES
(9, 'Emmanuel Laparan', 'Team_member_67485.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `terms_and_conditions`
--

CREATE TABLE `terms_and_conditions` (
  `id` int(11) NOT NULL,
  `customer_type` varchar(50) NOT NULL COMMENT 'college, student_org, external, office',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `version` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `terms_and_conditions`
--

INSERT INTO `terms_and_conditions` (`id`, `customer_type`, `title`, `content`, `file_name`, `version`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'college', 'CABEIHM Memo No. 3 Series of 2025 - Guidelines for Utilizing the Hostel Function Rooms', 'We would like to remind all colleges of the following rules and regulations in order to assure the proper use and maintenance of the ARASOF-Nasugbu Hostel function rooms:\r\n\r\n1. Reservation Process: The function rooms are available on a first-come, first-served basis, with priority given to events approved by the Office of the Chancellor.\r\n\r\n2. Reservation Form: To ensure appropriate service, all necessary information must be filled out on the hostel reservation form.\r\n\r\n3. Use of Equipment and Supplies: Any requests for tablecloths, napkins, dining utensils, or laboratory tools and equipment must be submitted using a requisition form and coordinated with the Hostel Laboratory Assistant.\r\n\r\n4. Decorations: Event decorations are allowed, but tapes, adhesives, nails, and screws are strictly prohibited to prevent damage. Balloons are not allowed.\r\n\r\n5. Water Dispensers: The organizer is responsible for providing drinking water during the event.\r\n\r\n6. Audio-Visual Equipment: Coordination with ICT or the Audio Room (PFM) is necessary for the use of a view board, projector, microphone, or sound system.\r\n\r\n7. Event Setup and Clean-Up: Facilitators are responsible for setting up chairs and tables prior to the event and cleaning up the function room afterward.\r\n\r\n8. Environmental Sustainability Guidelines:\r\n   • No Disposable Water Bottles – Bring personal tumblers and use refill stations.\r\n   • Buffet-Style Food Only – To minimize waste, only buffet-style meals are allowed.\r\n   • No Single-Use Plastics – Items like disposable food wrappers, containers, cups, and straws are strictly prohibited.\r\n   • CLAYGO Policy – All event participants must clean as they go.\r\n   • No Tarpaulins – The use of tarpaulins in university events is prohibited.\r\n   • Proper Waste Segregation – Waste must be sorted into designated bins.\r\n   • No Laminated Paper Products – Food containers, paper cups, and plates made from laminated paper are discouraged. Bringing personal food containers and tumblers is required for take-out meals.\r\n\r\n9. Post-Event Cleanliness: The function room must be returned to its original, clean, and damage-free condition following its use.\r\n\r\nAll colleges must ensure strict compliance to these guidelines. Failure to comply with these regulations could affect future reservations. For concerns, please reach out to the Hostel Management Office.\r\n\r\nThank you for your cooperation.', 'CABEIHM_Memo_No.3.s.2025.pdf', '2025', 1, '2026-03-02 02:49:23', '2026-03-02 03:20:28'),
(2, 'student_org', 'CABEIHM Memo No. 3 Series of 2025 - Guidelines for Utilizing the Hostel Function Rooms', 'We would like to remind all colleges of the following rules and regulations in order to assure the proper use and maintenance of the ARASOF-Nasugbu Hostel function rooms:\r\n\r\n1. Reservation Process: The function rooms are available on a first-come, first-served basis, with priority given to events approved by the Office of the Chancellor.\r\n\r\n2. Reservation Form: To ensure appropriate service, all necessary information must be filled out on the hostel reservation form.\r\n\r\n3. Use of Equipment and Supplies: Any requests for tablecloths, napkins, dining utensils, or laboratory tools and equipment must be submitted using a requisition form and coordinated with the Hostel Laboratory Assistant.\r\n\r\n4. Decorations: Event decorations are allowed, but tapes, adhesives, nails, and screws are strictly prohibited to prevent damage. Balloons are not allowed.\r\n\r\n5. Water Dispensers: The organizer is responsible for providing drinking water during the event.\r\n\r\n6. Audio-Visual Equipment: Coordination with ICT or the Audio Room (PFM) is necessary for the use of a view board, projector, microphone, or sound system.\r\n\r\n7. Event Setup and Clean-Up: Facilitators are responsible for setting up chairs and tables prior to the event and cleaning up the function room afterward.\r\n\r\n8. Environmental Sustainability Guidelines:\r\n   • No Disposable Water Bottles – Bring personal tumblers and use refill stations.\r\n   • Buffet-Style Food Only – To minimize waste, only buffet-style meals are allowed.\r\n   • No Single-Use Plastics – Items like disposable food wrappers, containers, cups, and straws are strictly prohibited.\r\n   • CLAYGO Policy – All event participants must clean as they go.\r\n   • No Tarpaulins – The use of tarpaulins in university events is prohibited.\r\n   • Proper Waste Segregation – Waste must be sorted into designated bins.\r\n   • No Laminated Paper Products – Food containers, paper cups, and plates made from laminated paper are discouraged. Bringing personal food containers and tumblers is required for take-out meals.\r\n\r\n9. Post-Event Cleanliness: The function room must be returned to its original, clean, and damage-free condition following its use.\r\n\r\nAll colleges must ensure strict compliance to these guidelines. Failure to comply with these regulations could affect future reservations. For concerns, please reach out to the Hostel Management Office.\r\n\r\nThank you for your cooperation.', 'CABEIHM_Memo_No.3.s.2025.pdf', '2025', 1, '2026-03-02 02:49:23', '2026-03-02 03:20:28'),
(3, 'external', 'HOSTEL FUNCTION ROOM AND EVENTS RULES AND GUIDELINES', '1. The function room reservation in the hostel operates on a first-come, first-served basis. We prioritize events with approved letters signed by the Office of the Chancellor.\r\n\r\n2. Make sure to fill out all the necessary information in the hostel reservation form so that we can better assist you with your events.\r\n\r\n3. If the event requires tablecloths, napkins, or even kitchen utensils and other laboratory tools and equipment, the facilitators must fill out the requisition form and coordinate with the Hostel Laboratory Assistant.\r\n\r\n4. Decorations and props are allowed to fit the theme of the event however, the use of tapes and all kinds of adhesives and nails/screws on the wall are not allowed to avoid chipping of paint and or leaving adhesive marks. The use of balloons as décor is not allowed.\r\n\r\n5. If water dispensers are needed for the said event, the person in charge is responsible for providing a gallon of water for the event.\r\n\r\n6. If the event requires the following: View board, Projector, Microphone, or basic sound system, the person in charge is responsible for coordinating with ICT or the Audio Room (PFM) to request the said items and assistance in setting up the equipment.\r\n\r\n7. If students or colleagues are the facilitators of the event, they will be responsible to set up the chairs and tables before the events and clean the function rooms after use.\r\n\r\n8. Environmental Sustainability Guidelines:\r\n   • No Disposable Water Bottles – The use of disposable water bottles is strictly prohibited. All members are encouraged to bring their own tumblers or use the water refill stations available across the campus.\r\n   • Buffet-Style Food Only – To reduce food waste, only buffet-style meals will be allowed during university events. Please take only what you can finish.\r\n   • No Single-Use Plastics or Disposables – The use of single-use plastics, including food wrappers, containers, balloons, paper and plastic cups, straws, plastic stirrers, and similar items, is strictly prohibited.\r\n   • Practice CLAYGO (Clean As You Go) – All event participants must practice the CLAYGO policy to maintain cleanliness and reduce the volume of waste.\r\n   • Prohibition on Tarpaulins – The use of tarpaulins is prohibited in all university activities and events.\r\n   • Proper Waste Segregation – All waste must be properly segregated according to the designated bins for biodegradable, recyclable, and non-recyclable materials.\r\n\r\n9. The use of laminated paper products such as food containers, paper cups, and paper plates is strictly discouraged. \"Bring your food container\" policy shall be implemented for \"take out\" food and bringing of personal sustainable tumbler/mug for water refilling is highly encouraged.\r\n\r\n10. The organizer must ensure that the function room is clean and damage-free after the activity.', 'function_rooms_HOUSE_RULES_2026.pdf', '2026', 1, '2026-03-02 02:49:23', '2026-03-02 03:50:37'),
(4, 'office', 'HOSTEL FUNCTION ROOM AND EVENTS RULES AND GUIDELINES', '1. The function room reservation in the hostel operates on a first-come, first-served basis. We prioritize events with approved letters signed by the Office of the Chancellor.\r\n\r\n2. Make sure to fill out all the necessary information in the hostel reservation form so that we can better assist you with your events.\r\n\r\n3. If the event requires tablecloths, napkins, or even kitchen utensils and other laboratory tools and equipment, the facilitators must fill out the requisition form and coordinate with the Hostel Laboratory Assistant.\r\n\r\n4. Decorations and props are allowed to fit the theme of the event however, the use of tapes and all kinds of adhesives and nails/screws on the wall are not allowed to avoid chipping of paint and or leaving adhesive marks. The use of balloons as décor is not allowed.\r\n\r\n5. If water dispensers are needed for the said event, the person in charge is responsible for providing a gallon of water for the event.\r\n\r\n6. If the event requires the following: View board, Projector, Microphone, or basic sound system, the person in charge is responsible for coordinating with ICT or the Audio Room (PFM) to request the said items and assistance in setting up the equipment.\r\n\r\n7. If students or colleagues are the facilitators of the event, they will be responsible to set up the chairs and tables before the events and clean the function rooms after use.\r\n\r\n8. Environmental Sustainability Guidelines:\r\n   • No Disposable Water Bottles – The use of disposable water bottles is strictly prohibited. All members are encouraged to bring their own tumblers or use the water refill stations available across the campus.\r\n   • Buffet-Style Food Only – To reduce food waste, only buffet-style meals will be allowed during university events. Please take only what you can finish.\r\n   • No Single-Use Plastics or Disposables – The use of single-use plastics, including food wrappers, containers, balloons, paper and plastic cups, straws, plastic stirrers, and similar items, is strictly prohibited.\r\n   • Practice CLAYGO (Clean As You Go) – All event participants must practice the CLAYGO policy to maintain cleanliness and reduce the volume of waste.\r\n   • Prohibition on Tarpaulins – The use of tarpaulins is prohibited in all university activities and events.\r\n   • Proper Waste Segregation – All waste must be properly segregated according to the designated bins for biodegradable, recyclable, and non-recyclable materials.\r\n\r\n9. The use of laminated paper products such as food containers, paper cups, and paper plates is strictly discouraged. \"Bring your food container\" policy shall be implemented for \"take out\" food and bringing of personal sustainable tumbler/mug for water refilling is highly encouraged.\r\n\r\n10. The organizer must ensure that the function room is clean and damage-free after the activity.', 'function_rooms_HOUSE_RULES_2026.pdf', '2026', 1, '2026-03-02 02:49:23', '2026-03-02 03:50:37'),
(5, 'guest', 'HOSTEL ROOM GUIDELINES AND PROHIBITED ACTS', '# HOSTEL ROOM GUIDELINES\r\n\r\n1. BatStateU_Hostel is a non-smoking area.  \r\n2. Standard Check-in time at 2:00 pm and 12:00 noon check out time.  \r\n3. The hostel is located at BatStateU_ARASOF - Nasugbu Campus. Maintaining good relationships with faculty and students must be observed. Be generally mindful by their presence as they move around the building.  \r\n4. Toned-down sounds between 7 AM until 6 PM are observed in consideration for the faculty and students during class hours.  \r\n5. No Curfew administered for all the guests, however perceive not to disturb others upon returning to the Hostel late at night.  \r\n6. Hostel Laundry Service for Php 100.00 per kilogram, inclusive of powder detergent w/ color protection and fabric softener. Housekeeping to assist with laundry provided with laundry bag.  \r\n7. Trash Bins are placed around the Hostel. Proper throwing of trash helps us maintain the cleanliness of the facilities for the guests as well as for the faculty and students.  \r\n8. Turning off the lights and air-conditioning as well as the faucet before leaving the Hostel room will help us conserve energy and water.  \r\n9. BatStateU_Hostel is not liable for any lost or damage of guest’s personal belongings.  \r\n10. Room Keys can be deposited at the reception. Any lost key will be charged accordingly.  \r\n11. Incidental charges will apply for any loss or damages at the Hostel property during the guest’s stay. Settlement must be done before check-out/departure and must be settled through cash.  \r\n12. The management reserves the right to refuse entry/stay to individuals violating Hotel policies and guidelines.  \r\n13. Hostel Housekeeping staff is authorized to enter your room with or without guests inside for a housekeeping operation.  \r\n\r\n---\r\n\r\n# PROHIBITED ACTS\r\n\r\n- Uncooked foods and cooking inside the Hostel room of prohibited.  \r\n- Deadly weapons and illegal drugs are STRICTLY PROHIBITED inside the hostel.  \r\n- Drinking inside the Hostel room is not allowed. Hostel Bar on the ground floor can be used for any alcoholic beverage consumption.  \r\n- Pets are not allowed inside the property.  \r\n- Only registered guests are allowed to stay in the Hostel room.  \r\n\r\nFor further clarification and queries please feel free to contact us at 09287842104 or email us at **hostel.nasugbu@g.batstate-u.edu.ph**.  \r\n\r\nThank you. We look forward in welcoming your group here at the Hostel!', 'hostel_room_guidelines_2026.pdf', '2026', 1, '2026-03-06 08:25:48', '2026-03-06 08:25:48');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `rating` int(11) DEFAULT 5,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_message`
--

CREATE TABLE `user_message` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_message`
--

INSERT INTO `user_message` (`sr_no`, `name`, `email`, `subject`, `message`, `date`, `seen`) VALUES
(17, 'Lucy Johnson', 'lucyjohnson.web@gmail.com', 'Re: Increase traffic to your website', 'Hello team, &quot;bsuarasofhostel.site&quot;\r\n\r\nWe can quickly promote your website We can place your website on top of the Natural Listings on Google, Yahoo and Bing.\r\n\r\nI’m an SEO Expert and I helped over 250 businesses rank on the (1st Page on Google). My rates are very affordable.\r\n\r\nWe can place your website on Google&#039;s 1st page. We will improve your website’s position on Google and get more traffic.\r\n\r\nPlease respond with your phone number, so we can schedule a follow-up call for you ', '2025-01-28', 1),
(19, 'Demetrius Ferrier', 'ferrier.demetrius@googlemail.com', 'Hi bsuarasofhostel.site Administrator.', 'Need a way to get millions of people to view your ad without high expenses? More Info: http://zmhw96.formblastmarketing.top', '2025-01-28', 1),
(20, 'Nitin Chaudhary', 'seo@rankinghat.co', 'Re: Want to attract more clients and customers?', 'Hello there,\r\n\r\nYour website&#039;s design is absolutely brilliant. The visuals really enhance your message and the content compels action. I&#039;ve forwarded it to a few of my contacts who I think could benefit from your services.\r\n\r\nWhen I was looking at your site &quot;www.bsuarasofhostel.site&quot;, though, I noticed some mistakes that you&#039;ve made re: search engine optimization (SEO) which may be leading to a decline in your organic SEO results.\r\n\r\nWould you like to fix it so that you ', '2025-01-29', 1),
(21, 'Anky', 'letsgetuoptimize@gmail.com', 'Re: Increase google organic ranking &amp; SEO', 'Hey team bsuarasofhostel.site,\r\n\r\nI would like to discuss SEO!\r\n\r\nI can help your website to get on first page of Google and increase the number of leads and sales you are getting from your website.\r\n\r\nMay I send you a quote &amp; price list?\r\n\r\nBests Regards,\r\nAnky\r\nLets Get You Optimize\r\nAccounts Manager\r\nwww.letsgetuoptimize.com\r\nPhone No: +1 (949) 508-0277', '2025-02-01', 1),
(22, 'Lucy Gordon', 'lucygordon.mkt@gmail.com', 'Re: Drive more qualified traffic', 'Hello bsuarasofhostel.site,\r\n\r\nLet me start with a question: do you have trouble generating leads and less website traffic?\r\n\r\nShould I send over more in-depth solutions for you to review?\r\n\r\nWell wishes,\r\nLucy Gordon | Digital Marketing Manager\r\n\r\n\r\n\r\nNote: - If you’re not Interested in our Services, send us  &quot;opt-out&quot;', '2025-02-20', 1),
(23, 'Thank you for registering - it was incredible and ', 'xrum003@24red.ru', 'Thank you for registering - it was incredible and pleasant all the best http://bsuarasofhostel.site ladonna cucumber', 'Thank you for registering - it was incredible and pleasant all the best http://yandex.ru ladonna  cucumber', '2025-02-24', 1),
(24, 'RobertNaw', 'aferinohis056@gmail.com', 'Hello    write about     prices', 'Hola, volia saber el seu preu.', '2025-02-24', 1),
(25, 'RaymondNer', 'raymondCeattson@gmail.com', 'A revolutionary system of email delivery.', 'Good afternoon! bsuarasofhostel.tech \r\n \r\nConnect with potential customers in a compliant manner and without hassle with targeted communication. \r\nThis guarantees a secure and lawful approach, ensuring legitimate and transparent outreach. \r\nAs Contact Forms prioritize real messages, emails sent this way are handled differently than bulk emails. \r\nYou have the opportunity test our service at no cost. \r\nWe can dispatch up to 50,000 messages on your behalf. \r\n \r\nThe cost of sending one million mess', '2025-03-25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_reg`
--

CREATE TABLE `user_reg` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` text NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `pincode` int(11) NOT NULL,
  `dob` date NOT NULL,
  `profile` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `token` varchar(200) DEFAULT NULL,
  `t_expired` date DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `client_type` varchar(100) DEFAULT NULL,
  `unique_id` int(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_reg`
--

INSERT INTO `user_reg` (`id`, `name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`, `is_verified`, `token`, `t_expired`, `status`, `datentime`, `client_type`, `unique_id`) VALUES
(23, 'hostel', 'hostel@gmail.com', '', '', 4213, '2024-12-01', 'IMG_53818.jpg', '$2y$10$Q9PcnjZlsIyDHlGku82xiuZpHNwRpM0Dp7nTLE9ujKeEMKO6AoJXK', 1, NULL, NULL, 1, '2024-12-11 09:04:01', 'ADMIN', 23),
(29, 'Emmanuel Laparan', 'erllaparan06@gmail.com', 'BALANOY', '9087547679', 4216, '2000-07-06', 'IMG_62125.jpg', '$2y$10$gHQn5XW1YNXbzU6FdmCdJ.URIBTVDegL/gpv7bXYBqUdEyyDytHPq', 1, 'ea0dde7c3ec60200fbbf88c93a1d5d70', NULL, 1, '2025-01-24 22:06:34', 'internal', 0),
(30, 'Francis Dave Laparan', 'erllaparan07@gmail.com', 'BALANOY', '09129098657', 4216, '1999-01-25', 'IMG_96358.jpg', '$2y$10$wtaatNY7VFarPNPzg9ckOOBeM18cHiYt72h/bbzvEziYTdWAYkwFC', 1, 'fab834a2b867fff3edcb3e93a1b3a0ce', NULL, 1, '2025-01-24 22:09:27', 'external', 0),
(32, 'Francia Masusi', 'franciamasusi8@gmail.com', 'Calatagan, Batangas', '09096071297', 4215, '2002-03-25', 'IMG_95655.jpg', '$2y$10$.3a1S7W/Gxt67DtDPqlCLeQfJlvuaF9NJqdUsoM/BmKkQ./CmAE3K', 1, 'bc14d704ab1ebe6545abafd55f46e118', NULL, 1, '2025-01-27 05:09:55', 'internal', 0),
(34, 'Roica Ellao', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', 4213, '2000-08-12', 'IMG_79466.jpg', '$2y$10$0946gzx30SpsyOBb6u.0wO2nQcLyEP68TnibaaEqhb3km5y3S6g1y', 1, 'b2780c1e671a134251af19b83cd4a544', NULL, 1, '2025-01-27 06:38:44', 'external', 0),
(35, 'Jethro laparan', 'jethrolaparan132@gmail.com', 'Prenza,Lian,Batangas', '09518602538', 4216, '2003-09-16', 'IMG_90211.jpg', '$2y$10$Ypt1ivuIkGxxoY1dJttcjurHhS0ulLWLAc8QO0L0fMidFjExDoJya', 1, '55663288333075ed92a5209a23cc904b', NULL, 1, '2025-01-28 12:07:02', 'internal', 0),
(36, 'Kaito', 'redebrewerteng@gmail.com', 'Lian, Batangas', '09555222255', 4216, '2001-01-13', 'IMG_85754.jpg', '$2y$10$9wjgLqiTtdArVLH.sY8Nx.769I9tgUxvGUiUlz3zr91EHDEUpl1qq', 1, '6c8d1f51c4cf93a76b0f2c33d36206e2', NULL, 1, '2025-01-28 12:23:53', 'internal', 0),
(37, 'Jonathan', 'jonathanlmyr@gmail.com', 'Nothing', '09304257288', 4215, '2003-10-01', 'IMG_33787.jpg', '$2y$10$NOVtzu0nWIR/d.SdBAlZEePXzRNtdcBKO2yJxF0w62CL5Gp1aHfHq', 0, 'd06ba0e0c582279ef1cf1485474b4ba2', NULL, 1, '2025-01-28 12:29:28', 'internal', 0),
(38, 'Joshua', 'izarjoshua829@gmail.com', 'Campo avejar lumbangan nasugbu batangas', '09919463809', 4231, '2006-01-16', 'IMG_44887.jpg', '$2y$10$M20I.iKP2golVktCGGGm/OPH.85yYCfCpsmdB8hDCU4M1W3J3JRCK', 1, 'e54e07de02351a9183833b115952b969', NULL, 1, '2025-01-28 12:31:44', 'external', 0),
(39, 'Ana Soleil Laguerta', 'anasoleil.laguerta@g.batstate-u.edu.ph', 'Brgy. Pob Cuatro Lian Batangas', '09478713546', 4216, '2000-07-05', 'IMG_58965.jpg', '$2y$10$uCqxKe5gB/8vd/Ub7cANoeXA.28bgXJHD0/dzuVK3dXIH5wN8b5KO', 0, '33d06e710a873fa7cc4d78ef877e89cb', NULL, 1, '2025-01-28 15:19:10', 'external', 0),
(40, 'Jade Anne', 'annemontealegre1206@gmail.com', 'San Diego, Lian, Batangas', '0926929834', 4216, '2000-12-06', 'IMG_60245.jpg', '$2y$10$1msoZ.2CuyHKx1uEs9kDs.U44jlIGFzSZbOPXD90JpmwGQx.zK1Wq', 1, 'cfac4e8bdef1487185337496c46ac337', NULL, 1, '2025-01-28 15:22:15', 'external', 0),
(41, 'Boss aloy', 'johncarlodiaz49@gmail.com', 'balanoy prenza lian batangas', '09283318873', 4216, '2000-11-03', 'IMG_83833.jpg', '$2y$10$5hQshhLg1x5aIFf3gYrdSOCsRyORQHofIC10lgx52WyT5INNBA.QO', 0, 'e25ed5ecce36cf9d8286ab592953b462', NULL, 1, '2025-01-28 16:04:18', 'external', 0),
(42, 'James F, Izar', 'james.izar@g.batstate-u.edu.ph', 'Lumbangan Nasugbu Batangas', '09919449203', 4231, '2001-01-28', 'IMG_67629.jpg', '$2y$10$Tgq1/Y0zINkekdLHgo.r3O8JfiOzBnyxNfhu3t.euEQKC8z8TiDWe', 1, 'b3f732c4d65e3ff72114d371e9f9ade2', NULL, 1, '2025-01-28 16:30:26', 'internal', 0),
(43, 'Aaron', 'roicanix@gmail.com', 'Gimalas', '09940677514', 4213, '1999-02-20', 'IMG_83469.jpg', '$2y$10$sUDD5tvi4Yp092rpQnJMheliOTvNcOJ0Vhdt5e7LTvLHvq0EkTNki', 1, '540d3b36a4ff78a1943dbff24943d26a', NULL, 1, '2025-02-27 04:59:15', 'internal', 0),
(44, 'Jack Ender', 'izarjamesf28@gmail.com', 'Lumbangan, Nasugbu, Batangas', '09919463805', 4231, '2001-01-28', 'IMG_45755.jpg', '$2y$10$VtaJRj19tcc6ecxxsJQqeOTCYDwuB/P.RB9/PZ9VdcncEWlCLsY0u', 1, '19f26f9025bad36a49ed3c9b0f4a0472', NULL, 1, '2025-02-27 05:26:22', 'external', 0),
(45, 'gre tomco', 'imgregtomco@gmail.com', 'jan lang sa tabi', '0912345678', 4231, '2002-02-14', 'IMG_95469.jpg', '$2y$10$Q9PcnjZlsIyDHlGku82xiuZpHNwRpM0Dp7nTLE9ujKeEMKO6AoJXK', 1, '03f7f6768f3232b7cbb44d2b0e635ee0', NULL, 1, '2026-02-18 10:14:24', 'internal', 0),
(46, 'greg', '22-78921@g.bastate-u.edu.ph', 'jan lang', '09', 1234, '2026-02-01', 'IMG_95568.jpg', '$2y$10$3rOHFPNXBEFnjib7yKES5eoLIKub8ed5RUiF07/d/CXWp3Jh.g1p.', 0, 'fdc53292c891eda1aac976fde020d50f', NULL, 1, '2026-02-18 10:35:40', 'internal', 0);

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT 0,
  `floor` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `color` varchar(20) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = bookable, 0 = hidden from showcase + reservation form',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Nightly rate for guest rooms / room hire fee for function rooms',
  `extra_bed_available` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = extra beds can be requested for this guest room',
  `extra_bed_price` decimal(10,2) DEFAULT 500.00 COMMENT 'Price per extra bed per night',
  `half_day_rate` decimal(10,2) NOT NULL DEFAULT 2000.00,
  `whole_day_rate` decimal(10,2) NOT NULL DEFAULT 3000.00,
  `extension_rate` decimal(10,2) NOT NULL DEFAULT 400.00,
  `sound_system_fee` decimal(10,2) NOT NULL DEFAULT 1500.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `capacity`, `floor`, `description`, `is_active`, `created_at`, `color`, `is_available`, `price`, `extra_bed_available`, `extra_bed_price`, `half_day_rate`, `whole_day_rate`, `extension_rate`, `sound_system_fee`) VALUES
(1, 'Function Room A', 40, 'Ground Floor', 'Spacious function room for meetings and events.', 1, '2026-02-25 06:38:50', NULL, 1, 5000.00, 0, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(2, 'Function Room B', 40, 'Ground Floor', 'Ideal for seminars and workshops.', 1, '2026-02-25 06:38:50', NULL, 1, 5000.00, 0, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(3, 'Function Room C', 40, 'Ground Floor', 'Largest function room with AV equipment.', 1, '2026-02-25 06:38:50', NULL, 1, 5000.00, 0, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(4, 'Function Room D', 40, 'Ground Floor', 'Small function room for intimate events.', 1, '2026-02-25 06:38:50', NULL, 1, 5000.00, 0, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(5, 'Function Room E', 40, 'Ground Floor', 'Versatile space for training and events.', 1, '2026-02-25 06:38:50', NULL, 1, 5000.00, 0, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(6, 'Guest Room 1', 4, '2nd Floor', 'Comfortable guest room with queen bed.', 1, '2026-02-25 06:38:50', NULL, 1, 5000.00, 1, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(7, 'Guest Room 2', 5, '2nd Floor', 'Guest room with city view.', 1, '2026-02-25 06:38:50', NULL, 1, 2500.00, 1, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(8, 'Guest Room 3', 5, '2nd Floor', 'Spacious guest room for small families.', 1, '2026-02-25 06:38:50', NULL, 1, 2500.00, 1, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(9, 'Guest Room 4', 8, '2nd Floor', 'Cozy room for couples or solo travelers.', 1, '2026-02-25 06:38:50', NULL, 1, 2500.00, 1, 500.00, 2000.00, 3000.00, 400.00, 1500.00),
(10, 'Dormitory', 24, 'Ground Floor', 'Spacious dormitory accommodating up to 24 guests. Ideal for student delegations, sports teams, and group accommodations.', 1, '2026-03-06 06:08:38', NULL, 1, 8000.00, 0, 0.00, 2000.00, 3000.00, 400.00, 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `venue_images`
--

CREATE TABLE `venue_images` (
  `id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue_images`
--

INSERT INTO `venue_images` (`id`, `venue_id`, `image_path`, `is_primary`, `sort_order`, `created_at`) VALUES
(1, 1, 'venue_1_1772592772_929.jpg', 1, 0, '2026-03-04 02:52:52'),
(2, 2, 'venue_2_1772594103_908.jpg', 1, 0, '2026-03-04 03:15:03'),
(3, 3, 'venue_3_1772594181_395.jpg', 1, 0, '2026-03-04 03:16:21'),
(4, 4, 'venue_4_1772594260_120.jpg', 1, 0, '2026-03-04 03:17:40'),
(5, 5, 'venue_5_1772594431_691.jpg', 1, 0, '2026-03-04 03:20:31'),
(8, 6, 'venue_6_1772674450_956.jpeg', 0, 1, '2026-03-05 01:34:10'),
(9, 6, 'venue_6_1772674479_395.jpeg', 0, 2, '2026-03-05 01:34:39'),
(10, 6, 'venue_6_1772674494_112.jpeg', 0, 3, '2026-03-05 01:34:54'),
(11, 6, 'venue_6_1772674500_333.jpeg', 0, 4, '2026-03-05 01:35:00'),
(12, 6, 'venue_6_1772675147_326.jpeg', 1, 0, '2026-03-05 01:45:47'),
(13, 7, 'venue_7_1772677019_647.jpeg', 1, 0, '2026-03-05 02:16:59'),
(14, 7, 'venue_7_1772677081_785.jpeg', 0, 1, '2026-03-05 02:18:01'),
(15, 7, 'venue_7_1772679446_714.jpeg', 0, 2, '2026-03-05 02:57:26'),
(16, 7, 'venue_7_1772679470_610.jpeg', 0, 3, '2026-03-05 02:57:50'),
(17, 7, 'venue_7_1772679595_466.jpeg', 0, 4, '2026-03-05 02:59:55'),
(18, 8, 'venue_8_1773015233_943.jpeg', 0, 0, '2026-03-09 00:13:53'),
(19, 8, 'venue_8_1773015256_866.jpeg', 1, 1, '2026-03-09 00:14:16'),
(20, 8, 'venue_8_1773015271_401.jpeg', 0, 2, '2026-03-09 00:14:31'),
(21, 8, 'venue_8_1773015297_241.jpeg', 0, 3, '2026-03-09 00:14:57'),
(22, 8, 'venue_8_1773015312_134.jpeg', 0, 4, '2026-03-09 00:15:12'),
(28, 9, 'venue_9_1773016823_929.jpeg', 1, 0, '2026-03-09 00:40:23'),
(30, 9, 'venue_9_1773016847_399.jpeg', 0, 2, '2026-03-09 00:40:47'),
(31, 9, 'venue_9_1773016858_538.jpeg', 0, 3, '2026-03-09 00:40:58'),
(32, 9, 'venue_9_1773016870_493.jpeg', 0, 4, '2026-03-09 00:41:10'),
(33, 10, 'venue_10_1773016997_270.png', 0, 0, '2026-03-09 00:43:17'),
(34, 10, 'venue_10_1773017027_362.png', 1, 1, '2026-03-09 00:43:47'),
(35, 10, 'venue_10_1773018827_346.jpeg', 0, 2, '2026-03-09 01:13:47'),
(36, 10, 'venue_10_1773018845_646.jpeg', 0, 3, '2026-03-09 01:14:05');

-- --------------------------------------------------------

--
-- Table structure for table `venue_setups`
--

CREATE TABLE `venue_setups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue_setups`
--

INSERT INTO `venue_setups` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Classroom Style', 'Rows of tables and chairs facing the front', '2026-02-23 01:25:18'),
(2, 'Theater Style', 'Rows of chairs only, no tables', '2026-02-23 01:25:18'),
(3, 'U-Shape', 'Tables arranged in U-shape facing the front', '2026-02-23 01:25:18'),
(4, 'Boardroom Style', 'Large table with chairs around it', '2026-02-23 01:25:18'),
(5, 'Banquet Style', 'Round tables with chairs', '2026-02-23 01:25:18'),
(6, 'Hollow Square', 'Tables arranged in hollow square formation', '2026-02-23 01:25:18'),
(7, 'Herringbone', 'Angled rows facing center', '2026-02-23 01:25:18'),
(8, 'Workshop Style', 'Multiple workstations for hands-on activities', '2026-02-23 01:25:18'),
(9, 'Reception Style', 'Open space with standing tables', '2026-02-23 01:25:18'),
(10, 'Custom Setup', 'Custom arrangement as specified', '2026-02-23 01:25:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `banquet`
--
ALTER TABLE `banquet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_sequences`
--
ALTER TABLE `booking_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_year_month` (`type`,`year`,`month`);

--
-- Indexes for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_details`
--
ALTER TABLE `contact_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `event_types`
--
ALTER TABLE `event_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facility_reservations`
--
ALTER TABLE `facility_reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_no` (`reservation_no`),
  ADD UNIQUE KEY `booking_no` (`booking_no`),
  ADD KEY `fk_fr_office_type` (`office_type_id`),
  ADD KEY `fk_fr_office` (`office_id`),
  ADD KEY `fk_fr_event_type` (`event_type_id`),
  ADD KEY `fk_fr_venue` (`venue_id`),
  ADD KEY `fk_fr_venue_setup` (`venue_setup_id`),
  ADD KEY `fk_fr_banquet_style` (`banquet_style_id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `function_calendar_config`
--
ALTER TABLE `function_calendar_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `function_rooms`
--
ALTER TABLE `function_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_name` (`room_name`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `function_room_availability`
--
ALTER TABLE `function_room_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_date_time` (`function_room_id`,`date`,`start_time`,`end_time`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_reservation` (`reservation_id`);

--
-- Indexes for table `function_room_blocked_dates`
--
ALTER TABLE `function_room_blocked_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `function_room_id` (`function_room_id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `function_room_images`
--
ALTER TABLE `function_room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `function_room_id` (`function_room_id`),
  ADD KEY `is_primary` (`is_primary`);

--
-- Indexes for table `function_room_reservations`
--
ALTER TABLE `function_room_reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_no` (`booking_no`),
  ADD UNIQUE KEY `reservation_no` (`reservation_no`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_function_room_id` (`function_room_id`),
  ADD KEY `idx_event_type` (`event_type_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_event_date` (`event_date`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `function_room_reservations_ibfk_3` (`banquet_style_id`),
  ADD KEY `function_room_reservations_ibfk_4` (`office_type_id`),
  ADD KEY `function_room_reservations_ibfk_5` (`office_id`),
  ADD KEY `function_room_reservations_ibfk_6` (`venue_setup_id`),
  ADD KEY `idx_function_reservation_dates` (`event_date`,`start_time`,`end_time`,`status`),
  ADD KEY `idx_function_reservation_user` (`user_id`,`created_at`);

--
-- Indexes for table `guest_calendar_config`
--
ALTER TABLE `guest_calendar_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `guest_details`
--
ALTER TABLE `guest_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `guest_rooms`
--
ALTER TABLE `guest_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `idx_room_type` (`room_type`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `guest_room_availability`
--
ALTER TABLE `guest_room_availability`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_date` (`guest_room_id`,`date`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_availability` (`is_available`);

--
-- Indexes for table `guest_room_images`
--
ALTER TABLE `guest_room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guest_room_id` (`guest_room_id`),
  ADD KEY `is_primary` (`is_primary`);

--
-- Indexes for table `guest_room_reservations`
--
ALTER TABLE `guest_room_reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_no` (`booking_no`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_guest_room_id` (`guest_room_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_dates` (`check_in_date`,`check_out_date`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_guest_reservation_dates` (`check_in_date`,`check_out_date`,`status`),
  ADD KEY `idx_guest_reservation_user` (`user_id`,`created_at`);

--
-- Indexes for table `hidden_users`
--
ALTER TABLE `hidden_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `miscellaneous_items`
--
ALTER TABLE `miscellaneous_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_office_type_name` (`office_type_id`,`name`),
  ADD KEY `idx_office_type_id` (`office_type_id`),
  ADD KEY `idx_office_name` (`name`);

--
-- Indexes for table `office_types`
--
ALTER TABLE `office_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation_venues`
--
ALTER TABLE `reservation_venues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservation` (`reservation_id`),
  ADD KEY `idx_venue` (`venue_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `team_details`
--
ALTER TABLE `team_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `terms_and_conditions`
--
ALTER TABLE `terms_and_conditions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_customer_type` (`customer_type`,`is_active`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_message`
--
ALTER TABLE `user_message`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `user_reg`
--
ALTER TABLE `user_reg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_venues_name` (`name`);

--
-- Indexes for table `venue_images`
--
ALTER TABLE `venue_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venue_id` (`venue_id`),
  ADD KEY `is_primary` (`is_primary`);

--
-- Indexes for table `venue_setups`
--
ALTER TABLE `venue_setups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `banquet`
--
ALTER TABLE `banquet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `booking_sequences`
--
ALTER TABLE `booking_sequences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `carousel_slides`
--
ALTER TABLE `carousel_slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `contact_details`
--
ALTER TABLE `contact_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `event_types`
--
ALTER TABLE `event_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `facility_reservations`
--
ALTER TABLE `facility_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `function_calendar_config`
--
ALTER TABLE `function_calendar_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `function_rooms`
--
ALTER TABLE `function_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `function_room_availability`
--
ALTER TABLE `function_room_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `function_room_blocked_dates`
--
ALTER TABLE `function_room_blocked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `function_room_images`
--
ALTER TABLE `function_room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `function_room_reservations`
--
ALTER TABLE `function_room_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_calendar_config`
--
ALTER TABLE `guest_calendar_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `guest_details`
--
ALTER TABLE `guest_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_rooms`
--
ALTER TABLE `guest_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `guest_room_availability`
--
ALTER TABLE `guest_room_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=772;

--
-- AUTO_INCREMENT for table `guest_room_images`
--
ALTER TABLE `guest_room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guest_room_reservations`
--
ALTER TABLE `guest_room_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `miscellaneous_items`
--
ALTER TABLE `miscellaneous_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `office_types`
--
ALTER TABLE `office_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reservation_venues`
--
ALTER TABLE `reservation_venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `team_details`
--
ALTER TABLE `team_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `terms_and_conditions`
--
ALTER TABLE `terms_and_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_message`
--
ALTER TABLE `user_message`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_reg`
--
ALTER TABLE `user_reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `venue_images`
--
ALTER TABLE `venue_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `venue_setups`
--
ALTER TABLE `venue_setups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`);

--
-- Constraints for table `facility_reservations`
--
ALTER TABLE `facility_reservations`
  ADD CONSTRAINT `fk_banquet_style` FOREIGN KEY (`banquet_style_id`) REFERENCES `banquet` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_type` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fr_banquet_style` FOREIGN KEY (`banquet_style_id`) REFERENCES `banquet` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fr_event_type` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fr_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fr_office_type` FOREIGN KEY (`office_type_id`) REFERENCES `office_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fr_venue_setup` FOREIGN KEY (`venue_setup_id`) REFERENCES `venue_setups` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_office` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_office_type` FOREIGN KEY (`office_type_id`) REFERENCES `office_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venue` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_venue_setup` FOREIGN KEY (`venue_setup_id`) REFERENCES `venue_setups` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `function_room_availability`
--
ALTER TABLE `function_room_availability`
  ADD CONSTRAINT `function_room_availability_ibfk_1` FOREIGN KEY (`function_room_id`) REFERENCES `function_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `function_room_availability_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `function_room_reservations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `function_room_blocked_dates`
--
ALTER TABLE `function_room_blocked_dates`
  ADD CONSTRAINT `function_room_blocked_dates_ibfk_1` FOREIGN KEY (`function_room_id`) REFERENCES `function_rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `function_room_images`
--
ALTER TABLE `function_room_images`
  ADD CONSTRAINT `function_room_images_ibfk_1` FOREIGN KEY (`function_room_id`) REFERENCES `function_rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `function_room_reservations`
--
ALTER TABLE `function_room_reservations`
  ADD CONSTRAINT `function_room_reservations_ibfk_1` FOREIGN KEY (`function_room_id`) REFERENCES `function_rooms` (`id`),
  ADD CONSTRAINT `function_room_reservations_ibfk_2` FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `function_room_reservations_ibfk_3` FOREIGN KEY (`banquet_style_id`) REFERENCES `banquet` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `function_room_reservations_ibfk_4` FOREIGN KEY (`office_type_id`) REFERENCES `office_types` (`id`),
  ADD CONSTRAINT `function_room_reservations_ibfk_5` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `function_room_reservations_ibfk_6` FOREIGN KEY (`venue_setup_id`) REFERENCES `venue_setups` (`id`),
  ADD CONSTRAINT `function_room_reservations_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `guest_details`
--
ALTER TABLE `guest_details`
  ADD CONSTRAINT `guest_details_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `guest_room_reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guest_room_availability`
--
ALTER TABLE `guest_room_availability`
  ADD CONSTRAINT `guest_room_availability_ibfk_1` FOREIGN KEY (`guest_room_id`) REFERENCES `guest_rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guest_room_images`
--
ALTER TABLE `guest_room_images`
  ADD CONSTRAINT `guest_room_images_ibfk_1` FOREIGN KEY (`guest_room_id`) REFERENCES `guest_rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guest_room_reservations`
--
ALTER TABLE `guest_room_reservations`
  ADD CONSTRAINT `guest_room_reservations_ibfk_1` FOREIGN KEY (`guest_room_id`) REFERENCES `guest_rooms` (`id`),
  ADD CONSTRAINT `guest_room_reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offices`
--
ALTER TABLE `offices`
  ADD CONSTRAINT `offices_ibfk_1` FOREIGN KEY (`office_type_id`) REFERENCES `office_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_venues`
--
ALTER TABLE `reservation_venues`
  ADD CONSTRAINT `reservation_venues_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `facility_reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_venues_ibfk_2` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `venue_images`
--
ALTER TABLE `venue_images`
  ADD CONSTRAINT `venue_images_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
