<?php
// File: src/Classes/Inventory/Models/InventoryInsert.php

// This file contains the InventoryInsert class which interacts with the database for inventory-related operations.
declare(strict_types=1);

namespace Inventory\Models;

class InventoryInsert
{
    private $pdo;
    private $log;
    private InventoryGet $get;
    private ?InventoryUpdate $update = null;

    public function __construct($pdo, $log, InventoryGet $get, ?InventoryUpdate $update = null)
    {
        $this->pdo = $pdo;
        $this->log = $log;
        $this->get = $get;

        // UpdateProduction is injected later to avoid circular constructor dependency 
        if ($update !== null) {
            $this->update = $update;
        }
    }

    public function setInventoryUpdate(InventoryUpdate $update)
    {
        $this->update = $update;
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

    public function addShipment($data)
    {
        $this->log->info("POST Data Received by model:\n" . print_r($data, true));

        if (!$data) {
            return ["success" => false, "message" => "addShipment failed to receive form data!"];
            exit();
        } else {
            $this->log->info("addShipment received an {$data['action']} request");
        }
        $affectedRows = 0;
        $this->pdo->beginTransaction();


        try {
            $sql = 'INSERT INTO weeklyshipment
                        (shipWeek,
                        productID,
                        shipQty)
                    VALUES (
                        :shipWeek,
                        :productID,
                        :shipQty)';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':shipWeek', $data['shipWeek'], \PDO::PARAM_STR);
            $stmt->bindParam(':productID', $data['productID'], \PDO::PARAM_STR);
            $stmt->bindParam(':shipQty', $data['shipQty'], \PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                throw new \Exception("Failed to add shipment for {$data['productID']} to inventory.");
            }

            $affectedRows = $stmt->rowCount();

            $this->updateProductQty($data['productID'], $data['shipQty'], '-');

            $this->pdo->commit();
            if ($affectedRows > 0) {
                return ["success" => true, "message" => "Shipment for {$data['productID']} added successfully."];
            } else {
                $this->pdo->rollBack();
                return ["success" => false, "message" => "No rows affected when adding shipment for {$data['productID']}."];
            }
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            $this->log->error("ERROR adding shipment: " . $e->getMessage());
            return ["success" => false, "message" => "No rows affected when adding shipment for {$data['productID']}."];
        }
    }

    public function addInventoryItem($data)
    {
        $this->log->info("POST Data Received by model:\n" . print_r($data, true));

        if (!$data) {
            return ["success" => false, "message" => "addInventoryItem failed to receive form data!"];
            exit();
        } else {
            $this->log->info("addInventoryItem received an {$data['action']} request");
        }
        $affectedRows = 0;
        $affectedInv = 0;

        $this->pdo->beginTransaction();

        switch ($data['action']) {
            case 'addProduct':
                $this->log->info("addInventoryItem was called with {$data['action']}.");
                $this->log->info("Product data: " . print_r($data, true));

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
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':productID', $data['productID'], \PDO::PARAM_STR);
                $stmt->bindParam(':partName', $data['partName'], \PDO::PARAM_STR);
                $stmt->bindParam(':minQty', $data['minQty'], \PDO::PARAM_INT);
                $stmt->bindParam(':boxesPerSkid', $data['boxesPerSkid'], \PDO::PARAM_INT);
                $stmt->bindParam(':partsPerBox', $data['partsPerBox'], \PDO::PARAM_INT);
                $stmt->bindParam(':partWeight', $data['partWeight'], \PDO::PARAM_STR);
                $stmt->bindParam(':displayOrder', $data['displayOrder'], \PDO::PARAM_INT);
                $stmt->bindParam(':customer', $data['customer'], \PDO::PARAM_STR);
                $stmt->bindParam(':productionType', $data['productionType'], \PDO::PARAM_STR);

                if (!$stmt->execute()) {
                    $this->pdo->rollBack();
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    throw new \Exception("Failed to add {$data['productID']} to inventory.");
                }

                $affectedRows = $stmt->rowCount();
                if ($affectedRows > 0) {
                    $affectedInv = $this->addInventoryRecord($data);
                }

                break;
            case 'addMaterial':
                $this->log->info("addInventoryItem was called with {$data['action']}.");
                $this->log->info("Material data: " . print_r($data, true));

                $sql = 'INSERT INTO material (
                            matPartNumber,
                            matName,
                            productID,
                            minLbs,
                            matCustomer,
                            matSupplier,
                            matPriceLbs,
                            comments,
                            displayOrder) 
                        VALUES (
                            :matPartNumber,
                            :matName,
                            :productID,
                            :minLbs,
                            :matCustomer,
                            :matSupplier,
                            :matPriceLbs,
                            :comments,
                            :displayOrder)';
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':matPartNumber', $data['matPartNumber'], \PDO::PARAM_STR);
                $stmt->bindValue(':matName', $data['matName'], \PDO::PARAM_STR);
                $stmt->bindValue(':productID', $data['productID'], \PDO::PARAM_STR);
                $stmt->bindValue(':minLbs', $data['minLbs'], \PDO::PARAM_STR);
                $stmt->bindValue(':matCustomer', $data['matCustomer'], \PDO::PARAM_STR);
                $stmt->bindValue(':matSupplier', $data['matSupplier'], \PDO::PARAM_STR);
                $stmt->bindValue(':matPriceLbs', $data['matPriceLbs'], \PDO::PARAM_STR);
                $stmt->bindValue(':comments', $data['comments'], \PDO::PARAM_STR);
                $stmt->bindValue(':displayOrder', $data['displayOrder'], \PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $this->pdo->rollBack();
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    throw new \Exception("Failed to add {$data['matPartNumber']} to inventory.");
                }
                $affectedRows = $stmt->rowCount();
                if ($affectedRows > 0) {
                    $affectedInv = $this->addInventoryRecord($data);
                }

                break;
            case 'addPfm':
                $this->log->info("addInventoryItem was called with {$data['action']}.");
                $this->log->info("PFM data: " . print_r($data, true));
                $sql = 'INSERT INTO pfm (
                            partNumber,
                            partName,
                            productID,
                            minQty,
                            customer,
                            displayOrder) 
                        VALUES (
                            :partNumber,
                            :partName,
                            :productID,
                            :minQty,
                            :customer,
                            :displayOrder)';

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam($data['partNumber'], \PDO::PARAM_STR);
                $stmt->bindParam($data['partName'], \PDO::PARAM_STR);
                $stmt->bindParam($data['productID'], \PDO::PARAM_STR);
                $stmt->bindParam($data['minQty'], \PDO::PARAM_INT);
                $stmt->bindParam($data['customer'], \PDO::PARAM_STR);
                $stmt->bindParam($data['displayOrder'], \PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $this->pdo->rollBack();
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    throw new \Exception("Failed to add {$data['partNumber']} to inventory.");
                }
                $affectedRows = $stmt->rowCount();
                if ($affectedRows > 0) {
                    $affectedInv = $this->addInventoryRecord($data);
                }

                break;
            default:
                $this->log->info('');
                break;
        }

        if ($affectedRows > 0  && $affectedInv > 0) {
            $this->pdo->commit();
            $this->log->info("{$data['action']} successfully added {$data['productID']} to inventory.");
            return ['success' => true, "message" => "{$data['action']} successfully added {$data['productID']} to inventory."];
        } else {
            $this->pdo->rollBack();
            $this->log->warning("No rows affected for {$data['action']}.");
            return ['success' => false, "message" => "No rows affected for {$data['action']}."];
        }
    }

    private function addInventoryRecord($data)
    {
        $this->log->info("addInventoryRecord called with data: " . print_r($data, true));
        $affectedRows = 0;

        switch ($data['action']) {
            case 'addProduct':
                $sql = 'INSERT INTO productInventory (productID, partQty) VALUES (:productID, :partQty)';
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':productID', $data['productID'], \PDO::PARAM_STR);
                $stmt->bindValue(':partQty', 0, \PDO::PARAM_INT);

                if (!$stmt->execute()) {
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    throw new \Exception("Failed to add inventory record for {$data['productID']}.");
                }

                $affectedRows = $stmt->rowCount();
                break;

            case 'addMaterial':
                $sql = 'INSERT INTO materialInventory (matPartNumber, matLbs) VALUES (:matPartNumber, :matLbs)';
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':matPartNumber', $data['matPartNumber'], \PDO::PARAM_STR);
                $stmt->bindValue(':matLbs', 0, \PDO::PARAM_INT);
                if (!$stmt->execute()) {
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    throw new \Exception("Failed to add inventory record for {$data['matPartNumber']}.");
                }
                $affectedRows = $stmt->rowCount();
                break;

            case 'addPfm':
                $sql = 'INSERT INTO pfmInventory (PartNumber, Qty) VALUES (:partNumber, :qty)';
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':partNumber', $data['partNumber'], \PDO::PARAM_STR);
                $stmt->bindValue(':qty', 0, \PDO::PARAM_INT);
                if (!$stmt->execute()) {
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error('SQL Error: ' . implode(" | ", $errorInfo));
                    throw new \Exception("Failed to add inventory record for {$data['partNumber']}.");
                }
                $affectedRows = $stmt->rowCount();

                break;

            default:
                $this->log->warning("Invalid action type: {$data['action']}");
                return 0;
        }

        return $affectedRows;
    }
}
