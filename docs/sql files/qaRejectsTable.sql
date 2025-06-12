CREATE TABLE `qarejects` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT COMMENT 'quality reject id',
  `prodDate` DATE NOT NULL,
  `prodLogID` INTEGER(11) NOT NULL,
  `productID` VARCHAR(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rejects` INTEGER(11) NOT NULL,
  `comments` MEDIUMTEXT COLLATE utf8mb4_general_ci,
  `created_at` TIMESTAMP NULL DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`id`)
) ENGINE=InnoDB
AUTO_INCREMENT=9 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

ALTER TABLE productionlogs 
ADD COLUMN qaRejects INTEGER(11) DEFAULT NULL AFTER startUpRejects;

CREATE TABLE `productTrans` (
  `transID` INTEGER(15) NOT NULL,
  `prodLogID` INTEGER(15) NOT NULL DEFAULT 0,
  `productID` VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
  `transDate` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `oldPartCount` INTEGER(11) NOT NULL,
  `transAmount` INTEGER(11) NOT NULL,
  `transType` VARCHAR(50) COLLATE latin1_general_ci NOT NULL COMMENT 'production log \r\npart inventory edit\r\nqa rejects\r\nshipped',
  `transComment` MEDIUMTEXT COLLATE utf8mb4_general_ci,
  PRIMARY KEY USING BTREE (`transID`),
  UNIQUE KEY `TransactionID` USING BTREE (`transID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

CREATE TABLE `materialTrans` (
  `transID` INTEGER(15) NOT NULL,
  `prodLogID` INTEGER(15) NOT NULL DEFAULT 0,
  `materialNumber` VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
  `transDate` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `oldMatCount` DOUBLE(11,3) NOT NULL,
  `transAmount` DOUBLE(11,3) NOT NULL,
  `transType` VARCHAR(50) COLLATE latin1_general_ci NOT NULL COMMENT 'production log \r\npart inventory edit\r\nqa rejects\r\nshipped',
  `transComment` MEDIUMTEXT COLLATE utf8mb4_general_ci,
  PRIMARY KEY USING BTREE (`transID`),
  UNIQUE KEY `TransactionID` USING BTREE (`transID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;