-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2024 at 09:03 PM
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
-- Database: `dwp`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `asset_id` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `asset_type` varchar(50) DEFAULT NULL,
  `asset_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `impact` int(11) DEFAULT NULL,
  `urgency` int(11) DEFAULT NULL,
  `ci_id` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `tag_number` varchar(50) DEFAULT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `installation_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `disposal_date` date DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `asset_id`, `user_id`, `asset_type`, `asset_name`, `description`, `impact`, `urgency`, `ci_id`, `status`, `tag_number`, `serial_number`, `available_date`, `installation_date`, `received_date`, `return_date`, `disposal_date`, `purchase_date`, `invoice_number`, `attachment_path`, `created_at`) VALUES
(1, 'CZITAST00101', 1, 'LAN/WAN Cable', 'vLAN-Cat5', 'The requested VM is up and accessible now, please confirm once you’re able to access it.', 1, 3, 'vLAN-101', 'In Repair', 'TAG34567', 'SRN98769', '2024-07-02', '2024-07-12', '2024-07-08', '2024-07-19', '2024-07-31', '2024-07-01', 'INV367898', '../uploads/ConZex_Wallpaper1.png', '2024-07-24 03:30:22'),
(2, 'CZITAST00102', 1, 'Chassis', 'HP', '    id INT AUTO_INCREMENT PRIMARY KEY,\r\n    asset_id VARCHAR(20),\r\n    user_id INT,\r\n    asset_type VARCHAR(50),', 4, 4, 'HP-Chassis-01', 'End Of Life', 'TAG34567', 'SRN98769', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '', '../uploads/ROUGH.txt', '2024-07-24 03:40:42'),
(3, 'CZITAST00103', 1, 'Card', 'HP', 'help', 4, 4, '', 'Down', '', '', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '2024-07-24 04:23:06'),
(4, 'CZITAST00104', 1, 'Keyboard (Wired)', 'HP', 'wifi keyboard', 1, 1, '', 'Received', '', '', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '2024-07-24 04:25:44'),
(5, 'CZITAST00105', 1, 'Accounts', 'Cash counting machine', 'CCM bought for cash handeleing', 4, 4, '', 'Ordered', '', '', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '2024-07-24 04:30:21'),
(6, 'CZITAST00106', 1, 'Computer System', 'Dell Poweredge', 'The requested VM is up and accessible now, please confirm once you’re able to access it.', 0, 0, 'Dell-Blade', 'Deployed', '', '', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '', '../uploads/ROUGH.txt', '2024-07-24 04:37:17'),
(7, 'CZITAST00107', 1, 'Mouse (Wired)', 'HP', 'wifi mouse', 1, 1, '', 'Ordered', '', '', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '', '', '2024-07-24 04:39:02'),
(8, 'CZITAST00108', 1, 'Disk Drive', 'HDD', 'WD 1TB ', 4, 3, '', 'Ordered', '', '', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '', '../uploads/Project, Ideas.txt', '2024-07-24 04:46:40'),
(9, 'CZITAST00109', 1, 'Storage', 'USB', '64GB HP USB', 4, 4, '', 'Received', 'TAG34567', 'SRN98769', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', 'INV367898', '../uploads/Sumit Kumawat.txt', '2024-07-24 15:11:47'),
(10, 'CZITAST00110', 1, 'Application', 'Google Chrome', 'Google chrome 2024 new vm.', 4, 4, '', 'Received', 'TAG34567', 'SRN98769', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', 'INV367898', '../uploads/bmc-logo.svg', '2024-07-24 15:27:49'),
(11, 'CZITAST00111', 1, 'Inventory Location', 'Root Server', 'Linux/Unix Root Server', 1, 4, '', 'In Repair', 'TAG34567', 'SRN98769', '2024-07-24', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', 'INV367898', '../uploads/bmc-logo.svg', '2024-07-25 09:05:57'),
(12, 'CZITAST00112', 1, 'CPU', 'Ryzon7', 'R7200', 4, 4, '', 'Deployed', 'TAG1234567', 'SRN98769', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', 'INV367898', '', '2024-08-20 19:18:23'),
(13, 'CZITAST00113', 1, 'CPU', 'Ryzon7', 'R7200', 4, 4, '', 'Deployed', 'TAG1234567', 'SRN98769', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', '0000-00-00', 'INV367898', '../uploads/Assets/-.txt', '2024-08-20 19:19:45');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `company_id` varchar(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `custom_country` varchar(255) DEFAULT NULL,
  `gst_number` varchar(50) DEFAULT NULL,
  `gst_percentage` decimal(5,2) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pin_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `company_id`, `company_name`, `address_line1`, `address_line2`, `country`, `custom_country`, `gst_number`, `gst_percentage`, `registration_number`, `currency`, `website`, `email`, `phone_number`, `fax`, `company_logo`, `created_at`, `pin_code`) VALUES
(1, 'CZORG00001', 'ConZex Global Private Limited', 'Atulya Nirman', 'Punawale', 'IN', '', 'SDFGHJ34567', 18.00, 'SDFGHJ45678DFG1234890VBN', '₹ - INR', 'www.conzex.com', 'support@conzex.com', '8007060308', '8007060308', NULL, '2024-07-30 18:15:41', '411033'),
(2, 'CZORG00002', 'Matrix.Cloud', 'Hinjawadi', 'Pune', 'IN', '', '29GGGGG1314R9Z6', 18.00, 'U62090PN2024PTC233073', '₹ - INR', 'www.cloud.matrix', 'info@cloud.matrix', '8007060308', '8007060308', '../uploads/RegCompany/logo.png', '2024-08-20 19:23:40', '411057');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` varchar(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `organization` int(11) DEFAULT NULL,
  `support_group` int(11) DEFAULT NULL,
  `reporting_manager` int(11) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `govt_id_proof` varchar(255) DEFAULT NULL,
  `tenth_certificate` varchar(255) DEFAULT NULL,
  `twelfth_certificate` varchar(255) DEFAULT NULL,
  `graduation_certificate` varchar(255) DEFAULT NULL,
  `post_graduation_certificate` varchar(255) DEFAULT NULL,
  `other_qualification` varchar(255) DEFAULT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `first_name`, `last_name`, `email`, `phone_number`, `dob`, `address`, `city`, `postal_code`, `country`, `organization`, `support_group`, `reporting_manager`, `passport_photo`, `govt_id_proof`, `tenth_certificate`, `twelfth_certificate`, `graduation_certificate`, `post_graduation_certificate`, `other_qualification`, `passport`, `username`, `gender`) VALUES
('CZEMP00101', 'Sumit', 'Kumawat', 'sumit_kumawat@conzex.com', '7507512005', '0000-00-00', 'Punawale', 'Pune', '411033', 'IN', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'skumawat', 'Male'),
('CZEMP00102', 'Anshuman', 'Chaudhary', 'anshuman_chaudhary@conzex.com', '86177 50206', '0000-00-00', 'Kolkata', 'West Bengal', '700001', 'IN', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'achaudhary', 'Male'),
('CZEMP00103', 'ABC', 'DEF', 'abc_def@conzex.com', '987654', '0000-00-00', 'iSREAL', 'LLC', '65438', 'IN', 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'adef', 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `reporting_managers`
--

CREATE TABLE `reporting_managers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_groups`
--

CREATE TABLE `support_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `unique_id` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_groups`
--

INSERT INTO `support_groups` (`id`, `name`, `unique_id`) VALUES
(1, 'Global-Admin-ALL', 'CZSUPGRP01'),
(3, 'Customer-Support', 'CZSUPGRP02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$4ojPPq5pq.GU/96Hr7j22egvUKJYB4HxS585l9kVtFTHT.GZTHeKy', 'sukumawa45@gmail.com', '2024-07-22 13:46:39', '2024-07-22 13:46:39'),
(2, 'anshu', '$2y$10$3Ox/u.pi4BXfY7HKNg9H8ewyIFal.zgzG7IMC03HodwBoeFGTZtDa', 'anshuman@gmail.com', '2024-07-23 02:40:15', '2024-07-23 02:40:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_id` (`company_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_employee_id` (`employee_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `reporting_managers`
--
ALTER TABLE `reporting_managers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_groups`
--
ALTER TABLE `support_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reporting_managers`
--
ALTER TABLE `reporting_managers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_groups`
--
ALTER TABLE `support_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
