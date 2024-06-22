-- MySQL dump 10.13  Distrib 8.0.22, for Linux (x86_64)
--
-- Host: localhost    Database: uas_projects
-- ------------------------------------------------------
-- Server version	8.0.22-0ubuntu0.20.04.3

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
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_type`
--

LOCK TABLES `product_type` WRITE;
/*!40000 ALTER TABLE `product_type` DISABLE KEYS */;
INSERT INTO `product_type` VALUES (1,'RGB Ortho','R'),(2,'RGB DEM','R'),(3,'MULTI Ortho','R'),(4,'NDVI','R'),(5,'ExG','R'),(6,'Thermal Ortho','R'),(7,'Canopy Cover Rendered','R'),(8,'GeoJSON','V');
/*!40000 ALTER TABLE `product_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagery_product`
--

DROP TABLE IF EXISTS `imagery_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imagery_product` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Flight` int DEFAULT NULL,
  `Type` int NOT NULL,
  `Bands` varchar(10) DEFAULT NULL,
  `MinZoom` int NOT NULL DEFAULT '17',
  `MaxZoom` int NOT NULL DEFAULT '25',
  `Zoom` int NOT NULL DEFAULT '19',
  `EPSG` int NOT NULL DEFAULT '32614',
  `FileName` varchar(200) NOT NULL,
  `Size` float NOT NULL,
  `ChunkCount` int NOT NULL,
  `Status` varchar(10) NOT NULL,
  `TempFolder` varchar(1000) NOT NULL DEFAULT '',
  `UploadFolder` varchar(1000) NOT NULL DEFAULT '',
  `DownloadPath` varchar(2000) NOT NULL DEFAULT '',
  `DisplayPath` varchar(2000) NOT NULL DEFAULT '',
  `ThumbPath` varchar(2000) NOT NULL DEFAULT '',
  `TMSPath` varchar(2000) NOT NULL DEFAULT '',
  `Boundary` varchar(200) NOT NULL DEFAULT '',
  `Identifier` varchar(300) NOT NULL,
  `Progress` int NOT NULL,
  `LastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Uploader` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Identifier` (`Identifier`),
  KEY `Flight` (`Flight`),
  KEY `Type` (`Type`),
  CONSTRAINT `product_flight_relation` FOREIGN KEY (`Flight`) REFERENCES `flight` (`ID`),
  CONSTRAINT `product_type_relation` FOREIGN KEY (`Type`) REFERENCES `product_type` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1428 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagery_product`
--

LOCK TABLES `imagery_product` WRITE;
/*!40000 ALTER TABLE `imagery_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagery_product` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=1263 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flight`
--

LOCK TABLES `flight` WRITE;
/*!40000 ALTER TABLE `flight` DISABLE KEYS */;
/*!40000 ALTER TABLE `flight` ENABLE KEYS */;
UNLOCK TABLES;

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

-- Dump completed on 2021-01-23  0:44:01
