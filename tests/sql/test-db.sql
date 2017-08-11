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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customertypeid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `address` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postalcode` varchar(50) DEFAULT NULL,
  `buyer` varchar(50) DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_customers` */

insert  into `bookstore_customers`(`id`,`customertypeid`,`name`,`address`,`city`,`state`,`postalcode`,`buyer`,`createdby`,`createdon`,`changedby`,`changedon`,`active`) values 
(1,2,'Kids Korner Bookstore','3001 Bee Caves Road','Austin','TX','78746','bob','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',1),
(2,1,'Kinder Kindles','9032 Main','Boston','MA','02746','','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',1);

/*Table structure for table `bookstore_customertypes` */

DROP TABLE IF EXISTS `bookstore_customertypes`;

CREATE TABLE `bookstore_customertypes` (
  `id` int(11) NOT NULL,
  `typedescription` varchar(50) NOT NULL,
  `discount` int(11) NOT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_customertypes` */

insert  into `bookstore_customertypes`(`id`,`typedescription`,`discount`,`createdby`,`createdon`,`changedby`,`changedon`) values 
(1,'Silver',10,'admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26'),
(2,'Gold',20,'admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26'),
(3,'Platinum',40,'admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26');

/*Table structure for table `bookstore_examples` */

DROP TABLE IF EXISTS `bookstore_examples`;

CREATE TABLE `bookstore_examples` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  `price` decimal(10,2) DEFAULT '2.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_examples` */

insert  into `bookstore_examples`(`id`,`name`,`isbn`,`authors`,`createdby`,`createdon`,`changedby`,`changedon`,`price`) values 
(1,'Mister Peanut Goes to Washington','99921-58-10-7','D.C. Lawrence','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',2.00),
(2,'An Elephant\'s Tale','9971-5-0210-0','Parker Packiderm','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',2.00),
(3,'Conversations with Babar','960-425-059-0','Jane Goodall','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',2.00),
(4,'Sticky Business','80-902734-1-6','Sojourner Ersatz','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',2.00),
(5,'Butter Nut Bother Me','85-359-0277-5','Abigail Adams','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26',2.00);

/*Table structure for table `bookstore_invoicelineitems` */

DROP TABLE IF EXISTS `bookstore_invoicelineitems`;

CREATE TABLE `bookstore_invoicelineitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceid` int(11) NOT NULL,
  `titleid` int(11) NOT NULL,
  `supplierid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost` decimal(10,0) NOT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_invoicelineitems` */

/*Table structure for table `bookstore_invoices` */

DROP TABLE IF EXISTS `bookstore_invoices`;

CREATE TABLE `bookstore_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerid` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `invoicedate` datetime NOT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_invoices` */

/*Table structure for table `bookstore_orderstatuses` */

DROP TABLE IF EXISTS `bookstore_orderstatuses`;

CREATE TABLE `bookstore_orderstatuses` (
  `id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_orderstatuses` */

insert  into `bookstore_orderstatuses`(`id`,`status`) values 
(0,'New'),
(1,'Submitted'),
(2,'Fullfilled'),
(3,'Cancelled');

/*Table structure for table `bookstore_suppliers` */

DROP TABLE IF EXISTS `bookstore_suppliers`;

CREATE TABLE `bookstore_suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postalcode` varchar(50) DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_suppliers` */

insert  into `bookstore_suppliers`(`id`,`name`,`address`,`city`,`state`,`postalcode`,`createdby`,`createdon`,`changedby`,`changedon`) values 
(1,'Wholesale Books','2342 Main','New York','New York','34523','admin','2017-05-06 06:49:55','admin','2017-05-06 06:49:55'),
(2,'Kidz Bookz R Uz','909 N. Lamar','Austin','Texas','78767','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(3,'Amazon.com','','','','','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53');

/*Table structure for table `bookstore_suppliertitles` */

DROP TABLE IF EXISTS `bookstore_suppliertitles`;

CREATE TABLE `bookstore_suppliertitles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplierid` int(11) NOT NULL,
  `titleid` int(11) NOT NULL,
  `discountquantity` int(11) NOT NULL,
  `unitprice` decimal(10,0) NOT NULL,
  `effectivedate` datetime NOT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_suppliertitles` */

insert  into `bookstore_suppliertitles`(`id`,`supplierid`,`titleid`,`discountquantity`,`unitprice`,`effectivedate`,`createdby`,`createdon`,`changedby`,`changedon`) values 
(1,1,1,10,22,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(2,1,2,15,5,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(3,1,3,10,15,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(4,2,1,10,32,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(5,2,2,15,8,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(6,2,3,100,43,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(7,2,4,50,21,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(8,2,5,20,10,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(9,3,1,20,40,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(10,3,3,20,11,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53'),
(11,3,5,20,39,'2017-05-05 06:51:53','admin','2017-05-06 06:51:53','admin','2017-05-06 06:51:53');

/*Table structure for table `bookstore_testtable` */

DROP TABLE IF EXISTS `bookstore_testtable`;

CREATE TABLE `bookstore_testtable` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_testtable` */

/*Table structure for table `bookstore_titles` */

DROP TABLE IF EXISTS `bookstore_titles`;

CREATE TABLE `bookstore_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createdon` datetime DEFAULT NULL,
  `changedby` varchar(50) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `bookstore_titles` */

insert  into `bookstore_titles`(`id`,`name`,`isbn`,`authors`,`createdby`,`createdon`,`changedby`,`changedon`) values 
(1,'Mister Peanut Goes to Washington','99921-58-10-7','D.C. Lawrence','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26'),
(2,'An Elephant\'s Tale','9971-5-0210-0','Parker Packiderm','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26'),
(3,'Conversations with Babar','960-425-059-0','Jane Goodall','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26'),
(4,'Sticky Business','80-902734-1-6','Sojourner Ersatz','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26'),
(5,'Butter Nut Bother Me','85-359-0277-5','Abigail Adams','admin','2017-05-06 06:42:26','admin','2017-05-06 06:42:26');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
