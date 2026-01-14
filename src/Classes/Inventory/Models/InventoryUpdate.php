<?php
// File: src/Classes/Inventory/Models/UpdateInventory.php

namespace Inventory\Models;

class UpdateInventory
{
    private $pdo;
    private $log;
    private InventoryGet $get;
    private InventoryInsert $insert;

    public function __construct($pdo, $log, InventoryGet $get, InventoryInsert $insert)
    {
        $this->pdo = $pdo;
        $this->log = $log;
        $this->get = $get;
        $this->insert = $insert;
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
                        $this->log->info("{$data['action']} successfully to added. ");
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

    public function updateProductQty($productID, $amount, $operator)
    {
        $op = $op = ($operator === "+") ? '+' : '-';

        $sql = "UPDATE productinventory SET partQty = partQty {$op} :partQty WHERE productID = :productID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partQty', $amount, \PDO::PARAM_INT);
        $stmt->bindParam(':productID', $productID, \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            $this->log->error("Failed to update Product: {$productID}'s qty. \n", [
                'errorInfo' => $error,
                'productID' => $productID,
                'Qty' => $amount,
                'operator' => $operator
            ]);
            throw new \Exception("Failed to update Product: {$productID}'s qty. ERROR: {$error}");
        }
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
}
