<?php
//FILE: src/Classes/quality/Models/QualityModel.php
declare(strict_types=1);

namespace Quality\Models;

use Psr\Log\LoggerInterface;
use Database\Connection;
use Exception;
use Util\Utilities;

class QualityModel
{
    private \PDO $pdo;
    private LoggerInterface $log;
    private $util;

    public function __construct(Connection $dbConnection, LoggerInterface $log, Utilities $util)
    {
        $this->util = $util;
        $this->pdo = $dbConnection->getPdo();
        $this->log = $log;
    }

    /*===========> INSERT FUNCTIONS <=========*/

    /**
     * insertQaRejects function
     *
     * @param [type] $productID
     * @param [type] $prodDate
     * @param [type] $rejects
     * @param [type] $comments
     * @return void
     */
    public function insertQaRejects($data)
    {
        $productID = $data['qaRejectData']['productID'];
        $rejects = $data['qaRejectData']['rejects'];
        $comments = $data['qaRejectData']['comments'];
        $prodDate = $data['qaRejectData']['prodDate'];

        $row = $this->getProductionLogID($productID, $prodDate);
        if (!$row) throw new \Exception('Failed to get prodLogID.');
        $prodLogID = $row['logID'];
        $productStockQty = $this->getProductInventory($productID);
        $pfmStockQty = $this->getPFMInventory('349-61A0');
        $copperPins = $rejects * 2;

        try {
            $this->pdo->beginTransaction();

            //SETUP ARRAY FOR Product TRANSACTION INSERT
            $transProductData = array(
                "action" => 'updateProdLog',
                "inventoryID" => $productID,
                "inventoryType" => 'product',
                "prodLogID" => $prodLogID,
                "oldStockCount" => $productStockQty,
                "transAmount" => $rejects,
                "transType" => "qa rejects",
                "transComment" => $comments,
            );

            //SETUP ARRAY FOR PFM TRANSACTION INSERT
            $transPFMData = array(
                "action" => 'updateProdLog',
                "inventoryID" => '349-61A0',
                "inventoryType" => 'pfm',
                "prodLogID" => $prodLogID,
                "oldStockCount" => $pfmStockQty,
                "transAmount" => $copperPins,
                "transType" => "qa rejects",
                "transComment" => 'qaReject Log',
            );
            $this->log->info('Reached insertQaRejects with data:', $data);
            $this->log->error("Logger alive check: about to try insert");

            // insert QA Rejects
            $sql = 'INSERT  
                    INTO qarejects (prodDate, prodLogID, productID, rejects, comments)
                    VALUES (:prodDate, :prodLogID, :productID, :rejects, :comments)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':prodDate', $prodDate, \PDO::PARAM_STR);
            $stmt->bindParam(':prodLogID', $prodLogID, \PDO::PARAM_INT);
            $stmt->bindParam(':productID', $productID, \PDO::PARAM_STR);
            $stmt->bindParam(':rejects', $rejects, \PDO::PARAM_INT);
            $stmt->bindParam(':comments', $comments, \PDO::PARAM_STR);

            try {
                $success = $stmt->execute();
                if(!$success){
                    $errorInfo = $stmt->errorInfo();
                    $this->log->error("Insert into qarejects failed", [
                        'errorInfo' => $errorInfo,
                        'prodDate' => $prodDate,
                        'prodLogID' => $prodLogID,
                        'productID' => $productID,
                        'rejects' => $rejects,
                        'comments' => $comments
                    ]);
                    throw new \Exception('Failed to insert QaReject log. ERROR: ' . $errorInfo[2]);
                }
            } catch (\PDOException $e) {
                $this->log->error("Insert into qarejects failed (via exception)", [
                    'exception' => $e->getMessage(),
                    'prodDate' => $prodDate,
                    'prodLogID' => $prodLogID,
                    'productID' => $productID,
                    'rejects' => $rejects,
                    'comments' => $comments
                ]);
                throw $e;
            }
            

            // insert transaction for QA rejects
            $this->insertTransactions($transProductData);

            // insert transaction for copper pins being added back into inventory
            $this->insertTransactions($transPFMData);

            // update production log with QA rejects
            $this->updateQaRejects($prodLogID, $rejects);

            // update subtract rejects for productID from product inventory
            $this->updateProductQty($productID, $rejects, "-");

            // update add copper pins to inventory
            $this->updatePFMQty('349-61A0', $copperPins, "+");

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Successfully added {$rejects} of {$productID} to  production log from {$prodDate} and added {$copperPins} copper pins back to inventory."
            ];
        } catch (\Throwable $e) {
            $this->pdo->rollback();

            return [
                'success' => false,
                'message' => 'QA Rejects insert failed: ' . $e->getMessage()
            ];
        }
    }

    public function insertTransactions($data)
    {
        $sql = 'INSERT INTO 
                    inventorytrans (inventoryID, inventoryType, prodLogID, oldStockCount, transAmount, transType, transComment) 
                VALUES (:inventoryID, :inventoryType, :prodLogID, :oldStockCount, :transAmount, :transType, :transComment)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':inventoryID', $data['inventoryID'], \PDO::PARAM_STR);
        $stmt->bindParam(':inventoryType', $data['inventoryType'], \PDO::PARAM_STR);
        $stmt->bindParam(':prodLogID', $data['prodLogID'], \PDO::PARAM_INT);
        $stmt->bindParam(':oldStockCount', $data['oldStockCount'], \PDO::PARAM_INT);
        $stmt->bindParam(':transAmount', $data['transAmount'], \PDO::PARAM_INT);
        $stmt->bindParam(':transType', $data['transType'], \PDO::PARAM_STR);
        $stmt->bindParam(':transComment', $data['transComment'], \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            $this->log->error("error insert inventory transaction: {$data['inventoryID']}:  ERROR: " . $error);
            throw new \Exception("Failed to insert transactions for {$data['inventoryID']}.  ERROR: {$error}");
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
    public function insertLotChange($data)
    {
        $productID = $data['lotChangeData']['productID'];
        $prodDate = $data['lotChangeData']['ChangeDate'];
        $row = $this->getProductionLogID($productID,  $prodDate);
        $prodLogID = $row['logID'];

        //get prodLogID but have to get prodDate first ????
        try {
            $sql = 'INSERT 
                    INTO lotChange (prodLogID, MatrerialName, ProductID, ChangeDate, ChangeTime, OldLot, NewLot, Comments) 
                    VALUES (:prodLogID, :MatrerialName, :productID, :ChangeDate, :ChangeTime, :OldLot, :NewLot, :Comments)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue('prodLogID', $prodLogID);
            $stmt->bindValue(':MaterialName', $data['lotChangeData']['MaterialName']);
            $stmt->bindValue(':productID', $data['lotChangeData']['productID']);
            $stmt->bindValue(':ChangeDate', $data['lotChangeData']['ChangeDate']);
            $stmt->bindValue(':ChangeTime', $data['lotChangeData']['ChangeTime']);
            $stmt->bindValue(':OldLot', $data['lotChangeData']['OldLot']);
            $stmt->bindValue(':NewLot', $data['lotChangeData']['NewLot']);
            $stmt->bindValue(':Comments', $data['lotChangeData']['Comments']);

            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();
                return [
                    'success' => false,
                    'message' => "Failed to insert lot change! ERROR MESSAGE: {$error}"
                ];
            }

            return [
                'success' => true,
                'message' => "Successfully added a lot change for {$data['lotChangeData']['MaterialName']}: old lot: {$data['lotChangeData']['OldLot']} and new lot:  {$data['lotChangeData']['NewLot']}."
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Uncaught error during insert lot change! ERROR MESSAGE: {$e->getMessage()}"
            ];
        }
    }

    /*===========> UPDATE FUNCTIONS <=========*/

    public function updateQaRejects($prodLogID, $rejects)
    {
        //Update productionLogs table
        $sql = 'UPDATE productionlogs 
                          SET qaRejects =  qaRejects + :rejects 
                          WHERE logID = :prodLogID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':prodLogID', $prodLogID, \PDO::PARAM_INT);
        $stmt->bindParam(':rejects', $rejects, \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            $this->log->error("Failed to update qarejects for productionLog id: {$prodLogID}. ERROR: {$error}");
            throw new \Exception("Failed to update qarejects for productionLog id: {$prodLogID}. ERROR: {$error}");
        }
    }

    public function updatePFMQty($pfmID, $copperPins, $operator)
    {
        $op = $op = ($operator === "+") ? '+' : '-';

        $sql = "UPDATE pfminventory SET Qty = Qty {$op} :partQty WHERE partNumber = :partNumber";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partQty', $copperPins, \PDO::PARAM_INT);
        $stmt->bindParam(':partNumber', $pfmID, \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            $this->log->error("Failed to update PFM: {$pfmID}'s qty. \n", [
                                'errorInfo' => $error,
                                'partNumber' => $pfmID,
                                'partQty' => $copperPins,
                                'operator' => $operator
                            ]);
            throw new \Exception("Failed to update PFM: {$pfmID}'s qty. ERROR: {$error}");
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

    /*===========> GET FUNCTIONS <=========*/

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
            $this->log->info("results of getMaterialList: " . print_r($result, true));

            if ($result) {
                return $result;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            $this->log->error("ERROR: Failed to get material list: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Failed to get material list: ERROR MESSAGE: {$e->getMessage()}"
            ];
        }
    }

    private function getPFMInventory($pfmID)
    {
        $sql = 'SELECT Qty FROM pfminventory WHERE partNumber = :partNumber';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partNumber', $pfmID, \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new \Exception("Failed to get PFM: {$pfmID}'s qty. ERROR: {$error}");
        }

        $result = $stmt->fetch();
        return $result;
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

    private function getProductInventory($productID)
    {
        $sql = 'SELECT partQty FROM productinventory WHERE productID = :productID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':productID', $productID, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            throw new \Exception("Failed to get product: {$productID}'s qty. ERROR: {$error}");
        }
        $result = $stmt->fetch();
        return $result;
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
}
