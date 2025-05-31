CREATE TABLE `qarejects` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'quality reject id',
  `prodDate` DATE NOT NULL,
  `prodLogID` INTEGER(11) NOT NULL,
  `productID` VARCHAR(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rejects` INTEGER(11) NOT NULL,
  `comments` MEDIUMTEXT COLLATE utf8mb4_general_ci,
  PRIMARY KEY USING BTREE (`id`)
) ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'

ALTER TABLE productionlogs 
ADD COLUMN qaRejects INTEGER(11) DEFAULT NULL AFTER startUpRejects;