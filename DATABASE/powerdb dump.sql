CREATE DATABASE  IF NOT EXISTS `powerdb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `powerdb`;
-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: powerdb
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `jldc`
--

DROP TABLE IF EXISTS `jldc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jldc` (
  `DATE` date NOT NULL,
  `TIME` varchar(50) NOT NULL,
  `POWER_GENERATION` double DEFAULT NULL,
  `UPDATEDBY` varchar(100) DEFAULT NULL,
  `UPDATED_ON` datetime DEFAULT NULL,
  `LOCATION` varchar(100) NOT NULL,
  PRIMARY KEY (`LOCATION`,`DATE`,`TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jldc`
--
/*!40000 ALTER TABLE `jldc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nspl`
--

DROP TABLE IF EXISTS `nspl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nspl` (
  `DATE` date NOT NULL,
  `TIME` varchar(50) NOT NULL,
  `LOADSECH` double DEFAULT NULL,
  `UPDATEDBY` varchar(100) DEFAULT NULL,
  `UPDATED_ON` datetime DEFAULT NULL,
  `LOCATION` varchar(100) NOT NULL,
  PRIMARY KEY (`LOCATION`,`DATE`,`TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `nspl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `platemill`
--

DROP TABLE IF EXISTS `platemill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `platemill` (
  `DATE` date NOT NULL,
  `TIME` varchar(50) NOT NULL,
  `LOADSECH` double DEFAULT NULL,
  `UPDATEDBY` varchar(100) DEFAULT NULL,
  `UPDATED_ON` datetime DEFAULT NULL,
  `LOCATION` varchar(100) NOT NULL,
  PRIMARY KEY (`LOCATION`,`DATE`,`TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40000 ALTER TABLE `platemill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `power_table`
--

DROP TABLE IF EXISTS `power_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `power_table` (
  `DATE` date NOT NULL,
  `TIME` varchar(100) NOT NULL,
  `POWER_GENERATION` double DEFAULT NULL,
  `LOAD_sECH_SMS2` double DEFAULT NULL,
  `LOAD_sECH_SMS3` double DEFAULT NULL,
  `LOAD_SECH_SMS_TOTAL` double DEFAULT NULL,
  `LOAD_SECH_RAILMILL` double DEFAULT NULL,
  `LOAD_SECH_PLATEMILL` double DEFAULT NULL,
  `LOAD_SECH_SPM` double DEFAULT NULL,
  `LOAD_SECH_NSPL` double DEFAULT NULL,
  `TOTAL` double DEFAULT NULL,
  `UPDATEDBY` varchar(100) DEFAULT NULL,
  `UPDATED_ON` datetime DEFAULT NULL,
  `LOCATION` varchar(100) NOT NULL,
  PRIMARY KEY (`LOCATION`,`DATE`,`TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40000 ALTER TABLE `power_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `railmill`
--

DROP TABLE IF EXISTS `railmill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `railmill` (
  `DATE` date NOT NULL,
  `TIME` varchar(50) NOT NULL,
  `LOADSECH` double DEFAULT NULL,
  `UPDATEDBY` varchar(100) DEFAULT NULL,
  `UPDATED_ON` datetime DEFAULT NULL,
  `LOCATION` varchar(100) NOT NULL,
  PRIMARY KEY (`LOCATION`,`DATE`,`TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40000 ALTER TABLE `railmill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms`
--

DROP TABLE IF EXISTS `sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms` (
  `DATE` date NOT NULL,
  `TIME` varchar(50) NOT NULL,
  `LOADSECH_SMS2` double DEFAULT NULL,
  `LOADSECH_SMS3` double DEFAULT NULL,
  `UPDATEDBY` varchar(100) DEFAULT NULL,
  `UPDATED_ON` datetime DEFAULT NULL,
  `LOCATION` varchar(100) NOT NULL,
  PRIMARY KEY (`LOCATION`,`DATE`,`TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40000 ALTER TABLE `sms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spm`
--

DROP TABLE IF EXISTS `spm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spm` (
  `DATE` date NOT NULL,
  `TIME` varchar(50) NOT NULL,
  `LOADSECH` double DEFAULT NULL,
  `UPDATEDBY` varchar(100) DEFAULT NULL,
  `UPDATED_ON` datetime DEFAULT NULL,
  `LOCATION` varchar(100) NOT NULL,
  PRIMARY KEY (`LOCATION`,`DATE`,`TIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40000 ALTER TABLE `spm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_details`
--

DROP TABLE IF EXISTS `user_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_details` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `FIRSTNAME` varchar(100) DEFAULT NULL,
  `LASTNAME` varchar(100) DEFAULT NULL,
  `USERNAME` varchar(100) DEFAULT NULL,
  `PASSWORD` varchar(100) DEFAULT NULL,
  `EMAILADD` varchar(500) DEFAULT NULL,
  `DEPT` varchar(20) DEFAULT NULL,
  `PHONENUMBER` varchar(100) DEFAULT NULL,
  `AGE` varchar(10) DEFAULT NULL,
  `GENDER` varchar(50) DEFAULT NULL,
  `USERLOCATION` varchar(100) DEFAULT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `reset_token_hash` (`reset_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-18 22:00:42
