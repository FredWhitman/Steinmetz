<?php
//FILE: src/Classes/Inventory/Models/InventoryGet.php

namespace Inventory\Models;

class InventoryGet
{
    private $pdo;
    private $log;

    public function __construct($pdo, $log)
    {
        $this->pdo = $pdo;
        $this->log = $log;
    }

    /**
     * getConnection  this returns the DB connection
     *
     * @return void
     */
    public function getConnection()
    {
        return $this->pdo;
    }

    /**
     * getLogger returns the logger
     *
     * @return void
     */
    public function getLogger()
    {
        return $this->log;
    }

    /**
     * getInventory returns the three tables of inventory data
     *
     * @return void
     */
    public function getInventory()
    {
        $this->log->info('Controller calling InventoryModel getInventory()');

        try {
            $sqlProduct = 'SELECT 
                                products.productID AS productID,
                                productinventory.partQty AS partQty,
                                products.minQty AS minQty,
                                products.displayOrder AS displayOrder
                            FROM
                                productinventory
                            INNER JOIN
                                products ON (productinventory.productID = products.productID)
                            ORDER BY displayOrder';

            $stmtProduct = $this->pdo->prepare($sqlProduct);

            $stmtProduct->execute();

            $products = $stmtProduct->fetchALL(\PDO::FETCH_ASSOC);
            $iCount = count($products);

            if (!$products) {
                $this->log->error('Nothing was returned $inventoryProducts.');
            } else {
                $this->log->info('$inventoryProducts row count :' . $iCount);
            }

            $sqlMaterial = 'SELECT 
                            `materialinventory`.`matPartNumber`,
                            `materialinventory`.`matLbs`,
                            `material`.`matPartNumber`,
                            `material`.`matName`,
                            `material`.`productID`,
                            `material`.`minLbs`,
                            `material`.`displayOrder`
                            FROM
                            `materialinventory`
                            INNER JOIN `material` ON (`materialinventory`.`matPartNumber` = `material`.`matPartNumber`)
                            ORDER BY
                            `material`.`displayOrder` ASC';
            $stmtMaterial = $this->pdo->prepare($sqlMaterial);
            $stmtMaterial->execute();
            $materials = $stmtMaterial->fetchALL(\PDO::FETCH_ASSOC);
            $mCount = count($materials);

            //throw exception if $material is empty
            if (!$materials) {
                $this->log->error('Nothing was returned $materials.');
                throw new ErrorException(
                    'Failed to get Material!',
                    0,
                    E_ERROR,
                    '',
                );
            } else {
                $this->log->info('$materials row count :' . $mCount);
            }

            $sqlPFM = 'SELECT 
                `pfm`.`pfmID`,
                `pfm`.`partNumber`,
                `pfm`.`partName`,
                `pfm`.`productID`,
                `pfm`.`minQty`,
                `pfm`.`customer`,
                `pfm`.`displayOrder`,
                `pfminventory`.`partNumber`,
                `pfminventory`.`Qty`
                FROM
                `pfminventory`
                INNER JOIN `pfm` ON (`pfminventory`.`partNumber` = `pfm`.`partNumber`)
                ORDER BY
                `pfm`.`displayOrder`';

            $stmtPFM = $this->pdo->prepare($sqlPFM);
            $stmtPFM->execute();
            $pfm = $stmtPFM->fetchAll(\PDO::FETCH_ASSOC);
            $pCount = count($pfm);

            if (!$pfm) {
                $this->log->error('Nothing was returned $inventoryPFMs.');
                throw new ErrorException(
                    'Failed to get PFM',
                    0,
                    E_ERROR,
                    '',
                );
            }
            $this->log->info("pfm results: \n", $pfm);
            $this->log->info('Returning table data to controller!');
            return ['products'  => $products, 'materials' => $materials, 'pfms' => $pfm];
        } catch (\PDOException $e) {
            $this->log->error("Error getting products: " . $e->getMessage());
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

    public function getProductInventory()
    {
        $sql =
            'SELECT 
                products.productID AS productID,
                productinventory.partQty AS partQty,
                products.minQty AS minQty,
                products.displayOrder AS displayOrder
            FROM
                productinventory
            INNER JOIN
                products ON (productinventory.productID = products.productID)
            ORDER BY displayOrder';

        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
        }
        $products = $stmt->fetchALL(\PDO::FETCH_ASSOC);
        return $products;
    }

    /**
     * get single record based on table and id
     *
     * @param [type] $id
     * @param [type] $table
     * @return void
     */
    public function getRecord($id, $table)
    {
        $this->log->info('getRecord called with these parameters: ' . $id . ' ' . $table);
        $sql = '';

        switch ($table) {
            case 'products':
                $this->log->info('product record requested');
                try {
                    $sql = 'SELECT * 
                        FROM products 
                        WHERE productID = :productID';

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([':productID' => $id]);
                    $result = $stmt->fetch();

                    if (!$result) {
                        $this->log->error("NO record found for the $id in table $table. ");
                        break;
                    }
                } catch (\PDOException $e) {
                    $this->log->error("Error getting product record for $id in table $table.");
                }
                break;
            case 'materials':
                $sql = 'SELECT * FROM material WHERE matPartNumber = :matPartNumber';

                try {
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([':matPartNumber' => $id]);
                    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if (!$result) {
                        $this->log->warning("NO record found for the $id in table $table. ");
                        break;
                    }
                    $this->log->info('getRecord returning : ' . $result['matPartNumber']);
                } catch (\PDOException $e) {
                    $this->log->error("Error getting material record for $id in table $table.");
                }
                break;
            case 'pfms':
                try {
                    $sql = 'SELECT * FROM pfm WHERE pfmID = :pfmID';
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([':pfmID' => $id]);
                    $result = $stmt->fetch();
                    if (!$result) {
                        $this->log->warning("NO record found for the $id in table $table. ");
                        break;
                    }
                    $this->log->info('getRecord returning : ' . $result['pfmID']);
                } catch (\PDOException $e) {
                    $this->log->error("ERROR getting {$table} record: " . $e->getMessage());
                    return ["success" => false, "message" => "An error occurred", "error" => $e->getMessage()];
                }
                break;
            default:
                $this->log->warning("Invalid table type requested: {$table}");
                return ["success" => false, "message" => "Invalid type requested: {$table}"];
                break;
        }
        return $result;
    }

    /**
     * getInventoryRecord function will return a joined inventory and details record
     * by join product & productInventory and the same for material and pfms
     *
     * @param [type] $id
     * @param [type] $table
     * @return void
     */
    public function getInventoryRecord($id, $table)
    {
        $this->log->info('getInventoryRecord called with these parameters: ' . $id . ' ' . $table);

        try {
            switch ($table) {
                case 'products':
                    $this->log->info('product record requested');
                    $sql = 'SELECT 
                        `products`.`productID`,
                        `products`.`partName`,
                        `productinventory`.`partQty`
                    FROM
                        `productinventory`
                    INNER JOIN `products` 
                    ON (`productinventory`.`productID` = `products`.`productID`)
                    WHERE
                    `productinventory`.`productID` = :productID';

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([':productID' => $id]);

                    break;

                case "materials":
                    $this->log->info("{$table} record for {$id} requested");

                    $sql = 'SELECT 
                                `materialinventory`.`matPartNumber`,
                                `materialinventory`.`matLbs`,
                                `material`.`matPartNumber`,
                                `material`.`matName`
                            FROM
                                `materialinventory`
                            INNER JOIN `material` 
                            ON 
                                (`materialinventory`.`matPartNumber` = `material`.`matPartNumber`)
                            WHERE
                                `materialinventory`.`matPartNumber` = :matPartNumber';

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':matPartNumber', $id, \PDO::PARAM_STR);
                    $stmt->execute();
                    break;

                case 'pfms':
                    $this->log->info("{$table} record for {$id} requested");
                    $sql = 'SELECT 
                                `pfm`.`pfmID`,
                                `pfm`.`partNumber`,
                                `pfminventory`.`PartNumber`,
                                `pfm`.`partName`,
                                `pfminventory`.`Qty`
                            FROM
                                `pfminventory`
                            INNER JOIN `pfm` ON (`pfminventory`.`PartNumber` = `pfm`.`partNumber`)
                            WHERE
                                `pfm`.`pfmID` = :pfmID';

                    $stmt = $this->pdo->prepare($sql);
                    // Bind the parameter for pfmID
                    $stmt->bindParam(':pfmID', $id, \PDO::PARAM_INT);
                    $stmt->execute();
                    break;

                default:
                    $this->log->warning("Invalid table type requested: {$table}");
                    return null;
            }

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$result) {
                $this->log->warning("NO record found for the {$id} in table {$table}. ");
                return null;
            }

            $this->log->info("result : \n" . print_r($result, true));

            return $result;
        } catch (\PDOException $e) {
            $this->log->error("DB Error fetching record for {$id} in table {$table}.");
            return null;
        }
    }

    /**
     * getProductList returns a list of productID and PartName
     * @return void
     */
    public function getProductList()
    {
        try {
            $sql = 'SELECT productID, partName, displayOrder from products Order By displayOrder ASC';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            $this->log->error("ERROR: Failed to get product list: " . $e->getMessage());
        }
    }

    public function getShipments()
    {
        try {
            $sql = 'SELECT 
                        *
                    FROM
                        `weeklyshipment`
                    WHERE shipWeek BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 WEEK) AND DATE_ADD(CURDATE(), INTERVAL 6 WEEK)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            } else {
                return [];
            }
        } catch (\PDOException $e) {
            $this->log->error("ERROR: Failed to get shipments list: " . $e->getMessage());
        }
    }

    public function getLogin($data)
    {
        $this->log->info("getLogin called with data: " . print_r($data, true));

        $sql = 'SELECT 
                    *
                FROM
                    users
                WHERE
                    username = :username AND loginPassword = :loginPassword';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $data['username'], \PDO::PARAM_STR);
        $stmt->bindParam(':loginPassword', $data['password'], \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
            throw new \Exception("Failed to get user record for {$data['user']}.");
        }

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result;
    }
}
