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
USE `twoquake_test`;

/*Table structure for table `bookstore_customers` */

DROP TABLE IF EXISTS `bookstore_customers`;

CREATE TABLE `bookstore_customers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `customertypeid` INT(11) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `address` VARCHAR(50) DEFAULT NULL,
  `city` VARCHAR(50) DEFAULT NULL,
  `state` VARCHAR(50) DEFAULT NULL,
  `postalcode` VARCHAR(50) DEFAULT NULL,
  `buyer` VARCHAR(50) DEFAULT NULL,
  `createdby` VARCHAR(50) DEFAULT NULL,
  `createdon` DATETIME DEFAULT NULL,
  `changedby` VARCHAR(50) DEFAULT NULL,
  `changedon` DATETIME DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_customers` */

INSERT  INTO `bookstore_customers`(`id`,`customertypeid`,`name`,`address`,`city`,`state`,`postalcode`,`buyer`,`createdby`,`createdon`,`changedby`,`changedon`,`active`) VALUES
  (1,2,'Kids Korner Bookstore','3001 Bee Caves Road','Austin','TX','78746','bob','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',1),
  (2,1,'Kinder Kindles','9032 Main','Boston','MA','02746','','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
