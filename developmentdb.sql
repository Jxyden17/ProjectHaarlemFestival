CREATE DATABASE  IF NOT EXISTS `railway` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `railway`;
-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: maglev.proxy.rlwy.net    Database: railway
-- ------------------------------------------------------
-- Server version	9.6.0

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Table structure for table `event_detail_pages`
--

DROP TABLE IF EXISTS `event_detail_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_detail_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `performer_id` int DEFAULT NULL,
  `page_id` int NOT NULL,
  `entity_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'performer',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_detail_pages_page_id` (`page_id`),
  UNIQUE KEY `uq_event_detail_pages_event_performer` (`event_id`,`performer_id`),
  KEY `idx_event_detail_pages_event_id` (`event_id`),
  KEY `idx_event_detail_pages_performer_id` (`performer_id`),
  CONSTRAINT `fk_event_detail_pages_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_detail_pages_page` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_event_detail_pages_performer` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_detail_pages`
--

LOCK TABLES `event_detail_pages` WRITE;
/*!40000 ALTER TABLE `event_detail_pages` DISABLE KEYS */;
INSERT INTO `event_detail_pages` VALUES (1,2,4,19,'performer',10,'2026-03-07 17:48:32','2026-03-11 18:57:26'),(2,2,6,20,'performer',20,'2026-03-07 17:48:32','2026-03-11 16:55:28');
/*!40000 ALTER TABLE `event_detail_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'A Stroll Through History','Guided walking tour through Haarlem'),(2,'Dance','Dance events in Haarlem'),(3,'TellingStory','Storytelling event in Haarlem'),(4,'Yummy','Restaurants in Haarlem'),(5,'Jazz','Jazz event voor haarlem festival');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `languages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
INSERT INTO `languages` VALUES (3,'Chinese'),(2,'Dutch'),(1,'English');
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,18,110.00,'2026-03-22 17:22:09','pending'),(2,18,110.00,'2026-03-22 18:18:11','pending'),(3,18,120.00,'2026-03-22 18:28:28','pending'),(4,18,132.50,'2026-03-22 19:30:18','pending'),(5,18,25.00,'2026-03-22 19:46:12','pending'),(6,18,40.00,'2026-03-22 19:48:26','pending'),(7,18,12.50,'2026-03-22 20:11:26','pending'),(8,18,12.00,'2026-03-22 20:48:44','pending'),(9,18,41.00,'2026-03-22 20:57:53','pending'),(10,18,35.00,'2026-03-22 21:00:07','pending'),(11,18,23.50,'2026-03-22 21:01:57','pending'),(12,18,297.50,'2026-03-22 21:14:22','pending'),(13,18,86.00,'2026-03-22 21:25:53','pending'),(14,18,10.00,'2026-03-22 21:27:30','pending'),(15,18,17.50,'2026-03-22 21:30:11','pending'),(16,17,6.00,'2026-03-23 18:12:16','pending'),(17,17,55.00,'2026-03-23 18:35:14','pending'),(18,18,6.00,'2026-03-25 06:30:34','pending'),(19,17,30.00,'2026-03-25 10:04:37','pending'),(20,20,50.00,'2026-03-25 12:36:52','pending'),(21,8,66.00,'2026-03-25 12:56:11','pending'),(22,18,92.50,'2026-04-02 21:27:14','pending'),(23,18,6.00,'2026-04-02 21:37:48','pending'),(24,18,17.50,'2026-04-02 21:38:34','pending'),(25,18,12.50,'2026-04-02 21:39:07','pending'),(26,18,6.00,'2026-04-02 23:27:52','pending'),(27,18,58.50,'2026-04-03 00:00:04','pending'),(28,18,35.00,'2026-04-03 00:06:00','pending');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_sections`
--

DROP TABLE IF EXISTS `page_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_id` int NOT NULL,
  `section_type` varchar(50) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `order_index` int DEFAULT '0',
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  CONSTRAINT `page_sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_sections`
--

LOCK TABLES `page_sections` WRITE;
/*!40000 ALTER TABLE `page_sections` DISABLE KEYS */;
INSERT INTO `page_sections` VALUES (1,1,'hero','A Stroll Through History',0,'Uncover the secrets of the Golden Age, hidden courtyards, and vibrant local life.',NULL),(3,1,'grid','History',2,NULL,NULL),(4,1,'discover','Discover Historic Haarlem',2,'<p>Discover why Haarlem is called the \'Little Amsterdam\'—but with more charm and fewer crowds. In this exclusive 2.5-hour guided walking tour, you will travel back to the Dutch Golden Age.</p>','<p>From the bustling Grote Markt to the hidden Hofjes (courtyards) where time seems to stand still. Our expert guides will reveal the stories behind the facades, the secrets of the spice trade, and the legends of local heroes like Kenau.</p>'),(8,3,'grid','<p>Featured Storytellers</p>',1,NULL,NULL),(9,3,'venues','<p>Festival Venues</p>',2,'<p>Plan your storytelling journey through the festival</p>',NULL),(10,3,'schedule','<p>Stories Schedule</p>',3,NULL,NULL),(11,3,'explore','<p>Explore more during The Festival</p>',4,NULL,NULL),(12,3,'faq','<p>FAQ</p>',5,NULL,NULL),(13,1,'guide','The Route',3,'A Historical Walkpath','<p>Our guides are local historians who love their city. Find out who leads you through old exhibitions, hidden courtyards, and the legends of Haarlem.</p>'),(14,2,'header','St.-Bavokerk123',0,'A Gothic masterpiece watching over the city for centuries.',NULL),(15,2,'history','History',1,NULL,'Learn more about the rich architectural and cultural history of the church.'),(16,2,'did_you_know','Did you know?',2,NULL,'Fascinating secrets and legends hidden within the walls.'),(17,2,'openings_time','Opening Hours',3,'Contact','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.4085245741785!2d4.632330831776949!3d52.38114417415409!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6bea0a4215%3A0x2cefd774cf4e0dab!2sThe%20St.%20Bavo%20Church%20in%20Haarlem!5e0!3m2!1spl!2snl!4v1771018889191!5m2!1spl!2snl'),(18,7,'header','Grote Markt',1,'The beating heart of Haarlem, where every stone tells a story.',NULL),(19,8,'header','De Hallen',0,'Renaissance grandeur housing the boldest strokes of modern art.',NULL),(20,9,'header','Proveniershof',1,'A hidden garden of tranquility once home to the city\'s pilgrims.',NULL),(21,10,'header','Jopenkerk',1,'From sacred prayers to golden beers: a transformation like no other.',NULL),(22,11,'header','Waalse Kerk',0,'The city\'s oldest sanctuary, a quiet witness to centuries of change.',NULL),(23,12,'header','Molen de Adriaan',1,'A skyline icon rising from the ashes to guard the river.',NULL),(24,13,'header','Amsterdamse Poort',1,'The last silent sentinel standing guard over Haarlem\'s medieval borders.',NULL),(25,14,'header','Hof van Bakenes',1,'Step back in time within the walls of the nation\'s oldest courtyard.',NULL),(26,2,'history','History',1,NULL,'Learn more about the rich architectural and cultural history of the church.'),(27,7,'history','History of Grote Markt',2,NULL,NULL),(28,8,'history','History of De Hallen',1,NULL,NULL),(29,9,'history','History of Proveniershof',2,NULL,NULL),(30,10,'history','History of Jopenkerk',2,NULL,NULL),(31,11,'history','History of Waalse Kerk',1,NULL,NULL),(32,12,'history','History of Molen de Adriaan',2,NULL,NULL),(33,13,'history','History of Amsterdamse Poort',2,NULL,NULL),(34,14,'history','History of Hof van Bakenes',2,NULL,NULL),(36,7,'did_you_know','Did you know?',3,NULL,NULL),(37,8,'did_you_know','Did you know?',2,NULL,NULL),(38,9,'did_you_know','Did you know?',3,NULL,NULL),(39,10,'did_you_know','Did you know?',3,NULL,NULL),(40,11,'did_you_know','Did you know?',2,NULL,NULL),(41,12,'did_you_know','Did you know?',3,NULL,NULL),(42,13,'did_you_know','Did you know?',3,NULL,NULL),(43,14,'did_you_know','Did you know?',3,NULL,NULL),(44,1,'tour_overview','Stops on the tour',1,NULL,NULL),(45,2,'contact','Contact',4,NULL,NULL),(46,7,'openings_time','Opening Hours',4,'Contact','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.394580858684!2d4.633654577592865!3d52.38139697202522!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6b972cd28d%3A0x909952e59fa28472!2sGrote%20Markt%2C%20Haarlem!5e0!3m2!1spl!2snl!4v1771028518351!5m2!1spl!2snl'),(47,8,'openings_time','Opening Hours',3,NULL,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.6597482547413!2d4.63105317759269!3d52.37658937202401!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef402ff6db3b%3A0x48cdf25945154d75!2sMuzeum%20Fransa%20Halsa!5e0!3m2!1spl!2snl!4v1771028631884!5m2!1spl!2snl'),(48,9,'openings_time','Opening Hours',4,'Contact','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4871.234900723815!2d4.628249877592708!3d52.377356272024215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef1555569fcf%3A0x1f066ef6d1316959!2sProveniershuis!5e0!3m2!1spl!2snl!4v1771028724192!5m2!1spl!2snl'),(49,10,'openings_time','Opening Hours',4,'Contact','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4871.234900723815!2d4.628249877592708!3d52.377356272024215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef14ed768603%3A0x5ff6ab7a87061c90!2sJopen!5e0!3m2!1spl!2snl!4v1771028822816!5m2!1spl!2snl'),(50,11,'openings_time','Opening Hours',3,NULL,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4870.668983325747!2d4.636578977592917!3d52.38248637202544!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6eac878693%3A0x4f36049541e081f1!2sWaalse%20Kerk!5e0!3m2!1spl!2snl!4v1771028873665!5m2!1spl!2snl'),(51,12,'openings_time','Opening Hours',4,'Contact','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.2570594732692!2d4.640108877592984!3d52.38389017202583!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6ee47c7b93%3A0xb548e94f26e9e63b!2sWindmill%20De%20Adriaan%20(1779)!5e0!3m2!1spl!2snl!4v1771028911268!5m2!1spl!2snl'),(52,13,'openings_time','Opening Hours',4,'Contact','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.4431634384737!2d4.644023877592816!3d52.38051617202511!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef663e696523%3A0x6b7eed60d6568553!2sAmsterdamse%20Poort%2C%20Haarlem!5e0!3m2!1spl!2snl!4v1771028953927!5m2!1spl!2snl'),(53,14,'openings_time','Opening Hours',4,'Contact','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.3846469519535!2d4.637363377592878!3d52.381577072025294!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef000c245f87%3A0x132348d65b49b1be!2sHofje%20van%20Bakenes!5e0!3m2!1spl!2snl!4v1771029001817!5m2!1spl!2snl'),(54,15,'hero','Main Festival Hero',0,NULL,NULL),(55,15,'about','About The Festival',1,'The Festival brings together culture, storytelling, music, food and history across Haarlem. During one vibrant week, the city transforms into a stage for live performances, intimate stories and shared experiences for all ages.','Events take place across multiple locations in Haarlem.'),(56,15,'discover_events','Discover Events',2,NULL,NULL),(57,15,'map_section','Festival Map',3,NULL,NULL),(58,15,'faq','Frequently Asked Questions',4,NULL,NULL),(59,18,'dance_banner','Dance Events In Haarlem',10,'This Weekend - July 24-26, 2026','<p>Discover the best <strong>dance</strong> music events happening this weekend. From progressive house to trance, experience world-class DJs in Haarlem\'s top venues.</p>'),(60,18,'dance_info','Important Information',20,NULL,'<ol><li data-list=\"bullet\">All shows are 18+ with valid ID required</li><li data-list=\"bullet\">Limited capacity book early to avoid disappointment</li><li data-list=\"bullet\">Doors open 1 hour before show time</li><li data-list=\"bullet\">No refunds on purchased tickets</li></ol>'),(61,18,'dance_schedule','DANCE! Festival Schedule',5,NULL,NULL),(62,18,'dance_artists','Featured Artists',15,NULL,NULL),(63,18,'dance_passes','All-Access Passes',40,NULL,NULL),(64,18,'dance_capacity','Capacity & Entry',50,NULL,'<p>• Club sessions have very limited capacity</p><p>• All-Access Pass entry is not guaranteed, due to safety regulations</p><p>Ticket allocation:</p><p>90% Single tickets</p><p>10% Walk-ins &amp; All-Access Pass holders</p>'),(65,18,'dance_special_session','Special Session: TiestoWorld',60,NULL,'<ol><li data-list=\"bullet\">A career-spanning Tiësto experience</li><li data-list=\"bullet\">Includes special guest appearances</li></ol>'),(66,16,'yummy_header','<p>Yummy!</p>',0,NULL,'<p>Gourmet with a twist</p>'),(67,16,'yummy-map','<p>Discover yummy locations</p>',1,NULL,NULL),(68,16,'yummy-restaurants','<p>Featured restaurants</p>',2,NULL,NULL),(69,19,'dance_detail_hero','Martin Garrix',10,'Featured Artists','World-Renowned DJ • Music Producer • Multi-Award-Winning EDM Artist'),(70,19,'dance_detail_highlights','Career Highlights',20,NULL,NULL),(71,19,'dance_detail_tracks','Iconic Tracks',30,NULL,'Click on any track to preview (simulated playback)'),(72,20,'dance_detail_hero','Tiësto',10,'Featured Artist','DJ Legend • Producer • Grammy Winner'),(73,20,'dance_detail_highlights','Career Highlights',20,NULL,NULL),(74,20,'dance_detail_tracks','Iconic Tracks',30,'','Click on any track to preview (simulated playback)'),(75,19,'dance_detail_info','Important Information',40,NULL,'<ol><li data-list=\"bullet\">Doors open 30 minutes before showtime.</li><li data-list=\"bullet\">Please bring a valid ticket confirmation.</li><li data-list=\"bullet\">Line-up times may be subject to change.</li></ol>'),(76,20,'dance_detail_info','Important Information',40,NULL,'<ol><li data-list=\"bullet\">Doors open 30 minutes before showtime.</li><li data-list=\"bullet\">Please bring a valid ticket confirmation.</li><li data-list=\"bullet\">Line-up times may be subject to change.</li></ol>'),(77,3,'hero','<p>Stories in Haarlem</p>',0,'Discover the fascinating stories of Haarlem through captivating storytelling events.',NULL),(78,21,'restaurant_hero','<p>Ratatouille</p>',0,NULL,NULL),(79,21,'restaurant_concept','<p>Concept</p>',1,NULL,'<p>Michelin star experience focusing on organic produce. Chef Jozua Jaring brings his passion for sustainable fishing and seasonal ingredients to create an unforgettable dining experience along the beautiful Spaarne river</p>'),(80,21,'restaurant_contact','<p>Contact and Location</p>',2,NULL,NULL),(81,17,'restaurant_hero','<p>Cafe De Roemer</p>',0,NULL,NULL),(82,17,'restaurant_concept','<p>Concept</p>',1,NULL,'<p>Café de Roemer blends classic charm with a warm, informal atmosphere and attentive service. Their freshly roasted coffee is brewed to order and pairs perfectly with a slice of homemade cake.</p>'),(83,17,'restaurant_contact','<p>Contact &amp; Location</p>',2,NULL,NULL),(84,3,'callout','Live stories across the city',15,'Intimate stories, live podcasts, and family theater','Stories in Haarlem brings together a diverse program of storytelling formats, including spoken-word performances, live podcasts, and theatrical stories for all ages. The festival explores themes of history, imagination, and human experience, with stories inspired by both local heritage and contemporary perspectives. Events take place at various locations across Haarlem, creating an accessible and intimate atmosphere where audiences can listen, reflect, and connect through stories.'),(103,4,'booking','Book Your Experience',50,NULL,NULL),(104,4,'featured','Featured Stories',40,NULL,NULL),(105,4,'about','About the Artist',30,NULL,NULL),(106,4,'gallery','Gallery',20,NULL,NULL),(107,4,'hero','Mister Anansi',10,NULL,NULL),(113,6,'booking','Book Your Experience',50,NULL,NULL),(114,6,'featured','Featured Stories',40,NULL,NULL),(115,6,'about','About the Story',30,NULL,NULL),(116,6,'gallery','Gallery',20,NULL,NULL),(117,6,'hero','Corrie ten Boom',10,NULL,NULL),(123,5,'booking','Book Your Experience',50,NULL,NULL),(124,5,'featured','Featured Stories',40,NULL,NULL),(125,5,'about','About the Artist',30,NULL,NULL),(126,5,'gallery','Gallery',20,NULL,NULL),(127,5,'hero','Omdenken Podcast',10,NULL,NULL),(128,27,'hero','Personal Program',10,'All tickets that you are interested in are here',NULL),(129,27,'schedule','Your Personal Schedule',20,NULL,NULL),(131,28,'jazz_schedule','Jazz Schedule',5,NULL,NULL),(133,28,'jazz_artists','Featured Artist',15,NULL,NULL),(135,28,'jazz_passes','Jazz daypasses',10,NULL,NULL),(138,29,'header','Title',0,'A Gothic masterpiece watching over the city for centuries.',NULL),(139,29,'history','History',1,NULL,NULL),(140,29,'did_you_know','Did you know?',2,NULL,NULL),(141,29,'openings_time','aaaaaaaaaaa',3,NULL,'aaaaaaaaaaaa'),(142,30,'hero','New Podcast Night',1,'Add a short intro for this Stories event.','Update this section with the main hook for your event page.'),(143,30,'about','About this story',2,'',''),(144,30,'gallery','Gallery',3,'',''),(145,30,'featured','Featured audio',4,'',''),(146,30,'booking','Book your experience',5,'Choose your ticket and reserve your spot.','Update the booking details once the session is ready.'),(147,31,'hero','Forpan',1,'Add a short intro for this Stories event.','Update this section with the main hook for your event page.'),(148,31,'about','About this story',2,'',''),(149,31,'gallery','Gallery',3,'',''),(150,31,'featured','Featured audio',4,'',''),(151,31,'booking','Book your experience',5,'Choose your ticket and reserve your spot.','Update the booking details once the session is ready.');
/*!40000 ALTER TABLE `page_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,1,'history-stroll','A Stroll Through History','2026-02-09 16:05:59'),(2,1,'st-bavo','St.-Bavokerk123','2026-02-10 19:21:00'),(3,3,'storytelling-home','Home Storytelling','2026-02-12 16:03:43'),(4,3,'mister-anansi','Mister Anansi','2026-02-12 16:03:43'),(5,3,'omdenken-podcast','Omdenken Podcast','2026-02-12 16:03:43'),(6,3,'corrie-ten-boom','Corrie ten Boom','2026-02-12 16:03:43'),(7,1,'grote-markt','Grote Markt','2026-02-13 02:22:56'),(8,1,'de-hallen','De Hallen','2026-02-13 02:22:56'),(9,1,'proveniershof','Proveniershof','2026-02-13 02:22:56'),(10,1,'jopenkerk','Jopenkerk','2026-02-13 02:22:56'),(11,1,'waalse-kerk','Waalse Kerk','2026-02-13 02:22:56'),(12,1,'molen-de-adriaan','Molen de Adriaan','2026-02-13 02:22:56'),(13,1,'amsterdamse-poort','Amsterdamse Poort','2026-02-13 02:22:56'),(14,1,'hof-van-bakenes','Hof van Bakenes','2026-02-13 02:22:56'),(15,NULL,'Home','The Festival Haarlem - Home','2026-02-17 00:24:02'),(16,4,'yummy','Yummy','2026-02-18 09:50:56'),(17,4,'cafe-de-roemer','Cafe de roemer','2026-02-18 09:50:56'),(18,2,'dance-home','Dance Homeasdas','2026-03-01 15:51:59'),(19,2,'martin-garrix','Dance Detail - Martin Garrix','2026-03-07 14:38:03'),(20,2,'tiesto','Dance Detail - Tiesto','2026-03-07 14:38:10'),(21,4,'ratatouille','Ratatouille','2026-03-09 12:37:30'),(22,4,'restaurant-ML','Restaurant ML','2026-03-09 12:37:31'),(23,4,'restaurant-fris','Restaurant Fris','2026-03-09 12:37:31'),(24,4,'new-vegas','New Vegas','2026-03-09 12:37:31'),(25,4,'grand-cafe-brinkman','Grand Cafe Brinkman','2026-03-09 12:37:31'),(26,4,'urban-frenchy-bistro-toujours','Urban Frenchy Bistro Toujours','2026-03-09 12:37:31'),(27,NULL,'personal-program','Personal Program','2026-03-22 14:04:08'),(28,5,'jazz-home','jazz homepage','2026-03-25 11:29:29'),(29,1,'hello','Hello','2026-04-01 02:37:27'),(30,3,'new-podcast-night','New Podcast Night','2026-04-03 00:32:02'),(31,3,'forpan','Forpan','2026-04-03 00:35:29');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_password_resets_token_hash` (`token_hash`),
  KEY `idx_password_resets_user_id` (`user_id`),
  KEY `idx_password_resets_expires_at` (`expires_at`),
  CONSTRAINT `fk_password_resets_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (9,8,'324d06f4791482108b235cae39ad0cd36c0b79449b759ec57112b84454f2e4fe','2026-02-18 11:44:32','2026-02-18 10:44:43');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `cart_id` int DEFAULT NULL,
  `method` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `provider_payment_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `cart_id` (`cart_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_cart` FOREIGN KEY (`cart_id`) REFERENCES `shopping_carts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `performers`
--

DROP TABLE IF EXISTS `performers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `performers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `performer_name` varchar(255) NOT NULL,
  `performer_type` varchar(100) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `performers_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performers`
--

LOCK TABLES `performers` WRITE;
/*!40000 ALTER TABLE `performers` DISABLE KEYS */;
INSERT INTO `performers` VALUES (1,2,'Afrojack','DJ','House','2026-02-10 16:40:38'),(2,2,'Armin van Buuren','DJ','Trance','2026-02-10 16:40:38'),(3,2,'Hardwell','DJ','Dance','2026-02-10 16:40:38'),(4,2,'Martin Garrix','DJ','Dance & Electronic','2026-02-10 16:40:38'),(5,2,'Nicky Romero','DJ','Electro House','2026-02-10 16:40:38'),(6,2,'Tiësto','DJ','Trance','2026-02-10 16:40:38'),(7,1,'Jan-Willem','Dutch',NULL,'2026-02-11 13:16:05'),(8,3,'Mister Anansi','Kids / Theatre','Caribbean spider tales brought to life with vibrant puppetry and storytelling.','2026-02-12 19:01:18'),(9,3,'Meneer Anansi','Kids / Theatre','Dutch family-friendly storytelling show featuring Anansi tales.','2026-02-12 19:01:18'),(10,3,'Omdenken Podcast','Podcast','Live podcast recording exploring perspectives and challenging assumptions.','2026-02-12 19:01:18'),(11,3,'Flip Thinking Podcast','Podcast','English live podcast session focused on rethinking assumptions and ideas.','2026-02-12 19:01:18'),(12,3,'Corrie ten Boom','History','The powerful story of a Dutch family who saved Jewish lives during WWII.','2026-02-12 19:01:18'),(13,3,'Corrie voor kinderen','Kids / History','A child-friendly version of the Corrie ten Boom story.','2026-02-12 19:01:18'),(14,3,'Stories for Haarlem','Storytelling','Local stories inspired by Haarlem’s heritage and contemporary perspectives.','2026-02-12 19:01:18'),(15,3,'Verhalen voor Haarlem','Storytelling','Dutch storytelling session inspired by Haarlem and its people.','2026-02-12 19:01:18'),(16,3,'The story of Buurderij Haarlem','Storytelling','A community story session hosted at Kweekcafé.','2026-02-12 19:01:18'),(17,3,'Het verhaal van de Oezerzwammerij','Storytelling','Storytelling session about the Oezerzwammerij, hosted at Kweekcafé.','2026-02-12 19:01:18'),(18,3,'Podcastlast Haarlem Special','Podcast','Special live podcast edition recorded with an audience in Haarlem.','2026-02-12 19:01:18'),(19,1,'Lianne','Expert guide','Expert guide specializing in Haarlem architecture.','2026-02-15 15:40:04'),(20,1,'Chen','Expert guide','Specialized in Chinese spoken history tours.','2026-02-15 15:40:04'),(21,5,'Gumbo Kings',NULL,NULL,'2026-02-18 10:08:14'),(22,5,'Evolve',NULL,NULL,'2026-02-18 10:09:02'),(23,5,'Ntjam Rosie',NULL,NULL,'2026-02-18 10:09:38'),(24,5,'Wicked Jazz Sounds\r\n',NULL,NULL,'2026-02-18 10:10:40'),(25,5,'Wouter Hamel\r\n',NULL,NULL,'2026-02-18 10:11:02'),(26,5,'Jonna Frazer\r\n',NULL,NULL,'2026-02-18 10:11:19'),(27,5,'Karsu\r\n',NULL,NULL,'2026-02-18 10:11:36'),(28,5,'Uncle Sue\r\n',NULL,NULL,'2026-02-18 10:11:56'),(29,5,'Chris Allen\r\n',NULL,NULL,'2026-02-18 10:12:12'),(30,5,'Myles Sanko\r\n',NULL,NULL,'2026-02-18 10:12:24'),(31,5,'Ilse Huizinga\r\n',NULL,NULL,'2026-02-18 10:12:36'),(32,5,'Eric Vloeimans and Hotspot!\r\n',NULL,NULL,'2026-02-18 10:12:57'),(33,5,'Gare du Nord\r\n',NULL,NULL,'2026-02-18 10:13:12'),(34,5,'Rilan & The Bombadiers\r\n',NULL,NULL,'2026-02-18 10:13:25'),(35,5,'Soul Six\r\n',NULL,NULL,'2026-02-18 10:13:39'),(36,5,'Han Bennink\r\n',NULL,NULL,'2026-02-18 10:13:51'),(37,5,'The Nordanians\r\n',NULL,NULL,'2026-02-18 10:14:21'),(38,5,'Lilith Merlot\r\n',NULL,NULL,'2026-02-18 10:14:21'),(39,5,'Ruis Soundsystem\r\n',NULL,NULL,'2026-02-18 10:14:38'),(40,3,'Winnie de Poeh','Act',NULL,'2026-03-12 20:50:46'),(41,3,'Omdenken Podcast','Act',NULL,'2026-03-12 20:50:46'),(42,3,'The story of Buurderij Haarlem','Act',NULL,'2026-03-12 20:50:46'),(43,3,'Corrie voor kinderen','Act',NULL,'2026-03-12 20:50:46'),(44,3,'verhalen voor Haarlem','Act',NULL,'2026-03-12 20:50:46'),(45,3,'Het verhaal van de Oeserzwammerij','Act',NULL,'2026-03-12 20:50:46'),(46,3,'Flip Thinking Podcast','Act',NULL,'2026-03-12 20:50:46'),(47,3,'Meneer Anansi','Act',NULL,'2026-03-12 20:50:46'),(48,3,'De geschiedenis van familie ten Boom','Act',NULL,'2026-03-12 20:50:46'),(49,3,'Podcast Haarlem Special','Act',NULL,'2026-03-12 20:50:46'),(50,3,'Mister Anansi','Act',NULL,'2026-03-12 20:50:46'),(51,3,'The history of the Ten Boom Family','Act',NULL,'2026-03-12 20:50:46'),(62,3,'aaaaaaaa','NONE','hello','2026-03-25 08:51:36');
/*!40000 ALTER TABLE `performers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrator'),(3,'Customer'),(2,'Employee');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `section_items`
--

DROP TABLE IF EXISTS `section_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `section_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `image_path` varchar(255) DEFAULT NULL,
  `link_url` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `order_index` int DEFAULT '0',
  `item_category` text,
  `item_subtitle` varchar(255) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT 'fa-clock',
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `section_items_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `page_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=613 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section_items`
--

LOCK TABLES `section_items` WRITE;
/*!40000 ALTER TABLE `section_items` DISABLE KEYS */;
INSERT INTO `section_items` VALUES (9,4,'Duration','<p>2.5 hours</p>',NULL,NULL,0,'grid',NULL,NULL,'?'),(10,4,'Group size','<p>Max 12 participants</p>',NULL,NULL,1,'grid',NULL,NULL,'?'),(11,4,'Language','<p>NL/EN</p>',NULL,NULL,2,'grid',NULL,NULL,'?'),(12,4,'Reviews','<p>4.9 (127 reviews)</p>',NULL,NULL,3,'grid',NULL,NULL,'?'),(13,1,'','','/img/historyIMG/hero1.png','',1,NULL,'','',''),(14,1,'','','/img/historyIMG/hero2.jpg','',2,NULL,'','',''),(15,1,'','','/img/historyIMG/hero3.png','',3,NULL,'','',''),(22,4,'Regular Ticket','<p>€37,50</p>',NULL,'Per person',4,'price',NULL,NULL,NULL),(23,4,'Family Ticket','<p>€60,00</p>',NULL,'2 adults + 2 kids',5,'price',NULL,NULL,NULL),(24,4,'Minimum age 12 years',NULL,NULL,NULL,6,'info',NULL,NULL,NULL),(25,4,'Strollers are not allowed',NULL,NULL,NULL,7,'info',NULL,NULL,NULL),(26,4,'Group size 12 participants + 1 guide',NULL,NULL,NULL,8,'info',NULL,NULL,NULL),(27,4,'Breaks at cafeterias (stop 5)',NULL,NULL,NULL,9,'info',NULL,NULL,NULL),(39,8,'<p>Mister Anansi</p>','<p>Caribbean spider tales brought to life with vibrant puppetry and storytelling</p>','/img/storiesIMG/Collage-photo1.jpg','/mister-anansi',0,'storyteller','Tag: Kids | Caribbean spider tales brought to life with vibrant puppetry and storytelling',NULL,'fa-clock'),(40,8,'<p>Omdenken Podcast</p>','<p>Live podcast recording exploring perspectives and challenging assumptions</p>','/img/storiesIMG/Collage-photo2.jpg','/omdenken-podcast',1,'grid','Tag: Adults | Live podcast recording exploring perspectives and challenging assumptions',NULL,'fa-clock'),(41,8,'<p>Corrie ten Boom</p>','<p>The powerful story of a Dutch family who saved Jewish lives during WWII</p>','/img/storiesIMG/Collage-photo3.jpg','/corrie-ten-boom',2,'grid','Tag: History | The powerful story of a Dutch family who saved Jewish lives during WWII',NULL,'fa-clock'),(44,10,'<p>Schedule Info</p>','<p>Filter sessions by day and language to plan your visit.</p>',NULL,NULL,0,'info',NULL,NULL,'fa-clock'),(46,11,'<p>Haarlem Jazz</p>','<p>Enjoy live jazz performances across the city during the festival evenings.</p>',NULL,'/haarlem-jazz',1,'promo',NULL,NULL,'fa-clock'),(51,12,'<p>Where do the performances take place?</p>','<p>Across multiple venues in Haarlem. Each session shows its location.</p>',NULL,NULL,4,'faq',NULL,NULL,'fa-clock'),(53,13,'Albert','<p>Expert on the Dutch Golden Age and Haarlem\'s secrets.</p>','/img/historyIMG/Guide1.jpg',NULL,0,'guide','Role: Senior Guide en Storyteller',NULL,NULL),(54,13,'Marta','<p>Passionate about hidden courtyards and local art.</p>','/img/historyIMG/Guide2.jpg',NULL,1,'guide','Role: Master Guide &amp; Architectural Historian',NULL,NULL),(55,13,'Peter','<p>Tells the stories behind the facades and monuments.</p>','/img/historyIMG/Guide3.jpg',NULL,2,'guide','Role: Cultural Guide &amp; Art Lover',NULL,NULL),(56,15,'The Gothic Giant','<p>Dominating the Grote Markt skyline, the St.-Bavokerk is more than just a church; it is the soul of Haarlem. Built between 1370 and 1520, this architectural marvel has survived fires, sieges, and revolutions. Step inside to experience the silence and grandeur of the Dutch Golden Age.</p>','/img/historyIMG/kerkHistory1.png',NULL,0,'history_card',NULL,NULL,'fa-clock'),(57,15,'A Musical Legend','<p>The church is world-famous for its massive Christian Müller organ. When it was completed in 1738, it was the largest organ in the world. Its fame was so great that composers like Handel and Mendelssohn traveled to Haarlem just to play it. Legend has it that a young Wolfgang Amadeus Mozart played this very instrument when he was only 10 years old.</p>','/img/historyIMG/kerkHistory2.png',NULL,1,'history_card',NULL,NULL,'fa-clock'),(58,15,'The Cannonball','<p>Look closely at the walls, and you might find a remnant of the Spanish Siege of Haarlem (1572-1573). A Spanish cannonball is still embedded in the church wall, a silent witness to the city’s turbulent past and the resilience of its citizens.</p>','/img/historyIMG/kerkHistory3.png',NULL,2,'history_card',NULL,NULL,'fa-clock'),(62,17,'Monday','10:00 AM - 5:00 PM','/img/historyIMG/entree1.png',NULL,3,'opening_hours',NULL,NULL,'fa-clock'),(63,17,'Tuesday','10:00 AM - 5:00 PM',NULL,NULL,4,'opening_hours',NULL,NULL,'fa-clock'),(64,17,'Wednesday','10:00 AM - 5:00 PM',NULL,NULL,5,'opening_hours',NULL,NULL,'fa-clock'),(65,17,'Thursday','10:00 AM - 5:00 PM',NULL,NULL,6,'opening_hours',NULL,NULL,'fa-clock'),(66,17,'Friday','10:00 AM - 5:00 PM',NULL,NULL,7,'opening_hours',NULL,NULL,'fa-clock'),(67,17,'Saturday','10:00 AM - 5:00 PM',NULL,NULL,8,'opening_hours',NULL,NULL,'fa-clock'),(68,17,'Sunday','Closed for services',NULL,NULL,9,'opening_hours',NULL,NULL,'fa-clock'),(69,17,'extra','Closed on Sundays and public holidays',NULL,NULL,10,'opening_hours',NULL,NULL,'fa-clock'),(70,14,'Church of St. Bavo','A Gothic masterpiece watching over the city for centuries.','/img/historyIMG/header1.png',NULL,0,'hero',NULL,NULL,'fa-clock'),(71,14,'Church of St. Bavo',NULL,'/img/historyIMG/header2.png',NULL,1,'hero',NULL,NULL,'fa-clock'),(72,14,'Church of St. Bavo',NULL,'/img/historyIMG/header3.png',NULL,2,'hero',NULL,NULL,'fa-clock'),(73,18,'Grote Markt','Experience the vibrant energy of Haarlem\'s historic center, surrounded by monumental architecture.','/img/historyIMG/hero.png',NULL,1,'hero',NULL,NULL,'fa-clock'),(74,18,'Grote Markt','','/img/historyIMG/hero.png',NULL,2,'hero',NULL,NULL,'fa-clock'),(75,18,'Grote Markt','','/img/historyIMG/hero.png',NULL,3,'hero',NULL,NULL,'fa-clock'),(76,19,'De Hallen','Discover the perfect blend of 17th-century architecture and the boldest strokes of modern art.','/img/historyIMG/hero.png',NULL,0,'hero',NULL,NULL,NULL),(77,19,'De Hallen',NULL,'/img/historyIMG/hero.png',NULL,1,'hero',NULL,NULL,NULL),(78,19,'De Hallen',NULL,'/img/historyIMG/hero.png',NULL,2,'hero',NULL,NULL,NULL),(79,20,'Proveniershof','A peaceful oasis where history and nature meet in the silence of this hidden city garden.','/img/historyIMG/hero.png',NULL,1,'hero',NULL,NULL,'fa-clock'),(80,20,'Proveniershof','','/img/historyIMG/hero.png',NULL,2,'hero',NULL,NULL,'fa-clock'),(81,20,'Proveniershof','','/img/historyIMG/hero.png',NULL,3,'hero',NULL,NULL,'fa-clock'),(82,21,'Jopenkerk','Taste the rich brewing history of Haarlem inside this stunningly restored former church.','/img/historyIMG/hero.png',NULL,1,'hero',NULL,NULL,'fa-clock'),(83,21,'Jopenkerk','','/img/historyIMG/hero.png',NULL,2,'hero',NULL,NULL,'fa-clock'),(84,21,'Jopenkerk','','/img/historyIMG/hero.png',NULL,3,'hero',NULL,NULL,'fa-clock'),(85,22,'Waalse Kerk','Admire the timeless beauty and serene atmosphere of the oldest standing church in the city.','/img/historyIMG/hero.png',NULL,0,'hero',NULL,NULL,NULL),(86,22,'Waalse Kerk',NULL,'/img/historyIMG/hero.png',NULL,1,'hero',NULL,NULL,NULL),(87,22,'Waalse Kerk',NULL,'/img/historyIMG/hero.png',NULL,2,'hero',NULL,NULL,NULL),(88,23,'Molen de Adriaan','The most representative windmill in Haarlem, rising from the ashes to guard the Spaarne river.','/img/historyIMG/molen1.png',NULL,1,'hero',NULL,NULL,'fa-clock'),(89,23,'Molen de Adriaan','','/img/historyIMG/molen2.png',NULL,2,'hero',NULL,NULL,'fa-clock'),(90,23,'Molen de Adriaan','','/img/historyIMG/molen3.png',NULL,3,'hero',NULL,NULL,'fa-clock'),(91,24,'Amsterdamse Poort','Cross the threshold of history at the last remaining gate of Haarlem\'s medieval borders.','/img/historyIMG/hero.png',NULL,1,'hero',NULL,NULL,'fa-clock'),(92,24,'Amsterdamse Poort','','/img/historyIMG/hero.png',NULL,2,'hero',NULL,NULL,'fa-clock'),(93,24,'Amsterdamse Poort','','/img/historyIMG/hero.png',NULL,3,'hero',NULL,NULL,'fa-clock'),(94,25,'Hof van Bakenes','Step back in time within the walls of the oldest preserved almshouse in the Netherlands.','/img/historyIMG/hero.png',NULL,1,'hero',NULL,NULL,'fa-clock'),(95,25,'Hof van Bakenes','','/img/historyIMG/hero.png',NULL,2,'hero',NULL,NULL,'fa-clock'),(96,25,'Hof van Bakenes','','/img/historyIMG/hero.png',NULL,3,'hero',NULL,NULL,'fa-clock'),(97,26,'The Gothic Giant','Dominating the Grote Markt skyline, the St.-Bavokerk is more than just a church; it is the soul of Haarlem. Built between 1370 and 1520, this architectural marvel has survived fires, sieges, and revolutions.','/img/historyIMG/kerkHistory1.png',NULL,0,'card',NULL,NULL,'fa-clock'),(98,26,'A Musical Legend','The church is world-famous for its massive Christian Müller organ. When it was completed in 1738, it was the largest organ in the world. Legend has it that a young Wolfgang Amadeus Mozart played this very instrument when he was only 10 years old.','/img/historyIMG/kerkHistory2.png',NULL,0,'card',NULL,NULL,'fa-clock'),(99,26,'The Cannonball','Look closely at the walls, and you might find a remnant of the Spanish Siege of Haarlem (1572-1573). A Spanish cannonball is still embedded in the church wall, a silent witness to the city\'s turbulent past.','/img/historyIMG/kerkHistory3.png',NULL,0,'card',NULL,NULL,'fa-clock'),(100,16,'The Bells of Damiate','<p>If you are in Haarlem at 9:00 PM, listen closely. Every night, the \'Damiaatjes\' bells ring for thirty minutes. This centuries-old tradition originally signaled the closing of the city gates. The bells commemorate the heroic role of Haarlem knights who cut the harbor chain of the Egyptian city of Damiate during the Fifth Crusade.</p>','/img/historyIMG/kerkdid1.png',NULL,0,'card',NULL,NULL,'fa-clock'),(102,16,'A Tower of Lead and Wood','<p>The iconic 78-meter high tower is actually a \'lightweight\' construction. It is made of wood covered in grey lead sheets. Why? The medieval builders realized the heavy stone pillars of the crossing could not support the weight of a stone tower. It gives the church its unique, slender silhouette.</p>','/img/historyIMG/kerkdid2.png',NULL,1,'card',NULL,NULL,'fa-clock'),(104,16,'The Flying Ships','<p>Look up! Suspended from the high vaulted ceiling, you will see detailed models of 16th and 17th-century warships. These were gifts from the powerful Shipbuilders\' Guild. They serve as a silent reminder of Haarlem\'s maritime power and the city\'s connection to the sea.</p>','/img/historyIMG/kerkdid3.png',NULL,2,'card',NULL,NULL,'fa-clock'),(107,44,'Church of St. Bavo','<p>The imposing Grote Kerk (Great Church) dominates the cityscape with its 80-meter-high tower. Here, the young Mozart played the famous Christiaan Müller organ from 1738, one of the most beautiful organs in the world.</p>','/img/historyIMG/churchOfStBavo.png','2',0,'stop','Departure point','65 min','B'),(108,44,'Grote Markt','<p>The place where it all started. Discover this wonderful square and its terraces and emblematic buildings.</p>','/img/historyIMG/GroteMarkt.png','7',1,'stop','The Heart of Haarlem','10 min','A'),(109,44,'De Hallen','<p>A former meat hall complex from 1603, a beautiful example of Dutch Renaissance architecture. Now a museum of modern art.</p>','/img/historyIMG/DeHallen.png','8',2,'stop','Renaissance Architecture','12 min','B'),(110,44,'Proveniershof','<p>A garden in the middle of the city that has had multiple services.</p>','/img/historyIMG/Proveniershof.png','9',3,'stop','a','8 min','C'),(111,44,'Jopenkerk','<p>A remarkable transformation: this former church from 1908 is now a bustling brewery. Perfect for a break and a tasting of Jopen beer from the 15th century.</p>','/img/historyIMG/Jopenkerk.png','10',4,'stop','Historic Brewery','20 min','D'),(112,44,'Waalse Kerk','<p>This 15th-century Gothic church symbolizes Haarlem\'s role as a refuge for religious refugees during the Eighty Years\' War.</p>','/img/historyIMG/WaalseKerk.png','11',5,'stop','E','10 min','E'),(113,44,'Molen de Adriaan','<p>The most representative windmill in Haarlem. Do you know what a mill is for?</p>','/img/historyIMG/MolenDeAdriaan.png','12',6,'stop','F','15 min','F'),(114,44,'Amsterdamse Poort','<p>Haarlem\'s only remaining city gate from around 1400. A striking medieval gate that marked the route to Amsterdam.</p>','/img/historyIMG/AmsterdamsePoort.png','13',7,'stop','G','8 min','G'),(115,44,'Hof van Bakenes','<p>The oldest almshouse in the Netherlands, founded in 1395. A beautifully preserved example of medieval social consciousness and urban charity.</p>','/img/historyIMG/HofVanBakenes.png','14',8,'stop','H','12 min','H'),(116,17,'Phone nummber','023-5532040',NULL,NULL,0,'info','Contact',NULL,'fa-clock'),(117,17,'Email','info@bavo.nl',NULL,NULL,1,'info','Contact',NULL,'fa-clock'),(118,17,'Website','www.bavo.nl',NULL,NULL,2,'info','Contact',NULL,'fa-clock'),(119,27,'The Medieval Heart','The Grote Markt has been the beating heart of Haarlem since the 13th century, where the Counts of Holland once had their hunting lodge. The imposing St. Bavo Church and the historic City Hall still serve as reminders of the central role of this square.','/img/historyIMG/historyMarkt1.png',NULL,1,'history',NULL,NULL,'fa-clock'),(120,27,'The Golden Age','In the 17th century, the Grote Markt became the symbol of Haarlem’s prosperity, with the iconic Vleeshal (Meat Hall) as its architectural highlight. At that time, the square was the most important meeting place for merchants and citizens in a flourishing trading city.','/img/historyIMG/historyMarkt2.jpg',NULL,2,'history',NULL,NULL,'fa-clock'),(121,27,'The City of Printing','The statue of Laurens Janszoon Coster on the square honors the Haarlem legend regarding the invention of the printing press. For centuries, this monument has symbolized Haarlem as a city of free thinkers, writers, and pioneers.','/img/historyIMG/historyMarkt3.jpg',NULL,3,'history',NULL,NULL,'fa-clock'),(122,28,'Dutch Renaissance','<p>The Vleeshal, completed in 1603 by Lieven de Key, is a masterpiece of the Dutch Renaissance and served for two centuries as the only place where meat was permitted to be sold. With its rich decorations and stepped gables, it is one of the most recognizable buildings.</p>','/img/historyIMG/historyHallen1.png',NULL,0,'history',NULL,NULL,NULL),(123,28,'Space for Craftsmanship','<p>The adjacent Verhuurhal was originally built for the sale of fish and later used by the civic guards of Haarlem. The sober but elegant design forms a beautiful contrast with the exuberant Vleeshal and showcases the city’s architectural diversity.</p>','/img/historyIMG/historyHallen2.jpg',NULL,1,'history',NULL,NULL,NULL),(124,28,'From Market to Museum','<p>Where market traders once offered their wares, De Hallen now serves as an important center for modern and contemporary art. The transformation of these historic market halls into a museum space connects Haarlem’s rich past with the present.</p>','/img/historyIMG/historyHallen3.jpg',NULL,2,'history',NULL,NULL,NULL),(125,29,'From Monastery to Courtyard','Originally, the Catholic St. Michael’s Monastery stood on this site, but after the Reformation in the 16th century, the grounds were given a new purpose. In 1592, the complex was converted into a residence for \"proveniers\" (pensioners).','/img/historyIMG/historyProven1.png',NULL,1,'history',NULL,NULL,'fa-clock'),(126,29,'The Guards of Haarlem','Before it became a quiet residential courtyard, the grounds served for years as the \"doelen\" (practice ground) for the St. George Civic Guard. The guards trained here with bows and crossbows until they moved in the 17th century.','/img/historyIMG/historyProven2.jpg',NULL,2,'history',NULL,NULL,'fa-clock'),(127,29,'A Stately Oasis','The current main entrance on the Grote Houtstraat dates from 1766 and provides direct access to one of Haarlem’s largest and most stately courtyards. With its beautiful inner garden and historic facades, it has offered a peaceful refuge for centuries.','/img/historyIMG/historyProven3.jpg',NULL,3,'history',NULL,NULL,'fa-clock'),(128,30,'From Prayer to Brewery','The Jopenkerk is located in the former St. James Church, which was transformed into a modern city brewery after years of vacancy. In 2010, this monumental building received a new purpose that perfectly aligns with Haarlem’s beer history.','/img/historyIMG/historyJopenkerk1.png',NULL,1,'history',NULL,NULL,'fa-clock'),(129,30,'Reviving Jopen Beer','The name \"Jopen\" refers to the large 112-liter wooden barrels in which Haarlem beer was formerly transported over the Spaarne river. By rediscovering old recipes from the 14th and 15th centuries, the brewery brought back a lost tradition.','/img/historyIMG/historyJopenkerk2.jpg',NULL,2,'history',NULL,NULL,'fa-clock'),(130,30,'Haarlem as a Beer City','In the Middle Ages, Haarlem was one of the most important beer cities in the Netherlands, with dozens of active breweries. The Jopenkerk honors this rich past by brewing in a traditional manner in full view of the guests.','/img/historyIMG/historyJopenkerk3.jpg',NULL,3,'history',NULL,NULL,'fa-clock'),(131,31,'The Oldest Building','<p>The Waalse Kerk is likely the oldest church building in Haarlem, originally dating back to the 14th century. At that time, it served as a chapel for the surrounding Begijnhof, where pious women lived in a secluded community.</p>','/img/historyIMG/historyWaalse1.png',NULL,0,'history',NULL,NULL,NULL),(132,31,'Refuge for Huguenots','<p>Since the 16th century, the church has been used by French-speaking Protestant refugees, the Huguenots. To this day, French-language services are still held here, which is a unique tradition in the city.</p>','/img/historyIMG/historyWaalse2.jpg',NULL,1,'history',NULL,NULL,NULL),(133,31,'Medieval Remains','<p>Rare medieval murals have been preserved in the interior, surviving the turbulent history of the building. This makes the church a hidden historical gem in the busy city center of Haarlem.</p>','/img/historyIMG/historyWaalse3.jpg',NULL,2,'history',NULL,NULL,NULL),(134,32,'Built on a Fortress','The Adriaan isn\'t just a windmill; it is a fortress. Its heavy, octagonal stone base is actually the remnant of the \'Goë Vrou\', an ancient defense tower built in 1479. Long before it ground grain, this strategic spot was used to spot enemies approaching on the river Spaarne and defend the Catrijne Gate.','/img/historyIMG/historyMolen1.png',NULL,1,'history',NULL,NULL,'fa-clock'),(135,32,'The Engine of the City','While many Dutch mills were used for pumping water or grinding flour, De Adriaan was an industrial powerhouse. Since 1778, it ground \'tuff\' (volcanic rock) to create waterproof mortar for city walls, oak bark for the tanning industry, and even tobacco. It was the heavy machinery that literally helped build the city.','/img/historyIMG/historyMolen2.png',NULL,2,'history',NULL,NULL,'fa-clock'),(136,32,'A Phoenix from the Ashes','On a dark evening in 1932, disaster struck. A spectacular fire consumed the mill, leaving the citizens of Haarlem in shock. For 70 years, a painful void remained in the city\'s skyline. But the locals never gave up. After decades of fundraising, the mill was rebuilt and reopened in 2002, restoring Haarlem’s iconic silhouette.','/img/historyIMG/historyMolen3.png',NULL,3,'history',NULL,NULL,'fa-clock'),(137,33,'The Last City Gate','The Amsterdamse Poort, built around 1355, is the only remaining city gate of the original twelve that Haarlem once possessed. For centuries, it served as the main entrance for travelers entering the city from Amsterdam.','/img/historyIMG/historyAmsterdams2.jpg',NULL,1,'history',NULL,NULL,'fa-clock'),(138,33,'Defensive Walls','With its thick walls and robust towers, the gate was part of the medieval city defenses against enemy attacks. Until well into the 19th century, the city gates were strictly closed every evening to ensure the safety of the residents.','/img/historyIMG/historyAmsterdams3.jpg',NULL,2,'history',NULL,NULL,'fa-clock'),(139,33,'The Road to the Capital','The gate marked the starting point of the canal to Amsterdam, where wooden towboats used to sail. For many travelers, this imposing structure was the first or last thing they saw of Haarlem during their long journey over water.','/img/historyIMG/historyAmsterdams1.png',NULL,3,'history',NULL,NULL,'fa-clock'),(140,34,'The Oldest Courtyard','The Hof van Bakenes was founded in 1395, making it the oldest \"hofje\" (almshouse) in the Netherlands. It was established by Dirck van Bakenes and originally provided housing for twenty single women of good character.','/img/historyIMG/historyBakenes2.png',NULL,1,'history',NULL,NULL,'fa-clock'),(141,34,'A Hidden Rhyme','Above the entrance on the Bakenessergracht is a famous rhyme: \"Entrance of Bakenes for women eight, but peace is preserved there through the night.\" This serves as a reminder of the strict rules and tranquility preserved behind these walls for centuries.','/img/historyIMG/historyBakenes3.jpg',NULL,2,'history',NULL,NULL,'fa-clock'),(142,34,'Medieval Architecture','Although many of the houses were renovated in the 17th century, the courtyard still breathes the atmosphere of medieval Haarlem. The narrow alleys and the enclosed inner garden form a historic sanctuary in the heart of the old Bakenes neighborhood.','/img/historyIMG/historyBakenes1.png',NULL,3,'history',NULL,NULL,'fa-clock'),(143,36,'The secret of Saint Bavo','Did you know that nearly 1,500 people are buried beneath the floor of St. Bavo\'s Church, including the famous painter Frans Hals? The expression \"rich stinkers\" comes from this, as the rich were buried in the church, and the smell there was sometimes very strong.','/img/historyIMG/didyouknowMarkt1.png',NULL,1,'did_you_know',NULL,NULL,'fa-clock'),(144,36,'A statue with a discussion','Did you know that the statue of Laurens Janszoon Coster stands there because Haarlem residents long believed he invented the printing press, not Gutenberg? While historically unlikely, \"Lau\" remains the one and only inventor for the people of Haarlem.','/img/historyIMG/didyouknowMarkt2.jpg',NULL,2,'did_you_know',NULL,NULL,'fa-clock'),(145,36,'The hidden fish market','Did you know that in the past, the Grote Markt sold not only meat but also plenty of fish? The narrow streets surrounding the square, such as Spekstraat and Warmoesstraat, still recall the various products that were once traded there.','/img/historyIMG/didyouknowMarkt3.jpg',NULL,3,'did_you_know',NULL,NULL,'fa-clock'),(146,37,'The bull on the facade','<p>Did you know that the heads of bulls and sheep on the facade of the Vleeshal directly revealed what was sold inside? In the 17th century, this was the only place in Haarlem where fresh meat was permitted to be sold, strictly controlled by the guild.</p>','/img/historyIMG/didyouknowHallen1.png',NULL,0,'did_you_know',NULL,NULL,NULL),(147,37,'An expensive masterpiece','<p>Did you know that the construction of the Vleeshal in 1603 cost a staggering 30,000 guilders, a staggering sum at the time? Architect Lieven de Key was commissioned to create the most luxurious market building in the Netherlands to showcase the city\'s wealth.</p>','/img/historyIMG/didyouknowHallen2.jpg',NULL,1,'did_you_know',NULL,NULL,NULL),(148,37,'From meat to modern art','<p>Did you know that this building is now part of the Frans Hals Museum and exhibits modern art? Where butchers once sold their wares, international artworks now hang in one of the best-preserved Renaissance buildings in Europe.</p>','/img/historyIMG/didyouknowHallen3.jpg',NULL,2,'did_you_know',NULL,NULL,NULL),(149,38,'The Price of Retirement','Did you know that the term \"provenier\" comes from the word \"proeve,\" meaning a gift or portion of food? In the 17th century, people had to pay a significant lump sum of money to buy their way into the hofje for a lifetime of guaranteed housing and meals.','/img/historyIMG/didyouknowProven1.png',NULL,1,'did_you_know',NULL,NULL,'fa-clock'),(150,38,'A Target Practice Past','Did you know that before the houses were built, this site was the training ground for the Civic Guard of Saint George? The city’s marksmen used the open space to practice their archery and musketry skills until the late 1500s.','/img/historyIMG/didyouknowProven1.jpg',NULL,2,'did_you_know',NULL,NULL,'fa-clock'),(151,38,'The Hidden Entrance','Did you know that the grand entrance gate on the Grote Houtstraat was added much later, in 1766? Behind this busy shopping street portal lies one of Haarlem’s largest and most tranquil courtyards, housing nearly 100 residents in a peaceful oasis.','/img/historyIMG/didyouknowProven1.jpg',NULL,3,'did_you_know',NULL,NULL,'fa-clock'),(152,39,'The Meaning of \"Jopen\"','Did you know that the name \"Jopen\" refers to the traditional 112-liter wooden barrels that were used to transport Haarlem beer in the Middle Ages? During the 14th century, Haarlem was one of the most important brewing cities in the Netherlands, and these iconic barrels were a common sight along the Spaarne river.','/img/historyIMG/didyouknowJopen1.png',NULL,1,'did_you_know',NULL,NULL,'fa-clock'),(153,39,'Ancient Recipes Reborn','Did you know that the brewery was founded to bring back historical flavors using recipes discovered in the city archives? Their \"Hoppenbier\" is based on a recipe from 1501, and the \"Koyt\" is brewed according to a local decree from 1407, allowing you to literally taste a piece of Haarlem’s medieval history.','/img/historyIMG/didyouknowJopen1.png',NULL,2,'did_you_know',NULL,NULL,'fa-clock'),(154,39,'From Bibles to Beer','Did you know that the building was originally the Vestekerk, a former Jacobite church that stood empty for years before its transformation? Today, the massive copper brewing kettles are placed right where the altar used to be, and the original stained-glass windows now overlook a lively cafe instead of a quiet congregation.','/img/historyIMG/didyouknowJopen1.png',NULL,3,'did_you_know',NULL,NULL,'fa-clock'),(155,40,'A Royal Connection','<p>Did you know that the Waalse Kerk has a strong bond with the Dutch Royal Family? Members of the House of Orange-Nassau have visited this church throughout history, and the French-language services still held today are a reminder of the time when French was the language of European royalty.</p>','/img/historyIMG/didyouknowWaalse1.jpg',NULL,0,'did_you_know',NULL,NULL,NULL),(156,40,'The Oldest Mural','<p>Did you know that during a restoration, a mural from the 14th century was discovered, which is considered the oldest in Haarlem? This depiction of \"The Three Living and the Three Dead\" once served as a reminder of the transience of life for the Beguines who prayed here.</p>','/img/historyIMG/didyouknowWaalse1.jpg',NULL,1,'did_you_know',NULL,NULL,NULL),(157,40,'French Language as Tradition','<p>Did you know that the Waalse Kerk is the only place in Haarlem where Protestant services have been held continuously in French since 1586? This began when the city assigned the church to Huguenots fleeing religious persecution in France and Belgium.</p>','/img/historyIMG/didyouknowWaalse1.jpg',NULL,2,'did_you_know',NULL,NULL,NULL),(158,41,'The Language of Sails','Windmills can talk! The position of the sails (wings) sends a message to the community. If the sails are stopped in a specific \"+\" or \"x\" position, it can announce a birth, a marriage, or a period of mourning. During WWII, millers even used secret sail positions to warn the resistance of approaching raids.','/img/historyIMG/didyouknowMolen1.png',NULL,1,'did_you_know',NULL,NULL,'fa-clock'),(159,41,'A Wedding Gift','Why is it called \'De Adriaan\'? The mill was built by Adriaan de Boois, a wealthy industrialist from Amsterdam. He moved to Haarlem and built this towering structure not just for business, but to secure his family\'s legacy. Today, the mill is still a popular wedding location for Haarlem couples.','/img/historyIMG/didyouknowMolen2.png',NULL,2,'did_you_know',NULL,NULL,'fa-clock'),(160,41,'The Best View','De Adriaan offers the undisputed best view of Haarlem. From the gallery platform, 12 meters above the river, you get a 360-degree panorama. You can see the winding Spaarne river, the roofs of the old city, and the massive Bavo Church dominating the center. On clear days, you can even spot the dunes.','/img/historyIMG/didyouknowMolen3.png',NULL,3,'did_you_know',NULL,NULL,'fa-clock'),(161,42,'The Last Survivor','Did you know that the Amsterdamse Poort is the only remaining city gate of the original twelve that once guarded Haarlem? Built around 1355, it is a rare surviving example of the city’s medieval defensive walls.','/img/historyIMG/didyouknowPoort1.png',NULL,1,'did_you_know',NULL,NULL,'fa-clock'),(162,42,'A Siege Survivor','Did you know that this gate played a crucial role during the Siege of Haarlem in 1572? While most of the city\'s fortifications were heavily damaged by Spanish troops, this gate stood firm and remains a symbol of Haarlem\'s historical resilience.','/img/historyIMG/didyouknowPoort1.png',NULL,2,'did_you_know',NULL,NULL,'fa-clock'),(163,42,'The Gateway to the Capital','Did you know that for centuries, this was the only way to travel from Haarlem to Amsterdam by land? It also marked the start of the \"trekvaart,\" a canal where horse-drawn boats transported people between the two cities in about two and a half hours.','/img/historyIMG/didyouknowPoort1.png',NULL,3,'did_you_know',NULL,NULL,'fa-clock'),(164,43,'The Oldest in the Country','Did you know that the Hof van Bakenes, founded in 1395, is officially the oldest \"hofje\" in the Netherlands? It was established through the will of Dirck van Bakenes to provide housing for twelve poor, unmarried women.','/img/historyIMG/didyouknowBakenes1.png',NULL,1,'did_you_know',NULL,NULL,'fa-clock'),(165,43,'A Rhyming Reminder','Did you know there is a famous rhyme above the entrance gate that dates back to 1610? It roughly translates to: \"Bakenes for women eight, and twice two as well,\" referring to the twenty original residents who lived there in peace.','/img/historyIMG/didyouknowBakenes1.png',NULL,2,'did_you_know',NULL,NULL,'fa-clock'),(166,43,'Architectural Evolution','Did you know that although the foundation is medieval, most of the current houses were rebuilt in the 17th century? You can still see the transition from Gothic to Renaissance styles in the brickwork and the layout of the serene inner courtyard.','/img/historyIMG/didyouknowBakenes1.png',NULL,3,'did_you_know',NULL,NULL,'fa-clock'),(167,46,'Monday','Open 24/7','/img/historyIMG/entreeMarkt.png',NULL,1,'opening_hours',NULL,NULL,'fa-clock'),(168,46,'Tuesday','Open 24/7',NULL,NULL,2,'opening_hours',NULL,NULL,'fa-clock'),(169,46,'Wednesday','Open 24/7',NULL,NULL,3,'opening_hours',NULL,NULL,'fa-clock'),(170,46,'Thursday','Open 24/7',NULL,NULL,4,'opening_hours',NULL,NULL,'fa-clock'),(171,46,'Friday','Open 24/7',NULL,NULL,5,'opening_hours',NULL,NULL,'fa-clock'),(172,46,'Saturday','Open 24/7',NULL,NULL,6,'opening_hours',NULL,NULL,'fa-clock'),(173,46,'Sunday','Open 24/7',NULL,NULL,7,'opening_hours',NULL,NULL,'fa-clock'),(174,46,'Address','Grote Markt, 2011 RD Haarlem',NULL,NULL,8,'info',NULL,NULL,'fa-clock'),(175,46,'Website','www.haarlem.nl',NULL,NULL,9,'info',NULL,NULL,'fa-clock'),(176,47,'Monday','Closed','/img/historyIMG/entreeHallen.png',NULL,2,'opening_hours',NULL,NULL,NULL),(177,47,'Tuesday','11:00 - 17:00',NULL,NULL,3,'opening_hours',NULL,NULL,NULL),(178,47,'Wednesday','11:00 - 17:00',NULL,NULL,4,'opening_hours',NULL,NULL,NULL),(179,47,'Thursday','11:00 - 17:00',NULL,NULL,5,'opening_hours',NULL,NULL,NULL),(180,47,'Friday','11:00 - 17:00',NULL,NULL,6,'opening_hours',NULL,NULL,NULL),(181,47,'Saturday','11:00 - 17:00',NULL,NULL,7,'opening_hours',NULL,NULL,NULL),(182,47,'Sunday','11:00 - 17:00',NULL,NULL,8,'opening_hours',NULL,NULL,NULL),(183,47,'Phone nummber','023-5115511',NULL,NULL,0,'info',NULL,NULL,NULL),(184,47,'Website','franshalsmuseum.nl',NULL,NULL,1,'info',NULL,NULL,NULL),(185,48,'Monday','09:00 - 18:00','/img/historyIMG/entreeProvenier.png',NULL,1,'opening_hours',NULL,NULL,'fa-clock'),(186,48,'Tuesday','09:00 - 18:00',NULL,NULL,2,'opening_hours',NULL,NULL,'fa-clock'),(187,48,'Wednesday','09:00 - 18:00',NULL,NULL,3,'opening_hours',NULL,NULL,'fa-clock'),(188,48,'Thursday','09:00 - 18:00',NULL,NULL,4,'opening_hours',NULL,NULL,'fa-clock'),(189,48,'Friday','09:00 - 18:00',NULL,NULL,5,'opening_hours',NULL,NULL,'fa-clock'),(190,48,'Saturday','09:00 - 18:00',NULL,NULL,6,'opening_hours',NULL,NULL,'fa-clock'),(191,48,'Sunday','09:00 - 18:00',NULL,NULL,7,'opening_hours',NULL,NULL,'fa-clock'),(192,48,'extra','Please respect the residents privacy',NULL,NULL,8,'opening_hours',NULL,NULL,'fa-clock'),(193,48,'Website','hofjesinhaarlem.nl',NULL,NULL,9,'info',NULL,NULL,'fa-clock'),(194,49,'Monday','10:00 - 01:00','/img/historyIMG/entreeJopenkerk.png',NULL,1,'opening_hours',NULL,NULL,'fa-clock'),(195,49,'Tuesday','10:00 - 01:00',NULL,NULL,2,'opening_hours',NULL,NULL,'fa-clock'),(196,49,'Wednesday','10:00 - 01:00',NULL,NULL,3,'opening_hours',NULL,NULL,'fa-clock'),(197,49,'Thursday','10:00 - 01:00',NULL,NULL,4,'opening_hours',NULL,NULL,'fa-clock'),(198,49,'Friday','10:00 - 01:00',NULL,NULL,5,'opening_hours',NULL,NULL,'fa-clock'),(199,49,'Saturday','10:00 - 01:00',NULL,NULL,6,'opening_hours',NULL,NULL,'fa-clock'),(200,49,'Sunday','10:00 - 01:00',NULL,NULL,7,'opening_hours',NULL,NULL,'fa-clock'),(201,49,'Phone nummber','023-5334114',NULL,NULL,8,'info',NULL,NULL,'fa-clock'),(202,49,'Website','jopenkerk.nl',NULL,NULL,9,'info',NULL,NULL,'fa-clock'),(203,50,'Thursday','Closed','/img/historyIMG/entreeWaalse.png',NULL,5,'opening_hours',NULL,NULL,NULL),(204,50,'Friday','Closed',NULL,NULL,6,'opening_hours',NULL,NULL,NULL),(205,50,'Saturday','10:00 - 12:00',NULL,NULL,7,'opening_hours',NULL,NULL,NULL),(206,50,'Sunday','10:30 - 12:00',NULL,NULL,8,'opening_hours',NULL,NULL,NULL),(207,50,'Address','Begijnhof 30, Haarlem',NULL,NULL,0,'info',NULL,NULL,NULL),(208,50,'Website','waalsekerkhaarlem.nl',NULL,NULL,1,'info',NULL,NULL,NULL),(209,51,'Monday','10:30 - 17:00','/img/historyIMG/entreeMolen1.png',NULL,1,'opening_hours',NULL,NULL,'fa-clock'),(210,51,'Tuesday','10:30 - 17:00',NULL,NULL,2,'opening_hours',NULL,NULL,'fa-clock'),(211,51,'Wednesday','10:30 - 17:00',NULL,NULL,3,'opening_hours',NULL,NULL,'fa-clock'),(212,51,'Thursday','10:30 - 17:00',NULL,NULL,4,'opening_hours',NULL,NULL,'fa-clock'),(213,51,'Friday','10:30 - 17:00',NULL,NULL,5,'opening_hours',NULL,NULL,'fa-clock'),(214,51,'Saturday','10:30 - 17:00',NULL,NULL,6,'opening_hours',NULL,NULL,'fa-clock'),(215,51,'Sunday','10:30 - 17:00',NULL,NULL,7,'opening_hours',NULL,NULL,'fa-clock'),(216,51,'extra','Closed on public holidays',NULL,NULL,8,'opening_hours',NULL,NULL,'fa-clock'),(217,51,'Phone nummber','023-5425259',NULL,NULL,9,'info',NULL,NULL,'fa-clock'),(218,51,'Email','info@molenadriaan.nl',NULL,NULL,10,'info',NULL,NULL,'fa-clock'),(219,51,'Website','www.molenadriaan.nl',NULL,NULL,11,'info',NULL,NULL,'fa-clock'),(220,52,'Monday','00:00 - 23:59','/img/historyIMG/entreePoort.png',NULL,1,'opening_hours',NULL,NULL,'fa-clock'),(221,52,'Tuesday','00:00 - 23:59',NULL,NULL,2,'opening_hours',NULL,NULL,'fa-clock'),(222,52,'Wednesday','00:00 - 23:59',NULL,NULL,3,'opening_hours',NULL,NULL,'fa-clock'),(223,52,'Thursday','00:00 - 23:59',NULL,NULL,4,'opening_hours',NULL,NULL,'fa-clock'),(224,52,'Friday','00:00 - 23:59',NULL,NULL,5,'opening_hours',NULL,NULL,'fa-clock'),(225,52,'Saturday','00:00 - 23:59',NULL,NULL,6,'opening_hours',NULL,NULL,'fa-clock'),(226,52,'Sunday','00:00 - 23:59',NULL,NULL,7,'opening_hours',NULL,NULL,'fa-clock'),(227,52,'Address','2011 BZ Haarlem',NULL,NULL,8,'info',NULL,NULL,'fa-clock'),(228,53,'Monday','10:00 - 17:00','/img/historyIMG/hero.png',NULL,1,'opening_hours',NULL,NULL,'fa-clock'),(229,53,'Tuesday','10:00 - 17:00',NULL,NULL,2,'opening_hours',NULL,NULL,'fa-clock'),(230,53,'Wednesday','10:00 - 17:00',NULL,NULL,3,'opening_hours',NULL,NULL,'fa-clock'),(231,53,'Thursday','10:00 - 17:00',NULL,NULL,4,'opening_hours',NULL,NULL,'fa-clock'),(232,53,'Friday','10:00 - 17:00',NULL,NULL,5,'opening_hours',NULL,NULL,'fa-clock'),(233,53,'Saturday','10:00 - 17:00',NULL,NULL,6,'opening_hours',NULL,NULL,'fa-clock'),(234,53,'Sunday','Closed',NULL,NULL,7,'opening_hours',NULL,NULL,'fa-clock'),(235,53,'Address','Wijde Appelaarsteeg 11G',NULL,NULL,8,'info',NULL,NULL,'fa-clock'),(236,54,'A city full of stories, music and shared moments','Week 30 2026','/img/homeIMG/hero.png',NULL,0,'hero',NULL,NULL,NULL),(237,55,'Multiple locations',NULL,NULL,NULL,0,'label',NULL,NULL,'?'),(238,55,'5 festival themes',NULL,NULL,NULL,1,'label',NULL,NULL,'?'),(239,55,'Live performances',NULL,NULL,NULL,2,'label',NULL,NULL,'?'),(240,55,'All ages',NULL,NULL,NULL,3,'label',NULL,NULL,'?'),(241,55,'For everyone',NULL,NULL,NULL,4,'label',NULL,NULL,'?'),(287,56,'Haarlem Jazz','Experience world-class jazz performances across multiple venues in historic Haarlem.','/img/homeIMG/HaarlemJazz.png',NULL,0,'event_card',NULL,NULL,NULL),(288,56,'Yummy!','Savor culinary delights from local and international chefs in our food festival.','/img/homeIMG/ImageYummy.png',NULL,1,'event_card',NULL,NULL,NULL),(289,56,'DANCE!','Dance the night away with top DJs and electronic music acts from around Europe.','/img/homeIMG/ImageDANCE.png',NULL,2,'event_card',NULL,NULL,NULL),(290,56,'History','Discover Haarlem\'s rich heritage through guided tours and special museum exhibitions.','/img/homeIMG/ImageHistory.png',NULL,3,'event_card',NULL,NULL,NULL),(291,56,'Stories','Immerse yourself in captivating theatrical performances and storytelling sessions.','/img/homeIMG/ImageStories.png',NULL,4,'event_card',NULL,NULL,NULL),(292,58,'When does the festival take place?','The festival takes place from July 20th until July 26th, 2026.',NULL,NULL,0,'faq',NULL,NULL,NULL),(293,58,'Where can I buy tickets?','Tickets are available online through our ticket page or at the central information desk in Haarlem.',NULL,NULL,1,'faq',NULL,NULL,NULL),(294,58,'Are the events accessible for wheelchairs?','Yes, almost all our locations are wheelchair accessible. Check specific event details for more info.',NULL,NULL,2,'faq',NULL,NULL,NULL),(295,58,'Can I get a refund for my ticket?','Tickets are non-refundable, but you can safely resell them via our official partner, TicketSwap.!',NULL,NULL,3,'faq',NULL,NULL,NULL),(298,57,'Jazz','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.306422843942!2d4.626055612977277!3d52.38299524624629!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5efb0eb820119%3A0x3469bc95e9c3bc06!2sPatronaat!5e0!3m2!1spl!2snl!4v1772520621798!5m2!1spl!2snl',NULL,NULL,0,'map_location',NULL,NULL,NULL),(299,57,'Yummy!','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.54426087518!2d4.635045812977146!3d52.378683246565025!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6a23ed5559%3A0x5c6f7362e29c258a!2sSpaarne%2096%2C%202011%20CL%20Haarlem!5e0!3m2!1spl!2snl!4v1772520725820!5m2!1spl!2snl',NULL,NULL,1,'map_location',NULL,NULL,NULL),(300,57,'DANCE!','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.121129075396!2d4.649172812977434!3d52.386354445998194!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef63abd6db51%3A0x1a67ea388cf3c163!2sLichtfabriek!5e0!3m2!1spl!2snl!4v1772520842686!5m2!1spl!2snl',NULL,NULL,2,'map_location',NULL,NULL,NULL),(301,57,'Tour','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.4085205902184!2d4.63462681297722!3d52.38114424638304!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6bea0a4215%3A0x2cefd774cf4e0dab!2sThe%20St.%20Bavo%20Church%20in%20Haarlem!5e0!3m2!1spl!2snl!4v1772520910208!5m2!1spl!2snl',NULL,NULL,3,'map_location',NULL,NULL,NULL),(302,57,'Stories','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2434.157689466445!2d4.643820512978114!3d52.403818244707075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef7630a4c555%3A0x4c3e9f0e94440c39!2sVerhalenhuis%20Haarlem!5e0!3m2!1spl!2snl!4v1772520978955!5m2!1spl!2snl',NULL,NULL,4,'map_location',NULL,NULL,NULL),(379,67,'','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9741.738251107163!2d4.624356227908438!3d52.380672102397135!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6c3bed21db%3A0xa93f41263360e707!2sCentrum%2C%20Haarlem!5e0!3m2!1snl!2snl!4v1772627766350!5m2!1snl!2snl',NULL,NULL,0,'map','<p>Explore the heart of Haarlem and discover where our featured restaurants are located</p>',NULL,NULL),(380,68,'<p>Ratatouille</p>','<p>French / Fish</p>','<p>★★★★☆</p>',NULL,0,'restaurant',NULL,NULL,NULL),(381,68,'<p>Restaurant ML</p>','<p>Dutch / Seafood</p>','<p>★★★★☆</p>',NULL,1,'restaurant',NULL,NULL,NULL),(382,68,'<p>Urban Frenchy Bistro Toujours</p>','<p>French / Bistro</p>','<p>★★★☆☆</p>',NULL,2,'restaurant',NULL,NULL,NULL),(383,68,'<p>Cafe de Roemer</p>','<p>Dutch / Seafood</p>','<p>★★★★☆</p>',NULL,3,'restaurant',NULL,NULL,NULL),(384,68,'<p>Restaurant Fris</p>','<p>World Food</p>','<p>★★★★☆</p>',NULL,4,'restaurant',NULL,NULL,NULL),(385,68,'<p>New Vegas</p>','<p>Vegan</p>','<p>★★★☆☆</p>',NULL,5,'restaurant',NULL,NULL,NULL),(386,68,'<p>Grand Cafe Brinkman</p>','<p>Dutch, European, Modern</p>','<p>★★★☆☆</p>',NULL,6,'restaurant',NULL,NULL,NULL),(387,66,'',NULL,'/img/yummyIMG/YummyHeaderImage.jpg',NULL,0,'hero',NULL,NULL,NULL),(400,62,'Afrojack','1','/img/danceIMG/afrojack.jpg',NULL,1,'artist',NULL,NULL,NULL),(401,62,'Armin van Buuren','2','/img/danceIMG/ArminVanBuuren.png',NULL,2,'artist',NULL,NULL,NULL),(402,62,'Hardwell','3','/img/danceIMG/hardwell.png',NULL,3,'artist',NULL,NULL,NULL),(403,62,'Martin Garrix','4','/img/danceIMG/martinGarrix.png',NULL,4,'artist',NULL,NULL,NULL),(404,62,'Nicky Romero','5','/img/danceIMG/nickyRomero.png',NULL,5,'artist',NULL,NULL,NULL),(405,62,'Tiësto','6','/img/danceIMG/Tiesto.jpg',NULL,6,'artist',NULL,NULL,NULL),(406,62,'Hardwell 2','Techno','',NULL,7,'artist',NULL,NULL,'fa-clock'),(407,63,'Friday','€125,00',NULL,NULL,1,'pass',NULL,NULL,NULL),(408,63,'Saturday & Sunday','€150,00',NULL,NULL,2,'pass',NULL,NULL,NULL),(409,63,'Full Weekend (Fri-Sun)','€250,00',NULL,'highlight',3,'pass',NULL,NULL,NULL),(410,69,'',NULL,'/img/danceIMG/MartinGarrixDetail1.png',NULL,1,'hero_image','',NULL,NULL),(411,69,'',NULL,'/img/danceIMG/martinGarrix.png',NULL,2,'hero_image','Martin Garrix',NULL,NULL),(412,69,'',NULL,'/img/danceIMG/MartinGarrixDetail2.png',NULL,3,'hero_image','',NULL,NULL),(413,70,'Youngest No. 1 DJ','Topped DJ Mag\'s Top 100 DJs at just 20, becoming the youngest to ever do so.',NULL,NULL,1,'highlight',NULL,NULL,'star'),(414,70,'Global Breakthrough Hit','Released \"Animals,\" a track that skyrocketed him to worldwide fame and reshaped big-room EDM.',NULL,NULL,2,'highlight',NULL,NULL,'music'),(415,70,'Founder of STMPD RCRDS','Launched his own label to champion fresh artists and maintain complete creative freedom.',NULL,NULL,3,'highlight',NULL,NULL,'award'),(416,70,'Olympic & Major Event Performer','Performed at international stages including the Olympics and major festivals like Tomorrowland and Ultra.',NULL,NULL,4,'highlight',NULL,NULL,'star'),(417,71,'Animals','2013','/img/danceIMG/MartinGarrixAnimals.jpg','/audio/dance/MartinGarrixAnimals.mp3',1,'track','Single',NULL,NULL),(418,71,'In The Name Of Love','2016','/img/danceIMG/MartinGarrixInTheNameOfLove.jpeg','/audio/dance/MartinGarrixInTheNameOfLove.mp3',2,'track','Single',NULL,NULL),(419,71,'Scared to Be Lonely','2017','/img/danceIMG/MartinGarrixScaredToBeLonely.jpg','/audio/dance/MartinGarrixScaredToBeLonely.mp3',3,'track','Single',NULL,NULL),(420,71,'Tremor','2014','/img/danceIMG/MartinGarrixTremor.jpg','/audio/dance/MartinGarrixTremor.mp3',4,'track','Single',NULL,NULL),(421,72,'',NULL,'/img/danceIMG/TiestoBanner1.png',NULL,1,'hero_image','',NULL,NULL),(422,72,'',NULL,'/img/danceIMG/TiestoBanner2.png',NULL,2,'hero_image','Tiësto live set',NULL,NULL),(423,72,'',NULL,'/img/danceIMG/TiestoBanner3.png',NULL,3,'hero_image','',NULL,NULL),(424,73,'Grammy Award Winner','Won Best Remixed Recording in 2015 for \"All Of Me\" (Tiësto\'s Birthday Treatment Remix).',NULL,NULL,1,'highlight',NULL,NULL,'star'),(425,73,'DJ Mag Top 100','Voted #1 DJ in the world three consecutive times (2002, 2003, 2004).',NULL,NULL,2,'highlight',NULL,NULL,'music'),(426,73,'Olympic Performance','First DJ to perform solo at the Olympic Games opening ceremony (Athens 2004).',NULL,NULL,3,'highlight',NULL,NULL,'award'),(427,73,'Chart Success','Multiple platinum records and over 36 million album sales worldwide.',NULL,NULL,4,'highlight',NULL,NULL,'star'),(428,74,'The Business','2020','/img/danceIMG/TiestoBusiness.jpg','/audio/dance/TiestoTheBusiness.mp3',1,'track','Single',NULL,NULL),(429,74,'Red Lights','2013','/img/danceIMG/TiestoRedLights.jpg','/audio/dance/TiestoRedLights.mp3',2,'track','Single',NULL,NULL),(430,74,'Adagio for Strings','2005','/img/danceIMG/TiestoAdagioForStrings.jpg','/audio/dance/TiestoAdagioForStrings.mp3',3,'track','Single',NULL,NULL),(431,74,'Traffic','2003','/img/danceIMG/TiestoTraffic.jpg','/audio/dance/TiestoTraffic.mp3',4,'track','Single',NULL,NULL),(432,77,'Stories Festival','<p>Experience the rich history and culture of Haarlem through immersive storytelling sessions featuring local legends, historical events, and contemporary narratives.</p>','/img/storiesIMG/hero-stories.jpg',NULL,0,'hero',NULL,NULL,'fa-clock'),(436,11,'<p>Yummy!</p>','<p>Pair your storytelling experience with a curated dinner at one of Haarlem\'s restaurants. Enjoy culinary delights that complement the festival atmosphere.</p>',NULL,'/stories/yummy',0,'explore',NULL,NULL,'fa-clock'),(438,12,'<p>Are stories available in English?</p>','<p>Yes. Some performances are available in English, while others are in Dutch. The language of each story is clearly indicated in the program schedule.</p>',NULL,NULL,0,'faq',NULL,NULL,'fa-clock'),(439,12,'<p>Is the festival suitable for children?</p>','<p>Yes. Many stories are created for children and families. Each performance includes an age indication, so you can easily see which stories are suitable for your child.</p>',NULL,NULL,1,'faq',NULL,NULL,'fa-clock'),(440,12,'<p>What does \"pay as you like\" mean?</p>','<p>\"Pay as you like\" means that you can choose how much you want to pay for the performance. This pricing model is used by the festival to keep events accessible for everyone. A reservation is still required to guarantee entry.</p>',NULL,NULL,2,'faq',NULL,NULL,'fa-clock'),(441,12,'<p>Do I need to reserve tickets in advance?</p>','<p>Yes. For all performances, a reservation is required to guarantee entry. Some events have a fixed ticket price, while others use a \"pay as you like\" pricing model.</p>',NULL,NULL,3,'faq',NULL,NULL,'fa-clock'),(443,12,'<p>Are there any discounts available?</p>','<p>Yes. Visitors with a HaarlemmPas receive a 25% discount on entry fees for Stories in Haarlem events.</p>',NULL,NULL,5,'faq',NULL,NULL,'fa-clock'),(444,9,'<p>Verhalenhuis Haarlem</p>','<p>A cultural storytelling venue dedicated to spoken word, family stories, and community storytelling. Verhalenhuis Haarlem hosts performances for all ages, including children\'s stories and storytelling competitions.</p>',NULL,NULL,0,'Stories for the whole family,Cultural venue,Community storytelling',NULL,NULL,'fa-clock'),(445,9,'<p>Haarlemmerhout</p>','<p>The oldest public park in the Netherlands, used as an outdoor location for storytelling events. Performances take place in a natural setting, creating an intimate atmosphere under the trees.</p>',NULL,NULL,1,'Outdoor seating,Weather dependent,Picnic area',NULL,NULL,'fa-clock'),(446,9,'<p>Buunderij Haarlem</p>','<p>A neighborhood café and community space hosting storytelling with social and historical impact. The venue offers an informal setting for intimate stories and audience-focused performances.</p>',NULL,NULL,2,'Wheelchair accessible,Parking available,Restrooms',NULL,NULL,'fa-clock'),(447,9,'<p>Corrie ten Boom Huis</p>','<p>The historic home of the Ten Boom family, used as a location for impactful storytelling about history and World War II. The venue adds authenticity and depth to historical performances.</p>',NULL,NULL,3,'Historical museum,Guided tours,Restrooms',NULL,NULL,'fa-clock'),(448,9,'<p>De Schuur</p>','<p>A well-known cultural venue hosting podcasts and live recordings with an audience. De Schuur is mainly used for adult-oriented storytelling and discussion-based performances.</p>',NULL,NULL,4,'Adult audience,Cultural venue,Recording podcast with audience',NULL,NULL,'fa-clock'),(449,9,'<p>Theater Elswout</p>','<p>A theater venue located near nature, hosting storytelling performances for children, families, and mixed-age audiences. The theater provides a calm and accessible setting for longer performances.</p>',NULL,NULL,5,'Stories for the whole family,Family-friendly,Theater venue',NULL,NULL,'fa-clock'),(451,78,'',NULL,'/img/yummyIMG/RatatouilleBanner.jpg',NULL,0,'hero',NULL,NULL,NULL),(452,79,'<p>North Sea Crab</p>','<p>North Sea Crab with citrus and herbs</p>',NULL,NULL,0,'concept',NULL,NULL,NULL),(453,79,'<p>Cod</p>','<p>Cod Fillet with seasonal vegetables</p>',NULL,NULL,1,'concept',NULL,NULL,NULL),(454,79,'<p>Chocolate Explosion</p>','<p>Chocolate Explosion with vanilla cream</p>',NULL,NULL,2,'concept',NULL,NULL,NULL),(455,80,'<p>Spaarne 96</p>','<p>2011 CL Haarlem</p>',NULL,NULL,0,'contact',NULL,NULL,NULL),(456,80,'<p>+31 23 542 7270</p>',NULL,NULL,NULL,1,'contact',NULL,NULL,NULL),(457,80,NULL,NULL,'','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4871.087912229754!2d4.634927276876137!3d52.378688772024454!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6bd9e573fb%3A0x8c3546c16902f0f2!2sRatatouille%20Food%20%26%20Wine!5e0!3m2!1snl!2snl!4v1773161367455!5m2!1snl!2snl',3,'map',NULL,NULL,NULL),(458,81,'',NULL,'/img/yummyIMG/CafeDeRoemerBanner.webp',NULL,0,'hero',NULL,NULL,NULL),(459,82,'<p>Dutch Shrimp</p>','<p>Dutch Shrimp with pickled cucumber and dill</p>',NULL,NULL,0,'concept',NULL,NULL,NULL),(460,82,'<p>Pot of mussels</p>','<p>A large pot of fresh Zeeland mussels cooked in white wine, garlic, celery, and herbs.</p>',NULL,NULL,1,'concept',NULL,NULL,NULL),(461,82,'<p>Fresh North Sea Fish</p>','<p>Fresh North Sea fish (sea bass or cod depending on catch)</p>',NULL,NULL,2,'concept',NULL,NULL,NULL),(462,82,'<p>Creamy Vanilla Cheesecake</p>','<p>Creamy vanilla cheesecake with stroopwafel crumble crust and warm caramel syrup.</p>',NULL,NULL,3,'concept',NULL,NULL,NULL),(463,83,'<p>Botermarkt 17</p>','<p>2011 XL Haarlem</p>',NULL,NULL,0,'contact',NULL,NULL,NULL),(464,83,'<p>+023 532 5267</p>',NULL,NULL,NULL,1,'contact',NULL,NULL,NULL),(465,83,NULL,NULL,NULL,'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7589.342352598097!2d4.629897485553806!3d52.38429769801127!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6ad50d053b%3A0x9f83720b021b43f2!2sCaf%C3%A9%20de%20Roemer!5e0!3m2!1snl!2snl!4v1773225061601!5m2!1snl!2snl',3,'map',NULL,NULL,NULL),(466,66,'',NULL,'/img/yummyIMG/YummyHeaderImage.jpg',NULL,1,'hero',NULL,NULL,NULL),(467,84,'Discover more stories','Explore live podcasts, guided performances, and family shows throughout Haarlem.','/img/storiesIMG/Hero-photo.jpg','/stories',1,'grid',NULL,NULL,'fa-clock'),(468,84,'Meet the storytellers','Learn about the artists and creators who bring the stories to life.',NULL,'/stories/meet-the-team',2,'grid',NULL,NULL,'fa-clock'),(517,103,'Add to Cart',NULL,NULL,'#',7,'button',NULL,NULL,'fa-clock'),(518,103,'€10.00',NULL,NULL,NULL,6,'price',NULL,NULL,'fa-clock'),(519,103,'Price per person',NULL,NULL,NULL,5,'price_label',NULL,NULL,'fa-clock'),(520,103,'12+',NULL,NULL,NULL,4,'tag',NULL,NULL,'fa-clock'),(521,103,'English Spoken',NULL,NULL,NULL,3,'tag',NULL,NULL,'fa-clock'),(522,103,'Theater Elswout',NULL,NULL,NULL,2,'location',NULL,NULL,'fa-clock'),(523,103,'Saturday 25, 15:00',NULL,NULL,NULL,1,'datetime',NULL,NULL,'fa-clock'),(524,104,'Anansi and the Tiger','How Anansi outsmarted the mighty tiger',NULL,'/audio/stories/busy-park.wav',2,'Listen',NULL,NULL,'fa-clock'),(525,104,'The Magic Pot','A tale of greed and wisdom',NULL,'/audio/stories/playground-with-kids-playing.wav',1,'Listen',NULL,NULL,'fa-clock'),(526,105,'','With a strong focus on participation, Mister Anansi creates an inclusive storytelling experience where audiences are invited to listen, laugh, and take part in the story. His performances are suitable for families and children of all ages.',NULL,NULL,2,'paragraph',NULL,NULL,'fa-clock'),(527,105,'','Mister Anansi brings Caribbean spider tales to life through energetic storytelling, music, and audience interaction. Inspired by traditional Anansi stories, his performances combine humor, rhythm, and imagination to engage both children and adults.',NULL,NULL,1,'paragraph',NULL,NULL,'fa-clock'),(528,106,'Gallery Image 3',NULL,'/img/storiesIMG/Mister-Anansi-3.png',NULL,3,'gallery',NULL,NULL,'fa-clock'),(529,106,'Gallery Image 2',NULL,'/img/storiesIMG/Mister-Anansi-2.png',NULL,2,'gallery',NULL,NULL,'fa-clock'),(530,106,'Gallery Image 1',NULL,'/img/storiesIMG/Mister-Anansi-1.png',NULL,1,'gallery',NULL,NULL,'fa-clock'),(531,107,'All ages',NULL,NULL,NULL,3,'tag',NULL,NULL,'fa-clock'),(532,107,'Family',NULL,NULL,NULL,2,'tag',NULL,NULL,'fa-clock'),(533,107,'Hero Image',NULL,'/img/storiesIMG/Hero-Mister-Anansi.png',NULL,1,'image',NULL,NULL,'fa-clock'),(534,113,'Pay As You Like',NULL,NULL,'/stories/pay-as-you-like',7,'button',NULL,NULL,'fa-clock'),(535,113,'€0.00',NULL,NULL,NULL,6,'price',NULL,NULL,'fa-clock'),(536,113,'Pay as you like',NULL,NULL,NULL,5,'price_label',NULL,NULL,'fa-clock'),(537,113,'10+',NULL,NULL,NULL,4,'tag',NULL,NULL,'fa-clock'),(538,113,'Dutch',NULL,NULL,NULL,3,'tag',NULL,NULL,'fa-clock'),(539,113,'Corrie ten Boom Huis',NULL,NULL,NULL,2,'location',NULL,NULL,'fa-clock'),(540,113,'Saturday 25, 13:00',NULL,NULL,NULL,1,'datetime',NULL,NULL,'fa-clock'),(541,114,'Remembering Corrie','A reflective storytelling session rooted in local history',NULL,'/audio/stories/crowd-in-church-applauding.wav',2,'Listen',NULL,NULL,'fa-clock'),(542,114,'A House of Courage','How an ordinary Haarlem home became a place of resistance',NULL,'/audio/stories/mixkit-small-crowd-applause.wav',1,'Listen',NULL,NULL,'fa-clock'),(543,117,'Hero Image',NULL,'/img/storiesIMG/Corrie-ten-Boom-Hero.png',NULL,1,'image',NULL,NULL,'fa-clock'),(544,115,'','Designed with a historical and local focus, the performance invites visitors to connect the past with the streets of Haarlem today. It offers an accessible introduction for families, students, and visitors who want to understand the human stories behind the city\'s wartime memory.',NULL,NULL,2,'paragraph',NULL,NULL,'fa-clock'),(545,115,'','This storytelling experience brings the Haarlem history of Corrie ten Boom closer to a new generation. Through personal memories, local context, and carefully staged narration, the audience discovers how courage, faith, and resistance shaped one of the city\'s most meaningful stories.',NULL,NULL,1,'paragraph',NULL,NULL,'fa-clock'),(546,116,'Gallery Image 3',NULL,'/img/storiesIMG/Corrie-ten-Boom-3.png',NULL,3,'gallery',NULL,NULL,'fa-clock'),(547,116,'Gallery Image 2',NULL,'/img/storiesIMG/Corrie-ten-Boom-2.png',NULL,2,'gallery',NULL,NULL,'fa-clock'),(548,116,'Gallery Image 1',NULL,'/img/storiesIMG/Corrie-ten-Boom-1.png',NULL,1,'gallery',NULL,NULL,'fa-clock'),(549,117,'Historic',NULL,NULL,NULL,2,'tag',NULL,NULL,'fa-clock'),(550,117,'Ages 10+',NULL,NULL,NULL,3,'tag',NULL,NULL,'fa-clock'),(557,123,'Add to Cart',NULL,NULL,'#',7,'button',NULL,NULL,'fa-clock'),(558,123,'€12.50',NULL,NULL,NULL,6,'price',NULL,NULL,'fa-clock'),(559,123,'Price per person',NULL,NULL,NULL,5,'price_label',NULL,NULL,'fa-clock'),(560,123,'16+',NULL,NULL,NULL,4,'tag',NULL,NULL,'fa-clock'),(561,123,'Dutch (NL)',NULL,NULL,NULL,3,'tag',NULL,NULL,'fa-clock'),(562,123,'De Schuur',NULL,NULL,NULL,2,'location',NULL,NULL,'fa-clock'),(563,123,'Friday 24, 19:00 - 20:15',NULL,NULL,NULL,1,'datetime',NULL,NULL,'fa-clock'),(564,124,'When Things Don\'t Go as Planned','Unexpected twists, honest stories, and practical humor',NULL,'/audio/stories/huge.wav',2,'Listen',NULL,NULL,'fa-clock'),(565,124,'Rethinking Everyday Problems','A live reflection on turning frustration into possibility',NULL,'/audio/stories/clearing-the-throat.wav',1,'Listen',NULL,NULL,'fa-clock'),(566,125,'','Each live session combines storytelling with conversation, creating a dynamic experience where the audience becomes part of the narrative. The performance is aimed at an adult audience and focuses on insight, perspective, and shared reflection.',NULL,NULL,2,'paragraph',NULL,NULL,'fa-clock'),(567,125,'','Omdenken Podcast is a live-recorded storytelling and discussion format that explores everyday situations from unexpected perspectives. Through humor, reflection, and interaction with the audience, the podcast challenges assumptions and invites listeners to rethink familiar topics.',NULL,NULL,1,'paragraph',NULL,NULL,'fa-clock'),(568,126,'Gallery Image 3',NULL,'/img/storiesIMG/Omdenken-Podcast-3.png',NULL,3,'gallery',NULL,NULL,'fa-clock'),(569,126,'Gallery Image 2',NULL,'/img/storiesIMG/Omdenken-Podcast-2.png',NULL,2,'gallery',NULL,NULL,'fa-clock'),(570,126,'Gallery Image 1',NULL,'/img/storiesIMG/Omdenken-Podcast-1.png',NULL,1,'gallery',NULL,NULL,'fa-clock'),(571,127,'Live Podcast',NULL,NULL,NULL,3,'tag',NULL,NULL,'fa-clock'),(572,127,'Adults',NULL,NULL,NULL,2,'tag',NULL,NULL,'fa-clock'),(573,127,'Hero Image',NULL,'/img/storiesIMG/Omdenken-Podcast-Hero.png',NULL,1,'image',NULL,NULL,'fa-clock'),(574,128,'Personal Program','All tickets that you are interested in are here','/img/programIMG/PersonalProgramBanner.jpg',NULL,1,'hero',NULL,NULL,'fa-clock'),(576,129,'Event','event',NULL,NULL,3,'label',NULL,NULL,'fa-clock'),(577,129,'Time','time',NULL,NULL,4,'label',NULL,NULL,'fa-clock'),(578,129,'Location','location',NULL,NULL,5,'label',NULL,NULL,'fa-clock'),(579,129,'Name','name',NULL,NULL,6,'label',NULL,NULL,'fa-clock'),(580,129,'Tickets','tickets',NULL,NULL,7,'label',NULL,NULL,'fa-clock'),(581,129,'Price','price',NULL,NULL,8,'label',NULL,NULL,'fa-clock'),(582,135,'daypass thursday','€35,00',NULL,NULL,1,'pass',NULL,NULL,'fa-clock'),(583,135,'daypass friday','€35,00',NULL,NULL,2,'pass',NULL,NULL,'fa-clock'),(584,135,'daypass saturday','€35,00',NULL,NULL,3,'pass',NULL,NULL,'fa-clock'),(585,135,'daypass all days','€80,00',NULL,NULL,4,'pass',NULL,NULL,'fa-clock'),(587,142,'Hero image','','',NULL,1,'image',NULL,NULL,NULL),(588,142,'Stories','',NULL,NULL,2,'tag',NULL,NULL,NULL),(589,143,'','Add the main description for this Stories event here.',NULL,NULL,1,'paragraph',NULL,NULL,NULL),(590,144,'Gallery image','','',NULL,1,'gallery',NULL,NULL,NULL),(591,145,'Featured track','Add a short teaser or highlight for this event.',NULL,'',1,'Listen',NULL,NULL,NULL),(592,146,'Add date and time','',NULL,'',1,'datetime',NULL,NULL,NULL),(593,146,'Add location','',NULL,'',2,'location',NULL,NULL,NULL),(594,146,'12+','',NULL,'',3,'tag',NULL,NULL,NULL),(595,146,'Price','',NULL,'',4,'price_label',NULL,NULL,NULL),(596,146,'Add price','',NULL,'',5,'price',NULL,NULL,NULL),(597,146,'Book now','',NULL,'',6,'button',NULL,NULL,NULL),(598,147,'Hero image','','',NULL,1,'image',NULL,NULL,NULL),(599,147,'Stories','',NULL,NULL,2,'tag',NULL,NULL,NULL),(600,148,'','Add the main description for this Stories event here.',NULL,NULL,1,'paragraph',NULL,NULL,NULL),(601,149,'Gallery image','','',NULL,1,'gallery',NULL,NULL,NULL),(602,150,'Featured track','Add a short teaser or highlight for this event.',NULL,'',1,'Listen',NULL,NULL,NULL),(603,151,'Add date and time','',NULL,'',1,'datetime',NULL,NULL,NULL),(604,151,'Add location','',NULL,'',2,'location',NULL,NULL,NULL),(605,151,'12+','',NULL,'',3,'tag',NULL,NULL,NULL),(606,151,'Price','',NULL,'',4,'price_label',NULL,NULL,NULL),(607,151,'Add price','',NULL,'',5,'price',NULL,NULL,NULL),(608,151,'Book now','',NULL,'',6,'button',NULL,NULL,NULL),(609,50,'dag','1',NULL,NULL,0,NULL,NULL,NULL,'fa-clock'),(610,50,'Wednesday','Closed',NULL,NULL,4,'opening_hours',NULL,NULL,NULL),(611,50,'Tuesday','Closed',NULL,NULL,3,'opening_hours',NULL,NULL,NULL),(612,50,'Monday','Closed',NULL,NULL,2,'opening_hours',NULL,NULL,NULL);
/*!40000 ALTER TABLE `section_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_performers`
--

DROP TABLE IF EXISTS `session_performers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_performers` (
  `session_id` int NOT NULL,
  `performer_id` int NOT NULL,
  PRIMARY KEY (`session_id`,`performer_id`),
  KEY `idx_sp_performer_id` (`performer_id`),
  CONSTRAINT `fk_sp_performer` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sp_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_performers`
--

LOCK TABLES `session_performers` WRITE;
/*!40000 ALTER TABLE `session_performers` DISABLE KEYS */;
INSERT INTO `session_performers` VALUES (8,1),(12,1),(15,1),(11,2),(13,2),(18,2),(10,3),(11,3),(19,3),(11,4),(14,4),(20,4),(17,5),(9,6),(12,6),(16,6),(2,7),(5,7),(7,7),(36,7),(37,7),(38,7),(39,7),(40,7),(41,7),(31,8),(28,9),(34,9),(22,10),(27,11),(24,13),(35,14),(25,15),(23,16),(1,19),(6,19),(71,19),(3,20),(4,20),(69,20),(42,21),(64,21),(43,22),(62,22),(44,23),(45,24),(61,24),(46,25),(47,26),(48,27),(49,28),(50,29),(51,30),(52,31),(53,32),(54,33),(65,33),(55,34),(56,35),(57,36),(58,37),(63,37),(59,38),(60,39),(21,40),(22,41),(23,42),(24,43),(25,44),(26,45),(27,46),(28,47),(34,47),(29,48),(30,49),(31,50),(33,51);
/*!40000 ALTER TABLE `session_performers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int DEFAULT NULL,
  `venue_id` int NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `label` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_spots` int NOT NULL,
  `amount_sold` int NOT NULL DEFAULT '0',
  `language_id` int DEFAULT NULL,
  `pricing_type` varchar(30) NOT NULL DEFAULT 'fixed',
  `minimum_price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `fk_language` (`language_id`),
  CONSTRAINT `fk_language` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (1,1,20,'2026-07-23','10:00:00','+12',17.50,12,0,1,'fixed',NULL),(2,1,20,'2026-07-23','13:00:00','+12',17.50,12,0,2,'fixed',NULL),(3,1,20,'2026-07-23','16:00:00','+12',17.50,12,0,1,'fixed',NULL),(4,1,20,'2026-07-23','19:00:00','+12',17.50,12,3,3,'fixed',NULL),(5,1,20,'2026-07-24','10:00:00','+12',17.50,12,0,1,'fixed',NULL),(6,1,20,'2026-07-24','13:00:00','+12',17.50,12,0,2,'fixed',NULL),(7,1,20,'2026-07-24','16:00:00','+12',17.50,12,0,1,'fixed',NULL),(8,2,3,'2026-07-24','20:00:00','+18',75.00,1501,0,1,'fixed',NULL),(9,2,5,'2026-07-24','22:00:00',NULL,60.00,200,0,NULL,'fixed',NULL),(10,2,2,'2026-07-24','23:00:00',NULL,60.00,300,0,NULL,'fixed',NULL),(11,2,1,'2026-07-25','14:00:00',NULL,110.00,2000,0,NULL,'fixed',NULL),(12,2,1,'2026-07-26','14:00:00',NULL,110.00,2000,0,NULL,'fixed',NULL),(13,2,6,'2026-07-24','22:00:00',NULL,60.00,200,0,NULL,'fixed',NULL),(14,2,4,'2026-07-24','22:00:00',NULL,60.00,200,0,NULL,'fixed',NULL),(15,2,2,'2026-07-25','22:00:00',NULL,60.00,300,0,NULL,'fixed',NULL),(16,2,3,'2026-07-25','21:00:00',NULL,75.00,1500,0,NULL,'fixed',NULL),(17,2,5,'2026-07-25','23:00:00',NULL,60.00,200,0,NULL,'fixed',NULL),(18,2,2,'2026-07-26','19:00:00',NULL,60.00,300,0,NULL,'fixed',NULL),(19,2,6,'2026-07-26','21:00:00',NULL,90.00,1500,0,NULL,'fixed',NULL),(20,2,6,'2026-07-26','18:00:00',NULL,60.00,200,0,NULL,'fixed',NULL),(21,3,7,'2026-07-23','16:00:00',NULL,6.00,4,0,2,'fixed',NULL),(22,3,11,'2026-07-23','19:00:00',NULL,12.50,16,0,2,'fixed',NULL),(23,3,9,'2026-07-23','19:00:00',NULL,0.00,16,0,2,'pay_as_you_like',5.00),(24,3,10,'2026-07-24','16:00:00',NULL,0.00,10,0,2,'pay_as_you_like',5.00),(25,3,7,'2026-07-24','19:00:00',NULL,12.50,12,0,2,'fixed',NULL),(26,3,9,'2026-07-24','19:00:00',NULL,0.00,16,0,2,'pay_as_you_like',5.00),(27,3,11,'2026-07-24','20:30:00',NULL,12.50,16,0,1,'fixed',NULL),(28,3,12,'2026-07-25','10:00:00',NULL,10.00,2,0,2,'fixed',NULL),(29,3,10,'2026-07-25','13:00:00',NULL,0.00,2,0,2,'pay_as_you_like',5.00),(30,3,11,'2026-07-25','14:00:00',NULL,12.50,12,0,2,'fixed',NULL),(31,3,12,'2026-07-25','15:00:00',NULL,10.00,12,0,1,'fixed',NULL),(32,3,11,'2026-07-26','10:00:00',NULL,10.00,2,0,1,'fixed',NULL),(33,3,10,'2026-07-26','13:00:00',NULL,0.00,2,0,1,'pay_as_you_like',5.00),(34,3,12,'2026-07-26','15:00:00',NULL,10.00,12,0,2,'fixed',NULL),(35,3,7,'2026-07-26','16:00:00',NULL,12.50,12,0,1,'fixed',NULL),(36,1,20,'2026-07-25','10:00:00','+12',17.50,12,0,1,'fixed',NULL),(37,1,20,'2026-07-25','13:00:00','+12',17.50,12,0,2,'fixed',NULL),(38,1,20,'2026-07-25','16:00:00','+12',17.50,12,0,1,'fixed',NULL),(39,1,20,'2026-07-26','10:00:00','+12',17.50,12,0,1,'fixed',NULL),(40,1,20,'2026-07-26','13:00:00','+12',17.50,12,0,2,'fixed',NULL),(41,1,20,'2026-07-26','16:00:00','+12',17.50,12,0,3,'fixed',NULL),(42,5,21,'2026-07-23','18:00:00',NULL,15.00,300,0,NULL,'fixed',NULL),(43,5,21,'2026-07-23','19:30:00',NULL,15.00,300,0,NULL,'fixed',NULL),(44,5,21,'2026-07-23','21:00:00',NULL,15.00,300,0,NULL,'fixed',NULL),(45,5,22,'2026-07-23','18:00:00',NULL,10.00,200,0,NULL,'fixed',NULL),(46,5,22,'2026-07-23','19:30:00',NULL,10.00,200,0,NULL,'fixed',NULL),(47,5,22,'2026-07-23','21:00:00',NULL,10.00,200,0,NULL,'fixed',NULL),(48,5,21,'2026-07-24','18:00:00',NULL,15.00,300,0,NULL,'fixed',NULL),(49,5,21,'2026-07-24','19:30:00',NULL,15.00,300,0,NULL,'fixed',NULL),(50,5,21,'2026-07-24','21:00:00',NULL,15.00,300,0,NULL,'fixed',NULL),(51,5,22,'2026-07-24','18:00:00',NULL,10.00,200,0,NULL,'fixed',NULL),(52,5,22,'2026-07-24','19:30:00',NULL,10.00,200,0,NULL,'fixed',NULL),(53,5,22,'2026-07-24','21:00:00',NULL,10.00,200,0,NULL,'fixed',NULL),(54,5,21,'2026-07-25','18:00:00',NULL,15.00,300,0,NULL,'fixed',NULL),(55,5,21,'2026-07-25','19:30:00',NULL,15.00,300,0,NULL,'fixed',NULL),(56,5,21,'2026-07-25','21:00:00',NULL,15.00,300,0,NULL,'fixed',NULL),(57,5,23,'2026-07-25','18:00:00',NULL,10.00,150,0,NULL,'fixed',NULL),(58,5,23,'2026-07-25','19:30:00',NULL,10.00,150,0,NULL,'fixed',NULL),(59,5,23,'2026-07-25','21:00:00',NULL,10.00,150,0,NULL,'fixed',NULL),(60,5,24,'2026-07-26','15:00:00',NULL,0.00,-1,0,NULL,'fixed',NULL),(61,5,24,'2026-07-26','16:00:00',NULL,0.00,-1,0,NULL,'fixed',NULL),(62,5,24,'2026-07-26','17:00:00',NULL,0.00,-1,0,NULL,'fixed',NULL),(63,5,24,'2026-07-26','18:00:00',NULL,0.00,-1,0,NULL,'fixed',NULL),(64,5,24,'2026-07-26','19:00:00',NULL,0.00,-1,0,NULL,'fixed',NULL),(65,5,24,'2026-07-26','20:00:00',NULL,0.00,-1,0,NULL,'fixed',NULL),(69,1,20,'2026-07-24','15:50:00','+18',17.50,12,0,3,'fixed',NULL),(71,1,20,'2026-07-24','04:10:00','+22',1222.00,12,0,1,'fixed',NULL);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopping_cart_items`
--

DROP TABLE IF EXISTS `shopping_cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shopping_cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `session_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_items_cart_session` (`cart_id`,`session_id`),
  KEY `idx_cart_items_cart_id` (`cart_id`),
  KEY `idx_cart_items_session_id` (`session_id`),
  CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `shopping_carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_items_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_cart_items_quantity` CHECK ((`quantity` > 0)),
  CONSTRAINT `chk_cart_items_unit_price` CHECK ((`unit_price` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopping_cart_items`
--

LOCK TABLES `shopping_cart_items` WRITE;
/*!40000 ALTER TABLE `shopping_cart_items` DISABLE KEYS */;
INSERT INTO `shopping_cart_items` VALUES (3,2,1,2,17.50,'2026-03-22 14:46:49','2026-03-22 17:10:28'),(11,2,2,2,17.50,'2026-03-22 16:45:08','2026-03-22 17:10:36'),(12,2,23,2,20.00,'2026-03-22 17:00:22','2026-03-22 17:10:31'),(14,5,1,1,17.50,'2026-03-22 18:17:28','2026-03-22 18:17:28'),(15,5,46,2,10.00,'2026-03-22 18:17:42','2026-03-22 18:17:42'),(16,5,10,1,60.00,'2026-03-22 18:17:52','2026-03-22 18:17:52'),(17,5,27,1,12.50,'2026-03-22 18:18:02','2026-03-22 18:18:02'),(19,6,14,2,60.00,'2026-03-22 18:28:15','2026-03-22 18:28:18'),(20,7,22,3,12.50,'2026-03-22 18:33:31','2026-03-22 18:33:39'),(21,7,5,2,17.50,'2026-03-22 18:33:48','2026-03-22 18:34:13'),(22,7,26,1,5.00,'2026-03-22 18:34:00','2026-03-22 18:34:00'),(23,7,36,1,17.50,'2026-03-22 18:34:27','2026-03-22 18:34:27'),(24,7,39,1,17.50,'2026-03-22 18:34:36','2026-03-22 18:34:36'),(25,7,23,1,20.00,'2026-03-22 18:34:54','2026-03-22 18:34:54'),(28,9,22,2,12.50,'2026-03-22 19:33:12','2026-03-22 19:33:32'),(29,10,23,2,20.00,'2026-03-22 19:48:21','2026-03-22 19:48:21'),(30,11,22,1,12.50,'2026-03-22 20:11:21','2026-03-22 20:11:21'),(36,12,21,2,6.00,'2026-03-22 20:31:22','2026-03-22 20:48:20'),(38,13,2,1,17.50,'2026-03-22 20:49:22','2026-03-22 20:49:22'),(39,13,21,1,6.00,'2026-03-22 20:52:40','2026-03-22 20:52:40'),(40,13,1,1,17.50,'2026-03-22 20:56:56','2026-03-22 20:56:56'),(41,14,1,2,17.50,'2026-03-22 20:59:25','2026-03-22 20:59:36'),(42,15,21,1,6.00,'2026-03-22 21:00:33','2026-03-22 21:00:33'),(43,15,1,1,17.50,'2026-03-22 21:00:57','2026-03-22 21:00:57'),(44,16,22,1,12.50,'2026-03-22 21:04:05','2026-03-22 21:04:05'),(45,16,3,3,17.50,'2026-03-22 21:07:25','2026-03-22 21:08:50'),(46,16,18,3,60.00,'2026-03-22 21:08:44','2026-03-22 21:09:17'),(47,16,2,3,17.50,'2026-03-22 21:12:32','2026-03-22 21:12:32'),(48,17,33,1,80.00,'2026-03-22 21:19:01','2026-03-22 21:19:01'),(49,17,21,1,6.00,'2026-03-22 21:25:40','2026-03-22 21:25:40'),(50,18,24,2,5.00,'2026-03-22 21:27:17','2026-03-22 21:27:17'),(51,19,1,1,17.50,'2026-03-22 21:28:01','2026-03-22 21:28:01'),(54,22,21,1,6.00,'2026-03-23 18:08:59','2026-03-23 18:08:59'),(55,23,21,1,6.00,'2026-03-23 18:52:57','2026-03-23 18:52:57'),(56,20,21,1,6.00,'2026-03-25 06:30:29','2026-03-25 06:30:29'),(57,24,22,1,12.50,'2026-03-25 06:35:19','2026-03-25 06:35:19'),(58,24,1,4,17.50,'2026-03-25 06:35:35','2026-04-02 21:26:56'),(59,24,31,1,10.00,'2026-03-25 06:42:50','2026-03-25 06:42:50'),(60,25,21,3,6.00,'2026-03-25 08:53:53','2026-03-25 08:53:53'),(61,27,22,1,12.50,'2026-03-25 09:30:46','2026-03-25 09:30:46'),(63,26,31,2,10.00,'2026-03-25 11:26:48','2026-03-25 12:36:02'),(64,26,23,6,5.00,'2026-03-25 12:33:19','2026-03-25 12:33:26'),(65,23,14,1,60.00,'2026-03-25 12:55:48','2026-03-25 12:55:48'),(68,32,21,1,6.00,'2026-04-02 21:37:44','2026-04-02 21:37:44'),(69,33,1,1,17.50,'2026-04-02 21:38:25','2026-04-02 21:38:25'),(70,34,22,1,12.50,'2026-04-02 21:38:49','2026-04-02 21:38:49'),(71,35,21,1,6.00,'2026-04-02 23:27:42','2026-04-02 23:27:42'),(74,36,23,3,5.00,'2026-04-03 00:09:00','2026-04-03 00:24:44'),(75,36,22,1,12.50,'2026-04-03 00:15:54','2026-04-03 00:15:54'),(76,36,9,4,60.00,'2026-04-03 00:18:34','2026-04-03 00:18:34');
/*!40000 ALTER TABLE `shopping_cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopping_carts`
--

DROP TABLE IF EXISTS `shopping_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shopping_carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_shopping_carts_user_id` (`user_id`),
  KEY `idx_shopping_carts_status` (`status`),
  CONSTRAINT `fk_shopping_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopping_carts`
--

LOCK TABLES `shopping_carts` WRITE;
/*!40000 ALTER TABLE `shopping_carts` DISABLE KEYS */;
INSERT INTO `shopping_carts` VALUES (2,18,'paid','2026-03-22 14:17:56','2026-04-02 23:40:47'),(5,18,'paid','2026-03-22 17:22:15','2026-04-02 23:40:47'),(6,18,'paid','2026-03-22 18:18:23','2026-04-02 23:40:47'),(7,18,'paid','2026-03-22 18:33:31','2026-04-02 23:40:47'),(9,18,'paid','2026-03-22 19:30:28','2026-04-02 23:40:47'),(10,18,'paid','2026-03-22 19:48:05','2026-04-02 23:40:47'),(11,18,'paid','2026-03-22 20:11:07','2026-04-02 23:40:47'),(12,18,'paid','2026-03-22 20:11:31','2026-04-02 23:40:47'),(13,18,'paid','2026-03-22 20:49:10','2026-04-02 23:40:47'),(14,18,'paid','2026-03-22 20:58:00','2026-04-02 23:40:47'),(15,18,'paid','2026-03-22 21:00:33','2026-04-02 23:40:47'),(16,18,'paid','2026-03-22 21:04:05','2026-04-02 23:40:47'),(17,18,'paid','2026-03-22 21:15:16','2026-04-02 23:40:47'),(18,18,'paid','2026-03-22 21:25:58','2026-04-02 23:40:47'),(19,18,'paid','2026-03-22 21:27:37','2026-04-02 23:40:47'),(20,18,'paid','2026-03-22 21:30:14','2026-04-02 23:40:47'),(22,8,'paid','2026-03-23 18:08:59','2026-04-02 23:40:47'),(23,8,'paid','2026-03-23 18:52:57','2026-04-02 23:40:47'),(24,18,'paid','2026-03-25 06:35:19','2026-04-02 23:40:47'),(25,11,'active','2026-03-25 08:53:53','2026-03-25 08:53:53'),(26,20,'paid','2026-03-25 09:28:16','2026-04-02 23:40:47'),(27,22,'active','2026-03-25 09:30:46','2026-03-25 09:30:46'),(30,8,'active','2026-04-02 15:13:42','2026-04-02 15:13:42'),(32,18,'paid','2026-04-02 21:37:04','2026-04-02 23:40:47'),(33,18,'paid','2026-04-02 21:38:12','2026-04-02 23:40:47'),(34,18,'paid','2026-04-02 21:38:49','2026-04-02 23:40:47'),(35,18,'paid','2026-04-02 21:39:13','2026-04-02 23:40:47'),(36,18,'active','2026-04-02 23:27:56','2026-04-02 23:27:56');
/*!40000 ALTER TABLE `shopping_carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_statuses`
--

DROP TABLE IF EXISTS `ticket_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_statuses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_statuses`
--

LOCK TABLES `ticket_statuses` WRITE;
/*!40000 ALTER TABLE `ticket_statuses` DISABLE KEYS */;
INSERT INTO `ticket_statuses` VALUES (1,'Valid'),(2,'Scanned');
/*!40000 ALTER TABLE `ticket_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `order_id` int NOT NULL,
  `session_id` int NOT NULL,
  `status_id` int DEFAULT '1',
  `qr_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_code` (`qr_code`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`),
  KEY `session_id` (`session_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`),
  CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `ticket_statuses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,17,17,16,1,NULL),(2,17,16,55,1,NULL);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `role_id` int DEFAULT '3',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `addres` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,2,'jureko21@gmail.com','$2y$12$StP95Vg4zZFWglKN57r5eeriNSgEvV.zLsM2AauGNfH8Vf5UOYcJe','2026-02-08 15:57:34',NULL,NULL,NULL,NULL,NULL,NULL),(7,1,'jureko221@gmail.com','$2y$12$wGs2b6rJjMavQW7njOm7BOQwZP1S5rvIkm1iEpHscKsMWtuRML0DK','2026-02-09 14:58:19',NULL,NULL,NULL,NULL,NULL,NULL),(8,1,'jayden@test.nl','$2y$12$Kt3r1zsNoS2PwmdmOQi65.REKTKZFrx2NaKSsQ13Ur8IWCFXGrUtO','2026-02-10 14:25:14',NULL,NULL,NULL,NULL,NULL,NULL),(9,2,'bobjan@gmail.com','$2y$12$QTU3Bc/5atnW391SOSGh3ulKYKxjJNoN/UFB5oLoMVupN8WVZ9rgG','2026-02-10 19:29:13',NULL,NULL,NULL,NULL,NULL,NULL),(10,3,'user7@example.com','$2y$12$EIDoCrjGhXUaHKOh6Jehx.wUU051qDqgt2l67J.FqEtdVANIxHR4m','2026-02-10 22:24:25',NULL,NULL,NULL,NULL,NULL,NULL),(11,1,'Admin@example.com','$2y$12$j/n19ivihMGM.SKkc/DDKOj61iT6/YB7WHPMABBAvmHCjHUAKuhq.','2026-02-10 22:24:52',NULL,NULL,NULL,NULL,NULL,NULL),(12,3,'kubatest12@gmail.com','$2y$12$cKwpywfhxrQ/u0US6SCbqumxNWnFQ5HzAFh5GeBbEN4.sI5fjIXh.','2026-02-11 09:03:58',NULL,NULL,NULL,NULL,NULL,NULL),(13,1,'miguel@gmail.com','$2y$12$fVHVlNqWtza3ZwGSQ/kLDOj07rAkjHZZThcV/ILJ7PwWyO5vNy6Qu','2026-02-11 09:37:38',NULL,NULL,NULL,NULL,NULL,NULL),(14,3,'user109@gmail.com','$2y$12$dq8eHeFPUoP30cS7wEvqYO.kYjD1/zYwY0mqvPZNGDH2EdAJLeCo2','2026-02-11 11:04:43',NULL,NULL,NULL,NULL,NULL,NULL),(15,3,'102user@gmail.com','$2y$12$ojxztLAF0wetPgONWGzJk.sr7.wySPQgmkbF.NHoK6vNXsrxASZ8G','2026-02-11 11:05:29',NULL,NULL,NULL,NULL,NULL,NULL),(16,1,'mennodewever@gmail.com','$2y$12$bK3IFN45y/4q58v66u5K..syqhK1Px8vOyUf4sjYDwCwjspwf47Om','2026-02-18 12:11:14',NULL,NULL,NULL,NULL,NULL,NULL),(17,1,'kubatest@gmail.com','$2y$12$HYYRTf8b4qT2ucwa2LU0ouhBQxUiI7Mr6oUYALM/FrKmIqRIucQkq','2026-03-13 10:13:19',NULL,NULL,NULL,NULL,NULL,NULL),(18,1,'miiguel@gmail.com','$2y$12$Q4M./HNSA/XKoNYmC6DgNuKI7.X0yTIAodZQMhZsKjha3BtrhPBFW','2026-03-18 21:41:56',NULL,NULL,NULL,NULL,NULL,NULL),(19,3,'menno@gmail.com','$2y$12$Rx94uUVpRNWXiEdBBZl4iOJJAuQc0Qlad2Ta3mKMf0PB7mpQUDq0K','2026-03-19 08:55:00','Menno de Wever','06 40319544','assendelft','jokesmit singel 16','1566 SL','the netherlands'),(20,1,'migueltest@gmail.com','$2y$12$SCJCVrvzJDuCABMdiot9genVPhlWobfVBw1lyf7ffB89j.vJhcxfS','2026-03-19 10:01:25',NULL,NULL,NULL,NULL,NULL,NULL),(22,3,'2mennodewever@gmail.com','$2y$12$WmxTrrXgfhJG23y6nnWkvOnJnq4xXXVAVym1CowNe/kMeyrB.MRQm','2026-03-22 13:42:30','Menno de Wever','0640319544','Assendelft','jokesmitsingel 16','1566 SL','Nederland'),(23,3,'kubatest2@gmail.com','$2y$12$/U/W/FuhwusLlFe/1WJ0MuX4jPHZ.HCW2ByXki6.irwMlz5QjJrCq','2026-03-27 12:18:11',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venues`
--

DROP TABLE IF EXISTS `venues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venues` (
  `id` int NOT NULL AUTO_INCREMENT,
  `event_id` int NOT NULL,
  `venue_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `venue_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `venues_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venues`
--

LOCK TABLES `venues` WRITE;
/*!40000 ALTER TABLE `venues` DISABLE KEYS */;
INSERT INTO `venues` VALUES (1,2,'Caprera Openluchttheater','Hoge Duin en Daalseweg 2, 2061 AC Bloemendaal',NULL,'2026-02-10 16:40:33'),(2,2,'Jopenkerk','Gedempte Voldersgracht 2, 2011 WD Haarlem',NULL,'2026-02-10 16:40:33'),(3,2,'Lichtfabriek','Minckelersweg 2, 2031 EM Haarlem',NULL,'2026-02-10 16:40:33'),(4,2,'Puncher comedy club','Grote Markt 10, 2011 RD Haarlem',NULL,'2026-02-10 16:40:33'),(5,2,'Slachthuis','Rockplein 6, 2033 KK Haarlem',NULL,'2026-02-10 16:40:33'),(6,2,'XO the Club','Grote Markt 8, 2011 RD Haarlem',NULL,'2026-02-10 16:40:33'),(7,3,'Verhalenhuis Haarlem','Van Egmondstraat 7, Haarlem-Noord','Cultural venue','2026-02-12 17:38:51'),(8,3,'Haarlemmerhout','Haarlemmerhout, 2012 ED Haarlem','Outdoor park','2026-02-12 17:38:51'),(9,3,'Buurderij Haarlem (Kweekcafé)','Zijlweg 184, 2015 BH Haarlem','Café / community space','2026-02-12 17:38:51'),(10,3,'Corrie ten Boom Huis','Barteljorisstraat 19, 2011 RA Haarlem','Museum / historic house','2026-02-12 17:38:51'),(11,3,'De Schuur','Lange Begijnestraat 9, 2011 HH Haarlem','Theater / cultural venue','2026-02-12 17:38:51'),(12,3,'Theater Elswout','Elswoutslaan 24-A, 2051 AE Overveen','Theater','2026-02-12 17:38:51'),(13,4,'Cafe de Roemer','Botermarkt 17, 2011 XL Haarlem','Restaurant','2026-02-13 12:18:48'),(14,4,'Ratatouille','Spaarne 96, 2011 CL Haarlem, Nederland\r\n','Restaurant','2026-02-14 10:02:39'),(15,4,'Restaurant ML','Kleine Houtstraat 70, 2011 DR Haarlem, Nederland','Restaurant','2026-02-14 10:02:39'),(16,4,'Restaurant Fris','Twijnderslaan 7, 2012 BG Haarlem, Nederland','Restaurant','2026-02-14 10:02:39'),(17,4,'New Vegas','Koningstraat 5, 2011 TB Haarlem','Restaurant','2026-02-14 10:02:39'),(18,4,'Grand Cafe Brinkman','Grote Markt 13, 2011 RC Haarlem, Nederland\r\n','Café','2026-02-14 10:02:39'),(19,4,'Urban Frenchy Bistro Toujours','Oude Groenmarkt 10-12, 2011 HL Haarlem, Nederland','Restaurant','2026-02-14 10:02:39'),(20,1,'Church of St. Bavo','Grote Markt 22, 2011 HL Haarlem','Church','2026-02-15 15:29:38'),(21,5,'Het Patronaat, Main Hall','Zijlsingel 2, 2013 DN Haarlem',NULL,'2026-02-18 09:43:37'),(22,5,'Het Patronaat, Second Hall','Zijlsingel 2, 2013 DN Haarlem',NULL,'2026-02-18 09:43:37'),(23,5,'Het Patronaat, Third Hall','Zijlsingel 2, 2013 DN Haarlem',NULL,'2026-02-18 09:43:37'),(24,5,'Grote Markt','Grote Markt, 2011 HL Haarlem',NULL,'2026-02-18 09:46:31'),(26,3,'Theater Het Huis','Korte Houtstraat 1, Haarlem','Theater','2026-03-05 14:01:23'),(27,3,'Bibliotheek Haarlem','Gasthuisstraat 10, Haarlem','Library','2026-03-05 14:01:23'),(28,3,'Café Central','Grote Markt 20, Haarlem','Café','2026-03-05 14:01:23');
/*!40000 ALTER TABLE `venues` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-03  2:57:26
