<?php
//FILE: src/Classes/quality/Models/QualityModel.php
declare(strict_types=1);

namespace Quality\Models;

use Psr\Log\LoggerInterface;
use Database\Connection;
use Exception;

class QualityModel
{
    private \PDO $pdo;
    private LoggerInterface $log;

    public function __construct(Connection $dbConnection, LoggerInterface $log)
    {
        $this->pdo = $dbConnection->getPdo();
        $this->log = $log;
    }

    public function getQALogs()
    {
        try {
            $qaRejectLogs = $this->getQARejectLogs();
            $qaOvenLogs = $this->getOvenLogs();
            $qaLotChangeLogs = $this->getLotChanges();

            return [
                'qaRejectLogs' => $qaRejectLogs ?? [],
                'ovenLogs' => $qaOvenLogs ?? [],
                'lotChangeLogs' => $qaLotChangeLogs ?? []
            ];
        } catch (\Throwable $e) {
            return ["success" => false, "message" => "Failed to getQA Logs: {$e}"];
        }
    }

    public function getQARejectLogs()
    {
        $sql = 'SELECT * FROM qarejects
                WHERE prodDate = NOW() - INTERVAL 4 WEEK Order By prodDate Desc';

        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new \Exception("Failed to get QA Reject logs. ERROR: {$errorInfo}");
        }
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function getLotChanges()
    {
        $sql = 'SELECT * FROM lotchange
                WHERE ChangeDate = NOW() - INTERVAL 12 WEEK Order By ChangeDate Desc';
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) throw new \Exception("Failed to get Lot Changes logs.");
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOvenLogs()
    {
        $sql = 'SELECT * FROM ovenlogs
                WHERE inOvenDate = NOW() - INTERVAL 4 WEEK Order By inOvenDate Desc';
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) throw new \Exception("Failed to get Oven Logs");
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
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
            $this->pdo->beginTransaction();

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

            $row = $this->getProductionLogID($productID, $prodDate);
            $invQty = $this->getInvQty($productID);

            if (!$row) {
                $this->log->info("Error: nothing was returned for previous log!");
                $this->pdo->rollback(); //revert changes
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

            $stmtInsert = $this->pdo->prepare($sqlInsert);

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
                $this->pdo->rollBack();
                $errorInfo = $InsertResult->errorInfo();
                return ["success" => false, "message" => "Database failed to insert a record into inventorytrans.", "error" => $errorInfo];
            }

            //Update productionLogs table
            $sqlUpdate = 'UPDATE productionlogs 
                          SET 
                            qaRejects =  qaRejects + :rejects 
                          WHERE 
                            logID = :prodLogID';

            $stmtUpdateLog = $this->pdo->prepare($sqlUpdate);

            $updateParams = [
                ':rejects' => [$rejects, \PDO::PARAM_INT],
                ':prodLogID' => [$prodLogID, \PDO::PARAM_INT],
            ];

            foreach ($updateParams as $key => [$value, $type]) {
                $stmtUpdateLog->bindParam($key, $value, $type);
            }

            $stmtUpdateLog->execute();

            $sqlProductUpdate = "UPDATE productInventory 
                                 SET partQty = partQty - :rejects 
                                WHERE productID = :productID";

            $stmtInvUpdate = $this->pdo->prepare($sqlProductUpdate);

            $InvUpdateParams = [
                ':rejects' => [$rejects, \PDO::PARAM_INT],
                ':productID' => [$productID, \PDO::PARAM_INT],
            ];

            foreach ($InvUpdateParams as $key => [$value, $type]) {
                $stmtInvUpdate->bindParam($key, $value, $type);
            }

            $stmtInvUpdate->execute();

            if ($stmtUpdateLog->rowCount() === 0 && $InsertResult === true && $stmtInvUpdate->rowCount() === 0) {
                $this->pdo->rollback();
                error_log("Transaction Failed: QA Rejects were not added and productionlogs qarejects was not updated.");
                return false;
            } else {
                //Commit transaction
                $this->pdo->commit();
                error_log("Transaction successful: QA Rejects added and productionlogs qarejects updated.");
                return true;
            }
        } catch (\Throwable $e) {
            $this->pdo->rollback();
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
            $this->pdo->beginTransaction();
            $row = $this->getProductionLogID($productID, $prodDate);

            if (!$row) throw new Exception("Error: nothing was returned for previous log!");

            $prodLogID = $row['logID'];

            //Update productionLogs table

            $sqlUpdate = 'UPDATE productionlogs SET purgelbs =  purgelbs + :purge WHERE logID = :prodLogID';
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":purge", $purge, \PDO::PARAM_STR);
            $stmtUpdate->bindParam(":prodLogID", $prodLogID, \PDO::PARAM_INT);

            if (!$stmtUpdate->execute()) throw new \Exception("Transaction Failed: unable to update productionlogs with purge amount.");

            //Commit transaction
            $this->pdo->commit();
            $this->log->info("Transaction successful: updated production logs with {$purge} lbs of purge.");
            return true;
        } catch (\Throwable $e) {
            $this->pdo->rollback();
            error_log('Error adding purge to production log: ' . $e->getMessage());
        }
    }

    /**
     * getProductionLog function
     * Returns logID, qaRejects , productID and prodDate so that QA Rejects can be added to a production logs
     * 
     * @param [type] $productID
     * @param [type] $prodDate
     * @return void
     */
    private function getProductionLogID($productID, $prodDate)
    {
        $sql = 'SELECT logID, qaRejects,productID, prodDate FROM `productionlogs` WHERE productID = :productID AND prodDate = :prodDate';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'productID' => $productID,
            'prodDate' => $prodDate
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) throw new \Exception("Failed to get production log for {$productID} made on {$prodDate}.");
        return $result;
    }
}
