-- phpMyAdmin-compatible SQL for developmentdb (generated on 2026-02-12)
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS developmentdb;
USE developmentdb;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'A Stroll Through History','Guided walking tour through Haarlem'),(2,'Dance','Dance events in Haarlem'),(3,'TellingStory','Storytelling event in Haarlem');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_sections`
--

LOCK TABLES `page_sections` WRITE;
/*!40000 ALTER TABLE `page_sections` DISABLE KEYS */;
INSERT INTO `page_sections` VALUES (1,1,'hero','A Stroll Through History',10,NULL,NULL),(2,2,'hero','St.-Bavokerk',1,NULL,NULL),(3,1,'grid','History',20,NULL,NULL),(4,1,'discover','Discover Historic Haarlem',30,'Discover why Haarlem is called the \'Little Amsterdam\'â€”but with more charm and fewer crowds. In this exclusive 2.5-hour guided walking tour, you will travel back to the Dutch Golden Age.','From the bustling Grote Markt to the hidden Hofjes (courtyards) where time seems to stand still. Our expert guides will reveal the stories behind the facades, the secrets of the spice trade, and the legends of local heroes like Kenau.'),(5,1,'stop','Stops on the tour',1,'','');
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
  `event_id` int NOT NULL,
  `slug` varchar(100) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,1,'history-stroll','A Stroll Through History','2026-02-09 16:05:59'),(2,1,'st-bavo','St.-Bavokerk','2026-02-10 19:21:00'),(3,3,'storytelling-home','Home Storytelling','2026-02-12 16:03:43'),(4,3,'mister-anansi','Mister Anansi','2026-02-12 16:03:43'),(5,3,'omdenken-podcast','Omdenken Podcast','2026-02-12 16:03:43'),(6,3,'corrie-ten-boom','Corrie ten Boom','2026-02-12 16:03:43');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
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
  `method` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `performers`
--

LOCK TABLES `performers` WRITE;
/*!40000 ALTER TABLE `performers` DISABLE KEYS */;
INSERT INTO `performers` VALUES (1,2,'Afrojack','DJ',NULL,'2026-02-10 16:40:38'),(2,2,'Armin van Buuren','DJ',NULL,'2026-02-10 16:40:38'),(3,2,'Hardwell','DJ',NULL,'2026-02-10 16:40:38'),(4,2,'Martin Garrix','DJ',NULL,'2026-02-10 16:40:38'),(5,2,'Nicky Romero','DJ',NULL,'2026-02-10 16:40:38'),(6,2,'TiÃ«sto','DJ',NULL,'2026-02-10 16:40:38'),(7,1,'Jan-Willem','Dutch',NULL,'2026-02-11 13:16:05');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `link_url` varchar(255) DEFAULT NULL,
  `order_index` int DEFAULT '0',
  `item_category` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `section_items_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `page_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `section_items`
--

LOCK TABLES `section_items` WRITE;
/*!40000 ALTER TABLE `section_items` DISABLE KEYS */;
INSERT INTO `section_items` VALUES (4,2,'Hero Image','A Gothic masterpiece watching over the city for centuries.','/img/historyIMG/hero.png',NULL,1,'grid'),(5,3,'The Gothic Giant','Dominating the Grote Markt skyline, the St.-Bavo is more than just a church; it is the soul of Haarlem.','/img/historyIMG/bavo1.png','/history/bavo/giant',1,'grid'),(6,3,'A Musical Legend','The church is world-famous for its massive Christian MÃ¼ller organ, once played by Mozart.','/img/historyIMG/bavo2.png','/history/bavo/organ',2,'grid'),(7,3,'The Cannonball','Look closely at the walls, and you might find a remnant of the Spanish Siege of Haarlem.','/img/historyIMG/bavo3.png','/history/bavo/cannonball',3,'grid'),(9,4,'Duration','2.5 hours','â±ï¸',NULL,2,'grid'),(10,4,'Group size','Max 12 participants','ðŸ‘¥',NULL,3,'grid'),(11,4,'Language','NL/EN','ðŸ—£ï¸',NULL,4,'grid'),(12,4,'Reviews','4.9 (127 reviews)','â­',NULL,5,'grid'),(13,1,'Hero Image 1','Uncover the secrets of the Golden Age, hidden courtyards, and vibrant local life.','/img/historyIMG/hero.png',NULL,1,'grid'),(14,1,'Hero Image 2',NULL,'/img/historyIMG/hero.png',NULL,2,'grid'),(15,1,'Hero Image 3',NULL,'/img/historyIMG/hero.png',NULL,3,'grid'),(22,4,'Regular Ticket','â‚¬37,50',NULL,'Per person',6,'price'),(23,4,'Family Ticket','â‚¬60,00',NULL,'2 adults + 2 kids',7,'price'),(24,4,'Minimum age 12 years','',NULL,NULL,8,'info'),(25,4,'Strollers are not allowed','',NULL,NULL,9,'info'),(26,4,'Group size 12 participants + 1 guide','',NULL,NULL,10,'info'),(27,4,'Breaks at cafeterias (stop 5)','',NULL,NULL,11,'info'),(28,5,'Church of ST. Bavo','The imposing Grote Kerk (Great Church) dominates the cityscape with its 80-meter-high tower. Here, the young Mozart played the famous Christiaan MÃ¼ller organ from 1738, one of the most beautiful organs in the world.','/img/historyIMG/churchOfStBavo.png','15 min',1,'stop'),(29,5,'Grote Markt','The place where it all started. Discover this wonderful square and its terraces and emblematic buildings.','/img/historyIMG/GroteMarkt.png','10 min',2,'stop'),(30,5,'De Hallen','A former meat hall complex from 1603, a beautiful example of Dutch Renaissance architecture. Now a museum of modern art.','/img/historyIMG/DeHallen.png','12 min',3,'stop'),(31,5,'Proveniershof','A garden in the middle of the city that has had multiple services.','/img/historyIMG/Proveniershof.png','8 min',4,'stop'),(32,5,'Jopenkerk','A remarkable transformation: this former church from 1908 is now a bustling brewery. Perfect for a break and a tasting of Jopen beer from the 15th century.','/img/historyIMG/Jopenkerk.png','20 min',5,'stop'),(33,5,'Waalse Kerk','This 15th-century Gothic church symbolizes Haarlems role as a refuge for religious refugees during the Eighty Years\' War.','/img/historyIMG/WaalseKerk.png','10 min',6,'stop'),(34,5,'Molen de Adriaan','The most representative windmill in Haarlem. Do you know what a mill is for?','/img/historyIMG/MolenDeAdriaan.png','15 min',7,'stop'),(35,5,'Amsterdamse Poort','Haarlem\'s only remaining city gate from around 1400. A striking medieval gate that marked the route to Amsterdam.','/img/historyIMG/AmsterdamsePoort.png','8 min',8,'stop'),(36,5,'Hof van Bakenes','The oldest almshouse in the Netherlands, founded in 1395. A beautifully preserved example of medieval social consciousness and urban charity.','/img/historyIMG/HofVanBakenes.png','12 min',9,'stop');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_performers`
--

LOCK TABLES `session_performers` WRITE;
/*!40000 ALTER TABLE `session_performers` DISABLE KEYS */;
INSERT INTO `session_performers` VALUES (8,1),(12,1),(15,1),(11,2),(13,2),(18,2),(10,3),(11,3),(19,3),(11,4),(14,4),(20,4),(8,5),(17,5),(9,6),(12,6),(16,6),(2,7);
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
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (1,1,1,'2026-07-23','10:00:00','English',17.50,12,0),(2,1,1,'2026-07-23','13:00:00','Dutch',17.50,12,0),(3,1,1,'2026-07-23','16:00:00','English',17.50,12,0),(4,1,1,'2026-07-23','19:00:00','Chinese',17.50,12,0),(5,1,1,'2026-07-24','10:00:00','English',17.50,12,0),(6,1,1,'2026-07-24','13:00:00','Dutch',17.50,12,0),(7,1,1,'2026-07-24','16:00:00','English',17.50,12,0),(8,2,3,'2026-07-24','20:00:00',NULL,75.00,1500,0),(9,2,5,'2026-07-24','22:00:00',NULL,60.00,200,0),(10,2,2,'2026-07-24','23:00:00',NULL,60.00,300,0),(11,2,1,'2026-07-25','14:00:00',NULL,110.00,2000,0),(12,2,1,'2026-07-26','14:00:00',NULL,110.00,2000,0),(13,2,6,'2026-07-24','22:00:00',NULL,60.00,200,0),(14,2,4,'2026-07-24','22:00:00',NULL,60.00,200,0),(15,2,2,'2026-07-25','22:00:00',NULL,60.00,300,0),(16,2,3,'2026-07-25','21:00:00',NULL,75.00,1500,0),(17,2,5,'2026-07-25','23:00:00',NULL,60.00,200,0),(18,2,2,'2026-07-26','19:00:00',NULL,60.00,300,0),(19,2,6,'2026-07-26','21:00:00',NULL,90.00,1500,0),(20,2,6,'2026-07-26','18:00:00',NULL,60.00,200,0);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopping_carts`
--

DROP TABLE IF EXISTS `shopping_carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shopping_carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `session_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `shopping_carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shopping_carts_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopping_carts`
--

LOCK TABLES `shopping_carts` WRITE;
/*!40000 ALTER TABLE `shopping_carts` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,2,'jureko21@gmail.com','$2y$12$StP95Vg4zZFWglKN57r5eeriNSgEvV.zLsM2AauGNfH8Vf5UOYcJe','2026-02-08 15:57:34'),(7,1,'jureko221@gmail.com','$2y$12$wGs2b6rJjMavQW7njOm7BOQwZP1S5rvIkm1iEpHscKsMWtuRML0DK','2026-02-09 14:58:19'),(8,1,'jayden@test.nl','$2y$12$Kt3r1zsNoS2PwmdmOQi65.REKTKZFrx2NaKSsQ13Ur8IWCFXGrUtO','2026-02-10 14:25:14'),(9,2,'bobjan@gmail.com','$2y$12$QTU3Bc/5atnW391SOSGh3ulKYKxjJNoN/UFB5oLoMVupN8WVZ9rgG','2026-02-10 19:29:13'),(10,3,'user7@example.com','$2y$12$EIDoCrjGhXUaHKOh6Jehx.wUU051qDqgt2l67J.FqEtdVANIxHR4m','2026-02-10 22:24:25'),(11,3,'Admin@example.com','$2y$12$j/n19ivihMGM.SKkc/DDKOj61iT6/YB7WHPMABBAvmHCjHUAKuhq.','2026-02-10 22:24:52'),(12,3,'kubatest12@gmail.com','$2y$12$cKwpywfhxrQ/u0US6SCbqumxNWnFQ5HzAFh5GeBbEN4.sI5fjIXh.','2026-02-11 09:03:58'),(13,3,'miguel@gmail.com','$2y$12$fVHVlNqWtza3ZwGSQ/kLDOj07rAkjHZZThcV/ILJ7PwWyO5vNy6Qu','2026-02-11 09:37:38'),(14,3,'user109@gmail.com','$2y$12$dq8eHeFPUoP30cS7wEvqYO.kYjD1/zYwY0mqvPZNGDH2EdAJLeCo2','2026-02-11 11:04:43'),(15,3,'101user@gmail.com','$2y$12$dcK9EDzH1jQs0srjQp4wc.2t3RViBf/TDNamiOqAhxc4HPsH0EU.2','2026-02-11 11:05:29');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venues`
--

LOCK TABLES `venues` WRITE;
/*!40000 ALTER TABLE `venues` DISABLE KEYS */;
INSERT INTO `venues` VALUES (1,2,'Caprera Openluchttheater','Hoge Duin en Daalseweg 2, 2061 AC Bloemendaal',NULL,'2026-02-10 16:40:33'),(2,2,'Jopenkerk','Gedempte Voldersgracht 2, 2011 WD Haarlem',NULL,'2026-02-10 16:40:33'),(3,2,'Lichtfabriek','Minckelersweg 2, 2031 EM Haarlem',NULL,'2026-02-10 16:40:33'),(4,2,'Puncher comedy club','Grote Markt 10, 2011 RD Haarlem',NULL,'2026-02-10 16:40:33'),(5,2,'Slachthuis','Rockplein 6, 2033 KK Haarlem',NULL,'2026-02-10 16:40:33'),(6,2,'XO the Club','Grote Markt 8, 2011 RD Haarlem',NULL,'2026-02-10 16:40:33');
/*!40000 ALTER TABLE `venues` ENABLE KEYS */;
UNLOCK TABLES;

--

SET FOREIGN_KEY_CHECKS=1;

