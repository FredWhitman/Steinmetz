<?php
// File: src/Classes/production/models/InsertProduction.php

// src/Classes/production/models/ProductionModel.php
// This file contains the ProductionModel class which interacts with the database for production-related operations.
declare(strict_types=1);

namespace Production\Models;

use Production\Models\GetProduction;

class InsertProduction
{
    private $pdo;
    private $log;
    private GetProduction $get;
    private ?UpdateProduction $update = null;

    public function __construct($pdo, $log, GetProduction $get, ?UpdateProduction $update = null)
    {
        $this->pdo = $pdo;
        $this->log = $log;
        $this->get = $get;

        // UpdateProduction is injected later to avoid circular constructor dependency 
        if ($update !== null) {
            $this->update = $update;
        }
    }

    public function setUpdateProduction(UpdateProduction $update)
    {
        $this->update = $update;
    }

    /**
     * insertProdLog
     * Main entry point for inserting a production log.
     * Handles:
     *  - starting a run
     *  - inserting the log
     *  - inserting material logs
     *  - inserting temp logs
     *  - updating inventory (via UpdateProduction)
     *  - linking matLog/tempLog to prodLog
     */
    public function insertProdLog($prodData, $materialData, $tempData)
    {
        try {

            $this->log->info("⏳ insertProdLog called — raw input snapshot", [
                'prodData' => $prodData,
                'materialData' => $materialData,
                'tempData' => $tempData
            ]);

            /*  setting variables that are need to complete the updates and inserts */
            $productID = $prodData['productID'];
            $prodDate = $prodData['prodDate'];
            $parts = $prodData['pressCounter'] - $prodData['startUpRejects'];
            $copperPins = $parts * 2;

            $oldProductStock = $this->get->getInvQty($productID);
            $this->log->info("Old inventory for {$productID} is {$oldProductStock}");

            if ($oldProductStock === null) {
                throw new \Exception("Failed to get old inventory for {$oldProductStock}");
            }

            /*  set runStatus to either start,in progress or end
                0 - get $prodRunID, using prodRunID get prevProdLogID and set prodData[] values
                1 - create Productionrun, set prodRunID, set $prevProdLogID to 0
                2 - get $prodRunID, get $prevProdLogID and set prodData[] values
            */
            switch ($prodData['runStatus']) {
                case '0':
                    $this->log->info('Continuing production run for product: ' . $productID);
                    $prodData['runStatus'] = 'in progress';
                    //use productID to get production run id
                    $prodRunID = $this->get->getProdRunID($productID);
                    if (!$prodRunID) throw new \RuntimeException("No active production run for product {$productID}");

                    //use prodRunID to get last prodLogID and set $prevProdLogID
                    $prevProdLogID = $this->get->getPrevProdLog($prodRunID);
                    if (!$prevProdLogID) throw new \RuntimeException("No previous production log found for run {$prodRunID}");

                    $this->log->info('Previous Log ID: ' . $prevProdLogID);
                    $prodData['runLogID'] = $prodRunID;
                    $prodData['prevProdLogID'] = $prevProdLogID;
                    break;

                case '1':
                    $this->log->info('Creating new production run for product: ' . $productID);
                    $prodData['runStatus'] = 'start';

                    $prodRunID = $this->insertProductionRun($productID, $prodData['prodDate']);
                    $this->log->info('prodRunID: ' . $prodRunID);
                    if (!$prodRunID) throw new \RuntimeException("Failed to create new production run for product {$productID}!");

                    $prodData['runLogID'] = $prodRunID;
                    $prevProdLogID = "0";
                    break;

                case '2':
                    $this->log->info('Ending production run for product: ' . $productID);

                    $prodData['runStatus'] = 'end';
                    $prodRunID = $this->get->getProdRunID($productID);
                    $this->log->info('prodRunID: ' . $prodRunID);

                    if (!$prodRunID) throw new \RuntimeException("No active production run for product {$productID}");

                    $prevProdLogID = $this->get->getPrevProdLog($prodRunID);
                    if (!$prevProdLogID) throw new  \RuntimeException("No previous production log found for run {$prodRunID}");
                    $this->log->info('Previous Log ID: ' . $prevProdLogID);

                    $prodData['runLogID'] = $prodRunID;
                    $prodData['prevProdLogID'] = $prevProdLogID;
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid runStatus: {$prodData['runStatus']}");
            }

            $this->pdo->beginTransaction();

            /* Insert materialLog & tempLog returnint their logIDs and add logID to each of their respective arrays */
            $matLogID = $this->insertMatLog($materialData);
            $materialData['logID'] = $matLogID;
            $tempLogID = $this->insertTempLog($tempData);
            $tempData['logID'] = $tempLogID;

            /////////////////////INSERT PRODUCTIONLOG INSERT START////////////////////////////
            $sqlInsertProdLog = "INSERT INTO productionlogs (
                                    productID, prodDate, runStatus, prevProdLogID, runLogID, matLogID, tempLogID, 
                                    pressCounter, startUpRejects, qaRejects, purgeLbs, maxMeltPressure, Comments) 
                                VALUES(
                                    :productID, :prodDate, :runStatus, :prevProdLogID, :runLogID, :matLogID, :tempLogID,
                                    :pressCounter, :startUpRejects, :qaRejects, :purgeLbs, :maxMeltPressure, :comments)";

            $stmtInsertProdLog = $this->pdo->prepare($sqlInsertProdLog);

            $InsertParams = [
                ':productID' => [$prodData['productID'],  \PDO::PARAM_STR],
                ':prodDate' => [$prodData['prodDate'],  \PDO::PARAM_STR],
                ':runStatus' => [$prodData['runStatus'],  \PDO::PARAM_STR],
                ':prevProdLogID' => [$prodData['prevProdLogID'],  \PDO::PARAM_INT],
                ':runLogID' => [$prodData['runLogID'], \PDO::PARAM_INT],
                ':matLogID' => [$matLogID,  \PDO::PARAM_INT],
                ':tempLogID' => [$tempLogID,  \PDO::PARAM_INT],
                ':pressCounter' => [$prodData['pressCounter'],  \PDO::PARAM_INT],
                ':startUpRejects' => [$prodData['startUpRejects'],  \PDO::PARAM_INT],
                ':qaRejects' => [$prodData['qaRejects'],  \PDO::PARAM_INT],
                ':purgeLbs' => [$prodData['purgeLbs'],  \PDO::PARAM_STR],
                ':maxMeltPressure' => [$prodData['maxMeltPressure'],  \PDO::PARAM_INT],
                ':comments' => [$prodData['comments'],  \PDO::PARAM_STR],
            ];

            foreach ($InsertParams as $key => [$value, $type]) {
                $stmtInsertProdLog->bindValue($key, $value, $type);
            }

            if (!$stmtInsertProdLog->execute()) {
                $this->log->error('Production log insert failed: ' . print_r($stmtInsertProdLog->errorInfo(), true));
                throw new \Exception("Production log insert failed.");
            }
            $prodLogID = $this->pdo->lastInsertId();
            $this->log->info('prodLogID: ' . $prodLogID);
            if (!$prodLogID) throw new \Exception("Failed to get last production log for production run!");

            /* add prodLogID to materialLog & tempLog */
            $materialData["prodLogID"] = $prodLogID;
            $this->update->updateMatLogProdLogID($materialData);
            $tempData["prodLogID"] = $prodLogID;
            $this->update->updateTempLogProdLogID($tempData);


            $this->update->updateMaterialInventory($materialData, '-');

            $transProductData = array(
                "action" => 'insertProdLog',
                "inventoryID" => $productID,
                "inventoryType" => 'product',
                "prodLogID" => $prodLogID,
                "oldStockCount" => $oldProductStock,
                "transAmount" => $parts,
                "transType" => "production log",
                "transComment" => $prodData['comments'],
            );

            $this->update->updateProductInventory($productID, $parts, '+');
            $this->insertTrans($transProductData);
            $this->update->updateProductionRun($prodRunID);


            // check to see if this is the end of the production run and fetch material data for the run
            // and update prodrunlog with totals, end date and completed

            if ($prodData['runStatus'] === 'end') {
                $this->log->info('End of prodcution run detected and end production run.');
                $this->update->markProductionRunCompleted($prodRunID);
            }

            $this->update->updatePFMInventory('349-61A0', $copperPins, '-');
            $this->pdo->commit();
            $message = "Transaction completed successfully added {$parts} of {$productID} into product 
                        inventory and removed {$copperPins} copper pins from pfm inventory.";
            return ["success" => true, "message" => $message];
        } catch (\Throwable $e) {
            $message = "Failed to add prodcution log and a rollback was triggered by this error: {$e}";

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
                $this->log->error('PDO Rollback.  Error failed to insert Production log: ' . $e->getMessage());
            }

            return [
                'success' => false,
                "message" => $message . " Error: " . $e->getMessage()
            ];
        }
    }

    /* 
        INSERT FUNCTIONS
    */

    /**
     * insertMatLog
     *
     * @param  mixed $materialData
     * @return void
     */
    public function insertMatLog($materialData)
    {
        $sql = "INSERT INTO materialLog (
                                        prodLogID,
                                        mat1,
                                        matUsed1,
                                        matDailyUsed1,
                                        mat2,
                                        matUsed2,
                                        matDailyUsed2,
                                        mat3,
                                        matUsed3,
                                        matDailyUsed3,
                                        mat4,
                                        matUsed4,
                                        matDailyUsed4) 
                                    VALUES (
                                        :prodLogID,
                                        :mat1,
                                        :matUsed1,
                                        :matDailyUsed1,
                                        :mat2,
                                        :matUsed2,
                                        :matDailyUsed2,
                                        :mat3,
                                        :matUsed3,
                                        :matDailyUsed3,
                                        :mat4,
                                        :matUsed4,
                                        :matDailyUsed4)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($materialData);

        $matLogID = $this->pdo->lastInsertId();
        $this->log->info('matLogID: ' . $matLogID);
        if (!$matLogID) throw new \Exception("Failed to insert into materialLog or set matLogID.");
        return $matLogID;
    }

    /**
     * insertTempLog
     *
     * @param  mixed $tempData
     * @return void
     */
    public function insertTempLog($tempData)
    {

        $sql = "INSERT INTO tempLog (
                    prodLogID,
                    bigDryerTemp,
                    bigDryerDew,
                    pressDryerTemp,
                    pressDryerDew, 
                    t1,
                    t2,
                    t3,
                    t4,
                    m1,
                    m2,
                    m3,
                    m4,
                    m5,
                    m6,
                    m7,
                    chillerTemp,
                    moldTemp,
                    z1,
                    z9)
                VALUES(
                    :prodLogID,
                    :bigDryerTemp,
                    :bigDryerDew,
                    :pressDryerTemp,
                    :pressDryerDew,
                    :t1,
                    :t2,
                    :t3,
                    :t4,
                    :m1,
                    :m2,
                    :m3,
                    :m4,
                    :m5,
                    :m6,
                    :m7,
                    :chillerTemp,
                    :moldTemp,
                    :z1,
                    :z9)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($tempData);
        $tempLogID = $this->pdo->lastInsertId();
        $this->log->info('tempLogID: ' . $tempLogID);
        if (!$tempLogID) throw new \Exception('Failed to insert tempLog and return tempLogID.');
        return $tempLogID;
    }

    public function addPurge($productID, $prodDate, $purgeLbs)
    {
        $this->log->info("addPurge called with productID: {$productID}, prodDate: {$prodDate}, purgeLbs: {$purgeLbs}.");

        $prodLogID = $this->get->getProdLogID($productID, $prodDate);

        if (!$prodLogID) {
            throw new \Exception("No production log found for product {$productID} on date {$prodDate}.");
        }

        // update detail row (handle NULL existing values)
        $sql = "UPDATE productionlogs 
            SET purgeLbs = COALESCE(purgeLbs, 0) + :purgeLbs 
            WHERE logID = :prodLogID";

        $stmt = $this->pdo->prepare($sql);

        $purgeVal = ($purgeLbs === '' || $purgeLbs === null) ? null : (float)$purgeLbs;

        $stmt->bindValue(':prodLogID', $prodLogID, \PDO::PARAM_INT);
        $stmt->bindValue(':purgeLbs', $purgeVal, $purgeVal === null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception("Failed to insert purge log for product {$productID}.");
        }

        // fetch the run id (runLogID / prodRunID) for this production log
        $sqlRun = "SELECT runLogID 
               FROM productionlogs 
               WHERE logID = :prodLogID 
               LIMIT 1";

        $stmtRun = $this->pdo->prepare($sqlRun);
        $stmtRun->bindValue(':prodLogID', $prodLogID, \PDO::PARAM_INT);
        $stmtRun->execute();

        $row = $stmtRun->fetch(\PDO::FETCH_ASSOC);
        $prodRunID = $row['runLogID'] ?? null;

        if ($prodRunID) {
            $this->log->info("Calling recalcRunPurgeTotal for prodRunID: {$prodRunID}");
            $ok = $this->update->recalcRunPurgeTotal($prodRunID);

            if ($ok === false) {
                $this->log->error("recalcRunPurgeTotal failed for prodRunID {$prodRunID}");
                throw new \Exception("Failed to update run purge total for run {$prodRunID}");
            }
        } else {
            $this->log->warning("Could not determine prodRunID for prodLogID {$prodLogID}");
        }

        return ['success' => true, 'message' => 'Purge added production log successfully.'];
    }


    /**
     * insertProductionRun create a production run
     *
     * @param  mixed $productID
     * @param  mixed $prodDate
     * @return void
     */
    public function insertProductionRun($productID, $prodDate)
    {
        $this->log->info("insertProductionRun called with productID: {$productID} and prodDate: {$prodDate}");

        $sql = "INSERT into prodrunlog (
                                productID,
                                startDate,
                                runComplete) 
                            Values (
                                :productID, 
                                :prodDate, 
                                :runComplete)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':productID' => $productID,
            ':prodDate' => $prodDate,
            ':runComplete' => 'no'
        ]);

        $prodRunID = $this->pdo->lastInsertId();
        if (!$prodRunID) throw new \Exception("failed to insert production run!");
        return $prodRunID;
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
        $this->log->info('Trans data passed to insertTrans function: ' . print_r($data, true));

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

        $stmt->bindParam(':inventoryID', $data['inventoryID'], \PDO::PARAM_STR);
        $stmt->bindParam(':inventoryType', $data['inventoryType'], \PDO::PARAM_STR);
        $stmt->bindParam(':prodLogID', $data['prodLogID'], \PDO::PARAM_INT);
        $stmt->bindValue(':oldStockCount', $data['oldStockCount']);
        $stmt->bindParam(':transAmount', $data['transAmount'], \PDO::PARAM_STR);
        $stmt->bindParam(':transType', $data['transType'], \PDO::PARAM_STR);
        $stmt->bindParam(':transComment', $data['transComment'], \PDO::PARAM_STR);

        $result = $stmt->execute();
        if (!$result) throw new \Exception("Failed to insert transaction for {$data['inventoryID']}.");
    }
}
