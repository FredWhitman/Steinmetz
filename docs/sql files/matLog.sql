CREATE TABLE `materiallog` (
  `matLogID` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `prodLogID` INTEGER(11) NOT NULL,
  `mat1` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed1` DOUBLE(15,3) NOT NULL,
  `matDailyUsed1` DOUBLE(15,3) NOT NULL,
  `mat2` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed2` DOUBLE(15,3) NOT NULL,
  `matDailyUsed2` DOUBLE(15,3) NOT NULL,
  `mat3` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed3` DOUBLE(15,3) NOT NULL,
  `matDailyUsed3` DOUBLE(15,3) NOT NULL,
  `mat4` VARCHAR(50) COLLATE utf8mb4_general_ci NOT NULL,
  `matUsed4` DOUBLE(15,3) NOT NULL,
  `matDailyUsed4` DOUBLE(15,3) NOT NULL,
  PRIMARY KEY USING BTREE (`matLogID`)
) ENGINE=InnoDB
AUTO_INCREMENT=117 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci'
;