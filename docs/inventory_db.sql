/* SQL Manager for MySQL                              5.7.2.52112 */
/* -------------------------------------------------------------- */
/* Host     : localhost                                           */
/* Port     : 3306                                                */
/* Database : inventory_db                                        */


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES 'utf8mb4' */;

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `inventory_db`
    CHARACTER SET 'utf8mb4'
    COLLATE 'utf8mb4_general_ci';

USE `inventory_db`;

/* Structure for the `dailyusage` table : */

CREATE TABLE `dailyusage` (
  `DailyID` INTEGER(20) NOT NULL AUTO_INCREMENT,
  `ProductionLogID` INTEGER(20) NOT NULL,
  `ProductID` VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
  `ProdDate` DATE NOT NULL,
  `ProductionRun` INTEGER(11) DEFAULT NULL,
  `DailyHopper1Lbs` DOUBLE(15,3) NOT NULL,
  `DailyHopper2Lbs` DOUBLE(15,3) NOT NULL,
  `DailyHopper3Lbs` DOUBLE(15,3) NOT NULL,
  `DailyHopper4Lbs` DOUBLE(15,3) NOT NULL,
  `PercentageH1` DOUBLE(15,3) NOT NULL,
  `PercentageH2` DOUBLE(15,3) NOT NULL,
  `PercentageH3` DOUBLE(15,3) NOT NULL,
  `PercentageH4` DOUBLE(15,3) NOT NULL,
  PRIMARY KEY USING BTREE (`DailyID`)
) ENGINE=InnoDB
AUTO_INCREMENT=3228 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `lotchange` table : */

CREATE TABLE `lotchange` (
  `LotChangeID` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `MaterialName` VARCHAR(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ProductID` VARCHAR(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ChangeDate` DATE DEFAULT NULL,
  `ChangeTime` TIME DEFAULT NULL,
  `OldLot` VARCHAR(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `NewLot` VARCHAR(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Comments` VARCHAR(1500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY USING BTREE (`LotChangeID`)
) ENGINE=InnoDB
AUTO_INCREMENT=590 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `material` table : */

CREATE TABLE `material` (
  `MaterialPartNumber` VARCHAR(20) COLLATE latin1_swedish_ci NOT NULL,
  `MaterialName` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL,
  `ProductID` VARCHAR(20) COLLATE latin1_swedish_ci DEFAULT NULL,
  `MinimumLbs` INTEGER(11) DEFAULT NULL,
  `Customer` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL COMMENT 'Material for Customer Products',
  PRIMARY KEY USING BTREE (`MaterialPartNumber`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `materialinventory` table : */

CREATE TABLE `materialinventory` (
  `MaterialPartNumber` VARCHAR(20) COLLATE latin1_swedish_ci NOT NULL,
  `Lbs` DOUBLE(15,3) NOT NULL,
  PRIMARY KEY USING BTREE (`MaterialPartNumber`),
  UNIQUE KEY `MaterialPartNumber` USING BTREE (`MaterialPartNumber`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `materiallog` table : */

CREATE TABLE `materiallog` (
  `logID` INTEGER(11) NOT NULL,
  `prodLogID` INTEGER(11) NOT NULL,
  `mat1` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed1` DOUBLE(15,3) NOT NULL,
  `mat2` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed2` DOUBLE(15,3) NOT NULL,
  `mat3` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed3` DOUBLE(15,3) NOT NULL,
  `mat4` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed4` DOUBLE(15,3) NOT NULL,
  PRIMARY KEY USING BTREE (`logID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `pfm` table : */

CREATE TABLE `pfm` (
  `PFMID` INTEGER(11) NOT NULL,
  `PartNumber` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `PartName` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `ProductID` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `MinimumQty` INTEGER(11) DEFAULT NULL,
  `AmstedPFM` INTEGER(11) NOT NULL DEFAULT 0,
  PRIMARY KEY USING BTREE (`PFMID`),
  UNIQUE KEY `PFMID` USING BTREE (`PFMID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT='Purchased Finished Material'
;

/* Structure for the `pfminventory` table : */

CREATE TABLE `pfminventory` (
  `PartNumber` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Qty` INTEGER(11) NOT NULL,
  PRIMARY KEY USING BTREE (`PartNumber`),
  UNIQUE KEY `PartNumber` USING BTREE (`PartNumber`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `prodrunlog` table : */

CREATE TABLE `prodrunlog` (
  `logID` INTEGER(15) NOT NULL,
  `productID` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `startDate` DATE NOT NULL,
  `endDate` DATE DEFAULT NULL,
  `mat1Lbs` DOUBLE(15,3) DEFAULT NULL COMMENT 'Lbs of material ran through hopper 1',
  `mat2Lbs` DOUBLE(15,3) DEFAULT NULL COMMENT 'Lbs of material ran through hopper 2',
  `mat3Lbs` DOUBLE(15,3) DEFAULT NULL COMMENT 'Lbs of material ran through hopper 3',
  `mat4Lbs` DOUBLE(15,3) DEFAULT NULL COMMENT 'Lbs of material ran through hopper 4',
  `partsProduced` INTEGER(11) DEFAULT NULL,
  `startupRejects` INTEGER(11) DEFAULT NULL,
  `qaRejects` INTEGER(11) NOT NULL,
  `purgeLbs` DOUBLE(15,3) NOT NULL,
  `runComplete` VARCHAR(10) COLLATE utf8mb4_general_ci NOT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`logID`),
  UNIQUE KEY `ProductionRunID` USING BTREE (`logID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `productinventory` table : */

CREATE TABLE `productinventory` (
  `ProductID` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `PartQty` INTEGER(11) DEFAULT NULL,
  `BlenderStartTotal` DOUBLE(11,3) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`ProductID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `productionlogs` table : */

CREATE TABLE `productionlogs` (
  `logID` INTEGER(20) NOT NULL,
  `productID` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prodDate` DATE NOT NULL,
  `runStatus` VARCHAR(11) COLLATE utf8mb4_general_ci NOT NULL,
  `prevProdLogID` INTEGER(20) NOT NULL,
  `runLogID` INTEGER(11) NOT NULL,
  `matLogID` INTEGER(11) NOT NULL,
  `tempLogID` INTEGER(11) NOT NULL,
  `pressCounter` INTEGER(11) DEFAULT NULL,
  `startUpRejects` INTEGER(11) DEFAULT NULL,
  `qaRejects` INTEGER(11) NOT NULL,
  `purgeLbs` DOUBLE(15,3) NOT NULL,
  `Comments` VARCHAR(2500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY USING BTREE (`logID`),
  UNIQUE KEY `ProductionID` USING BTREE (`logID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `products` table : */

CREATE TABLE `products` (
  `ProductID` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `PartName` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `MinimumQty` INTEGER(11) DEFAULT NULL,
  `BoxesPerSkid` INTEGER(4) DEFAULT NULL,
  `PartsPerBox` INTEGER(4) DEFAULT NULL,
  `PartWeight` DOUBLE(8,3) DEFAULT NULL,
  `displayOrder` INTEGER(11) DEFAULT NULL,
  `customer` VARCHAR(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `productionType` VARCHAR(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY USING BTREE (`ProductID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `producttrans` table : */

CREATE TABLE `producttrans` (
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

/* Structure for the `qarejects` table : */

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

/* Structure for the `templog` table : */

CREATE TABLE `templog` (
  `logID` INTEGER(11) NOT NULL,
  `prodLogID` INTEGER(11) NOT NULL,
  `bigDryerTemp` INTEGER(11) NOT NULL,
  `bigDryerDew` INTEGER(11) NOT NULL,
  `pressDryerTemp` INTEGER(11) NOT NULL,
  `pressDryerDew` INTEGER(11) NOT NULL,
  `t1` INTEGER(11) NOT NULL,
  `t2` INTEGER(11) NOT NULL,
  `t3` INTEGER(11) NOT NULL,
  `t4` INTEGER(11) NOT NULL,
  `m1` INTEGER(11) NOT NULL,
  `m2` INTEGER(11) NOT NULL,
  `m3` INTEGER(11) NOT NULL,
  `m4` INTEGER(11) NOT NULL,
  `m5` INTEGER(11) NOT NULL,
  `m6` INTEGER(11) NOT NULL,
  `m7` INTEGER(11) NOT NULL,
  `chillerTemp` INTEGER(11) NOT NULL,
  `moldTemp` INTEGER(11) NOT NULL,
  PRIMARY KEY USING BTREE (`logID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Definition for the `Get_All_Material_Info` procedure : */

DELIMITER $$

CREATE DEFINER = 'root'@'localhost' PROCEDURE `Get_All_Material_Info`()
    NOT DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
SELECT material.`MaterialPartNumber`, material.`MaterialName`, material.`Minimumlbs`,`materialINVENTORY`.lbs from Material JOIN `MaterialINVENTORY` ON `MaterialINVENTORY`.`materialpartnumber`=`Material`.materialpartnumber;
END$$

DELIMITER ;

/* Definition for the `Get_All_PFM_Info` procedure : */

DELIMITER $$

CREATE DEFINER = 'root'@'localhost' PROCEDURE `Get_All_PFM_Info`()
    NOT DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
SELECT pfm.`PARTNUMBER`, pfm.`PARTNAME`, pfm.`PRODUCTID`,pfm.`MINIMUMQTY`,`PFMINVENTORY`.Qty from PFM JOIN `PFMINVENTORY` ON `PFMINVENTORY`.`partnumber`=pfm.partnumber;
END$$

DELIMITER ;

/* Definition for the `Get_All_Product_Info` procedure : */

DELIMITER $$

CREATE DEFINER = 'root'@'localhost' PROCEDURE `Get_All_Product_Info`()
    NOT DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
SELECT `products`.`ProductID`,`products`.`PartName`,`products`.`MinimumQty`,productinventory.`PartQty` from `products`
JOIN `productinventory` ON `productinventory`.`ProductID` = `products`.`ProductID`;
END$$

DELIMITER ;

/* Definition for the `Get_Daily_Production_Log` procedure : */

DELIMITER $$

CREATE DEFINER = 'root'@'localhost' PROCEDURE `Get_Daily_Production_Log`(
        IN `ProductID` VARCHAR(25),
        IN `ProdDate` DATE
    )
    NOT DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
SELECT     productionlogs.ProductionID, productionlogs.ProductID, productionlogs.ProductionDate, productionlogs.Hopper1Mat, productionlogs.Hopper1Lbs, 
                      productionlogs.Hopper2Mat, productionlogs.Hopper2Lbs, productionlogs.Hopper3Mat, productionlogs.Hopper3Lbs, productionlogs.Hopper4Mat, 
                      productionlogs.Hopper4Lbs, productionlogs.BlenderTotal, productionlogs.BigDryerTemp, productionlogs.BigDryerDew, 
                      productionlogs.PressDryerTemp, productionlogs.PressDryerDew, productionlogs.PressCounter, productionlogs.Rejects, productionlogs.ChillerTemp, 
                      productionlogs.MoldTemp, productionlogs.T1, productionlogs.T2, productionlogs.T3, productionlogs.T4, productionlogs.M1, productionlogs.M2, 
                      productionlogs.M3, productionlogs.M4, productionlogs.M5, productionlogs.M6, productionlogs.M7, productionlogs.Comments, 
                      dailyusage.DailyHopper1Lbs, dailyusage.DailyHopper2Lbs, dailyusage.DailyHopper3Lbs, dailyusage.DailyHopper4Lbs, dailyusage.PercentageH1, 
                      dailyusage.PercentageH2, dailyusage.PercentageH3, dailyusage.PercentageH4
FROM       productionlogs INNER JOIN dailyusage ON productionlogs.ProductionID = dailyusage.ProductionLogID
WHERE     (productionlogs.ProductID = @ProductID) AND (productionlogs.ProductionDate = @ProdDate);
END$$

DELIMITER ;

/* Definition for the `Get_Test` procedure : */

DELIMITER $$

CREATE DEFINER = 'root'@'localhost' PROCEDURE `Get_Test`(
        IN `ProductID` VARCHAR(25),
        IN `ProdDate` DATE
    )
    NOT DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
SELECT     productionlogs.ProductionID, productionlogs.ProductID, productionlogs.ProductionDate, productionlogs.Hopper1Mat, productionlogs.Hopper1Lbs, 
                      productionlogs.Hopper2Mat, productionlogs.Hopper2Lbs, productionlogs.Hopper3Mat, productionlogs.Hopper3Lbs, productionlogs.Hopper4Mat, 
                      productionlogs.Hopper4Lbs, productionlogs.BlenderTotal, productionlogs.BigDryerTemp, productionlogs.BigDryerDew, 
                      productionlogs.PressDryerTemp, productionlogs.PressDryerDew, productionlogs.PressCounter, productionlogs.Rejects, productionlogs.ChillerTemp, 
                      productionlogs.MoldTemp, productionlogs.T1, productionlogs.T2, productionlogs.T3, productionlogs.T4, productionlogs.M1, productionlogs.M2, 
                      productionlogs.M3, productionlogs.M4, productionlogs.M5, productionlogs.M6, productionlogs.M7, productionlogs.Comments, 
                      dailyusage.DailyHopper1Lbs, dailyusage.DailyHopper2Lbs, dailyusage.DailyHopper3Lbs, dailyusage.DailyHopper4Lbs, dailyusage.PercentageH1, 
                      dailyusage.PercentageH2, dailyusage.PercentageH3, dailyusage.PercentageH4
FROM       productionlogs INNER JOIN dailyusage ON productionlogs.ProductionID = dailyusage.ProductionLogID
WHERE     (productionlogs.ProductID = @ProductID) AND (productionlogs.ProductionDate = @ProdDate);
END$$

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;