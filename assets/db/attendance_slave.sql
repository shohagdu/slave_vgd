-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2021 at 04:08 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance_slave`
--

-- --------------------------------------------------------

--
-- Table structure for table `configuration_info`
--

CREATE TABLE `configuration_info` (
  `id` int(11) NOT NULL,
  `union_name` varchar(100) DEFAULT NULL,
  `union_url` varchar(500) DEFAULT NULL,
  `access_file_location` varchar(500) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `created_ip` varchar(15) DEFAULT NULL,
  `updated_ip` int(15) DEFAULT NULL,
  `updated_time` datetime DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `configuration_info`
--



-- --------------------------------------------------------

--
-- Table structure for table `vgd_attendance_logs`
--

CREATE TABLE `vgd_attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `card_no` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `nid_no` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `attendance_date` datetime NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT 1,
  `is_process` tinyint(3) NOT NULL DEFAULT 0,
  `created_by_ip` varchar(45) CHARACTER SET utf8 NOT NULL,
  `updated_by_ip` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vgd_attendance_logs`
--


--
-- Indexes for dumped tables
--

--
-- Indexes for table `configuration_info`
--
ALTER TABLE `configuration_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vgd_attendance_logs`
--
ALTER TABLE `vgd_attendance_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `configuration_info`
--
ALTER TABLE `configuration_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vgd_attendance_logs`
--
ALTER TABLE `vgd_attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
