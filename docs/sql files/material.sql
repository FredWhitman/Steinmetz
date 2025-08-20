CREATE TABLE `material` (
  `matPartNumber` VARCHAR(20) COLLATE latin1_swedish_ci NOT NULL,
  `matName` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL,
  `productID` VARCHAR(20) COLLATE latin1_swedish_ci DEFAULT NULL,
  `minLbs` INTEGER(11) DEFAULT NULL,
  `matCustomer` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL COMMENT 'Material for Customer Products',
  `matSupplier` VARCHAR(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `matPriceLbs` DOUBLE(15,2) DEFAULT NULL,
  `comments` MEDIUMBLOB,
  `displayOrder` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`matPartNumber`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;