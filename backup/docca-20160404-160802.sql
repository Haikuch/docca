--
-- DbNinja v3.2.6 for MySQL
--
-- Dump date: 2016-04-04 16:08:02 (UTC)
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- Database: docca
--

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

USE `docca`;

--
-- Data for table: _dummy
--
LOCK TABLES `_dummy` WRITE;
ALTER TABLE `_dummy` DISABLE KEYS;

-- Table contains no data

ALTER TABLE `_dummy` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: doc_fields
--
LOCK TABLES `doc_fields` WRITE;
ALTER TABLE `doc_fields` DISABLE KEYS;

-- Table contains no data

ALTER TABLE `doc_fields` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: doc_files
--
LOCK TABLES `doc_files` WRITE;
ALTER TABLE `doc_files` DISABLE KEYS;

INSERT INTO `doc_files` (`id`,`doc_id`,`file_id`) VALUES (1,1,1),(2,1,2),(3,1,3),(4,2,4),(5,2,5),(6,2,6),(7,2,7);

ALTER TABLE `doc_files` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: doc_tags
--
LOCK TABLES `doc_tags` WRITE;
ALTER TABLE `doc_tags` DISABLE KEYS;

INSERT INTO `doc_tags` (`id`,`doc_id`,`tag_id`) VALUES (1,1,1),(2,2,2),(3,2,3),(4,2,4);

ALTER TABLE `doc_tags` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: docs
--
LOCK TABLES `docs` WRITE;
ALTER TABLE `docs` DISABLE KEYS;

INSERT INTO `docs` (`id`,`title`,`uploader`,`time_upload`,`time_original`,`comment`,`active`) VALUES (1,'Mieterhöhung 2016',NULL,1459786008,-288921600,'',1),(2,'Urteil Solvenz',NULL,1459786058,1460415600,'Hamwa verloren',1);

ALTER TABLE `docs` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: fields
--
LOCK TABLES `fields` WRITE;
ALTER TABLE `fields` DISABLE KEYS;

INSERT INTO `fields` (`id`,`name`) VALUES (1,'von'),(2,'an');

ALTER TABLE `fields` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: files
--
LOCK TABLES `files` WRITE;
ALTER TABLE `files` DISABLE KEYS;

INSERT INTO `files` (`id`,`name`) VALUES (1,'bag_check.png'),(2,'clinically_studied_ingredient.png'),(3,'cold.png'),(4,'five_years.png'),(5,'formal_languages.png'),(6,'interview.png'),(7,'jet_fuel.png');

ALTER TABLE `files` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: options
--
LOCK TABLES `options` WRITE;
ALTER TABLE `options` DISABLE KEYS;

INSERT INTO `options` (`id`,`value`,`field_id`) VALUES (1,'Gericht',1),(2,'Scharni',1),(3,'Factor',1),(4,'Bornemann',1),(5,'Draeger',1),(6,'Padovicz',1),(7,'Gericht',2),(8,'Scharni',2),(9,'Factor',2),(10,'Bornemann',2),(11,'Draeger',2),(12,'Padovicz',2);

ALTER TABLE `options` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;

--
-- Data for table: tags
--
LOCK TABLES `tags` WRITE;
ALTER TABLE `tags` DISABLE KEYS;

INSERT INTO `tags` (`id`,`name`) VALUES (1,''),(4,'3.OG'),(2,'Gericht'),(3,'urteil');

ALTER TABLE `tags` ENABLE KEYS;
UNLOCK TABLES;
COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

