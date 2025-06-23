CREATE TABLE `inventorytrans` (
  `transID` INTEGER(15) NOT NULL,
  `inventoryID` VARCHAR(35) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `inventoryType` ENUM('product','material','pfm') COLLATE utf8mb4_general_ci NOT NULL,
  `productLogID` INTEGER(11) NOT NULL,
  `transDate` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `oldStockCount` DECIMAL(10,3) NOT NULL DEFAULT 0.000,
  `transAmount` DECIMAL(10,3) NOT NULL DEFAULT 0.000,
  `transType` ENUM('production log','admin edit','qa rejects','shipped') COLLATE latin1_general_ci NOT NULL COMMENT 'production log \r\npart inventory edit\r\nqa rejects\r\nshipped',
  `transComment` MEDIUMTEXT COLLATE utf8mb4_general_ci,
  PRIMARY KEY USING BTREE (`transID`),
  UNIQUE KEY `TransactionID` USING BTREE (`transID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;