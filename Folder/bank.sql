-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2026 at 03:15 PM
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
-- Database: `bank`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `Account_ID` varchar(20) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Current_Balance` decimal(15,2) DEFAULT 0.00,
  `Account_Type` enum('Savings','Checking','Student') NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`Account_ID`, `User_ID`, `Current_Balance`, `Account_Type`, `Status`) VALUES
('AC-10001', 1, 9999999999999.99, 'Savings', 'Active'),
('SYS-00000', 1, 1000004999.00, 'Checking', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `Transaction_ID` int(11) NOT NULL,
  `Sender_Account` varchar(20) DEFAULT NULL,
  `Receiver_Account` varchar(20) NOT NULL,
  `Amount` decimal(15,2) NOT NULL,
  `Transaction_Type` enum('Deposit','Withdrawal','Transfer') NOT NULL,
  `Status` enum('Pending','Completed','Failed') DEFAULT 'Completed',
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`Transaction_ID`, `Sender_Account`, `Receiver_Account`, `Amount`, `Transaction_Type`, `Status`, `Timestamp`) VALUES
(2, 'SYS-00000', 'SYS-00000', 5000.00, 'Deposit', 'Completed', '2026-06-25 14:38:03'),
(3, 'SYS-00000', 'AC-10001', 100.00, 'Deposit', 'Completed', '2026-06-26 14:17:23'),
(4, 'SYS-00000', 'AC-10001', 200.00, 'Deposit', 'Completed', '2026-06-26 14:17:37'),
(5, 'SYS-00000', 'AC-10001', 500.00, 'Deposit', 'Completed', '2026-06-29 03:10:55'),
(6, 'SYS-00000', 'AC-10001', 9999999999999.99, 'Deposit', 'Completed', '2026-06-29 03:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password_Hash` varchar(255) NOT NULL,
  `Role` enum('Customer','Admin') DEFAULT 'Customer',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `Name`, `Email`, `Password_Hash`, `Role`, `Created_At`) VALUES
(1, 'kkk', 'kkk111@gmail.com', '$2y$10$PSknbVmBodkrwjeUkUO8newSPX1W4HMWXwEm/2RRdqB7WD/Mkjvh6', 'Customer', '2026-06-25 09:20:48'),
(2, 'Master Admin', 'admin@bankashkona.com', '$2y$10$wCjrQf9NncN/zyusXKZUK.5b5b9W50cjc9hrBnin6qvc.h/p5d6d.', 'Admin', '2026-06-25 09:42:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`Account_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`Transaction_ID`),
  ADD KEY `Sender_Account` (`Sender_Account`),
  ADD KEY `Receiver_Account` (`Receiver_Account`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `Transaction_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`Sender_Account`) REFERENCES `accounts` (`Account_ID`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`Receiver_Account`) REFERENCES `accounts` (`Account_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
