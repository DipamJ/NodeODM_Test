-- MySQL dump 10.13  Distrib 8.0.22, for Linux (x86_64)
--
-- Host: localhost    Database: uas_projects
-- ------------------------------------------------------
-- Server version	8.0.22-0ubuntu0.20.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `raw_data_upload_status`
--

DROP TABLE IF EXISTS `raw_data_upload_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `raw_data_upload_status` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Flight` int NOT NULL,
  `FileName` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Size` float NOT NULL,
  `ChunkCount` int NOT NULL,
  `Status` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `TempFolder` varchar(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `UploadFolder` varchar(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '',
  `Identifier` varchar(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `Progress` int DEFAULT NULL,
  `CheckSum` varchar(100) NOT NULL DEFAULT '',
  `Uploader` varchar(100) NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Identifier` (`Identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=706 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `raw_data_upload`
--

DROP TABLE IF EXISTS `raw_data_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `raw_data_upload` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UploadID` int NOT NULL,
  `DownloadPath` varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `DisplayPath` varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=287 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Identifier` varchar(100) NOT NULL,
  `Uploader` varchar(100) NOT NULL,
  `Receiver` varchar(100) NOT NULL,
  `CC` varchar(100) NOT NULL,
  `Note` varchar(400) NOT NULL,
  `FileName` varchar(100) NOT NULL,
  `FileSize` float NOT NULL,
  `Project` varchar(100) NOT NULL,
  `Folder` varchar(1000) NOT NULL,
  `Date` varchar(20) NOT NULL,
  `Flight` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=881 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight`
--

DROP TABLE IF EXISTS `flight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Date` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Project` int NOT NULL,
  `Platform` int NOT NULL,
  `Sensor` int NOT NULL,
  `Altitude` float NOT NULL,
  `Forward` float NOT NULL,
  `Side` float NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Project` (`Project`),
  KEY `Platform` (`Platform`),
  KEY `Sensor` (`Sensor`),
  CONSTRAINT `flight_platform_relation` FOREIGN KEY (`Platform`) REFERENCES `platform` (`ID`),
  CONSTRAINT `flight_project_relation` FOREIGN KEY (`Project`) REFERENCES `project` (`ID`),
  CONSTRAINT `flight_sensor_relation` FOREIGN KEY (`Sensor`) REFERENCES `sensor` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1272 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Crop` int NOT NULL,
  `PlantingDate` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `HarvestDate` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Description` varchar(3000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CenterLat` float NOT NULL,
  `CenterLng` float NOT NULL,
  `MinZoom` int NOT NULL,
  `MaxZoom` int NOT NULL,
  `DefaultZoom` int NOT NULL,
  `VisualizationPage` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `platform`
--

DROP TABLE IF EXISTS `platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `platform` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sensor`
--

DROP TABLE IF EXISTS `sensor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sensor` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_type`
--

DROP TABLE IF EXISTS `product_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_type` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Type` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'uas_projects'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-11-03 15:38:53
