CREATE TABLE if NOT EXISTS `tops_rolepermissions` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `permissionId` INT(11) DEFAULT NULL,
  `roleName` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissionRoleIdx` (`permissionId`,`roleName`)
) ENGINE=MYISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
