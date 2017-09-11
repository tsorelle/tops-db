ALTER TABLE table_name_here
  ADD COLUMN `createdby` VARCHAR(50) NOT NULL DEFAULT 'unknown',
  ADD COLUMN `createdon` DATETIME DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN `changedby` VARCHAR(50) DEFAULT NULL,
  ADD COLUMN `changedon` DATETIME DEFAULT NULL
  , ADD COLUMN `active` TINYINT(1) NOT NULL DEFAULT '1'
  
  -- select * from tops_mailboxes