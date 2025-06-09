<?php

use function PHPSTORM_META\map;

require_once 'ErrorHandler.php';
ErrorHandler::register();

require_once 'database.php';


/**
 * 
 */
class inventoryDB_SQL extends database
{

    // Constructor
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->con = $db;
    }

    public function getInventory()
    {
        try {
            $sqlProduct = 'SELECT `products`.`PartName`, `productinventory`.`productID`, `productinventory`.`partQty`, `products`.`MinimumQty`, `products`.`displayOrder`
                        FROM `products`
                        INNER JOIN `productinventory` ON (`products`.`ProductID` = `productinventory`.`productID`)
                        ORDER BY `products`.`displayOrder`';
            $stmtProduct = $this->con->prepare($sqlProduct);
            $stmtProduct->execute();

            $inventoryProducts = $stmtProduct->fetch(PDO::FETCH_ASSOC);
            if (!$inventoryProducts) {
            } else {
            }

            $sqlMaterial = 'SELECT 
                                `materialinventory`.`MaterialPartNumber`,
                                `materialinventory`.`Lbs`,
                                `material`.`MaterialPartNumber`,
                                `material`.`MaterialName`,
                                `material`.`ProductID`,
                                `material`.`MinimumLbs`,
                                `material`.`Customer`
                            FROM
                                `materialinventory`
                            INNER JOIN `material` ON (`materialinventory`.`MaterialPartNumber` = `material`.`MaterialPartNumber`)';
            $stmtMaterial = $this->con->prepare($sqlMaterial);
            $stmtMaterial->execute();
            $inventoryMaterials = $stmtMaterial->fetch(PDO::FETCH_ASSOC);

            if (!$inventoryMaterials) {
            } else {
            }
        } catch (PDOException $e) {
            error_log("Error getting products: " . $e->getMessage());
            //Convert this to an uncaught exception to let ErrorHandler process it
            throw new ErrorException(
                $e->getMessage(),
                0,
                E_ERROR,
                $e->getFile(),
                $e->getLine()
            );
        }
    }
}
