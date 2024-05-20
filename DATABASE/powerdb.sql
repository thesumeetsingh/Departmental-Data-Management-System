-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2024 at 04:36 PM
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
-- Database: `powerdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `jldc`
--

CREATE TABLE `jldc` (
  `TIME` varchar(100) NOT NULL,
  `DATE` date NOT NULL,
  `POWER_GENERATION` int(11) NOT NULL,
  `UPDATEDBY` varchar(100) NOT NULL,
  `UPDATED_ON` datetime NOT NULL,
  `LOCATION` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nspl`
--

CREATE TABLE `nspl` (
  `TIME` varchar(100) NOT NULL,
  `DATE` date NOT NULL,
  `LOADSECH` int(5) NOT NULL,
  `UPDATEDBY` varchar(100) NOT NULL,
  `UPDATED_ON` datetime NOT NULL,
  `LOCATION` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platemill`
--

CREATE TABLE `platemill` (
  `TIME` varchar(100) NOT NULL,
  `DATE` date NOT NULL,
  `LOADSECH` int(5) NOT NULL,
  `UPDATEDBY` varchar(100) NOT NULL,
  `UPDATED_ON` datetime NOT NULL,
  `LOCATION` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `power_table`
--

CREATE TABLE `power_table` (
  `TIME` varchar(100) NOT NULL,
  `DATE` date NOT NULL,
  `POWER_GENERATION` int(5) NOT NULL,
  `LOAD_SECH_SMS2` int(5) NOT NULL,
  `LOAD_SECH_SMS3` int(5) NOT NULL,
  `LOAD_SECH_SMS_TOTAL` int(5) NOT NULL,
  `LOAD_SECH_RAILMILL` int(5) NOT NULL,
  `LOAD_SECH_PLATEMILL` int(5) NOT NULL,
  `LOAD_SECH_SPM` int(5) NOT NULL,
  `LOAD_SECH_NSPL` int(5) NOT NULL,
  `TOTAL` int(5) NOT NULL,
  `UPDATEDBY` varchar(50) NOT NULL,
  `UPDATED_ON` datetime NOT NULL,
  `LOCATION` varchar(10000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `railmill`
--

CREATE TABLE `railmill` (
  `TIME` varchar(100) NOT NULL,
  `DATE` date NOT NULL,
  `LOADSECH` int(5) NOT NULL,
  `UPDATEDBY` varchar(100) NOT NULL,
  `UPDATED_ON` datetime NOT NULL,
  `LOCATION` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms`
--

CREATE TABLE `sms` (
  `TIME` varchar(100) NOT NULL,
  `DATE` date NOT NULL,
  `LOADSECH_SMS2` int(5) NOT NULL,
  `LOADSECH_SMS3` int(5) NOT NULL,
  `UPDATEDBY` varchar(100) NOT NULL,
  `UPDATED_ON` datetime NOT NULL,
  `LOCATION` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spm`
--

CREATE TABLE `spm` (
  `TIME` varchar(100) NOT NULL,
  `DATE` date NOT NULL,
  `LOADSECH` int(5) NOT NULL,
  `UPDATEDBY` varchar(100) NOT NULL,
  `UPDATED_ON` datetime NOT NULL,
  `LOCATION` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE `user_details` (
  `FIRSTNAME` varchar(100) NOT NULL,
  `LASTNAME` varchar(100) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PASSWORD` varchar(50) NOT NULL,
  `EMAILADD` varchar(500) NOT NULL,
  `DEPT` varchar(20) NOT NULL,
  `PHONENUMBER` int(20) NOT NULL,
  `AGE` int(3) NOT NULL,
  `GENDER` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
