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

/*Table structure for table `tops_permissions` */

DROP TABLE IF EXISTS `tops_permissions`;

CREATE TABLE `tops_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permissionName` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `tops_permissions` */

insert  into `tops_permissions`(`id`,`permissionName`,`description`) values 
(1,'add-mailbox','Add a mailbox'),
(2,'update-mailboxes','Manage mailbox list');

/*Table structure for table `tops_rolepermissions` */

DROP TABLE IF EXISTS `tops_rolepermissions`;

CREATE TABLE `tops_rolepermissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permissionId` int(11) DEFAULT NULL,
  `roleName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissionRoleIdx` (`permissionId`,`roleName`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `tops_rolepermissions` */

insert  into `tops_rolepermissions`(`id`,`permissionId`,`roleName`) values 
(1,1,'administrator'),
(2,2,'administrator'),
(3,2,'peanut-administrator');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
