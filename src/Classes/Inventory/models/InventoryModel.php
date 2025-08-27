<?php
// File: src/Classes/inventory/models/InventoryModel.php
declare(strict_types=1);

namespace Inventory\Models;

//require_once  __DIR__ . '/../../database.php';
//require_once __DIR__ . '/../utils/Util.php';

use Psr\Log\LoggerInterface;
use PDOException;
use ErrorException;
use Util\Utilities;
use Database\Connection;

class InventoryModel
{
    private \PDO $pdo;
    private LoggerInterface $log;
    private $util;

    /**
     * constructor for database and logger
     *
     * @param \PDO $dbConnection
     * @param LoggerInterface $log
     */
    public function __construct(Connection $dbConnection, LoggerInterface $log, Utilities $util)
    {
        $this->pdo = $dbConnection->getPDO();
        $this->log = $log;
        $this->util = $util;
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
                } catch (PDOException $e) {
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
     * edit Inventory item details
     *
     * @param  mixed $data form data sent 
     * @return void
     */
    public function editInventory($data)
    {
        $this->log->info("editInventory : " . print_r($data, true));

        switch ($data['action']) {
            case 'editProduct':
                $this->log->info("edit product has begun!");
                $sql = "UPDATE products 
                        SET partName = :partName, 
                            minQty = :minQty, 
                            boxesPerSkid = :boxesPerSkid, 
                            partsPerBox = :partsPerBox, 
                            partWeight = :partWeight, 
                            displayOrder = :displayOrder, 
                            customer = :customer, 
                            productionType = :productionType 
                        WHERE productID = :productID";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':productID', $data['products']['productID'], \PDO::PARAM_STR);
                $stmt->bindParam(':partName', $data['products']['partName'], \PDO::PARAM_STR);
                $stmt->bindParam(':minQty', $data['products']['minQty'], \PDO::PARAM_INT);
                $stmt->bindParam(':boxesPerSkid', $data['products']['boxesPerSkid'], \PDO::PARAM_INT);
                $stmt->bindParam(':partsPerBox', $data['products']['partsPerBox'], \PDO::PARAM_INT);
                $stmt->bindParam(':partWeight', $data['products']['partWeight'], \PDO::PARAM_STR);
                $stmt->bindParam(':displayOrder', $data['products']['displayOrder'], \PDO::PARAM_INT);
                $stmt->bindParam(':customer', $data['products']['customer'], \PDO::PARAM_STR);
                $stmt->bindParam(':productionType', $data['products']['productionType'], \PDO::PARAM_STR);

                $this->log->info("Executing update query with values: " . json_encode($data));

                try {
                    $result = $stmt->execute();

                    //check to make sure rows were actually affected
                    $affectedRows = $stmt->rowCount();
                    $this->log->info("Rows affected: " . $affectedRows);

                    if (!$result) {
                        $errorInfo = $stmt->errorInfo();
                        $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                        return ["success" => false, "message" => "Product update failed.", "error" => $errorInfo];
                    }
                    $this->log->info('Product Updated!');
                    return ["success" => true, "message" => "Transaction completed successfully.", "product" => $data['products']['productID']];
                } catch (\PDOException $e) {
                    $this->log->error("ERROR product update failed: " . $e->getMessage());
                    return ["success" => false, "message" => "An error occurred", "error" => $e->getMessage()];
                }
                break;
            case 'editMaterial':
                $this->log->info('edit material has begun.');

                $sql = 'UPDATE material 
                        SET 
                            matName = :matName, 
                            productID = :productID, 
                            minLbs = :minLbs, 
                            matSupplier = :matSupplier,
                            matPriceLbs = :matPriceLbs,
                            matCustomer = :matCustomer, 
                            displayOrder = :displayOrder
                        WHERE matPartNumber = :matPartNumber';
                $stmt = $this->pdo->prepare($sql);

                $stmt->bindParam(':matPartNumber', $data['materials']['matPartNumber'], \PDO::PARAM_STR);
                $stmt->bindParam(':matName', $data['materials']['matName'], \PDO::PARAM_STR);
                $stmt->bindParam(':productID', $data['materials']['productID'], \PDO::PARAM_STR);
                $stmt->bindParam(':minLbs', $data['materials']['minLbs'], \PDO::PARAM_STR);
                $stmt->bindParam(':matSupplier', $data['materials']['matSupplier'], \PDO::PARAM_STR);
                $stmt->bindParam(':matPriceLbs', $data['materials']['matPriceLbs'], \PDO::PARAM_STR);
                $stmt->bindParam(':matCustomer', $data['materials']['matCustomer'], \PDO::PARAM_STR);
                $stmt->bindParam(':displayOrder', $data['materials']['displayOrder'], \PDO::PARAM_INT);

                $this->log->info("Executing update query with values: " . json_encode($data));
                try {
                    $result = $stmt->execute();
                    //check to make sure rows were actually affected
                    $affectedRows = $stmt->rowCount();
                    $this->log->info("Rows affected: " . $affectedRows);
                    if (!$result) {
                        $errorInfo = $stmt->errorInfo();
                        $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                        return ["success" => false, "message" => "Material update failed.", "error" => $errorInfo];
                    }
                    $this->log->info('Material Updated!');
                    return ["success" => true, "message" => "Transaction completed successfully.", "material" => $data['materials']['matName']];
                } catch (\PDOException $e) {
                    $this->log->error("ERROR updateInventory: " . $e->getMessage());
                    return ["success" => false, "message" => "An error occurred", "error" => $e->getMessage()];
                }
                break;

            case 'editPFM':
                $this->log->info('edit PFM has begun.');
                $sql = 'UPDATE pfm 
                        SET 
                            partNumber = :partNumber,
                            partName = :partName,
                            productID = :productID,
                            minQty = :minQty,
                            customer = :customer,
                            displayOrder = :displayOrder
                        WHERE pfmID = :pfmID';

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':pfmID', $data['pfm']['pfmID'], \PDO::PARAM_STR);
                $stmt->bindParam(':partNumber', $data['pfm']['partNumber'], \PDO::PARAM_STR);
                $stmt->bindParam(':partName', $data['pfm']['partName'], \PDO::PARAM_STR);
                $stmt->bindParam(':productID', $data['pfm']['productID'], \PDO::PARAM_STR);
                $stmt->bindParam(':minQty', $data['pfm']['minQty'], \PDO::PARAM_INT);
                $stmt->bindParam(':customer', $data['pfm']['customer'], \PDO::PARAM_STR);
                $stmt->bindParam(':displayOrder', $data['pfm']['displayOrder'], \PDO::PARAM_INT);

                $this->log->info('Executing update query with values ' . json_encode($data));

                try {
                    $result = $stmt->execute();
                    $affectedRows = $stmt->rowCount();
                    $this->log->info('PFM Rows affected: ' . $affectedRows);
                    if (!$result) {
                        $errorInfo = $stmt->errorInfo();
                        $this->log->error('SQL error: ' . implode(" | ", $errorInfo));
                        return ["success" => false, "message" => 'PFM update failed.', "error" => $errorInfo];
                    }
                    $this->log->info("PFM updated!");
                    return ['success' => true, "message" => 'Transaction completed successfully.', 'pfm' => $data['pfm']['partName']];
                } catch (\PDOException $e) {
                    $this->log->error("ERROR update pfm failed!" . $e->getMessage());
                    return ['success' => false, 'message' => "an error occured", "error" => $e->getMessage()];
                }
                break;
            default:
                $this->log->error("Invalid action type: " . $data['action']);
                return ["success" => false, "message" => "Invalid action type: " . $data['action']];
        }
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
     * updateInvQty this function will update a single inventory item
     *
     * @param [type] $data
     * @return void
     */
    public function updateInvQty($data)
    {
        $this->log->info("POST Data Received by model:\n" . print_r($data, true));

        if (!$data) {
            return ["success" => false, "message" => "updateInvQty failed to receive formData!"];
            exit();
        } else {
            $action = $data['action'];
            $this->log->info("updateInvQty received an {$action} request");
            $inventoryType = substr($data['action'], 6);
        }

        try {
            $this->pdo->beginTransaction();

            //creating transArray to fill with transData for insert after the update
            $transData = array(
                "action" => $data['action'],
                "inventoryID" => "",
                "inventoryType" => $inventoryType,
                "prodLogID" => "0",
                "oldStockCount" => "",
                "transAmount" => "",
                "transType" => "admin edit",
                "transComment" => $data['comments']
            );
            switch ($action) {
                case "updateProduct":
                    //updating transData
                    $transData['inventoryID'] = $data['productID'];
                    $transData['oldStockCount'] = $data['partQty'];
                    $transData['transAmount'] = $data['changeAmount'];


                    $stockQty = $data['partQty'];
                    $amount = $data['changeAmount'];
                    $newStockQty = $this->util->getNewInvQty($stockQty, $data['operator'], $amount);

                    $sql = 'UPDATE productInventory SET partQty = :qty WHERE productID = :productID';
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        ':productID' => $data['productID'],
                        ':qty' => $newStockQty
                    ]);

                    $affected = $stmt->rowCount();

                    if (!$affected === 0) {
                        $this->pdo->rollBack();
                        $errorInfo = $stmt->errorInfo();
                        $this->log->warning("no rows updated for {$data['action']}.");
                        return ["success" => false, "message" => "Database failed to update.", "error" => $errorInfo];
                    } else {

                        $insertResult = $this->insertTrans($transData);

                        if (!$insertResult) {
                            $this->pdo->rollBack();
                            $this->log->warning("Transaction insert into inventorytrans failed: {$data['action']}");
                            return ['success' => false, "message" => "Failed to insert transaction for {$data['action']}"];
                        }
                        $this->pdo->commit();
                        $this->log->info("{$data['action']} successfully attempted to add {$newStockQty} ");
                        return ['success' => true, "message" => 'Transaction completed successfully. ', 'product' => $data['productID']];
                    }
                    break;
                case "updateMaterial":
                    //updating transData
                    $transData['inventoryID'] = $data['matPartNumber'];
                    $transData['oldStockCount'] = $data['matLbs'];
                    $transData['transAmount'] = $data['changeAmount'];

                    $stockLbs = $data['matLbs'];
                    $amount = $data['changeAmount'];
                    $newStockQty = $this->util->getNewInvQty($stockLbs, $data['operator'], $amount);
                    $this->log->info("New material: {$data['matPartNumber']} inventory qty: {$newStockQty}");
                    $sql = 'UPDATE 
                                materialInventory 
                            SET 
                                matLbs = :matLbs
                            WHERE matPartNumber = :matPartNumber';

                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        ':matPartNumber' => $data['matPartNumber'],
                        ':matLbs' => $newStockQty
                    ]);

                    $affected = $stmt->rowCount();

                    if (!$affected === 0) {
                        $this->pdo->rollBack();
                        $errorInfo = $stmt->errorInfo();
                        $this->log->warning("no rows updated for {$data['action']}.");
                        return ["success" => false, "message" => "Database failed to update.", "error" => $errorInfo];
                    } else {

                        $insertResult = $this->insertTrans($transData);

                        if (!$insertResult) {
                            $this->pdo->rollBack();
                            $this->log->warning("Transaction insert into inventorytrans failed: {$data['action']}");
                            return ['success' => false, "message" => "Failed to insert transaction for {$data['action']}"];
                        }
                        $this->pdo->commit();
                        $this->log->info("{$data['action']} successfully attempted to add {$data['matPartNumber']} ");
                        return ['success' => true, "message" => 'Transaction completed successfully. ', 'Material' => $data['matPartNumber']];
                    }

                    break;
                case "updatePfm":
                    //updating transData
                    $transData['inventoryID'] = $data['partNumber'];
                    $transData['oldStockCount'] = $data['Qty'];
                    $transData['transAmount'] = $data['changeAmount'];

                    $stockQty = $data['Qty'];
                    $amount = $data['changeAmount'];
                    $newStockQty = $this->util->getNewInvQty($stockQty, $data['operator'], $amount);

                    $sql = 'UPDATE 
                                pfmInventory
                            SET
                                Qty = :qty
                            WHERE
                                PartNumber = :partNumber';
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        ':qty' => $newStockQty,
                        ':partNumber' => $data['partNumber']
                    ]);
                    $this->log->info("PFM updated!");

                    $affected = $stmt->rowCount();

                    if (!$affected === 0) {
                        $this->pdo->rollBack();
                        $errorInfo = $stmt->errorInfo();
                        $this->log->warning("no rows updated for {$data['action']}.");
                        return ["success" => false, "message" => "Database failed to update.", "error" => $errorInfo];
                    } else {

                        $insertResult = $this->insertTrans($transData);

                        if (!$insertResult) {
                            $this->pdo->rollBack();
                            $this->log->warning("Transaction insert into inventorytrans failed: {$data['action']}");
                            return ['success' => false, "message" => "Failed to insert transaction for {$data['action']}"];
                        }
                        $this->pdo->commit();
                        $this->log->info("{$data['action']} successfully attempted to add {$data['matPartNumber']} ");
                        return ['success' => true, "message" => 'Transaction completed successfully. ', 'pfm' => $data['partNumber']];
                    }
                    break;
                default:
                    $this->pdo->rollBack();
                    $this->log->warning("Invalid table type requested: {$data['action']}");
                    return ["success" => false, "message" => "Invalid type requested: {$data['action']}"];
            }
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $this->log->error("ERROR updating inventory: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred", "error" => $e->getMessage()];
        }
    }


    /**
     * insertTrans 
     *
     * @param  mixed $data  this array must contain an action element for routing to the correct case
     * @return void
     */
    public function insertTrans($data)
    {
        //transtype ENUM('production log','admin edit','qa rejects','shipped')
        //inventoryType ENUM('product','material'pfm)
        $this->log->info("insertTrans called for : {$data['action']}");
        $sql = 'INSERT INTO inventorytrans
                    (inventoryID,
                    inventoryType,
                    prodLogID,
                    oldStockCount,
                    transAmount,
                    transType,  
                    transComment)
                VALUES (
                    :inventoryID,
                    :inventoryType,
                    :prodLogID,
                    :oldStockCount,
                    :transAmount,
                    :transType,
                    :transComment)';

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':inventoryID' => $data['inventoryID'],
            ':inventoryType' => $data['inventoryType'],
            ':prodLogID' => $data['prodLogID'],
            ':oldStockCount' => $data['oldStockCount'],
            ':transAmount' => $data['transAmount'],
            ':transType' => $data['transType'],
            ':transComment' => $data['transComment']
        ]);

        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            return ["success" => false, "message" => "Database failed to insert a record into inventorytrans.", "error" => $errorInfo];
        } else {
            return ["success" => true, "message" => "{$data['action']} successful!"];
        }
    }

    public function addInventoryItem($data){

        $this->log->info("POST Data Received by model:\n" . print_r($data, true));

        if (!$data) {
            return ["success" => false, "message" => "addInvItem failed to receive formData!"];
            exit();
        } else {
            $action = $data['action'];
            $this->log->info("addInvItem received an {$action} request");
        }

        $this->pdo->beginTransaction();

        switch ($data['action']) {
            case 'addProduct':
                $this->log->info("addInventoryItem was called with {$data['action']}.");

                $sql = 'INSERT INTO products (
                            productID,
                            partName,
                            minQty,
                            boxesPerSkid,
                            partsPerBox,
                            partWeight,
                            displayOrder,
                            customer,
                            productionType) 
                        VALUES (
                            :productID,
                            :partName,
                            :minQty,
                            :boxesPerSkid,
                            :partsPerBox,
                            :partWeight,
                            :displayOrder,
                            :customer,
                            :productionType)';
                $stmt=$this->pdo->prepare($sql);
                $stmt->bindParam(':productID', $data['product']['productID'], \PDO::PARAM_STR);
                $stmt->bindParam(':partName', $data['product']['partName'], \PDO::PARAM_STR);
                $stmt->bindParam(':boxesPerSkid', $data['product']['boxesPerSkid'], \PDO::PARAM_INT);
                $stmt->bindParam(':partsPerBox', $data['product']['partsPerBox'], \PDO::PARAM_INT);
                $stmt->bindParam(':partWeight', $data['product']['partWeight'], \PDO::PARAM_STR);
                $stmt->bindParam(':displayOrder', $data['product']['displayOrder'], \PDO::PARAM_INT);                
                $stmt->bindParam(':customer', $data['product']['customer'], \PDO::PARAM_STR);
                $stmt->bindParam(':productionType', $data['product']['productionType'], \PDO::PARAM_STR);

                if(!$stmt->execute()){
                    
                }
                break;
            case 'addMaterial':
                $this->log->info("addInventoryItem was called with {$data['action']}.");
                $sql = '';
                $stmt=$this->pdo->prepare($sql);
                $stmt->bindParam();

                if(!$stmt->execute()){

                }
                break;
            case 'addPfm':
                $this->log->info("addInventoryItem was called with {$data['action']}.");
                $sql = '';
                $stmt=$this->pdo->prepare($sql);
                $stmt->bindParam();

                if(!$stmt->execute()){

                }
                break;
            default:
                $this->log->info('');
                break;
        }
        
    }
}
