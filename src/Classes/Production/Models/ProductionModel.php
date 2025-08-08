<?php
// File: src/Classes/production/models/ProductionModel.php
// src/Classes/production/models/ProductionModel.php
// This file contains the ProductionModel class which interacts with the database for production-related operations.
declare(strict_types=1);

namespace Production\Models;

use Psr\Log\LoggerInterface;
use Exception;
use Util\Utilities;
use Database\Connection;


class ProductionModel
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
        error_log("✅ ProductionModel constructor reached");
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
     * Read4Wks - this function pulls the last four weeks of production logs for display in a table
     *
     * @return void
     */

    public function read4wks()
    {
        try {
            $sql = 'SELECT * FROM productionLogs WHERE prodDate >= NOW() - INTERVAL 4 WEEK Order By prodDate Desc';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $results;
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * readOne($id) function retrieves one record based on the production log id
     *
     * @param [type] $id
     * @return void
     */

    public function readOne($id)
    {    //returns the viewed log
        $sql = 'SELECT p.*, t.*, m.* FROM productionlogs AS p 
                INNER JOIN templog AS t ON p.logID = t.prodLogID 
                INNER JOIN materiallog AS m ON p.logID = m.prodLogID WHERE p.logID = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        //var_dump($result);
        return $result;
    }

    /**
     * readPrevious function  returns a materialLog along with some productionlog data based
     * on the id submitted to the function.
     *
     * @param [type] $id
     * @return void
     */
    public function readPrevious($id)
    {
        try {
            //this function returns the previous log of the viewed one
            $sql = 'SELECT p.logID, m.* FROM productionlogs AS p 
                    INNER JOIN materiallog AS m ON p.logID = m.prodLogID WHERE p.logID = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->log->info("result of ReadPrevious() function: " . print_r($result, true));

            return $result;
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * insertProdLog
     *
     * @param  mixed $prodData form data array
     * @param  mixed $materialData form data array
     * @param  mixed $tempData form data array
     * @return void
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

            $oldProductStock = $this->getInvQty($productID);
            if (!$oldProductStock) throw new \Exception("Failed to get old inventory for {$oldProductStock}");

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
                    $prodRunID = $this->getProdRunID($productID);
                    if (!$prodRunID) throw new \RuntimeException("No active production run for product {$productID}");

                    //use prodRunID to get last prodLogID and set $prevProdLogID
                    $prevProdLogID = $this->getPrevProdLog($prodRunID);
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
                    $prodRunID = $this->getProdRunID($productID);
                    $this->log->info('prodRunID: ' . $prodRunID);

                    if (!$prodRunID) throw new \RuntimeException("No active production run for product {$productID}");

                    $prevProdLogID = $this->getPrevProdLog($prodRunID);
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
                                    productID,
                                    prodDate,
                                    runStatus,
                                    prevProdLogID, 
                                    runLogID,
                                    matLogID,
                                    tempLogID,
                                    pressCounter,
                                    startUpRejects, 
                                    qaRejects,
                                    purgeLbs,
                                    Comments) 
                                VALUES(
                                    :productID,
                                    :prodDate,
                                    :runStatus,
                                    :prevProdLogID,
                                    :runLogID,
                                    :matLogID,
                                    :tempLogID,
                                    :pressCounter,
                                    :startUpRejects, 
                                    :qaRejects,
                                    :purgeLbs,
                                    :comments)";

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
            $this->updateMatLogProdLogID($materialData);
            $tempData["prodLogID"] = $prodLogID;
            $this->updateTempLogProdLogID($tempData);


            $this->updateMaterialInventory($materialData, '-');

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

            $this->updateProductInventory($productID, $parts, '+');
            $this->insertTrans($transProductData);

            // check to see if this is the end of the production run and fetch material data for the run
            // and update prodrunlog with totals, end date and completed

            if ($prodData['runStatus'] === 'end') {

                $this->log->info('End of prodcution run detected aquiring run production totals');

                $this->updateProductionRun($prodRunID, 'yes');
            }

            $this->updatePFMInventory('349-61A0', $copperPins, '-');

            $this->pdo->commit();
            $message = "Transaction completed successfully added {$parts} of {$productID} into product inventory and removed {$copperPins} copper pins from pfm inventory.";
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
    private function insertMatLog($materialData)
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
    private function insertTempLog($tempData)
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
                    moldTemp)
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
                    :moldTemp)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($tempData);
        $tempLogID = $this->pdo->lastInsertId();
        $this->log->info('tempLogID: ' . $tempLogID);
        if (!$tempLogID) throw new \Exception('Failed to insert tempLog and return tempLogID.');
        return $tempLogID;
    }

    /**
     * insertProductionRun create a production run
     *
     * @param  mixed $productID
     * @param  mixed $prodDate
     * @return void
     */
    private function insertProductionRun($productID, $prodDate)
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
        $stmt->bindParam(':oldStockCount', $data['oldStockCount'], \PDO::PARAM_STR);
        $stmt->bindParam(':transAmount', $data['transAmount'], \PDO::PARAM_STR);
        $stmt->bindParam(':transType', $data['transType'], \PDO::PARAM_STR);
        $stmt->bindParam(':transComment', $data['transComment'], \PDO::PARAM_STR);

        $result = $stmt->execute();
        if (!$result) throw new \Exception("Failed to insert transaction for {$data['inventoryID']}.");
    }

    /* UPDATE FUNCTIONS */

    /**
     * updatePFMInventory
     *
     * @param  mixed $partNumber
     * @param  mixed $amount
     * @param  mixed $operator
     * @return void
     */
    public function updatePFMInventory($partNumber, $amount, $operator)
    {
        $oldStock = $this->getPFMQty($partNumber);
        $sql = 'UPDATE pfmInventory SET qty = :newQty WHERE partNumber = :partNumber';
        ($operator === '+') ? $newQty = $amount + $oldStock : $newQty = $oldStock - $amount;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partNumber', $partNumber, \PDO::PARAM_STR);
        $stmt->bindParam(':newQty', $newQty, \PDO::PARAM_INT);

        if (!$stmt->execute()) throw new \Exception("Failed to update {$partNumber}.");
    }

    /**
     * updateMatLogProdLogID
     *
     * @param  mixed $materialData
     * @return void
     */
    private function updateMatLogProdLogID($materialData)
    {

        $sql = 'UPDATE materiallog SET prodLogID = :prodLogID WHERE matLogID = :logID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':prodLogID', $materialData['prodLogID'], \PDO::PARAM_INT);
        $stmt->bindParam(':logID', $materialData['logID'], \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $this->log->error('material log update failed: ' . print_r($stmt->errorInfo(), true));
            throw new \Exception("Material log update failed.");
        }
    }

    /**
     * updateTempLogProdLogID
     *
     * @param  mixed $tempData
     * @return void
     */
    private function updateTempLogProdLogID($tempData)
    {
        $sql = 'UPDATE tempLog SET prodLogID = :prodLogID WHERE tempLogID = :logID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':logID', $tempData['logID'], \PDO::PARAM_INT);
        $stmt->bindParam(':prodLogID', $tempData['prodLogID'], \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $this->log->error('temp log update failed: ' . print_r($stmt->errorInfo(), true));
            throw new \Exception("Production log insert failed.");
        }
    }

    /**
     * updateProductionRun
     *
     * @param  mixed $prodRunID
     * @param  mixed $runComplete
     * @return void
     */
    private function updateProductionRun($prodRunID, $runComplete)
    {
        /* SQL Query
        SELECT
            totals.totalPressCounter,
            totals.totalStartUpRejects,
            totals.totalQARejects,
            totals.totalPurgeLbs,
            lastRecord.matUsed1,
            lastRecord.matUsed2,
            lastRecord.matUsed3,
            lastRecord.matUsed4,
            lastRecord.prodDate AS lastProdDate
        FROM
        (
            SELECT
                SUM(pressCounter) AS totalPressCounter,
                SUM(startUpRejects) AS totalStartUpRejects,
                SUM(qaRejects) AS totalQARejects,
                SUM(purgeLbs) AS totalPurgeLbs
                FROM productionlogs
                WHERE productID = '10601'
                AND runLogID = '9'
        ) AS totals
        JOIN
        (
            SELECT
                pl.prodDate,
                ml.matUsed1,
                ml.matUsed2,
                ml.matUsed3,
                ml.matUsed4
                FROM productionlogs pl
                JOIN materiallog ml ON pl.matLogID = ml.matLogID
                WHERE pl.productID = '10601'
                AND pl.runLogID = '9'
                ORDER BY pl.prodDate DESC
                LIMIT 1
            ) AS lastRecord ON 1 = 1;
        */

        $this->log->info("updateProductionRun called with prodRunID: {$prodRunID} and runComplete: {$runComplete}");
        $totals = $this->getMaterialTotals($prodRunID);
        if (!$totals) throw new \Exception('Failed to get production run totals from getMaterialTotals');
        $sql = "UPDATE prodrunlog 
                    SET 
                        endDate = :endDate, 
                        mat1Lbs = :mat1Lbs, 
                        mat2Lbs = :mat2Lbs, 
                        mat3Lbs = :mat3Lbs, 
                        mat4Lbs = :mat4Lbs, 
                        partsProduced = :produced, 
                        startUpRejects= :startUpRejects, 
                        qaRejects=:qaRejects,
                        purgelbs = :purge, 
                        runComplete = :runComplete 
                    WHERE logID = :prodRunID";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':endDate', $totals['prodDate'], \PDO::PARAM_STR);
        $stmt->bindParam(':mat1Lbs', $totals['total_matUsed1'], \PDO::PARAM_STR);
        $stmt->bindParam(':mat2Lbs', $totals['total_matUsed2'], \PDO::PARAM_STR);
        $stmt->bindParam(':mat3Lbs', $totals['total_matUsed3'], \PDO::PARAM_STR);
        $stmt->bindParam(':mat4Lbs', $totals['total_matUsed4'], \PDO::PARAM_STR);
        $stmt->bindParam(':produced', $totals['total_produced'], \PDO::PARAM_STR);
        $stmt->bindParam(':startUpRejects', $totals['total_startUpRejects'], \PDO::PARAM_STR);
        $stmt->bindParam(':qaRejects', $totals['total_qaRejects'], \PDO::PARAM_STR);
        $stmt->bindParam(':purge', $totals['total_total_purgeLbs'], \PDO::PARAM_STR);
        $stmt->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_STR);
        $stmt->bindParam(':runComplete', $runComplete, \PDO::PARAM_STR);

        $result = $stmt->execute();
        if (!$result) throw new \Exception('Failed to update production run log.');
    }

    /**
     * updateProductInventory
     *
     * @param  mixed $productID
     * @param  mixed $transAmount
     * @param  mixed $oldStock
     * @param  mixed $operator
     * @return void
     */
    private function updateProductInventory($productID, $qty, $operator)
    {
        $op = $op = ($operator === "+") ? '+' : '-';
        $sql = "UPDATE productInventory SET partQty = partQty {$op} :qty WHERE productID = :productID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':qty', $qty, \PDO::PARAM_INT);
        $stmt->bindParam(':productID', $productID, \PDO::PARAM_INT);

        if (!$stmt->execute()) throw new \Exception("Failed to update product inventory for {$productID}.");
    }

    /**
     * updateMaterialInventory
     *
     * @param  mixed $matPartNumber
     * @param  mixed $matLbs
     * @param  mixed $oldStock
     * @param  mixed $operator
     * @return void
     */
    private function updateMaterialInventory($materialData, $operator)
    {
        $prodLogID = $materialData['prodLogID'];
        $op = ($operator === "+") ? '+' : '-';

        $matData = $materialData;

        $transMaterialData = array(
            "action" => 'insertProdLog',
            "inventoryID" => "",
            "inventoryType" => 'material',
            "prodLogID" => $prodLogID,
            "oldStockCount" => "",
            "transAmount" => "",
            "transType" => "production log",
            "transComment" => "",
        );

        /*  loop throw materials and update material inventory
            Insert trans record for each material */
        for ($i = 1; $i <= 4; $i++) {

            $matId = $matData["mat{$i}"];
            $used = floatval($matData["matDailyUsed{$i}"] ?? 0);

            if (!$matId || $used <= 0) continue;

            $oldMatStock = $this->getMaterialLbs($matId);
            $transMaterialData['oldStockCount'] = $oldMatStock;
            $transMaterialData['inventoryID'] = $matId;
            $transMaterialData['transAmount'] = $used;

            $sql = "UPDATE materialinventory 
                    SET matLbs = matlbs {$op} :matLbs 
                    WHERE matPartNumber = :matPartNumber";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':matLbs', $used, \PDO::PARAM_STR);
            $stmt->bindParam(':matPartNumber', $matId, \PDO::PARAM_STR);

            if (!$stmt->execute()) throw new \Exception("Failed to update material inventory for {$matId}.");

            $this->insertTrans($transMaterialData);
        }
    }

    /* GET FUNCTIONS */

    /**
     * getMaterialLbs
     *
     * @param  mixed $matPartNumber
     * @return void
     */
    private function getMaterialLbs($matPartNumber)
    {
        $sql = 'SELECT matLbs, matPartNumber FROM materialinventory WHERE matPartNumber = :matPartNumber';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':matPartNumber', $matPartNumber, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch();
        if (!$result) throw new \Exception("Failed to get {$matPartNumber} inventory amount!");
        $lbs = $result['matLbs'];
        return $lbs;
    }

    /**
     * getPFMQty
     *
     * @param  mixed $partNumber
     * @return void
     */
    private function getPFMQty($partNumber)
    {
        $sql = 'SELECT partNumber, qty FROM pfmInventory WHERE partNumber = :partNumber';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partNumber', $partNumber, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        if (!$result) throw new \Exception("Failed to get PFM Qty for {$partNumber}.");
        $qty = $result['qty'];

        return  $qty;
    }

    /**
     * getInvQty function
     *
     * @param [type] $productID
     * @return void
     */
    public function getInvQty($productID)
    {
        $sql = 'SELECT partQty FROM productInventory WHERE productID = :productID';
        $stmt = $this->pdo->prepare($sql);
        $qty = $stmt->execute([':productID' => $productID]);
        if (!$qty) throw new \Exception("Failed to get qty for {$productID}.");
        return $qty;
    }

    /**
     * getProductionLog function
     * Returns logID, qaRejects , productID and prodDate so that QA Rejects can be added to a production logs
     * 
     * @param [type] $productID
     * @param [type] $prodDate
     * @return void
     */
    public function getProductionlog($productID, $prodDate)
    {
        $sql = "SELECT 
                    `productionlogs`.*,
                    `materiallog`.*,
                    `templog`.*
                FROM
                    `templog`
                INNER JOIN `productionlogs` ON (`templog`.`prodLogID` = `productionlogs`.`logID`)
                INNER JOIN `materiallog` ON (`productionlogs`.`logID` = `materiallog`.`prodLogID`)
                WHERE
                    `productionlogs`.`productID` = :productID AND 
                    `productionlogs`.`prodDate` = :prodDate";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'productID' => $productID,
            'prodDate' => $prodDate
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) throw new \Exception("Failed to get production log for {$productID} made on {$prodDate}.");
        $data = $result;
        $mat1 = $data['mat1'];
        $mat2 = $data['mat2'];
        $mat3 = empty($data['mat3']) ? 'empty' : $data['mat3'];
        $mat4 = empty($data['mat4']) ? 'empty' : $data['mat4'];

        $mats = [$mat1, $mat2, $mat3, $mat4];

        $this->log->info("mats: " . print_r($mats, true));
        $matList = [];
        $matList = $this->getMaterialList();
        $matList = (array) $matList;

        $this->log->info("matList: " . print_r($matList, true));

        $lookupTable = array_column($matList, 'matName', 'matPartNumber');
        $data['mat1'] = $lookupTable[$data['mat1']] ?? $data['mat1'];
        $data['mat2'] = $lookupTable[$data['mat2']] ?? $data['mat2'];
        $data['mat3'] = $lookupTable[$data['mat3']] ?? $data['mat3'];
        $data['mat4'] = $lookupTable[$data['mat4']] ?? $data['mat4'];

        $data['mat3'] = empty($data['mat3']) ? 'empty' : $data['mat3'];
        $data['mat4'] = empty($data['mat4']) ? 'empty' : $data['mat4'];

        $this->log->info("data after replacement: " . print_r($data, true));

        return $data;
    }

    /**
     * getPrevProdLog
     *returns  productionlog ID based on the production Run ID  
     * 
     * @param  mixed $prodRunID
     * @return void
     */
    private function getPrevProdLog($prodRunID)
    {
        $sqlGetPrevLog = "SELECT logID, runLogID FROM productionlogs WHERE runLogID  = :prodRunID ORDER BY logID DESC LIMIT 1";

        $stmtGetPrevLog = $this->pdo->prepare($sqlGetPrevLog);
        $stmtGetPrevLog->execute(['prodRunID' => $prodRunID]);

        $row = $stmtGetPrevLog->fetch(\PDO::FETCH_ASSOC);
        $prevLogID = $row['logID'];
        if (!$prevLogID) throw new \Exception("Failed to return previous log in production run {$prodRunID}.");
        return $prevLogID;
    }

    /**
     * getProdRunID
     * This will return the logID of the production run no completed based on the part number.   
     *  
     * @param  mixed $productID
     * @return void
     */
    private function getProdRunID($productID)
    {
        try {
            //get current prodRunID and set $prodRunID
            $sqlGetRunID = 'SELECT logID, productID, runComplete FROM prodrunlog WHERE  productID = :prodID AND runComplete  = "no" ';
            $stmtGetRunID = $this->pdo->prepare($sqlGetRunID);
            $result = $stmtGetRunID->execute(['prodID' => $productID]);
            if ($result) {
                // Fetch data properly
                $row = $stmtGetRunID->fetch(\PDO::FETCH_ASSOC);
                $logID = $row['logID'];
                return $logID;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            echo 'Error getting prod run ID: ' . $e->getMessage();
        }
    }

    /**
     * getLastMaterialLogForRun
     * 
     *This function witll return the material info ffrom the previous production log
     *so that daily used can be filled out on the add productionlog form  \
       
     * @param  mixed $productID
     * @return void
     */
    public function getLastMaterialLogForRun($productID)
    {
        $prodRunID = $this->getProdRunID($productID);

        $this->log->info('getLastMaterialLogForRun->productID = ' . $productID);
        $this->log->info('getLastMaterialLogForRun->prodRunID = ' . $prodRunID);

        $prodLogID = $this->getPrevProdLog($prodRunID);
        $this->log->info('getLastMaterialLogForRun->prodLogID = ' . $prodLogID);


        $sql = 'SELECT * FROM materialLog WHERE prodLogID = :prodLogID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':prodLogID', $prodLogID,  \PDO::PARAM_STR);
        $stmt->execute();

        //$rowCount = $stmt->rowCount();
        //error_log('getLastMaterialLogForRun->row.rowcount: ' . print_r($rowCount));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            $this->log->error("No matching row found for prodLogID: " . $prodLogID);
            return []; // Explicitly return null instead of 1
        }
        return $row;
    }

    /**
     * getMaterialTotals
     *
     * Passing the production run id and return the material,productIDs, qarejects, purge totals for the production run.    
     * 
     * @param  mixed $prodRunID
     * @return void
     */
    private function getMaterialTotals($prodRunID)
    {
        $sqlGetTotals = "SELECT p.runLogID, p.prodDate, 
                        SUM(m.matDailyUsed1) AS total_matUsed1,
                        SUM(m.matDailyUsed2) AS total_matUsed2, 
                        SUM(m.matDailyUsed3) AS total_matUsed3, 
                        SUM(m.matDailyUsed4) AS total_matUsed4,
                        SUM(p.pressCounter) AS total_produced,
                        SUM(p.startUpRejects) AS total_startUpRejects,
                        SUM(p.qaRejects) AS total_qaRejects,
                        SUM(p.purgeLbs) AS total_purgeLbs
                    FROM productionlogs p
                    LEFT JOIN materialLog m ON p.logID = m.prodLogID
                    WHERE p.runLogID = :prodRunID
                    GROUP BY p.runLogID";

        $stmtGetTotals = $this->pdo->prepare($sqlGetTotals);
        $stmtGetTotals->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_INT);
        $stmtGetTotals->execute();
        $result = $stmtGetTotals->fetch(\PDO::FETCH_ASSOC);

        if (!$result) throw new \Exception("Error: productionDB_SQL->getMaterialTotals for prodRunID logID: " . $prodRunID);
        $this->log->info("Retrieved production totals for end of run and passed to insert function.");
        return $result;
    }

    /**
     * CheckProductionRuns
     * check to see if there is an open production run for the submitted productID 

     * @param  mixed $productID
     * @return void
     */
    public function CheckProductionRuns($productID)
    {
        error_log('productionDB_SQL->CheckProductionRuns Called');
        $sql = "SELECT logID, productID, runComplete FROM prodrunlog WHERE productID = :productID AND runComplete = :runComplete";
        $stmt = $this->pdo->prepare($sql);
        $no = 'no';
        $stmt->bindParam(':productID', $productID, \PDO::PARAM_STR);
        $stmt->bindParam(':runComplete', $no, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        $this->log->info('CheckRun $result: ' . $result);
        $this->log->info("CheckProductionRuns raw result: " . var_export($result, true));

        return ($result != false);
    }

    /**
     * checkLogDates
     * check production logs for the date of production log about to be added.  
     *
     * @param  mixed $productID
     * @param  mixed $prodDate
     * @return void
     */
    public function checkLogDates($productID, $prodDate)
    {
        try {
            $sql = 'SELECT COUNT(*) FROM productionLogs WHERE productID = :productID AND prodDate = :prodDate';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':productID', $productID, \PDO::PARAM_STR);
            $stmt->bindParam(':prodDate', $prodDate);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return ($count > 0);
        } catch (\PDOException $e) {
            error_log("Error checking production date: " . $e->getMessage());
            return false;
        }
    }

    /**
     * getProductList returns a list of productID and PartName
     * @return void
     */
    public function getProductList()
    {
        try {
            $sql = 'SELECT productID, partName from products';
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

    /**
     * getMaterialList returns a list of matPartNumber and matName
     *
     * @return void
     */
    public function getMaterialList()
    {
        try {
            $sql = 'SELECT matPartNumber, matName from material';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            } else {
                return 0;
            }
        } catch (\PDOException $e) {
            $this->log->error("ERROR: Failed to get material list: " . $e->getMessage());
        }
    }
}
