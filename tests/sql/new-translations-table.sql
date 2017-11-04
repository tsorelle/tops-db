/*
SQLyog Professional v12.4.3 (64 bit)
MySQL - 5.7.14 : Database - twoquake_test
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `tops_translations` */

DROP TABLE IF EXISTS tops_translations;
CREATE TABLE `tops_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(5) NOT NULL DEFAULT 'en',
  `code` varchar(128) NOT NULL,
  `text` varchar(1028) DEFAULT NULL,
  `createdby` varchar(50) NOT NULL DEFAULT 'system',
  `createdon` datetime DEFAULT CURRENT_TIMESTAMP,
  `changedby` varchar(50) DEFAULT 'system',
  `changedon` datetime DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `ix_translation_language_code` (`language`,`code`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
