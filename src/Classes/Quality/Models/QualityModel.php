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
        $this->pdo = $dbConnection->getPDO();
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
                if (!$success) {
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

    //This function handles both material and PFM receipts for received shipments
    public function recordInventoryReceipt(array $data)
    {
        try {
            $this->pdo->beginTransaction();

            $inventoryID   = $data['inventoryID'];
            $inventoryType = $data['inventoryType'];   // 'material' or 'pfm'
            $amount        = (int)$data['transAmount'];
            $date          = $data['deliveryDate'];
            $comment       = $data['transComment'];
            $transType     = $data['transType'] ?? 'received';
            $prodLogID     = $data['prodLogID'] ?? 0;

            // 1. Get current stock
            if ($inventoryType === 'material') {
                $currentStock = $this->getMaterialInventory($inventoryID)[0]['matLbs'];
                $this->updateMaterialQty($inventoryID, $amount, "+");
            } else {
                $currentStock = $this->getPFMInventory($inventoryID)['Qty'];
                $this->updatePFMQty($inventoryID, $amount, "+");
            }

            // 2. Insert into inventorytrans
            $this->insertTransactions([
                'inventoryID'   => $inventoryID,
                'inventoryType' => $inventoryType,
                'prodLogID'     => $prodLogID,
                'deliveryDate'  => $date,
                'oldStockCount' => $currentStock,
                'transAmount'   => $amount,
                'transType'     => $transType,
                'transComment'  => $comment
            ]);

            // 3. Insert into receivedshipments
            $sql = "INSERT INTO receivedshipments 
                    (inventoryType, inventoryID, receivedDate, receivedAmount, qaApprovedBy)
                    VALUES (:inventoryType, :inventoryID, :receivedDate, :receivedAmount, :qaApprovedBy)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':inventoryType'  => $inventoryType,
                ':inventoryID'    => $inventoryID,
                ':receivedDate'   => $date,
                ':receivedAmount' => $amount,
                ':qaApprovedBy'   => $comment
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Successfully received {$amount} of {$inventoryID}."
            ];
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->log->error("Failed to record inventory receipt for {$data['inventoryID']}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => "Failed to record inventory receipt: " . $e->getMessage()
            ];
        }
    }

    public function insertTransactions(array $data)
    {
        $sql = 'INSERT INTO inventorytrans 
            (inventoryID, inventoryType, prodLogID, deliveryDate, oldStockCount, transAmount, transType, transComment)
            VALUES (:inventoryID, :inventoryType, :prodLogID, :deliveryDate, :oldStockCount, :transAmount, :transType, :transComment)';

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':inventoryID'   => $data['inventoryID'],
            ':inventoryType' => $data['inventoryType'],
            ':prodLogID'     => $data['prodLogID'],
            ':deliveryDate'  => $data['deliveryDate'],
            ':oldStockCount' => $data['oldStockCount'],
            ':transAmount'   => $data['transAmount'],
            ':transType'     => $data['transType'],
            ':transComment'  => $data['transComment']
        ]);

        return true;
    }


    public function addReceivedShipments($data)
    {
        try {
            $this->pdo->beginTransaction();

            $this->insertTransactions($data);

            $sql = 'INSERT into receivedshipments (inventoryType, inventoryID, receivedDate, receivedAmount, qaApprovedBy) 
                    VALUES (:inventoryType, :inventoryID, :receivedDate, :receivedAmount, :qaApprovedBy)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':inventoryType', $data['inventoryType'], \PDO::PARAM_STR);
            $stmt->bindParam(':inventoryID', $data['inventoryID'], \PDO::PARAM_STR);
            $stmt->bindParam(':receivedDate', $data['matTransData']['deliveryDate'], \PDO::PARAM_STR);
            $stmt->bindParam(':receivedAmount', $data['matTransData']['transAmount'], \PDO::PARAM_INT);
            $stmt->bindParam(':qaApprovedBy', $data['matTransData']['transComment'], \PDO::PARAM_STR);


            $this->pdo->commit();
        } catch (\Throwable $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->log->error("Exception during insert of received shipments transaction: {$data['inventoryID']}:  ERROR: " . $e->getMessage());
            return ["success" => false, "message" => "Failed to insert received shipments transaction for {$data['inventoryID']}. ERROR: {$e->getMessage()}"];
        }
    }


    public function addPfmTransaction($data)
    {
        try {
            $this->pdo->beginTransaction();

            $currentStock = $this->getPFMInventory($data['inventoryID']);

            $data['oldStockCount'] = $currentStock;

            $this->insertTransactions($data);

            $sql = "Insert into receivedshipments (inventoryType, inventoryID, receivedDate, receivedAmount, qaApprovedBy) 
                    VALUES (:inventoryType, :inventoryID, :receivedDate, :receivedAmount, :qaApprovedBy)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':inventoryType', $data['inventoryType'], \PDO::PARAM_STR);
            $stmt->bindParam(':inventoryID', $data['inventoryID'], \PDO::PARAM_STR);
            $stmt->bindParam(':receivedDate', $data['deliveryDate'], \PDO::PARAM_STR);
            $stmt->bindParam(':receivedAmount', $data['transAmount'], \PDO::PARAM_INT);
            $stmt->bindParam(':receivedAmount', $data['transAmount'], \PDO::PARAM_INT);
            $stmt->bindParam(':qaApprovedBy', $data['transComment'], \PDO::PARAM_STR);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            $this->log->error("Exception during insert of received shipments transaction: {$data['inventoryID']}:  ERROR: " . $e->getMessage());
            return ["success" => false, "message" => "Failed to insert received shipments transaction for {$data['inventoryID']}. ERROR: {$e->getMessage()}"];
        }
    }

    /* public function insertTransactions($data)
    {

        $sql = 'INSERT INTO 
                    inventorytrans (
                        inventoryID, 
                        inventoryType, 
                        prodLogID, 
                        deliveryDate, 
                        oldStockCount, 
                        transAmount, 
                        transType, 
                        transComment) 
                VALUES (
                    :inventoryID, 
                    :inventoryType, 
                    :prodLogID, :deliveryDate, :oldStockCount, :transAmount, :transType, :transComment)';
        $stmt = $this->pdo->prepare($sql);

        try {

            if ($data['action'] == 'matReceived') {

                $currentStock = $this->getMaterialInventory($data['matTransData']['inventoryID']);

                $this->updateMaterialQty($data['matTransData']['inventoryID'], $data['matTransData']['lbsReceived'], "+");

                $stmt->bindParam(':inventoryID', $data['matTransData']['inventoryID'], \PDO::PARAM_STR);
                $stmt->bindParam(':inventoryType', $data['matTransData']['inventoryType'], \PDO::PARAM_STR);
                $stmt->bindParam(':prodLogID', $data['matTransData']['inventoryLogID'], \PDO::PARAM_INT);
                $stmt->bindParam(':deliveryDate', $data['matTransData']['deliveryDate'], \PDO::PARAM_STR);
                $stmt->bindParam(':oldStockCount', $currentStock, \PDO::PARAM_STR);
                $stmt->bindParam(':transAmount', $data['matTransData']['lbsReceived'], \PDO::PARAM_STR);
                $stmt->bindParam(':transType', $data['matTransData']['transType'], \PDO::PARAM_STR);
                $stmt->bindParam(':transComment', $data['matTransData']['transComment'], \PDO::PARAM_STR);
            } else {

                $currentStock = $this->getPFMInventory($data['inventoryID']);

                $this->updatePFMQty($data['inventoryID'], $data['transAmount'], "+");

                $stmt->bindParam(':inventoryID', $data['inventoryID'], \PDO::PARAM_STR);
                $stmt->bindParam(':inventoryType', $data['inventoryType'], \PDO::PARAM_STR);
                $stmt->bindParam(':prodLogID', $data['prodLogID'], \PDO::PARAM_INT);
                $stmt->bindValue(':deliveryDate', "null");
                $stmt->bindParam(':oldStockCount', $currentStock, \PDO::PARAM_INT);
                $stmt->bindParam(':transAmount', $data['transAmount'], \PDO::PARAM_INT);
                $stmt->bindParam(':transType', $data['transType'], \PDO::PARAM_STR);
                $stmt->bindParam(':transComment', $data['transComment'], \PDO::PARAM_STR);
            }

            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();

                $this->log->error("error insert inventory transaction: {$data['inventoryID']}:  ERROR: " . $error);
                throw new \Exception("Failed to insert transactions for {$data['inventoryID']}.  ERROR: {$error}");
                return ["success" => false, "message" => "Failed to insert inventory transaction for {$data['inventoryID']}. ERROR: {$error}"];
            } else {

                $this->log->info("Successfully inserted inventory transaction for {$data['matTransData']['inventoryID']}.");
                $message = "Transaction completed successfully.";
                return ["success" => true, "message" => $message];
            }
        } catch (\Throwable $e) {
            $this->log->error("Exception during insert inventory transaction: {$data['inventoryID']}:  ERROR: " . $e->getMessage());
            return ["success" => false, "message" => "Failed to insert inventory transaction for {$data['inventoryID']}. ERROR: {$e->getMessage()}"];
        }
    } */

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
                    INTO lotChange (prodLogID, MaterialName, ProductID, ChangeDate, ChangeTime, OldLot, NewLot, Comments) 
                    VALUES (:prodLogID, :MaterialName, :productID, :ChangeDate, :ChangeTime, :OldLot, :NewLot, :Comments)';
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
            $this->log->error("Insert into lot change failed", [
                'errorInfo' => $e->getMessage(),
                'prodDate' => $prodDate,
                'prodLogID' => $prodLogID,
                'productID' => $productID,
                'ChangeDate' => $data['lotChangeData']['ChangeDate'],
                'MaterialName' => $data['lotChangeData']['MaterialName']
            ]);
            return [
                'success' => false,
                'message' => "Uncaught error during insert lot change! ERROR MESSAGE: {$e->getMessage()}"
            ];
        }
    }

    public function insertOvenLog($data)
    {
        try {
            $sql = "INSERT 
                    INTO ovenlogs (productID, inOvenDate, firstShift, secondShift, thirdShift, inOvenTime, inOvenTemp, inOvenInitials, ovenComments) 
                    VALUES (:productID, :inOvenDate, :firstShift, :secondShift, :thirdShift, :inOvenTime, :inOvenTemp, :inOvenInitials, :ovenComments)";

            $data['ovenLogData']['firstShift']  = (int)$data['ovenLogData']['firstShift'];
            $data['ovenLogData']['secondShift'] = (int)$data['ovenLogData']['secondShift'];
            $data['ovenLogData']['thirdShift']  = (int)$data['ovenLogData']['thirdShift'];

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':productID', $data['ovenLogData']['productID']);
            $stmt->bindValue(':inOvenDate', $data['ovenLogData']['inOvenDate']);
            $stmt->bindValue(':firstShift', $data['ovenLogData']['firstShift']);
            $stmt->bindValue(':secondShift', $data['ovenLogData']['secondShift']);
            $stmt->bindValue(':thirdShift', $data['ovenLogData']['thirdShift']);
            $stmt->bindValue(':inOvenTime', $data['ovenLogData']['inOvenTime']);
            $stmt->bindValue(':inOvenTemp', $data['ovenLogData']['inOvenTemp']);
            $stmt->bindValue(':inOvenInitials', $data['ovenLogData']['inOvenInitials']);
            $stmt->bindValue(':ovenComments', $data['ovenLogData']['ovenComments']);

            $this->log->info("inOvenInitials: {$data['ovenLogData']['inOvenInitials']}");

            foreach (['productID', 'inOvenDate', 'firstShift', 'secondShift', 'thirdShift', 'inOvenTime', 'inOvenTemp', 'inOvenInitials', 'ovenComments'] as $key) {
                $this->log->info("Bound {$key}", ['value' => $data['ovenLogData'][$key], 'type' => gettype($data['ovenLogData'][$key])]);
            }

            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();
                $this->log->error("Failed to insert oven logs", [
                    'success' => false,
                    'error' => $error,
                ]);
                throw new \Exception();
            }
            return [
                'success' => true,
                'message' => "Successfully added an Oven log for {$data['ovenLogData']['inOvenDate']} at {$data['ovenLogData']['inOvenTime']}."

            ];
        } catch (\PDOException $e) {
            $this->log->error("Insert into lot change failed", [
                'errorInfo' => $e->getMessage(),
                'productID' => $data['ovenLogData']['productID'],
                'inOvenDate' => $data['ovenLogData']['inOvenDate'],
                'inOvenTime' => $data['ovenLogData']['inOvenTime'],
                'inOvenInitials' => $data['ovenLogData']['inOvenInitials'],
            ]);
            return [
                'success' => false,
                'message' => "Uncaught error during insert ovenLog! ERROR MESSAGE: {$e->getMessage()}"
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

    private function updateMaterialQty($matPartNumber, $amount, $operator)
    {
        $op = $op = ($operator === "+") ? '+' : '-';

        $sql = "UPDATE materialInventory SET matLbs = matLbs {$op} :matLbs WHERE matPartNumber = :matPartNumber";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':matLbs', $amount, \PDO::PARAM_INT);
        $stmt->bindParam(':matPartNumber', $matPartNumber, \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            $this->log->error("Failed to update Material: {$matPartNumber}'s qty. \n", [
                'errorInfo' => $error,
                'matPartNumber' => $matPartNumber,
                'Qty' => $amount,
                'operator' => $operator
            ]);
            throw new \Exception("Failed to update Material: {$matPartNumber}'s qty. ERROR: {$error}");
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

    public function updateOvenLog($data)
    {
        $this->log->info('QualityModel->updateOveLog called');
        $this->log->info('$data: ' . print_r($data, true));

        $sql = "UPDATE ovenlogs 
                SET outOvenDate = :outOvenDate, 
                    outOvenTime = :outOvenTime, 
                    outOvenTemp = :outOvenTemp, 
                    outOvenInitials = :outOvenInitials,
                    ovenComments = :ovenComments
                WHERE ovenLogID = :ovenLogID";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':ovenLogID', $data['ovenlog']['ovenLogID']);
            $stmt->bindValue(':outOvenDate', $data['ovenlog']['outOvenDate']);
            $stmt->bindValue(':outOvenTime', $data['ovenlog']['outOvenTime']);
            $stmt->bindValue(':outOvenTemp', $data['ovenlog']['outOvenTemp']);
            $stmt->bindValue(':outOvenInitials', $data['ovenlog']['outOvenInitials']);
            $stmt->bindValue(':ovenComments', $data['ovenlog']['ovenComments']);

            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();
                $this->log->error("Failed to update Oven log. \n", [
                    'errorInfo' => $error,
                    'out oven date' => $data['ovenlog']['outOvenDate'],
                    'out oven time' => $data['ovenlog']['outOvenTime'],
                ]);
                throw new \Exception("Failed to update oven log: ERROR: {$error}");
            }

            return [
                'success' => true,
                'message' => "Successfully updated an Oven log."

            ];
        } catch (\PDOException $e) {
            $this->log->error('Error during updateOvenLog. message: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => "Uncaught error during update ovenLog! ERROR MESSAGE: {$e->getMessage()}"
            ];
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
            $sql = 'SELECT productID, partName from products Order By displayOrder ASC';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            } else {
                return 0;
            }

            return [
                'success' => true,
                'message' => "Successfully updated oven log."
            ];
        } catch (\PDOException $e) {
            $this->log->error("ERROR: Failed to update oven log: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Update oven log failed: ' . $e->getMessage()
            ];
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
            $sql = 'SELECT matPartNumber, matName from material Order By displayOrder ASC';
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

    public function getPFmList()
    {
        try {
            $sql = 'SELECT partNumber, partName from pfm Order By displayOrder ASC';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if ($result) {
                return $result;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            $this->log->error("ERROR: Failed to get PFM list: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Failed to get PFM list: ERROR MESSAGE: {$e->getMessage()}"
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

    private function getMaterialInventory($matPartNumber)
    {
        $sql = "SELECT matLbs FROM materialInventory WHERE matPartNumber = :matPartNumber";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':matPartNumber', $matPartNumber, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            $error = $stmt->errorInfo();
            $this->log->error("Failed to get material Inventory for {$matPartNumber}. ERROR: " . $error);
            throw new \Exception("Failed to get material Inventory for {$matPartNumber}");
        }
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
                WHERE prodDate >= DATE_SUB(NOW(), INTERVAL 12 WEEK) ORDER BY prodDate DESC';
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
        $sql = 'SELECT 
                    `material`.`matPartNumber`,
                    `material`.`matName`,
                    `lotchange`.`MaterialName`,
                    `lotchange`.`LotChangeID`,
                    `lotchange`.`prodLogID`,
                    `lotchange`.`ProductID`,
                    `lotchange`.`ChangeDate`,
                    `lotchange`.`ChangeTime`,
                    `lotchange`.`OldLot`,
                    `lotchange`.`NewLot`,
                    `lotchange`.`Comments`
                FROM
                `material`
                INNER JOIN `lotchange` ON (`material`.`matPartNumber` = `lotchange`.`MaterialName`)
                WHERE ChangeDate >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
                ORDER BY ChangeDate DESC';

        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) throw new \Exception("Failed to get Lot Changes logs.");
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function getOvenLogs()
    {
        $sql = 'SELECT * FROM ovenlogs
                WHERE inOvenDate >= DATE_SUB(NOW(), INTERVAL 4 WEEK) 
                Order By updatedAt DESC, inOvenDate ASC';
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) throw new \Exception("Failed to get Oven Logs");
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function getOvenLog($id)
    {
        $sql = 'SELECT * FROM ovenlogs WHERE ovenLogID = :ovenLogID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':ovenLogID', $id);

        try {
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                $this->log->error('Failed to getOvenLog: ERROR-' . $errorInfo);
                throw new \Exception("Failed to get Oven log. ERROR: {$errorInfo}");
            }

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->log->info('returned values: ' . print_r($result, true));
            return $result;
        } catch (\PDOException $e) {
            $this->log->error('Failed to getOvenLog: ERROR-' . $e->getMessage());
            return ["success" => false, "message" => "Failed to getOvenLog: {$e}"];
        }
    }

    public function getLotChange($id)
    {

        try {
            $sql = 'SELECT * FROM lotchange WHERE LotChangeID = :lotchangeID';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':lotchangeID', $id);
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                $this->log->error('Failed to getLotChangeLog: ERROR-' . $errorInfo);
                throw new \Exception("Failed to get Lot Change log. ERROR: {$errorInfo}");
            }
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            $this->log->error('Failed to getLotChangeLog: ERROR-' . $e->getMessage());
            return ["success" => false, "message" => "Failed to getLotChangeLog: {$e}"];
        }
    }

    public function getQaRejectLog($id)
    {
        $this->log->info('QualityModel->getQaRejectLog has been called');
        $sql = 'SELECT * FROM qarejects WHERE qaRejectLogID = :logID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':logID', $id);

        try {
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                $this->log->error('Failed to getQaRejectLog: ERROR-' . $errorInfo);
                throw new \Exception("Failed to get QA Reject log. ERROR: {$errorInfo}");
            }

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $this->log->info('returned values: ' . print_r($result, true));
            return $result;
        } catch (\PDOException $e) {
            $this->log->error('Failed to getQaRejectLog: ERROR-' . $e->getMessage());
            return ["success" => false, "message" => "Failed to getQA Log: {$e}"];
        }
    }

    public function getReceivedShipments()
    {
        $sql = 'SELECT * FROM receivedshipments
                WHERE deliveryDate >= DATE_SUB(NOW(), INTERVAL 12 WEEK) ORDER BY deliveryDate DESC';
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            throw new \Exception("Failed to get received shipments logs. ERROR: {$errorInfo}");
        }
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}
