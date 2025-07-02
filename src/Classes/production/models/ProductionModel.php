<?php
// File: src/Classes/production/models/ProductionModel.php
namespace Production\Models;

require_once  __DIR__ . '/../../database.php';
require_once __DIR__ . '/../utils/Util.php';

use Psr\Log\LoggerInterface;
use PDOException;
use ErrorException;
use Production\utils\Util;
use Throwable;

class ProductionModel
{

    private $con;
    private $log;
    private $util;

    /**
     * constructor for database and logger
     *
     * @param \PDO $dbConnection
     * @param LoggerInterface $log
     */
    public function __construct(\PDO $dbConnection, LoggerInterface $log, Util $util)
    {
        error_log("✅ ProductionModel constructor reached");
        $this->con = $dbConnection;
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
        return $this->con;
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
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $results;
        } catch (PDOException $e) {
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
        $stmt = $this->con->prepare($sql);
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
            $stmt = $this->con->prepare($sql);
            $stmt->execute(['id' => $id]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->log->info("result of ReadPrevious() function: " . print_r($result, true));

            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * insertQaRejects function
     *
     * @param [type] $productID
     * @param [type] $prodDate
     * @param [type] $rejects
     * @param [type] $comments
     * @return void
     */
    public function insertQaRejects($productID, $prodDate, $rejects, $comments)
    {
        try {
            $this->con->beginTransaction();

            //SETUP ARRAY FOR TRANSACTION INSERT
            $transData = array(
                "action" => 'updateProdLog',
                "inventoryID" => $productID,
                "inventoryType" => 'product',
                "prodLogID" => "0",
                "oldStockCount" => "",
                "transAmount" => $rejects,
                "transType" => "qa rejects",
                "transComment" => $comments,
            );

            $row = $this->getProductionlog($productID, $prodDate);
            $invQty = $this->getInvQty($productID);

            if (!$row) {
                $this->log->info("Error: nothing was returned for previous log!");
                $this->con->rollback(); //revert changes
                return false;
            } else {
                $prodLogID = $row['logID'];
                $prevRejects = $row['qaRejects'];

                $transData['prodLogID'] = $prodLogID;
                $transData['oldStockCount'] = $invQty;
            }

            $newTotal = $prevRejects + $rejects;
            //Insert info into qaRejects Table
            $sqlInsert = "INSERT 
                          INTO qarejects (
                            prodDate,
                            prodLogID, 
                            productID, 
                            rejects, 
                            comments) 
                          VALUES (
                            :prodDate,
                            :prodLogID,
                            :productID,
                            :rejects,
                            :comments)";

            $stmtInsert = $this->con->prepare($sqlInsert);

            //setup bindParams
            $insertParams = [
                ':prodDate' => [$prodDate, \PDO::PARAM_STR],
                ':prodLogID' => [$prodLogID, \PDO::PARAM_STR],
                ':productID' => [$productID, \PDO::PARAM_INT],
                ':rejects' => [$rejects, \PDO::PARAM_INT],
                ':comments' => [$comments, \PDO::PARAM_INT],
            ];

            //set bindParams
            foreach ($insertParams as $key => [$value, $type]) {
                $stmtInsert->bindParam($key, $value, $type);
            }

            $InsertResult = $stmtInsert->execute();

            if (!$InsertResult) {
                $this->con->rollBack();
                $errorInfo = $InsertResult->errorInfo();
                return ["success" => false, "message" => "Database failed to insert a record into inventorytrans.", "error" => $errorInfo];
            }

            //Update productionLogs table
            $sqlUpdate = 'UPDATE productionlogs 
                          SET 
                            qaRejects =  qaRejects + :rejects 
                          WHERE 
                            logID = :prodLogID';

            $stmtUpdateLog = $this->con->prepare($sqlUpdate);

            $updateParams = [
                ':rejects' => [$rejects, \PDO::PARAM_INT],
                ':prodLogID' => [$prodLogID, \PDO::PARAM_INT],
            ];

            foreach ($updateParams as $key => [$value, $type]) {
                $stmtUpdateLog->bindParam($key, $value, $type);
            }

            $stmtUpdateLog->execute();

            $sqlProductUpdate = "UPDATE productInventory 
                                 SET 
                                    partQty = partQty - :rejects 
                                WHERE 
                                    productID = :productID";

            $stmtInvUpdate = $this->con->prepare($sqlProductUpdate);

            $InvUpdateParams = [
                ':rejects' => [$rejects, \PDO::PARAM_INT],
                ':productID' => [$productID, \PDO::PARAM_INT],
            ];

            foreach ($InvUpdateParams as $key => [$value, $type]) {
                $stmtInvUpdate->bindParam($key, $value, $type);
            }

            $stmtInvUpdate->execute();

            if ($stmtUpdateLog->rowCount() === 0 && $InsertResult === true && $stmtInvUpdate->rowCount() === 0) {
                $this->con->rollback();
                error_log("Transaction Failed: QA Rejects were not added and productionlogs qarejects was not updated.");
                return false;
            } else {
                //Commit transaction
                $this->con->commit();
                error_log("Transaction successful: QA Rejects added and productionlogs qarejects updated.");
                return true;
            }
        } catch (PDOException $e) {
            $this->con->rollback();
            error_log("QA Rejects Transaction failed: " . $e->getMessage());
        }
    }

    /**
     * addPurge
     *
     * @param  mixed $productID
     * @param  mixed $prodDate
     * @param  mixed $purge
     * @return void
     */
    public function addPurge($productID, $prodDate, $purge)
    {
        try {
            $this->con->beginTransaction();
            $row = $this->getProductionlog($productID, $prodDate);

            if (!$row) {
                error_log("Error:  nothing was returned for previous log!");
                $this->con->rollback(); //revert changes
                return false;
            }

            $prodLogID = $row['logID'];

            //Update productionLogs table

            $sqlUpdate = 'UPDATE productionlogs SET purgelbs =  purgelbs + :purge WHERE logID = :prodLogID';
            $stmtUpdate = $this->con->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":purge", $purge, \PDO::PARAM_STR);
            $stmtUpdate->bindParam(":prodLogID", $prodLogID, \PDO::PARAM_INT);
            $stmtUpdate->execute();

            if ($stmtUpdate->rowCount() === 0) {
                error_log("Transaction Failed: productionlogs purge update.");
                $this->con->rollback();
                return false;
            } else {
                //Commit transaction
                $this->con->commit();
                error_log("Transaction successful: productionlogs purge updated.");
                return true;
            }
        } catch (PDOException $e) {
            error_log('Error adding purge to production log: ' . $e->getMessage());
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
            $this->con->beginTransaction();

            $this->log->info("⏳ insertProdLog called — raw input snapshot", [
                'prodData' => $prodData,
                'materialData' => $materialData,
                'tempData' => $tempData
            ]);

            $productID = $prodData['productID'];

            /*  set runStatus to either start,in progress or end
                0 - get $prodRunID, using prodRunID get prevProdLogID and set prodData[] values
                1 - create Productionrun, set prodRunID, set $prevProdLogID to 0
                2 - get $prodRunID, get $prevProdLogID and set prodData[] values
            */
            switch ($prodData['runStatus']) {
                case '0':
                    $prodData['runStatus'] = 'in progress';
                    //use productID to get production run id
                    $prodRunID = $this->getProdRunID($productID);
                    if (!$prodRunID) throw new \Exception("Failed to get production run ID");
                    //use prodRunID to get last prodLogID and set $prevProdLogID
                    $prevProdLogID = $this->getPrevProdLog($prodRunID);
                    if(!$prevProdLogID) throw new \Exception("Failed to get previous production log ID");
                    $this->log->info('Previous Log ID: ' . $prevProdLogID);
                    $prodData['runLogID'] = $prodRunID;
                    $prodData['prevProdLogID'] = $prevProdLogID;
                    break;
                case '1':
                    $prodData['runStatus'] = 'start';
                    $prodRunID = $this->insertProductionRun($productID, $prodData['prodDate']);
                    if(!$prodRunID) throw new \Exception("Failed to get prodRunID!");
                    $prodData['runLogID'] = $prodRunID;
                    $prevProdLogID = "0";
                    break;
                case '2':
                    $prodData['runStatus'] = 'end';
                    $prodRunID = $this->getProdRunID($productID);
                    if (!$prodRunID) throw new \Exception("Failed to get production run ID");
                    $prevProdLogID = $this->getPrevProdLog($prodRunID);
                    if(!$prevProdLogID) throw new \Exception("Failed to get previous production log ID.");
                    $this->log->info('Previous Log ID: ' . $prevProdLogID);
                    $prodData['runLogID'] = $prodRunID;
                    $prodData['prevProdLogID'] = $prevProdLogID;
                    break;
                default:
                    $this->log->error("RunStatus was not the correct type!");
                    throw new \Exception("Invalid runStatus in prodData");
            }

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

            $stmtInsertProdLog = $this->con->prepare($sqlInsertProdLog);
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
            $prodLogID = $this->con->lastInsertId();
            $this->log->info('prodLogID: ' . $prodLogID);
            if (!$prodLogID) throw new \Exception("Failed to get last production log for production run!");

            /* add prodLogID to materialLog & tempLog */
            $materialData["prodLogID"] = $prodLogID;
            $this->updateMatLogProdLogID($materialData);
            $tempData["prodLogID"] = $prodLogID;
            $this->updateTempLogProdLogID($tempData);

            /////////////////////INSERT PRODUCTIONLOG INSERT END//////////////////////////////////

            //////////Insert Transaction for startuprejects, mat used, parts added to inventory, copper pins 
            

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
                $used = floatval($matData["matUsed{$i}"] ?? 0);
                if (!$matId || $used <= 0) continue;
                $transMaterialData['oldStockCount'] = $this->getMaterialLbs($matId);
                $transMaterialData['inventoryID'] = $matId;
                $transMaterialData['transAmount'] = $used;

                $this->updateMaterialInventory(
                    $transMaterialData['inventoryID'],
                    $transMaterialData['transAmount'],
                    $transMaterialData['oldStockCount'],
                    '-'
                );

                $this->insertTrans($transMaterialData);
            }

            //parts getting added to inventory

            $parts = $prodData['pressCounter'] - $prodData['startUpRejects'];
            /* prep and insert trans record for product inventory update */
            
            $oldStock = $this->getInvQty($productID);
            $transProductData = array(
                "action" => 'insertProdLog',
                "inventoryID" => $productID,
                "inventoryType" => 'product',
                "prodLogID" => $prodLogID,
                "oldStockCount" => $oldStock,
                "transAmount" => $parts,
                "transType" => "production log",
                "transComment" => $prodData['comments'],
            );

            $this->updateProductInventory($productID, $transProductData['transAmount'], $oldStock, '+');
            $this->insertTrans($transProductData);

            // check to see if this is the end of the production run and fetch material data for the run
            // and update prodrunlog with totals, end date and completed

            $prodDate = $prodData['prodDate'];
            if ($prodData['runStatus'] === 'end') {

                $this->log->info('End of prodcution run detected aquiring run production totals');

                $this->updateProductionRun($prodDate, 'yes');
            }

            /* copper pins removed from inventory */
            $copperPins = $parts * 2;

            $this->updatePFMInventory('349-61A0', $copperPins, '-');

            $this->con->commit();
            return ["success" => true, "message" => "Transaction completed successfully.", "prodLogID" => $prodLogID];
        } catch (\Throwable $e) {
            $this->con->rollBack();
            $this->log->error('PDO Rollback.  Error failed to insert Production log: ' . $e->getMessage());
            throw $e;
        }
    }
    
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
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':partNumber', $partNumber, \PDO::PARAM_STR);
        $stmt->bindParam(':newQty', $newQty, \PDO::PARAM_INT);

        if(!$stmt->execute()) throw new \Exception("Failed to update {$partNumber}.");
    }

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
                                        mat2,
                                        matUsed2,
                                        mat3,
                                        matUsed3,
                                        mat4,
                                        matUsed4) 
                                    VALUES (
                                        :prodLogID,
                                        :mat1,
                                        :matUsed1,
                                        :mat2,
                                        :matUsed2,
                                        :mat3,
                                        :matUsed3,
                                        :mat4,
                                        :matUsed4)";

        $stmt = $this->con->prepare($sql);
        $stmt->execute($materialData);

        $matLogID = $this->con->lastInsertId();
        $this->log->info('matLogID: ' . $matLogID);
        if (!$matLogID) throw new \Exception("Failed to insert into materialLog or set matLogID.");
        return $matLogID;
    }
    
    /**
     * updateMatLogProdLogID
     *
     * @param  mixed $materialData
     * @return void
     */
    private function updateMatLogProdLogID($materialData)
    {
        try {
            $sql = 'UPDATE materialLog SET prodLogID = :prodLogID WHERE matLogID = :logID';
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':prodID', $materialData['prodLogID'], \PDO::PARAM_INT);
            $stmt->bindParam(':logID', $materialData['logID'], \PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $this->log->error('material log update failed: ' . print_r($stmt->errorInfo(), true));
                throw new \Exception("Material log update failed.");
            }
        } catch (\PDOException $e) {
            $this->log->error('material log update failed: ' . $e->getMessage());
        }
    }
    
    /**
     * insertTempLog
     *
     * @param  mixed $tempData
     * @return void
     */
    private function insertTempLog($tempData)
    {
        try {
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

            $stmt = $this->con->prepare($sql);
            $stmt->execute($tempData);
            $tempLogID = $this->con->lastInsertId();
            $this->log->info('tempLogID: ' . $tempLogID);
            if (!$tempLogID) throw new \Exception('Failed to insert tempLog and return tempLogID.');
            return $tempLogID;
        } catch (\PDOException $e) {
            $this->log->error('Failed to insert templog: ' . $e->getMessage());
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
        try {
            $sql = 'UPDATE tempLog SET prodLogID = :prodLogID WHERE tempLogID = :logID';
            $stmt = $this->con->prepare($sql);

            if ($stmt->execute()) {
                $this->log->error('temp log update failed: ' . print_r($stmt->errorInfo(), true));
                throw new \Exception("Production log insert failed.");
            }
        } catch (\Throwable $e) {
            $this->log->error('templog update failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * getMaterialLbs
     *
     * @param  mixed $matPartNumber
     * @return void
     */
    private function getMaterialLbs($matPartNumber){
        $sql = 'SELECT matLbs FROM materialInventory WHERE matPartNumber = : matPartNumber';
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':matPartNumber', $matPartNumber, \PDO::PARAM_STR);
        $result = $stmt->execute();
        if(!$result) throw new \Exception("Failed to get {$matPartNumber} inventory amount!");
        return $result;
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
        try {
            $sql = "INSERT into prodrunlog (
                                    productID,
                                    startDate) 
                                Values (
                                    :productID, 
                                    :prodDate, 
                                    :runComplete)";
            $stmt = $this->con->prepare($sql);

            $stmt->execute([
                ':productID' => $productID,
                ':prodDate' => $prodDate,
                ':runComplete' => 'no'
            ]);
            $prodRunID = $this->con->lastInsertId();
            if (!$prodRunID)
                return $prodRunID;
        } catch (\PDOException $e) {
            $this->log->error('Error creating production run: ' . $e->getMessage());
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

        $stmt = $this->con->prepare($sql);

        $prodRun_Params = [
            ':endDate' => [$totals['prodDate'], \PDO::PARAM_STR],
            ':mat1Lbs' => [$totals['total_matUsed1'], \PDO::PARAM_STR],
            ':mat2Lbs' => [$totals['total_matUsed2'], \PDO::PARAM_STR],
            ':mat3Lbs' => [$totals['total_matUsed3'], \PDO::PARAM_STR],
            ':mat4Lbs' => [$totals['total_matUsed4'], \PDO::PARAM_STR],
            ':produced' => [$totals['total_produced'], \PDO::PARAM_STR],
            ':startUpRejects' => [$totals['total_startUpRejects'], \PDO::PARAM_STR],
            ':qaRejects' => [$totals['total_qaRejects'], \PDO::PARAM_STR],
            ':purge' => [$totals['total_total_purgeLbs'], \PDO::PARAM_STR],
            ':prodRunID' => [$prodRunID, \PDO::PARAM_STR],
            ':runComplete' => [$runComplete, \PDO::PARAM_STR],
        ];

        foreach ($prodRun_Params as $key => [$value, $type]) {
            $stmt->bindParam($key, $value, $type);
        }

        $result = $stmt->execute();
        if (!$result) throw new \Exception('Failed to update production run log.');
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
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':partNumber', $partNumber, \PDO::PARAM_STR);
        $qty = $stmt->execute();
        return  $qty;
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

        $stmt = $this->con->prepare($sql);
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

    /**
     * getInvQty function
     *
     * @param [type] $productID
     * @return void
     */
    public function getInvQty($productID)
    {
        $sql = 'SELECT partQty FROM productInventory WHERE productID = :productID';
        $stmt = $this->con->prepare($sql);
        $qty = $stmt->execute([':productID' => $productID]);

        return $qty;
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
    private function updateProductInventory($productID, $transAmount, $oldStock, $operator)
    {
        $sql = 'UPDATE productInventory SET qty = :qty WHERE productID = :productID';
        ($operator === '+') ? $qty = $transAmount + $oldStock : $qty = $oldStock - $transAmount;
        $stmt = $this->con->prepare($sql);
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
    private function updateMaterialInventory($matPartNumber, $matLbs, $oldStock, $operator)
    {
        $sql = 'UPDATE materialInventory SET matLbs = : matLbs WHERE matPartNumber = : matPartNumber';
        ($operator === '+') ? $qty = $matLbs + $oldStock : $qty = $oldStock - $matLbs;
        $stmt = $this->con->prepare($sql);
        $stmt->bindParam(':matLbs', $qty, \PDO::PARAM_STR);
        $stmt->bindParam(':matPartNumber', $matPartNumber, \PDO::PARAM_STR);

        if (!$stmt->execute()) throw new \Exception("Failed to update material inventory for {$matPartNumber}.");
    }

    /**
     * getProductionLog function
     * Returns logID, qaRejects , productID and prodDate so that QA Rejects can be added to a production logs
     * 
     * @param [type] $productID
     * @param [type] $prodDate
     * @return void
     */
    private function getProductionlog($productID, $prodDate)
    {
        try {
            $sql = 'SELECT logID, qaRejects,productID, prodDate FROM `productionlogs` WHERE productID = :productID AND prodDate = :prodDate';
            $stmt = $this->con->prepare($sql);
            $stmt->execute([
                'productID' => $productID,
                'prodDate' => $prodDate
            ]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    //returns  productionlog ID based on the production Run ID     
    /**
     * getPrevProdLog
     *
     * @param  mixed $prodRunID
     * @return void
     */
    private function getPrevProdLog($prodRunID)
    {
        try {
            $sqlGetPrevLog = "SELECT logID, runLogID FROM productionlogs WHERE runLogID  = :prodRunID ORDER BY logID DESC LIMIT 1";

            $stmtGetPrevLog = $this->con->prepare($sqlGetPrevLog);
            $stmtGetPrevLog->execute(['prodRunID' => $prodRunID]);

            $row = $stmtGetPrevLog->fetch(\PDO::FETCH_ASSOC);
            $prevLogID = $row['logID'];

            return $prevLogID;
        } catch (PDOException $e) {
            error_log('Error: Getting Previous log ID for production log insert: ' . $e->getMessage());
        }
    }

    //This will return the logID of the production run no completed based on the part number.    
    /**
     * getProdRunID
     *
     * @param  mixed $productID
     * @return void
     */
    private function getProdRunID($productID)
    {
        try {
            //get current prodRunID and set $prodRunID
            $sqlGetRunID = 'SELECT logID, productID, runComplete FROM prodrunlog WHERE  productID = :prodID AND runComplete  = "no" ';
            $stmtGetRunID = $this->con->prepare($sqlGetRunID);
            $result = $stmtGetRunID->execute(['prodID' => $productID]);
            if ($result) {
                // Fetch data properly
                $row = $stmtGetRunID->fetch(\PDO::FETCH_ASSOC);
                $logID = $row['logID'];
                return $logID;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
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

        try {
            $sql = 'SELECT * FROM materialLog WHERE prodLogID = :prodLogID';
            $stmt = $this->con->prepare($sql);
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
        } catch (PDOException $e) {
            $this->log->error("ERROR: Failed to get materialLog for the production log: " . $e->getMessage());
        }
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
        try {
            $sqlGetTotals = "SELECT p.runLogID, p.prodDate, 
                            SUM(m.matUsed1) AS total_matUsed1,
                            SUM(m.matUsed2) AS total_matUsed2, 
                            SUM(m.matUsed3) AS total_matUsed3, 
                            SUM(m.matUsed4) AS total_matUsed4,
                            SUM(p.pressCounter) AS total_produced,
                            SUM(p.startUpRejects) AS total_startUpRejects,
                            SUM(p.qaRejects) AS total_qaRejects,
                            SUM(p.purgeLbs) AS total_purgeLbs
                        FROM productionlogs p
                        LEFT JOIN materialLog m ON p.logID = m.prodLogID
                        WHERE p.runLogID = :prodRunID
                        GROUP BY p.runLogID";

            $stmtGetTotals = $this->con->prepare($sqlGetTotals);
            $stmtGetTotals->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_INT);
            $stmtGetTotals->execute();
            $result = $stmtGetTotals->fetch(\PDO::FETCH_ASSOC);

            if (!$result) throw new \Exception("Error: productionDB_SQL->getMaterialTotals for prodRunID logID: " . $prodRunID);
            $this->log->info("Retrieved production totals for end of run and passed to insert function.");
            return $result;
        } catch (PDOException $e) {
            $this->log->error("ERROR: Failed to get production totals for end of run: " . $e->getMessage());
        }
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
        $stmt = $this->con->prepare($sql);
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
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':productID', $productID, \PDO::PARAM_STR);
            $stmt->bindParam(':prodDate', $prodDate);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return ($count > 0);
        } catch (PDOException $e) {
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
            $stmt = $this->con->prepare($sql);
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
            $stmt = $this->con->prepare($sql);
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
