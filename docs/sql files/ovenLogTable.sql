CREATE TABLE `ovenlogs` (
  `ovenLogID` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `productID` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `inOvenTime` TIME NOT NULL,
  `inOvenDate` DATE NOT NULL,
  `inOvenTemp` INTEGER(11) NOT NULL,
  `outOvenTemp` INTEGER(11) DEFAULT NULL,
  `outOvenTime` TIME DEFAULT NULL,
  `outOvenDate` DATE DEFAULT NULL,
  `inOvenInitials` VARCHAR(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `outOvenInitials` VARCHAR(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ovenComments` MEDIUMBLOB,
  `updatedAt` TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY USING BTREE (`ovenLogID`)
) ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;