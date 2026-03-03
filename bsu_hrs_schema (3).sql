-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2026 at 09:49 AM
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
  `source_table` enum('reservations','room_reservation') NOT NULL
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
(7, 'admin', '$2y$10$bBebV2Cv8MqUlKXuspWljOhAwaHlggSg3EmlKBVJNbMAsQ/WhWx3G', 'admin@bsu.edu.ph', 'super_admin', '2026-03-03 13:43:47', '2026-02-25 03:44:02');

-- --------------------------------------------------------

--
-- Table structure for table `banquet`
--

CREATE TABLE `banquet` (
  `id` int(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banquet`
--

INSERT INTO `banquet` (`id`, `image`, `name`, `description`) VALUES
(3, 'IMG_70840.png', 'THEATER TYPE', 'In this type of setup, rows of rectangle meeting tables or banquet tables with skirting and banquet chairs are facing a stage or screen. This style is ideal for events where attendees will be sitting and watching for an extended period.'),
(9, 'IMG_69154.png', 'BOARDROOM TYPE', 'It&#039;s referred to as the boardroom setup because it looks like all the standard boardrooms and conference rooms in corporate office spaces. If you have the space in your hotel, a boardroom setup in a smaller banquet hall is ideal as a standing de'),
(10, 'IMG_99683.png', 'U-SHAPE TYPE', 'U-shaped floor plans layout tables and seating to form a &quot;U&quot; toward the front of the room where the speaker will lead a discussion. U-shaped floor plans are good for smaller gatherings in rectangle-shaped rooms.'),
(11, 'IMG_90306.png', 'WEDDING STYLE', 'A banquet is a formal meal held for a large group of people. Banquets are typically held for special occasions like wedding receptions, recognition ceremonies, or large conferences. Many different banquet styles exist including buffet, reception, caf'),
(12, 'IMG_93068.png', 'HERRING BONE TYPE', 'This style is very similar to Classroom, however with a Herringbone seating arrangement, each consecutive row of chairs and tables are angled inwards. Positives: – All of the seats are angled inward towards the podium. – All of the seats are facing f'),
(13, 'IMG_87503.png', 'HOLLOW SQUARE TYPE', 'The hollow square layout is similar to the u-shape floor plan but simply closes off the fourth side to form a closed square or rectangle. It also has an open space in the middle of the table.'),
(14, 'IMG_23903.png', 'CLASSROOM TYPE', 'The classroom setup is a simple and easy setup with rows of rectangular tables and chairs on either side. This setup works for a banquet event with a speaker. That said, it can also be used for a banquet that is devoted to the full course meal and socializing with the other guests.'),
(15, 'IMG_64307.png', 'T-SHAPE TYPE', 'This type of seating is followed in conferences, where the top table is laid down and there is one spring attached with the top table.');

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
(1, 'Welcome to BSU Hostel', 'The perfect venue for your events. Spacious function rooms and comfortable guest rooms for meetings, celebrations, and group stays. Reserve your space today.', 'View Rooms', 'rooms.php', 'hostel/hostel2.png', 1, 1, '2026-02-19 05:14:52'),
(2, 'Book Your Function or Guest Room', 'Check availability and reserve your stay in minutes.', 'Check Availability', 'rooms.php', 'rooms/IMG_19689.jpg', 2, 1, '2026-02-19 05:14:52'),
(3, 'Your Comfort, Our Priority', 'Modern amenities and a welcoming environment for every guest.', 'See Amenities', 'facilities.php', 'rooms/IMG_85146.png', 3, 1, '2026-02-19 05:14:52'),
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
(24, 'FAC-20260303-316', 'RES-20260303-887', 'Curry', 'Marco', 'C', 1, 2, '', 'CTE Assembly', 2, 1, 1, 9, '2026-03-05 07:00:00', '2026-03-05 12:00:00', 60, 'gomari13@gmail.com', '09087547440', '{\"basic_sound_system\":{\"speaker\":2,\"mic\":2},\"banquet_chairs\":{\"quantity\":2},\"view_board\":{\"requested\":true}}', '', 'pencil_booked', '\n--- 2026-03-03 14:23:58 (Pencil booked) ---\nSubmitted a paper signed by me', '2026-03-03 06:22:31', '2026-03-03 06:23:58');

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
-- Table structure for table `function_room_reviews`
--

CREATE TABLE `function_room_reviews` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `function_room_reviews`
--

INSERT INTO `function_room_reviews` (`id`, `reservation_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(28, 111, 43, 5, 'Excellent service, highly recommended!', '2025-03-09 11:08:48');

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
-- Table structure for table `hostel`
--

CREATE TABLE `hostel` (
  `sr_no` int(11) NOT NULL,
  `image` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hostel`
--

INSERT INTO `hostel` (`sr_no`, `image`) VALUES
(6, 'IMG_36905.png'),
(7, 'IMG_88715.png'),
(11, 'IMG_89818.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `liabilities`
--

CREATE TABLE `liabilities` (
  `id` int(11) NOT NULL,
  `reservation_no` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `function_no` varchar(4) NOT NULL,
  `date` date NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `purpose` text NOT NULL,
  `room_type` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `doe` date NOT NULL,
  `toe` time NOT NULL,
  `end_of_event` time NOT NULL,
  `nop` int(11) NOT NULL,
  `vsetup` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `remarks` text DEFAULT NULL,
  `miscellaneous` text DEFAULT NULL,
  `banquet_type` varchar(255) NOT NULL,
  `booking_no` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Denied','Canceled') NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(10) NOT NULL DEFAULT 'UNPAID',
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `function_no`, `date`, `full_name`, `department`, `purpose`, `room_type`, `event_name`, `doe`, `toe`, `end_of_event`, `nop`, `vsetup`, `email`, `contact`, `remarks`, `miscellaneous`, `banquet_type`, `booking_no`, `status`, `payment_status`, `attachment`, `created_at`, `updated_at`, `deleted`) VALUES
(100, 34, '0005', '2025-02-24', 'Roica Ellao', 'Cvf', ' D f', 'ROOM D', ' Gg ', '2025-03-19', '01:39:00', '16:39:00', 60, 'Hrn', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', '09940677596', '', '', 'THEATER TYPE', 'EV RES-20250224-0005', 'Approved', 'PAID', 'IMG_2153.png', '2025-02-25 08:14:43', '2025-02-27 07:39:22', 0),
(101, 34, '0006', '2025-02-24', 'Roica Ellao', 'Hebe', ' Dhr', 'ROOM C', ' Frb', '2031-02-24', '17:40:00', '10:44:00', 30, 'Bdbd', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', '09940677596', '', '', 'HERRING BONE TYPE', 'EV RES-20250224-0006', 'Denied', 'PAID', 'IMG_2231.jpeg', '2025-02-25 08:14:43', '2025-02-27 05:20:45', 0),
(102, 34, '0007', '2025-02-24', 'Roica Ellao', 'Jsns', ' Jnxm', 'ROOM C', 'Jzizn', '2025-03-19', '18:16:00', '06:17:00', 30, 'Sijx', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', '09940677596', '', '', 'CLASSROOM TYPE', 'EV RES-20250224-0007', 'Denied', 'PAID', 'IMG_2221.jpeg', '2025-02-25 08:14:43', '2025-02-27 05:20:47', 0),
(103, 34, '0008', '2025-02-24', 'Roica Ellao', 'Dci', 'Anx', 'ROOM B', ' Jxk', '2025-03-10', '18:17:00', '18:23:00', 50, 'Isns', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', '09940677596', '', '', 'HERRING BONE TYPE', 'EV RES-20250224-0008', 'Canceled', 'UNPAID', 'IMG_2492.png', '2025-02-25 08:14:43', '2025-03-04 03:38:12', 0),
(104, 34, '0009', '2025-02-24', 'Roica Ellao', 'Ksnd', 'Nenjd', 'ROOM A', 'Nejnd', '2025-03-10', '07:59:00', '19:32:00', 60, 'Iwhidj', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', '09940677596', '', '', 'U-SHAPE TYPE', 'EV RES-20250224-0009', 'Pending', 'UNPAID', 'IMG_2492.png', '2025-02-25 08:14:43', '2025-02-27 05:17:49', 0),
(105, 29, '0010', '2025-02-25', 'Emmanuel Laparan', 'CICS', 'Anything', 'ROOM C', 'Career-Day', '2025-02-28', '08:01:00', '11:09:00', 30, 'Theater', 'erllaparan06@gmail.com', '9087547679', '', 'Rectangular Table, Basic Sound System, Mono Block Chair, Projector Screen', 'T-SHAPE TYPE', 'EV RES-20250225-0010', 'Denied', 'UNPAID', NULL, '2025-02-25 08:14:43', '2025-03-09 10:22:23', 1),
(106, 29, '0010', '2025-02-25', 'Emmanuel Laparan', 'CICS', 'Anything', 'ROOM C', 'Career-Day', '2025-02-28', '08:01:00', '11:09:00', 30, 'Theater', 'erllaparan06@gmail.com', '9087547679', '', 'Rectangular Table, Basic Sound System, Mono Block Chair, Projector Screen', 'T-SHAPE TYPE', 'EV RES-20250225-0010', 'Pending', 'UNPAID', NULL, '2025-02-25 08:14:43', '2025-02-25 08:14:43', 0),
(107, 29, '0011', '2025-02-25', 'Emmanuel Laparan', 'CICS', 'wer', 'ROOM E', 'sdf', '2025-03-03', '09:12:00', '14:13:00', 50, 'Theater', 'erllaparan06@gmail.com', '9087547679', '', 'Rectangular Table', 'BOARDROOM TYPE', 'EV RES-20250225-0011', 'Pending', 'UNPAID', NULL, '2025-02-25 08:14:43', '2025-02-25 08:14:43', 0),
(108, 30, '0012', '2025-02-27', 'Francis Dave Laparan', 'fff', 'ffff', 'ROOM E, ROOM D', 'fff', '2025-03-01', '06:21:00', '07:22:00', 110, 'ggg', 'erllaparan07@gmail.com', '09129098657', 'ggg', 'Rectangular Table, Basic Sound System', 'THEATER TYPE', 'EV RES-20250227-0012', 'Canceled', 'PAID', 'Reg.JPG', '2025-02-27 03:50:03', '2025-02-27 09:32:45', 1),
(109, 34, '0013', '2025-02-27', 'Roica Ellao', 'MAIN', 'NIAM', 'ROOM B, ROOM A', 'QWERTT', '2025-02-25', '12:50:00', '20:50:00', 110, 'Bahzns', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', '09940677596', '', 'Rectangular Table, Round Table, Basic Sound System, Mono Block Chair, Projector Screen', 'HERRING BONE TYPE', 'EV RES-20250227-0013', 'Approved', 'PAID', 'IMG_2314.jpeg', '2025-02-27 03:51:24', '2025-02-27 05:28:49', 0),
(110, 43, '0014', '2025-02-27', 'Aaron', 'Majka', ' Sksl', 'ROOM C, ROOM B', 'Makks', '2025-03-11', '13:08:00', '20:08:00', 80, 'Msks', 'roicanix@gmail.com', '09940677514', '', 'Basic Sound System, Projector Screen', 'T-SHAPE TYPE', 'EV RES-20250227-0014', 'Approved', 'UNPAID', NULL, '2025-02-27 05:08:52', '2025-02-27 05:12:18', 0),
(111, 43, '0015', '2025-02-27', 'Aaron', 'CICS', 'KANSK', 'ROOM C, ROOM B, ROOM A', 'JAISIS', '2025-03-02', '07:00:00', '12:00:00', 140, 'Makanns', 'roicanix@gmail.com', '09940677514', '', 'Basic Sound System, Mono Block Chair, Projector Screen', 'THEATER TYPE', 'EV RES-20250227-0015', 'Approved', 'UNPAID', NULL, '2025-02-27 05:29:14', '2025-02-27 05:31:52', 0),
(112, 44, '0016', '2025-02-27', 'Jack Ender', 'Jeros2Pharmacy', 'Birthday Celebration', 'ROOM C, ROOM B, ROOM A', 'Jeros Birthday', '2025-03-14', '09:01:00', '13:01:00', 140, 'T-Shape', 'izarjamesf28@gmail.com', '09919463805', '', 'Rectangular Table, Basic Sound System, Mono Block Chair, Projector Screen', 'T-SHAPE TYPE', 'EV RES-20250227-0016', 'Approved', 'PAID', 'rimuru.jpg', '2025-02-27 05:36:06', '2025-02-27 06:32:39', 0),
(114, 44, '0018', '2025-02-27', 'Jack Ender', 'kahit ano', 'asdf', 'ROOM E, ROOM C, ROOM B', 'KAHIT ANO', '2025-03-18', '08:25:00', '16:25:00', 130, 'Classroom', 'izarjamesf28@gmail.com', '09919463805', '', 'Mono Block Chair', 'CLASSROOM TYPE', 'EV RES-20250227-0018', 'Canceled', 'UNPAID', NULL, '2025-02-27 06:26:40', '2025-02-27 06:34:48', 0),
(115, 30, '0019', '2025-02-27', 'Francis Dave Laparan', 'dd', 'dd', 'ROOM D', 'ddd', '2025-03-08', '09:12:00', '06:15:00', 60, 'dddd', 'erllaparan07@gmail.com', '09129098657', 'dddd', 'Rectangular Table, Basic Sound System', 'THEATER TYPE', 'EV RES-20250227-0019', 'Canceled', 'UNPAID', NULL, '2025-02-27 08:08:57', '2025-03-20 03:51:03', 1),
(116, 44, '0020', '2025-03-03', 'Jack Ender', 'Dot Company ', 'Celebration ', 'ROOM D, ROOM C', 'Celebration ', '2025-03-02', '08:00:00', '12:00:00', 90, 'Anything ', 'izarjamesf28@gmail.com', '09919463805', '', 'Rectangular Table, Mono Block Chair, Projector Screen', 'U-SHAPE TYPE', 'EV RES-20250303-0020', 'Approved', 'PAID', 'IMG20250103100307.jpg', '2025-03-03 06:58:33', '2025-03-03 07:38:07', 1),
(117, 30, '0021', '2025-03-03', 'Francis Dave Laparan', 'None', 'None', 'ROOM E', 'Christmas party ', '2025-03-05', '15:00:00', '16:00:00', 50, 'None', 'erllaparan07@gmail.com', '09129098657', 'None', 'Rectangular Table, Basic Sound System', 'THEATER TYPE', 'EV RES-20250303-0021', 'Canceled', 'UNPAID', NULL, '2025-03-03 07:00:42', '2025-03-04 04:16:05', 1),
(118, 44, '0022', '2025-03-03', 'Jack Ender', 'gggdg', 'hshsh', 'ROOM C', 'shh', '2025-03-05', '16:19:00', '17:20:00', 30, 'hshshs', 'izarjamesf28@gmail.com', '09919463805', 'shshh', 'Rectangular Table, Basic Sound System', 'BOARDROOM TYPE', 'EV RES-20250303-0022', 'Pending', 'UNPAID', NULL, '2025-03-03 07:19:45', '2025-03-03 07:19:45', 0),
(119, 30, '0023', '2025-03-04', 'Francis Dave Laparan', 'ABSOLUTE', 'None', 'ROOM D', 'Christmas party ', '2025-03-06', '01:00:00', '05:00:00', 60, 'None', 'erllaparan07@gmail.com', '09129098657', 'None', 'Rectangular Table, Basic Sound System', 'WEDDING STYLE', 'EV RES-20250304-0023', 'Canceled', 'UNPAID', NULL, '2025-03-04 03:34:07', '2025-03-14 08:19:15', 1),
(120, 29, '0024', '2025-03-09', 'Emmanuel Laparan', 'Jeros Company', 'wedding', 'ROOM E', 'asd wedding', '2025-03-10', '10:00:00', '18:30:00', 50, 'WEDDING', 'erllaparan06@gmail.com', '9087547679', '', 'Rectangular Table, Round Table, Basic Sound System, Mono Block Chair, Projector Screen', 'WEDDING STYLE', 'EV RES-20250309-0024', 'Pending', 'UNPAID', NULL, '2025-03-09 11:58:21', '2025-03-09 11:58:21', 0),
(121, 30, '0024', '2025-03-09', 'Francis Dave Laparan', 'None', 'None', 'ROOM E', 'Christmas party ', '2025-03-12', '22:58:00', '12:58:00', 50, 'None', 'erllaparan07@gmail.com', '09129098657', 'None', 'Rectangular Table, Basic Sound System', 'THEATER TYPE', 'EV RES-20250309-0024', 'Canceled', 'UNPAID', NULL, '2025-03-09 11:58:48', '2025-03-20 03:50:53', 1),
(122, 30, '0025', '2025-03-14', 'Francis Dave Laparan', 'none', 'none', 'ROOM E', 'hahaha', '2025-03-15', '18:11:00', '18:13:00', 50, 'ssss', 'erllaparan07@gmail.com', '09129098657', 'ssss', 'Rectangular Table, Basic Sound System', 'BOARDROOM TYPE', 'EV RES-20250314-0025', 'Canceled', 'UNPAID', NULL, '2025-03-14 08:12:06', '2025-03-20 03:50:35', 1),
(123, 43, '0026', '2025-03-15', 'Aaron', 'CICS', 'MEETING', 'ROOM C', 'CICS Yearly Meeting', '2025-03-15', '17:00:00', '19:00:00', 30, 'classroom type', 'roicanix@gmail.com', '09940677514', '', 'Rectangular Table, Round Table, Basic Sound System, Mono Block Chair, Projector Screen', 'CLASSROOM TYPE', 'EV RES-20250315-0026', 'Pending', 'UNPAID', NULL, '2025-03-15 08:01:43', '2025-03-15 08:01:43', 0),
(124, 29, '0027', '2025-03-15', 'Emmanuel Laparan', 'CTE', 'CTE Meeting', 'ROOM D', 'CTE Yearly Meeting', '2025-03-30', '08:00:00', '10:00:00', 60, 'classroom type', 'erllaparan06@gmail.com', '9087547679', '', 'Rectangular Table, Round Table, Basic Sound System, Mono Block Chair, Projector Screen', 'CLASSROOM TYPE', 'EV RES-20250315-0027', 'Pending', 'UNPAID', NULL, '2025-03-15 08:10:45', '2025-03-15 08:10:45', 0),
(125, 44, '0028', '2025-03-16', 'Jack Ender', 'BFP Nasugbu', ' Anniversary Celebration', 'ROOM E, ROOM C', 'BFP Nasugbu Aniversary', '2025-03-31', '08:00:00', '22:00:00', 80, 'specific', 'izarjamesf28@gmail.com', '09919463805', '', 'Rectangular Table, Round Table, Basic Sound System, Mono Block Chair, Projector Screen', 'HOLLOW SQUARE TYPE', 'EV RES-20250316-0028', 'Approved', 'PAID', 'Screenshot_16-3-2025_163443_bsuarasofhostel.tech.jpeg', '2025-03-16 08:34:20', '2025-03-16 08:37:22', 0);

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
(22, 24, 3, '2026-03-05 16:00:00', '2026-03-05 18:00:00', '2026-03-03 06:22:31');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `area` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` varchar(750) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `removed` int(11) NOT NULL DEFAULT 0,
  `type_id` int(11) NOT NULL,
  `seats_capacity` int(11) DEFAULT NULL COMMENT 'Max seats (function rooms)',
  `tables_count` int(11) DEFAULT NULL COMMENT 'Number of tables (function rooms)',
  `details_extra` text DEFAULT NULL COMMENT 'Additional details',
  `adult_capacity` int(11) DEFAULT 0,
  `children_capacity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `area`, `price`, `quantity`, `description`, `status`, `removed`, `type_id`, `seats_capacity`, `tables_count`, `details_extra`, `adult_capacity`, `children_capacity`) VALUES
(29, 'DORMITORY', 114, 8000, 12, 'This room is designed to accommodate larger groups, making it an ideal choice for student delegations, sports teams, event participants, or any gathering that requires ample space and comfort. It offers a welcoming and functional environment, ensuring that all guests can relax and enjoy their stay, whether they are here for a team-building event, a competition, or a group excursion. With its spacious layout and thoughtful amenities, this room guarantees a memorable and hassle-free experience for everyone', 1, 0, 14, NULL, NULL, NULL, 0, 0),
(37, 'Function Room A', 50, 1500, 0, 'Spacious function room for meetings and events.', 0, 0, 15, NULL, NULL, NULL, 30, 0),
(38, 'Function Room B', 60, 1800, 0, 'Ideal for seminars and workshops.', 0, 0, 15, NULL, NULL, NULL, 40, 0),
(39, 'Function Room C', 70, 2000, 0, 'Largest function room with AV equipment.', 0, 0, 15, NULL, NULL, NULL, 50, 0),
(40, 'Function Room D', 40, 1200, 0, 'Small function room for intimate events.', 0, 0, 15, NULL, NULL, NULL, 20, 0),
(41, 'Function Room E', 55, 1600, 0, 'Versatile space for training and events.', 0, 0, 15, NULL, NULL, NULL, 35, 0),
(42, 'Guest Room 1', 25, 800, 0, 'Comfortable guest room with queen bed.', 0, 0, 14, NULL, NULL, NULL, 2, 1),
(43, 'Guest Room 2', 28, 850, 0, 'Guest room with city view.', 0, 0, 14, NULL, NULL, NULL, 2, 1),
(44, 'Guest Room 3', 30, 900, 0, 'Spacious guest room for small families.', 0, 0, 14, NULL, NULL, NULL, 3, 1),
(45, 'Guest Room 4', 22, 750, 0, 'Cozy room for couples or solo travelers.', 0, 0, 14, NULL, NULL, NULL, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_facilities`
--

CREATE TABLE `room_facilities` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `facilities_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_facilities`
--

INSERT INTO `room_facilities` (`sr_no`, `room_id`, `facilities_id`) VALUES
(332, 29, 13);

-- --------------------------------------------------------

--
-- Table structure for table `room_guests`
--

CREATE TABLE `room_guests` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `guest_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `age` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_guests`
--

INSERT INTO `room_guests` (`id`, `reservation_id`, `guest_name`, `dob`, `age`) VALUES
(187, 161, 'Sc', '1985-06-24', 39),
(188, 161, 'Cd', '2008-09-24', 16),
(189, 161, 'Cd', '2007-02-24', 18),
(190, 161, 'Cws', '2009-06-24', 15),
(191, 161, 'Cfd', '1987-09-24', 37),
(192, 162, '', '0000-00-00', 0),
(199, 165, 'Jsn', '1963-03-24', 61),
(200, 166, '1', '1988-01-01', 37),
(201, 166, '2', '1975-08-02', 49),
(202, 166, '3', '1979-10-03', 45),
(203, 166, '4', '1974-10-04', 50),
(204, 166, '5', '1987-11-05', 37),
(218, 171, '', '0000-00-00', 0),
(219, 172, 'Zc ', '2004-09-27', 20),
(220, 172, 'Rsxc', '2025-02-27', 0),
(221, 172, 'Accd', '1990-12-27', 34),
(222, 172, 'Ax d', '2004-07-27', 20),
(223, 172, 'Axxs', '1999-05-27', 25),
(224, 173, 'Kaisj', '1994-11-27', 30),
(225, 173, 'Xdjsj', '1997-07-27', 27),
(226, 173, 'Sjanz', '1985-07-27', 39),
(227, 173, ' Skkxj', '1997-09-27', 27),
(228, 173, 'Shwla', '1992-11-27', 32),
(229, 174, 'Miggy ', '2000-01-01', 25),
(230, 174, 'Marione', '2001-02-02', 24),
(231, 174, 'Jiciele', '2002-03-03', 22),
(232, 174, 'Marian', '2005-04-04', 19),
(233, 174, 'Marry', '2006-05-05', 18),
(234, 175, '', '0000-00-00', 0),
(235, 176, '', '0000-00-00', 0),
(236, 177, 'Def', '1980-03-03', 45),
(237, 177, 'Lef', '1962-03-03', 63),
(238, 178, 'xsdvbv', '2022-12-22', 2),
(239, 179, 'def', '2024-04-09', 0),
(240, 179, 'wse', '2023-02-02', 2),
(241, 179, 'we', '2019-06-11', 5),
(242, 179, 'wer', '2019-07-24', 5),
(243, 179, '12we', '2009-07-09', 15),
(244, 180, 'df', '1997-03-06', 28),
(245, 181, 'dddd', '2021-06-08', 3),
(246, 182, 'roica', '2000-08-12', 24),
(247, 182, 'kinsay', '2020-07-14', 4),
(248, 183, 'aaron', '1999-02-20', 26),
(249, 183, 'kinsay', '2020-07-14', 4),
(250, 184, 'chloe', '2011-06-02', 13),
(251, 184, 'thirdy', '2010-04-10', 14),
(252, 184, 'oden', '2016-01-27', 9),
(253, 184, 'casius', '2005-02-20', 20);

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_id`, `image_path`, `is_primary`) VALUES
(1, 37, 'rooms/IMG_19689.jpg', 1),
(2, 38, 'rooms/IMG_19689.jpg', 1),
(3, 39, 'rooms/IMG_19689.jpg', 1),
(4, 40, 'rooms/IMG_19689.jpg', 1),
(5, 41, 'rooms/IMG_19689.jpg', 1),
(8, 42, 'rooms/IMG_85146.png', 1),
(9, 43, 'rooms/IMG_85146.png', 1),
(10, 44, 'rooms/IMG_85146.png', 1),
(11, 45, 'rooms/IMG_85146.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_reservation`
--

CREATE TABLE `room_reservation` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_no` varchar(20) NOT NULL,
  `principal_guest_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `arrival_date` date NOT NULL,
  `departure_date` date NOT NULL,
  `checkin_time` time NOT NULL,
  `checkout_time` time NOT NULL,
  `room_type` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Denied','Canceled') NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(10) NOT NULL DEFAULT 'UNPAID',
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_reservation`
--

INSERT INTO `room_reservation` (`id`, `user_id`, `booking_no`, `principal_guest_name`, `dob`, `email`, `address`, `contact`, `arrival_date`, `departure_date`, `checkin_time`, `checkout_time`, `room_type`, `remarks`, `signature`, `status`, `payment_status`, `attachment`, `created_at`, `updated_at`, `deleted`) VALUES
(161, 34, 'GM RES-20250224-161', 'Roica Ellao', '2000-08-12', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', '2025-02-25', '2025-02-27', '14:00:00', '12:00:00', 'ROOM 304', '', 'https://bsuarasofhostel.site/images/signature/1740382871_signature.png', 'Approved', 'PAID', NULL, '2025-02-25 08:14:43', '2025-02-27 07:39:59', 0),
(162, 34, 'GM RES-20250224-162', 'Roica Ellao', '2000-08-12', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', '2025-02-25', '2025-02-28', '14:00:00', '12:00:00', 'DORMITORY', '', 'https://bsuarasofhostel.site/images/signature/1740383447_IMG_2390.jpeg', 'Approved', 'PAID', 'IMG_2295.png', '2025-02-25 08:14:43', '2025-02-27 05:21:36', 0),
(165, 34, 'GM RES-20250224-165', 'Roica Ellao', '2000-08-12', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', '2025-02-28', '2025-03-01', '14:00:00', '12:00:00', 'ROOM 305', '', 'https://bsuarasofhostel.site/images/signature/1740389490_signature.png', 'Approved', 'PAID', 'IMG_2331.jpeg', '2025-02-25 08:14:43', '2025-02-27 09:31:31', 0),
(166, 34, 'GM RES-20250224-166', 'Roica Ellao', '2000-08-12', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', '2025-02-24', '2025-02-25', '14:00:00', '12:00:00', 'ROOM 305', '', 'https://bsuarasofhostel.site/images/signature/1740389572_signature.png', 'Pending', 'PAID', 'agile.png', '2025-02-25 08:14:43', '2025-02-27 09:10:13', 0),
(171, 30, 'GM RES-20250227-171', 'Francis Dave Laparan', '1999-01-25', 'erllaparan07@gmail.com', 'BALANOY', '09129098657', '2025-03-07', '2025-03-08', '14:00:00', '12:00:00', 'ROOM 305', 'dddd', 'https://bsuarasofhostel.tech/images/signature/1740628558_signature.png', 'Approved', 'PAID', 'gclass.JPG', '2025-02-27 03:55:58', '2025-02-27 05:21:31', 0),
(172, 34, 'GM RES-20250227-172', 'Roica Ellao', '2000-08-12', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', '2025-03-02', '2025-03-03', '14:00:00', '12:00:00', 'ROOM 303', '', 'https://bsuarasofhostel.tech/images/signature/1740630135_IMG_2314.jpeg', 'Pending', 'UNPAID', 'IMG_2492.png', '2025-02-27 04:22:15', '2025-02-27 04:23:10', 0),
(173, 43, 'GM RES-20250227-173', 'Aaron', '1999-02-20', 'roicanix@gmail.com', 'Gimalas', '09940677514', '2025-03-13', '2025-03-16', '14:00:00', '12:00:00', 'ROOM 305', '', 'https://bsuarasofhostel.tech/images/signature/1740633008_IMG_2496.png', 'Pending', 'UNPAID', NULL, '2025-02-27 05:10:08', '2025-02-27 05:10:08', 0),
(174, 44, 'GM RES-20250227-174', 'Jack Ender', '2001-01-28', 'izarjamesf28@gmail.com', 'Lumbangan, Nasugbu, Batangas', '09919463805', '2025-03-09', '2025-03-10', '14:00:00', '12:00:00', 'ROOM 305', '', 'https://bsuarasofhostel.tech/images/signature/1740634329_signature.png', 'Canceled', 'UNPAID', 'rimuru1.jpg', '2025-02-27 05:32:09', '2025-03-16 10:06:38', 1),
(175, 44, 'GM RES-20250227-175', 'Jack Ender', '2001-01-28', 'izarjamesf28@gmail.com', 'Lumbangan, Nasugbu, Batangas', '09919463805', '2025-03-13', '2025-03-14', '14:00:00', '12:00:00', 'ROOM 304', '', 'https://bsuarasofhostel.tech/images/signature/1740634379_signature.png', 'Pending', 'UNPAID', 'index.jpg', '2025-02-27 05:32:59', '2025-02-27 05:37:37', 0),
(176, 30, 'GM RES-20250227-176', 'Francis Dave Laparan', '1999-01-25', 'erllaparan07@gmail.com', 'BALANOY', '09129098657', '2025-03-01', '2025-03-07', '14:00:00', '12:00:00', 'DORMITORY', 'ddddd', 'https://bsuarasofhostel.tech/images/signature/1740643776_signature.png', 'Approved', 'UNPAID', NULL, '2025-02-27 08:09:36', '2025-02-27 09:23:44', 0),
(177, 44, 'GM RES-20250303-177', 'Jack Ender', '2001-01-28', 'izarjamesf28@gmail.com', 'Lumbangan, Nasugbu, Batangas', '09919463805', '2025-03-19', '2025-03-20', '14:00:00', '12:00:00', 'ROOM 304', '', 'https://bsuarasofhostel.tech/images/signature/1740984866_signature.png', 'Pending', 'UNPAID', NULL, '2025-03-03 06:54:26', '2025-03-03 06:54:26', 0),
(178, 29, 'GM RES-20250309-178', 'Emmanuel Laparan', '2000-07-06', 'erllaparan06@gmail.com', 'BALANOY', '9087547679', '2025-03-11', '2025-03-13', '14:00:00', '12:00:00', 'DORMITORY', '', 'https://bsuarasofhostel.tech/images/signature/1741515179_signature.png', 'Approved', 'UNPAID', NULL, '2025-03-09 10:12:59', '2025-03-09 10:21:55', 0),
(179, 29, 'GM RES-20250309-179', 'Emmanuel Laparan', '2000-07-06', 'erllaparan06@gmail.com', 'BALANOY', '9087547679', '2025-03-17', '2025-03-18', '14:00:00', '12:00:00', 'ROOM 304', '', 'https://bsuarasofhostel.tech/images/signature/1741515580_signature.png', 'Approved', 'UNPAID', NULL, '2025-03-09 10:19:40', '2025-03-09 10:22:02', 0),
(180, 29, 'GM RES-20250309-180', 'Emmanuel Laparan', '2000-07-06', 'erllaparan06@gmail.com', 'BALANOY', '9087547679', '2025-03-16', '2025-03-18', '14:00:00', '12:00:00', 'DORMITORY', '', 'https://bsuarasofhostel.tech/images/signature/1741522811_signature.png', 'Pending', 'UNPAID', NULL, '2025-03-09 12:20:11', '2025-03-09 12:20:11', 0),
(181, 30, 'GM RES-20250314-181', 'Francis Dave Laparan', '1999-01-25', 'erllaparan07@gmail.com', 'BALANOY', '09129098657', '2025-03-20', '2025-03-21', '14:00:00', '12:00:00', 'ROOM 305', 'ddd', 'https://bsuarasofhostel.tech/images/signature/1741940031_signature.png', 'Canceled', 'UNPAID', NULL, '2025-03-14 08:13:51', '2025-03-14 08:15:31', 0),
(182, 43, 'GM RES-20250315-182', 'Aaron', '1999-02-20', 'roicanix@gmail.com', 'Gimalas', '09940677514', '2025-03-18', '2025-03-19', '14:00:00', '12:00:00', 'ROOM 305', '', 'https://bsuarasofhostel.tech/images/signature/1742025809_signature.png', 'Pending', 'UNPAID', NULL, '2025-03-15 08:03:29', '2025-03-15 08:03:29', 0),
(183, 34, 'GM RES-20250315-183', 'Roica Ellao', '2000-08-12', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', '2025-03-19', '2025-03-20', '14:00:00', '12:00:00', 'ROOM 301', '', 'https://bsuarasofhostel.tech/images/signature/1742025916_signature.png', 'Pending', 'UNPAID', NULL, '2025-03-15 08:05:16', '2025-03-15 08:05:16', 0),
(184, 34, 'GM RES-20250315-184', 'Roica Ellao', '2000-08-12', 'mariaroicalouisse.ellao@g.batstate-u.edu.ph', 'Gimalas', '09940677596', '2025-03-21', '2025-03-23', '14:00:00', '12:00:00', 'ROOM 304', '', 'https://bsuarasofhostel.tech/images/signature/1742026099_signature.png', 'Pending', 'UNPAID', NULL, '2025-03-15 08:08:19', '2025-03-15 08:08:19', 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_reviews`
--

CREATE TABLE `room_reviews` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_reviews`
--

INSERT INTO `room_reviews` (`id`, `reservation_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(65, 161, 34, 5, 'nice accommodation! highly recommended.', '2025-03-09 11:04:57'),
(66, 165, 34, 5, 'Smooth booking process, very convenient.', '2025-03-09 11:09:42');

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
(4, 'office', 'HOSTEL FUNCTION ROOM AND EVENTS RULES AND GUIDELINES', '1. The function room reservation in the hostel operates on a first-come, first-served basis. We prioritize events with approved letters signed by the Office of the Chancellor.\r\n\r\n2. Make sure to fill out all the necessary information in the hostel reservation form so that we can better assist you with your events.\r\n\r\n3. If the event requires tablecloths, napkins, or even kitchen utensils and other laboratory tools and equipment, the facilitators must fill out the requisition form and coordinate with the Hostel Laboratory Assistant.\r\n\r\n4. Decorations and props are allowed to fit the theme of the event however, the use of tapes and all kinds of adhesives and nails/screws on the wall are not allowed to avoid chipping of paint and or leaving adhesive marks. The use of balloons as décor is not allowed.\r\n\r\n5. If water dispensers are needed for the said event, the person in charge is responsible for providing a gallon of water for the event.\r\n\r\n6. If the event requires the following: View board, Projector, Microphone, or basic sound system, the person in charge is responsible for coordinating with ICT or the Audio Room (PFM) to request the said items and assistance in setting up the equipment.\r\n\r\n7. If students or colleagues are the facilitators of the event, they will be responsible to set up the chairs and tables before the events and clean the function rooms after use.\r\n\r\n8. Environmental Sustainability Guidelines:\r\n   • No Disposable Water Bottles – The use of disposable water bottles is strictly prohibited. All members are encouraged to bring their own tumblers or use the water refill stations available across the campus.\r\n   • Buffet-Style Food Only – To reduce food waste, only buffet-style meals will be allowed during university events. Please take only what you can finish.\r\n   • No Single-Use Plastics or Disposables – The use of single-use plastics, including food wrappers, containers, balloons, paper and plastic cups, straws, plastic stirrers, and similar items, is strictly prohibited.\r\n   • Practice CLAYGO (Clean As You Go) – All event participants must practice the CLAYGO policy to maintain cleanliness and reduce the volume of waste.\r\n   • Prohibition on Tarpaulins – The use of tarpaulins is prohibited in all university activities and events.\r\n   • Proper Waste Segregation – All waste must be properly segregated according to the designated bins for biodegradable, recyclable, and non-recyclable materials.\r\n\r\n9. The use of laminated paper products such as food containers, paper cups, and paper plates is strictly discouraged. \"Bring your food container\" policy shall be implemented for \"take out\" food and bringing of personal sustainable tumbler/mug for water refilling is highly encouraged.\r\n\r\n10. The organizer must ensure that the function room is clean and damage-free after the activity.', 'function_rooms_HOUSE_RULES_2026.pdf', '2026', 1, '2026-03-02 02:49:23', '2026-03-02 03:50:37');

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
-- Table structure for table `types_room`
--

CREATE TABLE `types_room` (
  `id` int(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(750) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `types_room`
--

INSERT INTO `types_room` (`id`, `image`, `name`, `description`) VALUES
(14, '', 'Guest Room', 'A welcoming space for guests attending events, seminars, or meetings. Private and comfortable rooms to unwind and recharge during your stay.'),
(15, 'IMG_32980.jpg', 'Function Room', 'Whether it\'s a corporate meeting or a celebration, our function room fits the occasion. Modern space with AV equipment and flexible seating to match your needs.');

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
  `color` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `capacity`, `floor`, `description`, `is_active`, `created_at`, `color`) VALUES
(1, 'Function Room A', 40, 'Ground Floor', 'Spacious function room for meetings and events.', 1, '2026-02-25 06:38:50', NULL),
(2, 'Function Room B', 40, 'Ground Floor', 'Ideal for seminars and workshops.', 1, '2026-02-25 06:38:50', NULL),
(3, 'Function Room C', 40, 'Ground Floor', 'Largest function room with AV equipment.', 1, '2026-02-25 06:38:50', NULL),
(4, 'Function Room D', 40, 'Ground Floor', 'Small function room for intimate events.', 1, '2026-02-25 06:38:50', NULL),
(5, 'Function Room E', 40, 'Ground Floor', 'Versatile space for training and events.', 1, '2026-02-25 06:38:50', NULL),
(6, 'Guest Room 1', 2, '2nd Floor', 'Comfortable guest room with queen bed.', 1, '2026-02-25 06:38:50', NULL),
(7, 'Guest Room 2', 2, '2nd Floor', 'Guest room with city view.', 1, '2026-02-25 06:38:50', NULL),
(8, 'Guest Room 3', 3, '2nd Floor', 'Spacious guest room for small families.', 1, '2026-02-25 06:38:50', NULL),
(9, 'Guest Room 4', 2, '2nd Floor', 'Cozy room for couples or solo travelers.', 1, '2026-02-25 06:38:50', NULL);

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
-- Indexes for table `function_room_reviews`
--
ALTER TABLE `function_room_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hidden_users`
--
ALTER TABLE `hidden_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `hostel`
--
ALTER TABLE `hostel`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `liabilities`
--
ALTER TABLE `liabilities`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doe_toe` (`doe`,`toe`);

--
-- Indexes for table `reservation_venues`
--
ALTER TABLE `reservation_venues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reservation` (`reservation_id`),
  ADD KEY `idx_venue` (`venue_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_type_id` (`type_id`);

--
-- Indexes for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `facilities id` (`facilities_id`),
  ADD KEY `room id` (`room_id`);

--
-- Indexes for table `room_guests`
--
ALTER TABLE `room_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_reservation`
--
ALTER TABLE `room_reservation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_reviews`
--
ALTER TABLE `room_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `types_room`
--
ALTER TABLE `types_room`
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
  ADD UNIQUE KEY `name` (`name`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
-- AUTO_INCREMENT for table `function_room_reviews`
--
ALTER TABLE `function_room_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `hostel`
--
ALTER TABLE `hostel`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `liabilities`
--
ALTER TABLE `liabilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `reservation_venues`
--
ALTER TABLE `reservation_venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `room_facilities`
--
ALTER TABLE `room_facilities`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- AUTO_INCREMENT for table `room_guests`
--
ALTER TABLE `room_guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `room_reservation`
--
ALTER TABLE `room_reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `room_reviews`
--
ALTER TABLE `room_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `types_room`
--
ALTER TABLE `types_room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- Constraints for table `function_room_reviews`
--
ALTER TABLE `function_room_reviews`
  ADD CONSTRAINT `function_room_reviews_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `function_room_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_type_id` FOREIGN KEY (`type_id`) REFERENCES `types_room` (`id`);

--
-- Constraints for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD CONSTRAINT `facilities id` FOREIGN KEY (`facilities_id`) REFERENCES `facilities` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `room id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `room_guests`
--
ALTER TABLE `room_guests`
  ADD CONSTRAINT `room_guests_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `room_reservation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_reviews`
--
ALTER TABLE `room_reviews`
  ADD CONSTRAINT `room_reviews_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `room_reservation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
