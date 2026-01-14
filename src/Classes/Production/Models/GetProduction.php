<?php

namespace Production\Models;

class GetProduction
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


    // ------- Production Log Retrieval -------
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
    {
        //returns the viewed log
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

    public function getProdLogID($productID, $prodDate)
    {
        $sql = "SELECT logID FROM productionlogs WHERE productID = :productID AND prodDate = :prodDate";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'productID' => $productID,
            'prodDate' => $prodDate
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) throw new \Exception("Failed to get production log ID for {$productID} made on {$prodDate}.");
        return $result['logID'];
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
    public function getPrevProdLog($prodRunID)
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
     * getRunProdLogs
     * This function will return all production logs for a given production run ID
     * 
     * @param  mixed $prodRunID
     * @return void
     */
    public function getRunProdLogs($prodRunID)
    {
        $sql = "SELECT 
                    *
                FROM
                    productionlogs pl
                WHERE
                    pl.runLogID = :prodRunID
                ORDER BY
                    pl.prodDate ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!$result) throw new \Exception("Error: productionDB_SQL->getProdLogsForRun for prodRunID logID: " . $prodRunID);
        $this->log->info("Retrieved production logs for production run ID: {$prodRunID}.");
        return $result;
    }
    
    // ------- Production Run Retrieval -------
    /**
     * getProdRunID
     * This will return the logID of the production run no completed based on the part number.   
     *  
     * @param  mixed $productID
     * @return void
     */
    public function getProdRunID($productID)
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

    public function getCompletedProdRuns()
    {
        $sql = "SELECT * FROM prodrunlog WHERE runComplete = 'yes' ORDER BY startDate DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getActiveProdRuns()
    {
        $sql = "SELECT * FROM prodrunlog WHERE runComplete = 'no' ORDER BY startDate DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
    public function getMaterialTotals($prodRunID)
    {

        $sql = "SELECT
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
                    WHERE runLogID = :prodRunID
                ) AS totals
                JOIN
                (
                    SELECT
                    pl.prodDate,
                    pl.productID,
                    ml.matUsed1,
                    ml.matUsed2,
                    ml.matUsed3,
                    ml.matUsed4
                    FROM productionlogs pl
                JOIN materiallog ml ON pl.matLogID = ml.matLogID
                    WHERE pl.runLogID = :prodRunID
                    ORDER BY pl.prodDate DESC
                    LIMIT 1
                ) AS lastRecord ON 1 = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

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

    //  ------- Inventory Lookup Functions -------
    /**
     * getMaterialLbs
     *
     * @param  mixed $matPartNumber
     * @return void
     */
    public function getMaterialLbs($matPartNumber)
    {
        $sql = 'SELECT matLbs, matPartNumber FROM materialinventory WHERE matPartNumber = :matPartNumber';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':matPartNumber', $matPartNumber, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) throw new \Exception("Failed to get {$matPartNumber} inventory amount!");
        $lbs = (float)$result['matLbs'];
        return $lbs;
    }

    /**
     * getPFMQty
     *
     * @param  mixed $partNumber
     * @return void
     */
    public function getPFMQty($partNumber)
    {
        $sql = 'SELECT partNumber, qty FROM pfmInventory WHERE partNumber = :partNumber';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partNumber', $partNumber, \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$result) throw new \Exception("Failed to get PFM Qty for {$partNumber}.");
        $qty = $result['qty'];

        return  (int)$qty;
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
        $stmt->execute([':productID' => $productID]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->log->info("Current inventory for {$productID} using getInvQty() function.");
        if (!$row) throw new \Exception("Failed to get qty for {$productID}.");
        return (int)$row['partQty'];
    }

    //  ------- Lists for form selects -------

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

    /**
     * getMaterialList returns a list of matPartNumber and matName
     *
     * @return void
     */
    public function getMaterialList()
    {
        try {
            $sql = 'SELECT matPartNumber, matName, displayOrder FROM material Order By displayOrder ASC';
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
