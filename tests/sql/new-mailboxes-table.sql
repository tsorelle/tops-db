CREATE TABLE IF NOT EXISTS `tops_mailboxes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `mailboxcode` VARCHAR(30) NOT NULL DEFAULT '',
  `address` VARCHAR(100) DEFAULT NULL,
  `displaytext` VARCHAR(100) DEFAULT NULL,
  `description` VARCHAR(100) DEFAULT NULL,
  `createdby` VARCHAR(50) NOT NULL DEFAULT 'unknown',
  `createdon` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `changedby` VARCHAR(50) DEFAULT NULL,
  `changedon` DATETIME DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT '1',
  `public` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `boxIndex` (`mailboxcode`)
) ENGINE=MYISAM AUTO_INCREMENT=164 DEFAULT CHARSET=latin1;

DELETE FROM tops_mailboxes;

INSERT  INTO `tops_mailboxes`(mailboxcode,`address`,`displaytext`,`active`,`public`) VALUES
  ('clerk','clerk@friends.com','Clerk',1,1),
  ('support','support@friends.com','Support',1,1);



