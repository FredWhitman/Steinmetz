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
  `prodLogID` INTEGER(11) NOT NULL,
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
  `matPartNumber` VARCHAR(20) COLLATE latin1_swedish_ci NOT NULL,
  `matName` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL,
  `productID` VARCHAR(20) COLLATE latin1_swedish_ci DEFAULT NULL,
  `minLbs` INTEGER(11) DEFAULT NULL,
  `customer` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL COMMENT 'Material for Customer Products',
  `displayOrder` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`matPartNumber`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `materialinventory` table : */

CREATE TABLE `materialinventory` (
  `matPartNumber` VARCHAR(20) COLLATE latin1_swedish_ci NOT NULL,
  `matLbs` DOUBLE(15,3) NOT NULL,
  PRIMARY KEY USING BTREE (`matPartNumber`),
  UNIQUE KEY `MaterialPartNumber` USING BTREE (`matPartNumber`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `materiallog` table : */

CREATE TABLE `materiallog` (
  `logID` INTEGER(11) NOT NULL AUTO_INCREMENT,
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
AUTO_INCREMENT=111 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `parttransaction` table : */

CREATE TABLE `parttransaction` (
  `TransactionID` INTEGER(15) NOT NULL AUTO_INCREMENT,
  `ProductionID` INTEGER(15) NOT NULL,
  `ProductID` VARCHAR(20) COLLATE latin1_general_ci NOT NULL,
  `TransactionDate` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `OldInventoryAmount` INTEGER(11) NOT NULL,
  `TransactionAmount` INTEGER(11) NOT NULL,
  `NewInventoryAmount` INTEGER(11) NOT NULL,
  `TransactionType` VARCHAR(20) COLLATE latin1_general_ci NOT NULL COMMENT 'production log or inventory edit',
  PRIMARY KEY USING BTREE (`TransactionID`),
  UNIQUE KEY `TransactionID` USING BTREE (`TransactionID`)
) ENGINE=InnoDB
AUTO_INCREMENT=8485 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `pfm` table : */

CREATE TABLE `pfm` (
  `pFMID` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `partNumber` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `partName` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `productID` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `minQty` INTEGER(11) DEFAULT NULL,
  `customer` VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `displayOrder` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`pFMID`),
  UNIQUE KEY `PFMID` USING BTREE (`pFMID`)
) ENGINE=InnoDB
AUTO_INCREMENT=27 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
COMMENT='Purchased Finished Material'
;

/* Structure for the `pfminventory` table : */

CREATE TABLE `pfminventory` (
  `partNumber` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Qty` INTEGER(11) NOT NULL,
  PRIMARY KEY USING BTREE (`partNumber`),
  UNIQUE KEY `PartNumber` USING BTREE (`partNumber`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `prodrunlog` table : */

CREATE TABLE `prodrunlog` (
  `logID` INTEGER(15) NOT NULL AUTO_INCREMENT,
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
  `runComplete` VARCHAR(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY USING BTREE (`logID`),
  UNIQUE KEY `ProductionRunID` USING BTREE (`logID`)
) ENGINE=InnoDB
AUTO_INCREMENT=10 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `productinventory` table : */

CREATE TABLE `productinventory` (
  `productID` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `partQty` INTEGER(11) DEFAULT NULL,
  `updated_last` TIMESTAMP NULL DEFAULT current_timestamp(),
  PRIMARY KEY USING BTREE (`productID`)
) ENGINE=InnoDB
ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `productionlogs` table : */

CREATE TABLE `productionlogs` (
  `logID` INTEGER(20) NOT NULL AUTO_INCREMENT,
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
AUTO_INCREMENT=111 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;

/* Structure for the `products` table : */

CREATE TABLE `products` (
  `productID` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `partName` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `minQty` INTEGER(11) DEFAULT NULL,
  `boxesPerSkid` INTEGER(4) DEFAULT NULL,
  `partsPerBox` INTEGER(4) DEFAULT NULL,
  `partWeight` DOUBLE(8,3) DEFAULT NULL,
  `displayOrder` INTEGER(11) DEFAULT NULL,
  `customer` VARCHAR(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `productionType` VARCHAR(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY USING BTREE (`productID`)
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
  `logID` INTEGER(11) NOT NULL AUTO_INCREMENT,
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
AUTO_INCREMENT=111 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
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

/* Data for the `lotchange` table  (LIMIT 0,500) */

INSERT INTO `lotchange` (`LotChangeID`, `prodLogID`, `MaterialName`, `ProductID`, `ChangeDate`, `ChangeTime`, `OldLot`, `NewLot`, `Comments`) VALUES
  (589,0,'Texin 255','10601','2025-04-07','13:16:00','asdasd','gsdfaa','sdf');
COMMIT;

/* Data for the `material` table  (LIMIT 0,500) */

INSERT INTO `material` (`matPartNumber`, `matName`, `productID`, `minLbs`, `customer`, `displayOrder`) VALUES
  (' LC10358','Red Pad Colorant','98-1-10881',100,'Amsted',8),
  ('01622858','Texin 255','10601',20000,'Amsted',1),
  ('01623811','Texin 990R','10454',1400,'Amsted',2),
  ('02983811','Texin 945U','10454',1400,'Amsted',3),
  ('0931474','Yellow  BBG Colorant','WE-5525',99,'Amsted',11),
  ('100708001','Chemlon 109H','WE-5525',1400,'Amsted',9),
  ('1195A10','1195A10 Elastollan','10454',10,NULL,20),
  ('223451','Avalon 95AB','10601',1000,NULL,14),
  ('223478','Avalon 60DB','10601',1000,NULL,22),
  ('255 Mix','255 Mix','10601',0,NULL,12),
  ('A92P4637','A92P4637','10454',10,NULL,21),
  ('ARC6608-NT','ARC6608-NT','WE-5525',0,'Amsted',4),
  ('B1_Blend_Alt','B1_Blend_Alt','10454',1,'Amsted',13),
  ('BSP1000','BSP1000 (Black TPU Pigment)','10601',50,NULL,10),
  ('Desmopan 9390AU','Desmopan 9390AU','10454',0,NULL,19),
  ('Desmopan 9395AU','Desmopan 9395AU','10454',0,NULL,18),
  ('Elastollan C95A10','Elastollan C95A10','10601',0,NULL,17),
  ('Elastollan S95A55N','Elastollan S95A55N','10601',2000,NULL,16),
  ('Laripur 5725','Laripur 5725','10601',0,NULL,15),
  ('LC11700','Yellow pigment Drumco','WE-5525',0,NULL,7),
  ('RV53631502','Teal Blue','10454',800,'Amsted',6),
  ('RV64631325','Texin 255 Green','10601',2000,'Amsted',5),
  ('Texin 292A','Texin 292A','10601',0,NULL,23);
COMMIT;

/* Data for the `materialinventory` table  (LIMIT 0,500) */

INSERT INTO `materialinventory` (`matPartNumber`, `matLbs`) VALUES
  (' LC10358',0.000),
  ('01622858',33160.414),
  ('01623811',1467.658),
  ('02983811',1111.866),
  ('0931474',492.557),
  ('100708001',0.000),
  ('1195A10',0.000),
  ('223451',0.000),
  ('223478',0.000),
  ('255 Mix',0.000),
  ('A92P4637',0.000),
  ('Andur 75DPLF',0.000),
  ('ARC6608-NT',4923.448),
  ('B1_Blend_Alt',115.052),
  ('BSP1000',87.280),
  ('Desmopan 9390AU',800.000),
  ('Desmopan 9395AU',0.000),
  ('Elastollan C95A10',0.000),
  ('Elastollan S95A55N',0.000),
  ('Laripur 5725',0.000),
  ('RV53631502',1072.502),
  ('RV64631325',1974.467),
  ('T-Color',0.000),
  ('T-Material',0.000),
  ('Texin 292A',0.000);
COMMIT;

/* Data for the `materiallog` table  (LIMIT 0,500) */

INSERT INTO `materiallog` (`logID`, `prodLogID`, `mat1`, `matUsed1`, `mat2`, `matUsed2`, `mat3`, `matUsed3`, `mat4`, `matUsed4`) VALUES
  (1,1,'Texin 255',1844.752,'Texin 255 Green',50.822,'',0.000,'',0.000),
  (2,2,'Texin 255',3924.352,'Texin 255 Green',107.997,'',0.000,'',0.000),
  (3,3,'Texin 255',5552.146,'Texin 255 Green',154.567,'',0.000,'',0.000),
  (4,4,'Texin 255',7642.719,'Texin 255 Green',214.614,'',0.000,'',0.000),
  (5,5,'Texin 255',9722.006,'Texin 255 Green',274.115,'',0.000,'',0.000),
  (6,6,'Texin 255',11816.689,'Texin 255 Green',334.492,'',0.000,'',0.000),
  (7,7,'Texin 255',12152.683,'Texin 255 Green',344.155,'',0.000,'',0.000),
  (8,8,'Texin 255',14485.839,'Texin 255 Green',411.436,'',0.000,'',0.000),
  (9,9,'Texin 255',16916.495,'Texin 255 Green',481.182,'',0.000,'',0.000),
  (10,10,'Texin 255',19312.410,'Texin 255 Green',550.012,'',0.000,'',0.000),
  (11,11,'Texin 255',21719.686,'Texin 255 Green',619.102,'',0.000,'',0.000),
  (12,12,'Texin 255',24142.224,'Texin 255 Green',656.923,'',0.000,'',0.000),
  (13,13,'Texin 255',26591.946,'Texin 255 Green',690.575,'',0.000,'',0.000),
  (14,14,'Texin 255',28594.757,'Texin 255 Green',717.834,'',0.000,'',0.000),
  (15,15,'Texin 255',31123.588,'Texin 255 Green',752.365,'',0.000,'',0.000),
  (16,16,'Texin 255',33561.257,'Texin 255 Green',785.966,'',0.000,'',0.000),
  (17,17,'Texin 255',36003.241,'Texin 255 Green',819.566,'',0.000,'',0.000),
  (18,18,'Texin 255',38446.488,'Texin 255 Green',852.768,'',0.000,'',0.000),
  (19,19,'Texin 255',40896.214,'Texin 255 Green',886.059,'',0.000,'',0.000),
  (20,20,'Texin 255',40896.214,'Texin 945U',886.059,'',0.000,'',0.000),
  (21,21,'ARC6608-NT',420.556,'Yellow BBG Colorant',6.404,'',0.000,'',0.000),
  (22,22,'ARC6608-NT',776.860,'Yellow  BBG Colorant',11.830,'',0.000,'',0.000),
  (23,23,'ARC6608-NT',1185.734,'Yellow  BBG Colorant',18.057,'',0.000,'',0.000),
  (24,24,'ARC6608-NT',1693.906,'Yellow  BBG Colorant',25.796,'',0.000,'',0.000),
  (25,25,'ARC6608-NT',2190.395,'Yellow  BBG Colorant',33.357,'',0.000,'',0.000),
  (26,26,'ARC6608-NT',2721.931,'Yellow  BBG Colorant',41.451,'',0.000,'',0.000),
  (27,27,'ARC6608-NT',3044.357,'Yellow  BBG Colorant',46.361,'',0.000,'',0.000),
  (28,28,'Texin 990R',582.500,'Teal Blue',22.944,'B1_Blend_Alt',213.108,'Texin 945U',583.474),
  (29,29,'Texin 990R',1110.163,'Teal Blue',44.045,'B1_Blend_Alt',407.102,'Texin 945U',1111.837),
  (30,30,'Texin 990R',1730.003,'Teal Blue',68.584,'B1_Blend_Alt',634.883,'Texin 945U',1732.334),
  (31,31,'Texin 990R',2284.369,'Teal Blue',89.078,'B1_Blend_Alt',838.266,'Texin 945U',2287.515),
  (32,32,'Texin 990R',2723.105,'Teal Blue',105.058,'B1_Blend_Alt',1000.014,'Texin 945U',2727.110),
  (33,33,'Texin 990R',495.139,'Teal Blue',18.681,'B1_Blend_Alt',181.513,'Texin 945U',492.115),
  (34,34,'Texin 990R',836.830,'Teal Blue',32.015,'B1_Blend_Alt',306.220,'Texin 945U',833.912),
  (35,35,'Texin 990R',1340.903,'Teal Blue',51.718,'B1_Blend_Alt',490.080,'Texin 945U',1338.270),
  (36,36,'Texin 990R',1781.537,'Teal Blue',68.646,'B1_Blend_Alt',650.944,'Texin 945U',1779.454),
  (37,37,'Texin 990R',2049.743,'Teal Blue',77.929,'B1_Blend_Alt',674.183,'Texin 945U',2044.669),
  (38,38,'Texin 990R',2663.345,'Teal Blue',98.672,'B1_Blend_Alt',674.183,'Texin 945U',2659.141),
  (39,39,'Texin 990R',3187.798,'Teal Blue',116.830,'B1_Blend_Alt',674.183,'Texin 945U',3182.325),
  (40,40,'Texin 990R',3557.019,'Teal Blue',130.625,'B1_Blend_Alt',674.183,'Texin 945U',3551.673),
  (41,41,'Texin 990R',4041.531,'Teal Blue',145.650,'B1_Blend_Alt',674.183,'Texin 945U',4036.161),
  (42,42,'Texin 990R',4041.531,'Teal Blue',145.650,'B1_Blend_Alt',674.186,'Texin 945U',4036.161),
  (43,43,'Texin 990R',467.087,'Red Pad Colorant',17.178,'B1_Blend_Alt',170.505,'Texin 945U',467.392),
  (44,44,'Texin 990R',639.502,'Red Pad Colorant',23.425,'B1_Blend_Alt',233.453,'Texin 945U',639.799),
  (45,45,'Texin 990R',790.136,'Red Pad Colorant',29.742,'B1_Blend_Alt',288.094,'Texin 945U',788.810),
  (46,46,'Texin 990R',1172.102,'Red Pad Colorant',45.214,'B1_Blend_Alt',427.377,'Texin 945U',1170.206),
  (47,47,'Texin 990R',1788.332,'Red Pad Colorant',71.818,'B1_Blend_Alt',652.116,'Texin 945U',1787.121),
  (48,48,'Texin 990R',2131.669,'Red Pad Colorant',86.834,'B1_Blend_Alt',777.172,'Texin 945U',2130.945),
  (49,49,'Texin 990R',2131.669,'Red Pad Colorant',86.834,'B1_Blend_Alt',777.172,'Texin 945U',2130.945),
  (50,50,'Texin 255',2364.128,'Texin 255 Green',43.121,'',0.000,'255 Mix',128.979),
  (51,51,'Texin 255',4358.183,'Texin 255 Green',79.290,'',0.000,'255 Mix',237.672),
  (52,52,'Texin 255',6363.037,'Texin 255 Green',115.269,'',0.000,'255 Mix',347.009),
  (53,53,'Texin 255',8104.910,'Texin 255 Green',148.632,'',0.000,'255 Mix',441.988),
  (54,54,'Texin 255',10075.537,'Texin 255 Green',186.262,'',0.000,'255 Mix',549.180),
  (55,55,'Texin 255',12009.278,'Texin 255 Green',223.292,'',0.000,'255 Mix',654.319),
  (56,56,'Texin 255',13953.176,'Texin 255 Green',260.433,'',0.000,'255 Mix',760.538),
  (57,57,'Texin 255',15381.681,'Texin 255 Green',287.522,'',0.000,'255 Mix',838.422),
  (58,58,'Texin 255',17340.889,'Texin 255 Green',324.475,'',0.000,'255 Mix',945.495),
  (59,59,'Texin 255',19264.004,'Texin 255 Green',361.162,'',0.000,'255 Mix',1050.240),
  (60,60,'Texin 255',21243.285,'Texin 255 Green',398.449,'',0.000,'255 Mix',1158.266),
  (61,61,'Texin 255',22654.893,'Texin 255 Green',425.495,'',0.000,'255 Mix',1224.416),
  (62,62,'Texin 255',24644.345,'Texin 255 Green',465.002,'',0.000,'255 Mix',1224.416),
  (63,63,'Texin 255',24988.059,'Texin 255 Green',471.924,'',0.000,'255 Mix',1224.416),
  (64,64,'Texin 255',27012.945,'Texin 255 Green',512.945,'',0.000,'255 Mix',1224.416),
  (65,65,'Texin 255',29129.159,'Texin 255 Green',554.283,'',0.000,'255 Mix',1224.416),
  (66,66,'Texin 255',31221.885,'Texin 255 Green',595.688,'',0.000,'255 Mix',1224.416),
  (67,67,'Texin 255',33322.463,'Texin 255 Green',637.501,'',0.000,'255 Mix',1224.416),
  (68,68,'Texin 255',33666.072,'Texin 255 Green',644.297,'',0.000,'255 Mix',1224.416),
  (69,69,'Texin 255',35770.867,'Texin 255 Green',681.792,'',0.000,'255 Mix',1224.416),
  (70,70,'Texin 255',37871.186,'Texin 255 Green',718.534,'',0.000,'255 Mix',1224.416),
  (71,71,'Texin 255',39395.242,'Texin 255 Green',745.255,'',0.000,'255 Mix',1224.416),
  (72,72,'Texin 255',41506.900,'Texin 255 Green',782.744,'',0.000,'255 Mix',1224.416),
  (73,73,'Texin 255',43583.572,'Texin 255 Green',819.597,'',0.000,'255 Mix',1224.416),
  (74,74,'Texin 255',45628.986,'Texin 255 Green',855.839,'',0.000,'255 Mix',1224.416),
  (75,75,'Texin 255',47537.725,'Texin 255 Green',889.670,'',0.000,'255 Mix',1224.416),
  (76,76,'Texin 255',48994.813,'Texin 255 Green',915.419,'',0.000,'255 Mix',1224.416),
  (77,77,'Texin 255',51076.008,'Texin 255 Green',952.242,'',0.000,'255 Mix',1224.416),
  (78,78,'Texin 255',53112.495,'Texin 255 Green',989.864,'',0.000,'255 Mix',1224.416),
  (79,79,'Texin 255',53882.361,'Texin 255 Green',1004.065,'',0.000,'255 Mix',1224.416),
  (80,80,'ARC6608-NT',282.707,'Yellow  BBG Colorant',4.305,'',0.000,'',0.000),
  (81,81,'ARC6608-NT',815.411,'Yellow  BBG Colorant',12.417,'',0.000,'',0.000),
  (82,82,'ARC6608-NT',1348.115,'Yellow  BBG Colorant',20.529,'',0.000,'',0.000),
  (83,83,'ARC6608-NT',1933.388,'Yellow  BBG Colorant',29.442,'',0.000,'',0.000),
  (84,84,'ARC6608-NT',2448.569,'Yellow  BBG Colorant',37.287,'',0.000,'',0.000),
  (85,85,'ARC6608-NT',2987.114,'Red Pad Colorant',45.488,'',0.000,'',0.000),
  (86,86,'ARC6608-NT',3557.200,'Yellow  BBG Colorant',54.170,'',0.000,'',0.000),
  (87,87,'ARC6608-NT',4100.418,'Yellow  BBG Colorant',62.442,'',0.000,'',0.000),
  (88,88,'ARC6608-NT',4651.813,'Yellow  BBG Colorant',70.839,'',0.000,'',0.000),
  (89,89,'Texin 990R',226.945,'Red Pad Colorant',7.930,'',0.000,'Texin 945U',223.508),
  (90,90,'Texin 990R',226.945,'Red Pad Colorant',7.930,'',0.000,'Texin 945U',223.383),
  (91,91,'Texin 990R',444.302,'Teal Blue',14.295,'',0.000,'Texin 945U',444.565),
  (92,92,'Texin 990R',854.780,'Teal Blue',27.619,'',0.000,'Texin 945U',855.411),
  (93,93,'Texin 990R',1322.371,'Teal Blue',42.861,'',0.000,'Texin 945U',1323.337),
  (94,94,'ARC6608-NT',5203.208,'Yellow  BBG Colorant',79.236,'',0.000,'',0.000),
  (95,95,'ARC6608-NT',5733.575,'Yellow  BBG Colorant',87.313,'',0.000,'',0.000),
  (96,96,'Texin 990R',1415.390,'Teal Blue',45.887,'',0.000,'Texin 945U',1416.092),
  (97,97,'ARC6608-NT',6259.270,'Yellow  BBG Colorant',95.319,'',0.000,'',0.000),
  (98,98,'ARC6608-NT',6645.948,'Yellow  BBG Colorant',101.207,'',0.000,'',0.000),
  (99,99,'Texin 990R',1843.243,'Teal Blue',60.244,'',0.000,'Texin 945U',1843.357),
  (100,100,'Texin 990R',2414.438,'Teal Blue',81.798,'',0.000,'Texin 945U',2409.922),
  (101,101,'ARC6608-NT',7193.838,'Yellow  BBG Colorant',109.551,'',0.000,'',0.000),
  (102,102,'Texin 990R',3065.499,'Teal Blue',106.317,'',0.000,'Texin 945U',3061.324),
  (103,103,'ARC6608-NT',7624.907,'Yellow  BBG Colorant',116.116,'',0.000,'',0.000),
  (104,104,'Texin 990R',3308.031,'Teal Blue',115.583,'empty',0.000,'Texin 945U',3304.183),
  (105,105,'Texin 990R',3834.724,'Teal Blue',134.209,'empty',0.000,'Texin 945U',381.410),
  (106,106,'Texin 990R',4220.509,'Teal Blue',145.941,'empty',0.000,'Texin 945U',4200.509),
  (107,107,'Texin 255',2004.303,'Texin 255 Green',30.641,'',0.000,'',0.000),
  (108,108,'Texin 255',4106.076,'Texin 255 Green',64.198,'',0.000,'',0.000),
  (109,109,'01622858',6200.599,'RV64631325',97.616,'',0.000,'',0.000),
  (110,110,'Texin 255',8303.261,'Texin 255 Green',130.771,'',0.000,'',0.000);
COMMIT;

/* Data for the `pfm` table  (LIMIT 0,500) */

INSERT INTO `pfm` (`pFMID`, `partNumber`, `partName`, `productID`, `minQty`, `customer`, `displayOrder`) VALUES
  (1,'0 Inspection Stickers','0 Inspection Stickers','10601',1000,'Amsted',22),
  (2,'1 Inspection Stickers','1 Inspection Stickers','10601',1000,'Amsted',26),
  (3,'10 1/2\" x 17\"','Box Dividers','10601',6000,'Amsted',3),
  (4,'10603-2','Copper Pins - 10603-2','98-1-10894',500,'Amsted',4),
  (5,'17 1/2\" x 10 1/2\" x17\"','Brake Beam Guide Boxes','WE-5525',120,'Amsted',2),
  (6,'18 3/4\" x 10 1/8\" x 20 3/4\"','Adapter Plus Boxes','10601',1000,'Amsted',1),
  (7,'2 Inspection Stickers','2 Inspection Stickers','10601',1000,'Amsted',25),
  (8,'3 Inspection Stickers','3 Inspection Stickers','10601',500,'Amsted',18),
  (9,'349-61A0','Copper Pins - 10603','10601',80000,'Amsted',27),
  (10,'4 Inspection Stickers','4 Inspection Stickers','10601',1000,'Amsted',24),
  (11,'Blank Label','Blank Label','10601',500,'Amsted',23),
  (12,'Blue Stickers','Blue Stickers','10454',1000,'Amsted',15),
  (13,'Green Stickers','Green Stickers','10601',1000,'Amsted',14),
  (14,'QC Accepted Stickers','QC Accepted Stickers','10601',1000,'Amsted',20),
  (15,'QC Approval Stickers','QC Approval Stickers','10601',1000,'Amsted',19),
  (16,'Red Stickers','Red Stickers','98-1-10881',1000,'Amsted',16),
  (17,'S-458-1 \"10454 Labels\"','10454 Labels','10454',1000,'Amsted',6),
  (18,'S-458-1 \"10457 Labels\"','10457 Labels','10457',500,'Amsted',11),
  (19,'S-458-1 \"10471 Labels\"','10471 Labels','10471',500,'Amsted',12),
  (20,'S-458-1 \"10522A Labels\"','10522A Labels','10522A',500,'Amsted',13),
  (21,'S-458-1 \"10601 Labels\"','10601 Labels','10601',1000,'Amsted',5),
  (22,'S-458-1 \"98-1-10835 Labels\"','98-1-10835 Labels','98-1-10835',1000,'Amsted',9),
  (23,'S-458-1 \"98-1-10881 Labels\"','98-1-10881','98-1-10881',1000,'Amsted',7),
  (24,'S-458-1 \"98-1-10894 Labels\"','98-1-10894 Labels','98-1-10894',500,'Amsted',10),
  (25,'S-458-1 \"WE-5525 Labels\"','WE-5525 Labels','WE-5525',500,'Amsted',8),
  (26,'Yellow Stickers','Yellow Stickers','WE-5525',500,'Amsted',17);
COMMIT;

/* Data for the `pfminventory` table  (LIMIT 0,500) */

INSERT INTO `pfminventory` (`partNumber`, `Qty`) VALUES
  ('0 Inspection Stickers',4000),
  ('1 Inspection Stickers',4000),
  ('10 1/2\" x 17\"',26000),
  ('10603-2',10093),
  ('17 1/2\" x 10 1/2\" x17\"',1200),
  ('18 3/4\" x 10 1/8\" x 20 3/4\"',6644),
  ('2 Inspection Stickers',9500),
  ('3 Inspection Stickers',0),
  ('349-61A0',38405),
  ('4 Inspection Stickers',5500),
  ('Black Stickers',2500),
  ('Blank Label',500),
  ('Blue Stickers',8500),
  ('Green Stickers',3000),
  ('QC Accepted Stickers',1500),
  ('QC Approval Stickers',1500),
  ('Red Stickers',2000),
  ('S-458-1 \"10454 Labels\"',5000),
  ('S-458-1 \"10457 Labels\"',1500),
  ('S-458-1 \"10471 Labels\"',500),
  ('S-458-1 \"10522A Labels\"',500),
  ('S-458-1 \"10601 Labels\"',2000),
  ('S-458-1 \"98-1-10835 Labels\"',1000),
  ('S-458-1 \"98-1-10881 Labels\"',1000),
  ('S-458-1 \"98-1-10894 Labels\"',500),
  ('S-458-1 \"WE-5525 Labels\"',6000),
  ('Yellow Stickers',2000);
COMMIT;

/* Data for the `prodrunlog` table  (LIMIT 0,500) */

INSERT INTO `prodrunlog` (`logID`, `productID`, `startDate`, `endDate`, `mat1Lbs`, `mat2Lbs`, `mat3Lbs`, `mat4Lbs`, `partsProduced`, `startupRejects`, `qaRejects`, `purgeLbs`, `runComplete`) VALUES
  (1,'10601','2024-12-18','2025-01-31',40896.214,886.059,0.000,0.000,32044,279,0,0.000,'yes'),
  (2,'WE-5525','2025-01-22','2025-02-03',3044.357,145.650,0.000,0.000,5212,110,0,0.000,'yes'),
  (3,'10454','2025-02-03','2025-03-07',4041.531,145.650,674.186,4036.161,11784,66,0,0.000,'yes'),
  (4,'98-1-10881','2025-02-12','2025-02-21',2131.669,86.834,777.172,2130.945,4130,67,0,0.000,'yes'),
  (5,'10601','2025-03-11','2025-05-06',53882.361,1004.065,0.000,1224.416,42522,472,0,0.000,'yes'),
  (6,'WE-5525','2025-05-08','2025-05-29',7624.904,116.116,0.000,0.000,12978,98,0,0.000,'yes'),
  (7,'98-1-10881','2025-05-14','2025-05-16',226.945,7.930,0.000,223.380,350,25,0,0.000,'yes'),
  (8,'10454','2025-05-19','2025-05-31',22723.287,774.754,0.000,19240.110,7498,18,0,0.000,'yes'),
  (9,'10601','2025-06-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0.000,'no');
COMMIT;

/* Data for the `productinventory` table  (LIMIT 0,500) */

INSERT INTO `productinventory` (`productID`, `partQty`, `updated_last`) VALUES
  ('10454',13093,NULL),
  ('10457',-20,NULL),
  ('10471',0,NULL),
  ('10522A',0,NULL),
  ('10601',24094,NULL),
  ('98-1-10702',36,NULL),
  ('98-1-10730',84,NULL),
  ('98-1-10835',896,NULL),
  ('98-1-10881',206,NULL),
  ('98-1-10894',1158,NULL),
  ('98-1-11020',52,NULL),
  ('WE-5525',6302,NULL);
COMMIT;

/* Data for the `productionlogs` table  (LIMIT 0,500) */

INSERT INTO `productionlogs` (`logID`, `productID`, `prodDate`, `runStatus`, `prevProdLogID`, `runLogID`, `matLogID`, `tempLogID`, `pressCounter`, `startUpRejects`, `qaRejects`, `purgeLbs`, `Comments`) VALUES
  (1,'10601','2024-12-18','start',0,1,1,1,1628,0,0,0.000,''),
  (2,'10601','2024-12-19','in progress',1,1,2,2,1630,31,0,0.000,''),
  (3,'10601','2025-01-06','in progress',2,1,3,3,1252,48,0,0.000,''),
  (4,'10601','2025-01-07','in progress',3,1,4,4,1616,92,0,0.000,''),
  (5,'10601','2025-01-08','in progress',4,1,5,5,1644,58,0,0.000,''),
  (6,'10601','2025-01-09','in progress',5,1,6,6,1632,68,0,0.000,''),
  (7,'10601','2025-01-10','in progress',6,1,7,7,270,14,0,0.000,''),
  (8,'10601','2025-01-13','in progress',7,1,8,8,1832,28,0,0.000,'Dropped cooling time, increase screw recovery and raised temps on hot runner.'),
  (9,'10601','2025-01-14','in progress',8,1,9,9,1898,16,0,0.000,''),
  (10,'10601','2025-01-15','in progress',9,1,10,10,1902,22,0,0.000,''),
  (11,'10601','2025-01-16','in progress',10,1,11,11,1896,16,0,0.000,''),
  (12,'10601','2025-01-20','in progress',11,1,12,12,1866,18,0,0.000,''),
  (13,'10601','2025-01-21','in progress',12,1,13,13,1900,10,0,0.000,''),
  (14,'10601','2025-01-22','in progress',13,1,14,14,1548,16,0,0.000,'Down for close feed line.'),
  (15,'10601','2025-01-23','in progress',14,1,15,15,1960,26,0,0.000,''),
  (16,'10601','2025-01-27','in progress',15,1,16,16,1872,20,0,0.000,''),
  (17,'10601','2025-01-28','in progress',16,1,17,17,1894,3,0,0.000,''),
  (18,'10601','2025-01-29','in progress',17,1,18,18,1906,8,0,0.000,''),
  (19,'10601','2025-01-30','in progress',18,1,19,19,1898,5,0,0.000,''),
  (20,'10601','2025-01-31','end',19,1,20,20,0,0,0,0.000,'Ending production run'),
  (21,'WE-5525','2025-01-22','start',0,2,21,21,720,40,0,0.000,''),
  (22,'WE-5525','2025-01-23','in progress',21,2,22,22,610,40,0,0.000,''),
  (23,'WE-5525','2025-01-24','in progress',22,2,23,23,700,0,0,0.000,''),
  (24,'WE-5525','2025-01-27','in progress',23,2,24,24,870,0,0,0.000,''),
  (25,'WE-5525','2025-01-28','in progress',24,2,25,25,850,17,0,0.000,''),
  (26,'WE-5525','2025-01-29','in progress',25,2,26,26,910,10,0,0.000,''),
  (27,'WE-5525','2025-02-03','end',26,2,27,27,552,3,0,0.000,''),
  (28,'10454','2025-02-03','start',0,3,28,28,708,7,0,0.000,''),
  (29,'10454','2025-02-04','in progress',28,3,29,29,1104,1,0,0.000,''),
  (30,'10454','2025-02-05','in progress',29,3,30,30,1132,0,0,0.000,''),
  (31,'10454','2025-02-06','in progress',30,3,31,31,1098,0,0,0.000,''),
  (32,'10454','2025-02-10','in progress',31,3,32,32,1018,7,0,0.000,''),
  (33,'10454','2025-02-24','in progress',32,3,33,33,212,32,0,0.000,'data check data could be wrong'),
  (34,'10454','2025-02-25','in progress',33,3,34,34,658,6,0,0.000,''),
  (35,'10454','2025-02-26','in progress',34,3,35,35,988,1,0,0.000,''),
  (36,'10454','2025-02-27','in progress',35,3,36,36,1004,1,0,0.000,''),
  (37,'10454','2025-02-28','in progress',36,3,37,37,294,1,0,0.000,''),
  (38,'10454','2025-03-03','in progress',37,3,38,38,1004,8,0,0.000,''),
  (39,'10454','2025-03-04','in progress',38,3,39,39,1026,0,0,0.000,''),
  (40,'10454','2025-03-05','in progress',39,3,40,40,426,5,0,0.000,''),
  (41,'10454','2025-03-06','in progress',40,3,41,41,798,8,0,0.000,''),
  (42,'10454','2025-03-07','end',41,3,42,42,314,5,0,0.000,''),
  (43,'98-1-10881','2025-02-12','start',0,4,43,43,464,38,0,0.000,''),
  (44,'98-1-10881','2025-02-13','in progress',43,4,44,44,320,19,0,0.000,''),
  (45,'98-1-10881','2025-02-14','in progress',44,4,45,45,298,6,0,0.000,''),
  (46,'98-1-10881','2025-02-17','in progress',45,4,46,46,918,22,0,0.000,''),
  (47,'98-1-10881','2025-02-19','in progress',46,4,47,47,1028,0,0,0.000,''),
  (48,'98-1-10881','2025-02-20','in progress',47,4,48,48,1018,0,0,0.000,''),
  (49,'98-1-10881','2025-02-21','end',48,4,49,49,84,0,0,0.000,''),
  (50,'10601','2025-03-11','start',0,5,50,50,1480,115,0,0.000,''),
  (51,'10601','2025-03-12','in progress',50,5,51,51,1624,23,0,0.000,''),
  (52,'10601','2025-03-13','in progress',51,5,52,52,1644,14,0,0.000,''),
  (53,'10601','2025-03-18','in progress',52,5,53,53,1420,15,0,0.000,''),
  (54,'10601','2025-03-19','in progress',53,5,54,54,1608,25,0,0.000,''),
  (55,'10601','2025-03-20','in progress',54,5,55,55,1580,11,0,0.000,''),
  (56,'10601','2025-03-25','in progress',55,5,56,56,1596,9,0,0.000,''),
  (57,'10601','2025-03-26','in progress',56,5,57,57,1152,8,0,0.000,'down for cycle time monitor.  Ejectors needed to be zeroed.'),
  (58,'10601','2025-03-27','in progress',57,5,58,58,1590,26,0,0.000,''),
  (59,'10601','2025-03-31','in progress',58,5,59,59,1568,26,0,0.000,''),
  (60,'10601','2025-04-01','in progress',59,5,60,60,1602,23,0,0.000,''),
  (61,'10601','2025-04-02','in progress',60,5,61,61,1158,13,0,0.000,'Shut down earlier because parts weights were high.  Check this morning and find that screw recovery was higher than normal due to material on hopper window affect material load times.'),
  (62,'10601','2025-04-03','in progress',61,5,62,62,1526,32,0,0.000,''),
  (63,'10601','2025-04-04','in progress',62,5,63,63,262,4,0,0.000,''),
  (64,'10601','2025-04-07','in progress',63,5,64,64,1568,20,0,0.000,''),
  (65,'10601','2025-04-08','in progress',64,5,65,65,1622,26,0,0.000,''),
  (66,'10601','2025-04-09','in progress',65,5,66,66,1640,16,0,0.000,''),
  (67,'10601','2025-04-10','in progress',66,5,67,67,1638,16,0,0.000,''),
  (68,'10601','2025-04-11','in progress',67,5,68,68,272,2,0,0.000,''),
  (69,'10601','2025-04-15','in progress',68,5,69,69,1608,4,0,0.000,''),
  (70,'10601','2025-04-16','in progress',69,5,70,70,1630,0,0,0.000,''),
  (71,'10601','2025-04-17','in progress',70,5,71,71,1180,8,0,0.000,''),
  (72,'10601','2025-04-22','in progress',71,5,72,72,1608,6,0,0.000,''),
  (73,'10601','2025-04-23','in progress',72,5,73,73,1614,0,0,0.000,''),
  (74,'10601','2025-04-24','in progress',73,5,74,74,1602,0,0,0.000,''),
  (75,'10601','2025-04-29','in progress',74,5,75,75,1456,12,0,0.000,''),
  (76,'10601','2025-04-30','in progress',75,5,76,76,1110,6,0,0.000,''),
  (77,'10601','2025-05-01','in progress',76,5,77,77,1622,0,0,0.000,''),
  (78,'10601','2025-05-05','in progress',77,5,78,78,1568,8,0,0.000,''),
  (79,'10601','2025-05-06','end',78,5,79,79,974,4,0,0.000,'end of run switching to blue ''10454'''),
  (80,'WE-5525','2025-05-08','start',0,6,80,80,484,24,0,0.000,''),
  (81,'WE-5525','2025-05-09','in progress',80,6,81,81,912,6,0,0.000,''),
  (82,'WE-5525','2025-05-12','in progress',81,6,82,82,912,2,0,0.000,''),
  (83,'WE-5525','2025-05-13','in progress',82,6,83,83,926,2,0,0.000,''),
  (84,'WE-5525','2025-05-14','in progress',83,6,84,84,882,3,0,0.000,''),
  (85,'WE-5525','2025-05-15','in progress',84,6,85,85,922,5,0,0.000,''),
  (86,'WE-5525','2025-05-16','in progress',85,6,86,86,976,0,0,0.000,''),
  (87,'WE-5525','2025-05-19','in progress',86,6,87,87,930,1,0,0.000,''),
  (88,'WE-5525','2025-05-20','in progress',87,6,88,88,944,1,0,0.000,''),
  (89,'98-1-10881','2025-05-14','start',0,7,89,89,350,25,0,0.000,''),
  (90,'98-1-10881','2025-05-16','end',89,7,90,90,0,0,0,0.000,'Ended production run due to lack of pigment.  Switching to ''10454''.'),
  (91,'10454','2025-05-19','start',0,8,91,91,662,8,0,0.000,'Started in the afternoon'),
  (92,'10454','2025-05-20','in progress',91,8,92,92,868,0,0,0.000,''),
  (93,'10454','2025-05-21','in progress',92,8,93,93,940,0,0,0.000,''),
  (94,'WE-5525','2025-05-21','in progress',88,6,94,94,944,0,0,0.000,''),
  (95,'WE-5525','2025-05-22','in progress',94,6,95,95,908,2,0,0.000,''),
  (96,'10454','2025-05-22','in progress',93,8,96,96,488,0,0,0.000,''),
  (97,'WE-5525','2025-05-23','in progress',95,6,97,97,900,0,0,0.000,''),
  (98,'WE-5525','2025-05-27','in progress',97,6,98,98,662,0,0,0.000,''),
  (99,'10454','2025-05-23','in progress',96,8,99,99,532,6,0,0.000,''),
  (100,'10454','2025-05-27','in progress',99,8,100,100,794,2,0,0.000,''),
  (101,'WE-5525','2025-05-28','in progress',98,6,101,101,938,0,0,0.000,''),
  (102,'10454','2025-05-28','in progress',100,8,102,102,944,0,0,0.000,''),
  (103,'WE-5525','2025-05-29','end',101,6,103,103,738,52,0,0.000,'Ran out of pigment and ended production run'),
  (104,'10454','2025-05-29','in progress',102,8,0,0,402,2,0,0.000,NULL),
  (105,'10454','2025-05-30','in progress',104,8,0,0,952,0,0,0.000,NULL),
  (106,'10454','2025-05-31','end',105,8,0,0,916,0,0,0.000,NULL),
  (107,'10601','2025-06-02','start',0,9,107,107,1130,8,0,0.000,NULL),
  (108,'10601','2025-06-03','in progress',107,9,108,108,1636,0,0,0.000,NULL),
  (109,'10601','2025-06-04','in progress',108,9,109,109,1628,0,0,0.000,NULL),
  (110,'10601','2025-06-05','in progress',109,9,110,110,1626,0,0,0.000,NULL);
COMMIT;

/* Data for the `products` table  (LIMIT 0,500) */

INSERT INTO `products` (`productID`, `partName`, `minQty`, `boxesPerSkid`, `partsPerBox`, `partWeight`, `displayOrder`, `customer`, `productionType`) VALUES
  ('10454','10454',7000,20,32,1.243,2,'Amsted','Injection'),
  ('10457','10457',500,20,32,NULL,10,'Amsted','Cast'),
  ('10471','10471',500,20,32,NULL,9,'Amsted','Cast'),
  ('10522A','10522A',100,20,32,NULL,12,'Amsted','Cast'),
  ('10601','10601',20000,20,32,1.303,1,'Amsted','Injection'),
  ('98-1-10702','98-1-10702',500,20,32,NULL,11,'Amsted','Cast'),
  ('98-1-10730','98-1-10730',100,20,32,NULL,7,'Amsted','Cast'),
  ('98-1-10835','98-1-10835',10,20,32,1.242,6,'Amsted','Injection'),
  ('98-1-10881','98-1-10881',2000,20,32,1.242,4,'Amsted','Injection'),
  ('98-1-10894','98-1-10894',50,0,0,NULL,5,'Amsted','Injection'),
  ('98-1-11020','98-1-11020',1000,20,32,NULL,8,'Amsted','Injection'),
  ('WE-5525','WE-5525',1000,20,50,NULL,3,'Amsted','Injection');
COMMIT;

/* Data for the `templog` table  (LIMIT 0,500) */

INSERT INTO `templog` (`logID`, `prodLogID`, `bigDryerTemp`, `bigDryerDew`, `pressDryerTemp`, `pressDryerDew`, `t1`, `t2`, `t3`, `t4`, `m1`, `m2`, `m3`, `m4`, `m5`, `m6`, `m7`, `chillerTemp`, `moldTemp`) VALUES
  (1,1,180,-40,0,0,425,425,425,425,430,430,430,430,430,430,430,67,91),
  (2,2,180,-40,0,0,426,425,425,426,430,429,430,430,429,430,430,66,90),
  (3,3,180,-40,0,0,425,425,425,425,430,430,431,430,430,429,435,67,88),
  (4,4,180,-40,0,0,425,425,425,425,430,430,430,430,430,430,430,65,90),
  (5,5,180,-40,0,0,426,426,427,426,430,430,431,430,430,431,431,65,93),
  (6,6,180,-40,0,0,425,425,425,425,430,430,430,430,430,430,430,67,87),
  (7,7,180,-40,0,0,425,425,425,425,430,431,431,430,430,430,431,66,90),
  (8,8,180,-40,0,0,435,435,435,435,444,446,444,444,444,444,445,66,92),
  (9,9,180,-40,0,0,435,435,435,435,446,444,446,445,445,446,446,66,97),
  (10,10,180,-40,0,0,435,435,435,435,441,440,440,441,441,441,441,65,87),
  (11,11,180,-40,0,0,435,435,435,435,439,440,440,440,441,440,440,66,96),
  (12,12,180,-40,0,0,435,435,435,434,439,441,440,439,440,440,441,64,91),
  (13,13,180,-40,0,0,435,435,435,435,440,441,441,440,440,441,440,67,92),
  (14,14,180,-40,0,0,436,435,435,435,441,439,441,441,441,441,440,66,92),
  (15,15,180,-40,0,0,435,435,435,435,440,440,440,440,440,440,440,65,92),
  (16,16,180,-40,0,0,435,435,435,435,435,435,435,435,435,435,435,65,90),
  (17,17,180,-40,0,0,436,436,435,435,436,435,435,434,435,435,435,64,92),
  (18,18,180,-41,0,0,435,436,435,435,434,435,435,434,436,435,435,66,90),
  (19,19,180,-41,0,0,435,435,434,435,434,435,435,434,434,434,436,65,88),
  (20,20,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
  (21,21,180,-60,180,-40,560,0,0,0,0,0,0,0,0,0,0,70,105),
  (22,22,180,-40,0,0,540,0,0,0,0,0,0,0,0,0,0,69,106),
  (23,23,180,-60,180,-60,542,0,0,0,0,0,0,0,0,0,0,79,106),
  (24,24,180,-60,179,-60,540,0,0,0,0,0,0,0,0,0,0,77,105),
  (25,25,180,-60,180,-60,540,0,0,0,0,0,0,0,0,0,0,76,105),
  (26,26,180,-60,180,-60,540,0,0,0,0,0,0,0,0,0,0,75,105),
  (27,27,180,-60,180,-60,546,0,0,0,0,0,0,0,0,0,0,75,104),
  (28,28,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,85),
  (29,29,180,-40,0,0,410,410,410,410,410,410,410,410,410,411,410,66,87),
  (30,30,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,86),
  (31,31,179,-41,0,0,410,410,410,410,410,410,410,410,410,410,410,65,87),
  (32,32,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,85),
  (33,33,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,85),
  (34,34,180,-40,0,0,411,412,411,412,410,409,411,411,411,411,413,67,85),
  (35,35,180,-41,0,0,410,410,410,410,410,411,410,411,410,410,410,65,84),
  (36,36,180,-41,0,0,410,410,410,410,410,410,410,410,410,410,410,65,83),
  (37,37,180,-40,0,0,410,410,410,410,411,411,411,411,411,411,406,65,86),
  (38,38,180,-41,0,0,410,410,410,410,411,410,409,409,409,409,410,64,88),
  (39,39,179,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,66,87),
  (40,40,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,85),
  (41,41,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,88),
  (42,42,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,87),
  (43,43,180,-41,0,0,410,410,410,410,410,410,410,410,410,410,410,66,85),
  (44,44,180,-40,0,0,410,410,410,410,410,411,410,410,410,410,410,65,85),
  (45,45,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,66,87),
  (46,46,180,-40,0,0,410,410,410,410,411,410,410,410,410,410,410,65,85),
  (47,47,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,68,89),
  (48,48,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,85),
  (49,49,179,-41,0,0,410,410,410,410,410,410,410,410,410,410,410,67,85),
  (50,50,180,-40,0,0,435,435,435,435,435,435,435,435,435,435,435,64,98),
  (51,51,179,-40,0,0,435,435,435,434,435,435,434,435,434,435,435,66,96),
  (52,52,181,-40,0,0,435,435,435,435,435,435,435,435,435,435,435,68,91),
  (53,53,180,-40,0,0,436,436,436,435,435,435,435,435,435,435,436,66,92),
  (54,54,181,-40,0,0,435,435,435,435,435,435,434,435,435,435,436,66,96),
  (55,55,180,-40,0,0,435,433,434,435,434,435,435,435,434,435,435,65,96),
  (56,56,180,-40,0,0,435,435,435,435,436,436,436,436,436,436,436,66,98),
  (57,57,180,-40,0,0,435,435,435,435,436,435,436,436,436,436,435,66,96),
  (58,58,180,-41,0,0,435,435,435,435,435,436,435,435,435,435,436,68,97),
  (59,59,180,-40,0,0,435,435,435,435,435,435,435,435,435,435,435,65,93),
  (60,60,180,-40,0,0,435,435,435,435,435,435,435,435,435,435,435,66,96),
  (61,61,180,-40,0,0,435,435,435,435,435,435,435,435,435,435,435,65,95),
  (62,62,180,-40,0,0,430,430,435,435,430,430,429,435,435,434,433,64,695),
  (63,63,180,-40,0,0,431,431,436,436,431,430,431,436,436,436,436,65,93),
  (64,64,180,-40,0,0,435,435,435,435,429,430,430,435,435,435,434,63,96),
  (65,65,179,-40,0,0,431,430,435,435,430,430,429,435,435,434,436,67,97),
  (66,66,180,-40,0,0,431,430,435,435,429,431,429,434,435,435,436,67,96),
  (67,67,180,-40,0,0,430,430,435,435,430,430,430,435,434,435,436,67,96),
  (68,68,180,-40,0,0,430,430,435,435,431,430,431,436,435,435,435,67,96),
  (69,69,181,-40,0,0,430,430,435,435,430,430,430,434,434,434,435,64,97),
  (70,70,179,-40,0,0,430,430,435,435,431,431,430,436,436,436,435,64,95),
  (71,71,180,-40,0,0,431,430,435,435,431,429,431,436,436,436,436,65,94),
  (72,72,179,-40,0,0,427,431,431,432,433,433,432,435,435,435,435,66,93),
  (73,73,180,-40,0,0,430,430,436,435,431,430,431,435,436,435,436,68,93),
  (74,74,179,-40,0,0,430,430,435,435,430,431,430,435,435,435,435,65,94),
  (75,75,180,-40,0,0,425,432,430,430,434,435,434,434,433,434,435,65,97),
  (76,76,180,-40,0,0,430,430,430,430,435,434,435,435,435,435,435,65,95),
  (77,77,180,-40,0,0,430,430,430,430,435,435,435,435,435,435,435,65,94),
  (78,78,179,-40,0,0,430,430,430,430,435,436,436,434,434,434,434,63,94),
  (79,79,180,-40,0,0,430,430,429,430,434,434,435,435,435,435,435,63,94),
  (80,80,180,-60,180,-60,515,0,0,0,0,0,0,0,0,0,0,73,105),
  (81,81,180,-60,180,-60,516,0,0,0,0,0,0,0,0,0,0,75,105),
  (82,82,180,-60,180,-60,513,0,0,0,0,0,0,0,0,0,0,67,105),
  (83,83,180,-60,180,-60,514,0,0,0,0,0,0,0,0,0,0,66,105),
  (84,84,175,-40,180,-40,516,0,0,0,0,0,0,0,0,0,0,62,104),
  (85,85,180,-60,180,-60,516,0,0,0,0,0,0,0,0,0,0,65,105),
  (86,86,180,-60,180,-50,516,0,0,0,0,0,0,0,0,0,0,65,105),
  (87,87,180,-60,180,-60,516,0,0,0,0,0,0,0,0,0,0,67,104),
  (88,88,180,-60,180,-60,519,0,0,0,0,0,0,0,0,0,0,62,105),
  (89,89,180,-40,0,0,409,410,410,409,410,410,410,410,410,409,410,66,85),
  (90,90,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
  (91,91,181,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,65,81),
  (92,92,180,-40,0,0,410,410,410,410,409,411,409,409,410,409,410,68,85),
  (93,93,180,-40,0,0,410,410,410,410,410,411,410,410,410,410,411,67,83),
  (94,94,180,-60,180,-60,519,0,0,0,0,0,0,0,0,0,0,62,105),
  (95,95,180,-60,180,-60,514,0,0,0,0,0,0,0,0,0,0,61,104),
  (96,96,180,-40,0,0,409,409,410,408,410,410,410,409,410,411,406,67,88),
  (97,97,180,-60,180,-60,516,0,0,0,0,0,0,0,0,0,0,67,105),
  (98,98,180,-60,180,-60,515,0,0,0,0,0,0,0,0,0,0,65,105),
  (99,99,180,-40,0,0,410,410,409,410,410,411,410,410,409,411,411,65,83),
  (100,100,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,66,85),
  (101,101,180,-60,180,-60,513,0,0,0,0,0,0,0,0,0,0,66,104),
  (102,102,180,-40,0,0,410,410,410,410,410,410,410,410,410,410,410,66,86),
  (103,103,180,-60,180,-60,516,0,0,0,0,0,0,0,0,0,0,65,105),
  (104,104,180,-40,0,0,410,410,410,410,411,411,411,411,411,411,410,67,83),
  (105,105,180,-40,0,0,410,409,409,410,410,410,410,410,409,409,410,67,85),
  (106,106,179,-40,0,0,410,410,410,411,410,409,409,410,411,410,410,65,84),
  (107,107,180,-40,0,0,430,430,430,430,436,435,435,435,435,434,436,65,92),
  (108,108,180,-40,0,0,430,430,430,430,436,435,435,435,436,436,436,65,97),
  (109,109,180,-40,0,0,430,430,430,430,435,435,436,436,436,435,436,64,95),
  (110,110,180,-40,0,0,430,430,430,430,434,435,435,434,435,434,435,65,98);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;