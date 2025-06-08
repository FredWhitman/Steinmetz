<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'database.php';

class productionDB extends database
{

    // Constructor
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->con = $db;
    }

    public function read4wks()
    {
        try {
            $sql = 'SELECT * FROM productionLogs WHERE prodDate >= NOW() - INTERVAL 4 WEEK Order By prodDate Desc';
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($results);
            return $results; //code...
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Fetch single record from DB
    public function readOne($id)
    {    //returns the viewed log
        $sql = 'SELECT p.*, t.*, m.* FROM productionlogs AS p 
                INNER JOIN templog AS t ON p.logID = t.prodLogID 
                INNER JOIN materiallog AS m ON p.logID = m.prodLogID WHERE p.logID = :id';
        $stmt = $this->con->prepare($sql);
        $stmt->execute(['id' => $id]);

        // fetch for one record not fetchAll
        //columns returned: logID,productID,prodDate,runStatus,preProdLogID,runLogID,matLogID,tempLogID,pressCounter,startUpRejects,purgeLbs,
        //  Comments, bigDryerTemp,bigDryerDew,pressDryerTemp,pressDryerDew,t1,t2,t3,t4,m1,m2,m3,m4,m5,m6,m7,chillerTemp,moldTemp,mat1,matUsed1,mat2,
        //  matUsed2,mat3,matUsed3,mat4,matUsed4,

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //var_dump($result);
        return $result;
    }

    public function readPrevious($id)
    {
        try {
            //this function returns the previous log of the viewed one
            $sql = 'SELECT p.logID, m.* FROM productionlogs AS p 
                    INNER JOIN materiallog AS m ON p.logID = m.prodLogID WHERE p.logID = :id';
            $stmt = $this->con->prepare($sql);
            $stmt->execute(['id' => $id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function insertQaRejects($productID, $prodDate, $rejects, $comments)
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
            $prevRejects = $row['qaRejects'];

            $newTotal = $prevRejects + $rejects;
            //Insert info into qaRejects Table
            $sqlInsert = "INSERT INTO qarejects (prodDate,prodLogID,productID,rejects,comments) 
                            VALUES (:prodDate,:prodLogID,:productID,:rejects,:comments)";
            $stmtInsert = $this->con->prepare($sqlInsert);
            $stmtInsert->bindParam(":prodDate", $prodDate, PDO::PARAM_STR);
            $stmtInsert->bindParam(":prodLogID", $prodLogID, PDO::PARAM_STR);
            $stmtInsert->bindParam(":productID", $productID, PDO::PARAM_INT);
            $stmtInsert->bindParam(":rejects", $rejects, PDO::PARAM_INT);
            $stmtInsert->bindParam(":comments", $comments, PDO::PARAM_INT);
            $InsertResult = $stmtInsert->execute();

            //Update productionLogs table
            $sqlUpdate = 'UPDATE productionlogs SET qaRejects =  qaRejects + :rejects WHERE logID = :prodLogID';
            $stmtUpdateLog = $this->con->prepare($sqlUpdate);
            $stmtUpdateLog->bindParam(":rejects", $rejects, PDO::PARAM_INT);
            $stmtUpdateLog->bindParam(":prodLogID", $prodLogID, PDO::PARAM_INT);
            $stmtUpdateLog->execute();

            $sqlProductUpdate = "UPDATE productInventory SET partQty = partQty - :rejects WHERE productID = :productID";
            $stmtInvUpdate = $this->con->prepare($sqlProductUpdate);
            $stmtInvUpdate->bindParam('rejects', $rejects, PDO::PARAM_INT);
            $stmtInvUpdate->bindParam('productID', $productID, PDO::PARAM_INT);
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

    private function getProductInventory($productID)
    {
        try {
            $sql = "SELECT productID, PartQty FROM productinvnentory WHERE productID = :productID";

            $stmt = $this->con->prepare($sql);
            $stmt->execute([
                ':productID' => $productID
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result;
        } catch (PDOException $e) {
            error_log("ERROR: Failed to get product inventory for {$productID}: " . $e->getMessage());
        }
    }

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
            $stmtUpdate->bindParam(":purge", $purge, PDO::PARAM_STR);
            $stmtUpdate->bindParam(":prodLogID", $prodLogID, PDO::PARAM_INT);
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

    public function AddLotChange()
    {
        error_log("");
    }

    public function insertProdLog($prodData, $materialData, $tempData)
    {
        try {
            $this->con->beginTransaction();
            
            $productID = $prodData['productID'];
            //Check to see if this is a start production run
            if ($prodData['runStatus'] === '1') {
                try {
                    $sql = "INSERT into prodrunlog (productID,startDate) Values (:productID, :prodDate, :runComplete)";
                    $stmt = $this->con->prepare($sql);
                    $stmt->execute([
                        ':productID' => $productID,
                        ':prodDate' => $prodData['prodDate'],
                        ':runComplete' => 'no'
                    ]);
                    $prodRunID = $this->con->lastInsertId();
                } catch (PDOException $e) {
                    
                    error_log('Error submitting a new production run into prodrunlog: ' . $e->getMessage());
                }
                $prevProdLogID = '0';
            } else {
                //use productID to get production run id
                $prodRunID = $this->getProdRunID($productID);
                if(!$prodRunID){ throw new Exception("Failed to get production run ID"); }
                //use prodRunID to get last prodLogID and set $prevProdLogID
                $prevProdLogID = $this->getPrevProdLog($prodRunID);
                error_log('Previous Log ID: ' . $prevProdLogID);
            }

            $prodData['runLogID'] = $prodRunID;
            $prodData['prevProdLogID'] = $prevProdLogID;

            //change runStatus to proper value for insert into produciton DB
            if ($prodData['runStatus'] === '2') {
                $prodData['runStatus'] = 'end';
                error_log('updating runStatus from 2 to ' . $prodData['runStatus']);
            } else if ($prodData['runStatus'] === '1') {
                $prodData['runStatus'] = 'start';
                error_log('updating runStatus from 1 to ' . $prodData['runStatus']);
            } else {
                $prodData['runStatus'] = 'in progress';
                error_log('updating runStatus from 0 to ' . $prodData['runStatus']);
            }

            /////////////////////INSERT PRODUCTIONLOG INSERT START////////////////////////////
            $sqlInsertProdLog = "INSERT INTO productionlogs (productID,prodDate,runStatus,prevProdLogID, runLogID,matLogID,tempLogID,pressCounter,startUpRejects, qaRejects,purgeLbs,Comments) 
                                    VALUES(:productID,:prodDate,:runStatus,:prevProdLogID,:runLogID,:matLogID,:tempLogID,:pressCounter,:startUpRejects, :qaRejects,:purgeLbs,:comments)";
            $stmtInsertProdLog = $this->con->prepare($sqlInsertProdLog);
            $stmtInsertProdLog->bindParam(':productID', $prodData['productID'],  PDO::PARAM_STR);
            $stmtInsertProdLog->bindParam(':prodDate', $prodData['prodDate'],  PDO::PARAM_STR);
            $stmtInsertProdLog->bindParam(':runStatus', $prodData['runStatus'],  PDO::PARAM_STR);
            $stmtInsertProdLog->bindParam(':prevProdLogID', $prodData['prevProdLogID'],  PDO::PARAM_INT);
            $stmtInsertProdLog->bindParam(':runLogID', $prodData['runLogID'],  PDO::PARAM_INT);
            $stmtInsertProdLog->bindParam(':matLogID', $prodData['matLogID'],  PDO::PARAM_INT);
            $stmtInsertProdLog->bindParam(':tempLogID', $prodData['tempLogID'],  PDO::PARAM_INT);
            $stmtInsertProdLog->bindParam(':pressCounter', $prodData['pressCounter'],  PDO::PARAM_INT);
            $stmtInsertProdLog->bindParam(':startUpRejects', $prodData['startUpRejects'],  PDO::PARAM_INT);
            $stmtInsertProdLog->bindParam(':qaRejects', $prodData['qaRejects'],  PDO::PARAM_INT);
            $stmtInsertProdLog->bindParam(':purgeLbs', $prodData['purgeLbs'],  PDO::PARAM_STR);
            $stmtInsertProdLog->bindParam(':comments', $prodData['comments'],  PDO::PARAM_STR);

            //$stmtInsertProdLog->execute();
            if (!$stmtInsertProdLog->execute()) {
                error_log('Production log insert failed: ' . print_r($stmtInsertProdLog->errorInfo(), true));
                throw new Exception("Production log insert failed.");
            }

            $prodLogID = $this->con->query("SELECT LAST_INSERT_ID()")->fetchColumn();

            if (!$prodLogID) throw new Exception("Failed to insert into productionlogs");

            $materialData["prodLogID"] = $prodLogID;
            error_log('prodLogID from $materialData["prodLogID"]: ' . $materialData["prodLogID"]);
            /////////////////////INSERT PRODUCTIONLOG INSERT END//////////////////////////////////

            /////////////////////////////////////////////////////////////////////////////////////  

            ////////////////////INSERT MATERIALLOG START//////////////////////////////////////////
            $sqlInsertMaterialLog = "INSERT INTO materialLog (prodLogID,mat1,matUsed1,mat2,matUsed2,mat3,matUsed3,mat4,matUsed4) 
                                        VALUES (:prodLogID,:mat1,:matUsed1,:mat2,:matUsed2,:mat3,:matUsed3,:mat4,:matUsed4)";
            $stmtInsertMatLog = $this->con->prepare($sqlInsertMaterialLog);
            $stmtInsertMatLog->execute($materialData);
            //error_log('Executing $stmtInsertMatLog Query: ' . $stmtInsertMatLog->queryString);

            $matLogID = $this->con->query("SELECT LAST_INSERT_ID()")->fetchColumn();

            if (!$matLogID) throw new Exception("Failed to insert into materialLog or set matLogID.");
            ////////////////////INSERT MATERIALLOG END/////////////////////////////////////////////

            /////////////////////////////////////////////////////////////////////////////////////  
            //insert tempLog return logID and set $tempLogID to this value
            $tempData['prodLogID'] = $prodLogID;
            $sqlInsertTempLog = "INSERT INTO tempLog (prodLogID,bigDryerTemp,bigDryerDew,pressDryerTemp,pressDryerDew, t1,t2,t3,t4,m1,m2,m3,m4,m5,m6,m7,chillerTemp,moldTemp)
                                    VALUES(:prodLogID,:bigDryerTemp,:bigDryerDew,:pressDryerTemp,:pressDryerDew,:t1,:t2,:t3,:t4,:m1,:m2,:m3,:m4,:m5,:m6,:m7,:chillerTemp,:moldTemp)";

            $stmtInsertTempLog = $this->con->prepare($sqlInsertTempLog);
            $stmtInsertTempLog->execute($tempData);
            //error_log('Executing Query: ' . $stmtInsertTempLog->queryString);
            //error_log('Temp Log Query Debug: ' . $stmtInsertTempLog->debugDumpParams());

            $tempLogID = $this->con->query("SELECT LAST_INSERT_ID()")->fetchColumn();

            if (!$tempLogID) throw new Exception('Failed to insert tempLog.');
            ////////////////////////////INSERT TEMPLOG END/////////////////////////////////////////////

            //update productionLog with $matLogID & tempLogID
            $sqlUpdateProdLog = "UPDATE productionlogs SET matLogID = :matLogID, tempLogID = :tempLogID WHERE logID = :prodLogID";
            $stmtUpdateProdLog = $this->con->prepare($sqlUpdateProdLog);
            $stmtUpdateProdLog->execute([
                ':matLogID' => $matLogID,
                ':tempLogID' => $tempLogID,
                ':prodLogID' => $prodLogID
            ]);

            // check to see if this is the end of the production run and fetch material data for the run
            // and update prodrunlog with totals, end date and completed
            if ($prodData['runStatus'] === 'end') {
                error_log('End of prodcution run detected aquiring run production totals');
                //Insert values into prodrunLog
                $totals = $this->getMaterialTotals($prodRunID);

                if (!$totals) throw new Exception('Failed to get production run totals from getMaterialTotals');
                error_log("\$Totals - Data collected: " . print_r($totals, true));
                error_log("Material totals collected preparing the update!");
                $sqlProdRunLogUpdate = "UPDATE prodrunlog SET endDate = :endDate, mat1Lbs = :mat1Lbs, mat2Lbs = :mat2Lbs, mat3Lbs = :mat3Lbs, mat4Lbs = :mat4Lbs, partsProduced = :produced, startUpRejects= :startUpRejects, qaRejects=:qaRejects,purgelbs = :purge, runComplete = :runComplete WHERE logID = :prodRunID";
                $stmtProdlogUpdate = $this->con->prepare($sqlProdRunLogUpdate);
                $result = $stmtProdlogUpdate->execute([
                    ':endDate' => $totals['prodDate'],
                    ':mat1Lbs' => $totals['total_matUsed1'],
                    ':mat2Lbs' => $totals['total_matUsed2'],
                    ':mat3Lbs' => $totals['total_matUsed3'],
                    ':mat4Lbs' => $totals['total_matUsed4'],
                    ':produced' => $totals['total_produced'],
                    ':startUpRejects' => $totals['total_startUpRejects'],
                    ':qaRejects' => $totals['total_qaRejects'],
                    ':purge' => $totals['total_total_purgeLbs'],
                    ':prodRunID' => $prodRunID,
                    ':runComplete'=> 'yes' 
                ]);
                if (!$result) throw new Exception('Failed to update production run log.');
            }

            //Update product Inventory
            $partsToAdd = $prodData['pressCounter'] - $prodData['startUpRejects'];
            $sqlInventoryUpdate = 'UPDATE productInventory SET partQty = partQty + :partsToAdd WHERE productID = :productID';
            $stmtUpdateInventory = $this->con->prepare($sqlInventoryUpdate);
            $result = $stmtUpdateInventory->execute(['partsToAdd' => $partsToAdd, 'productID' => $productID]);
            if(!$result)throw new Exception("Failed to update product inventory!");

            $this->con->commit();
            return ["success" => true, "message" => "Transaction completed successfully.", "prodLogID" => $prodLogID];
        } catch (PDOException $e) {
            $this->con->rollBack();
            error_log('PDO Rollback');
            error_log('ERROR PDO TRANSACTION FAILED FOR insertProdLog: ' . $e->getMessage());
        }
    }

    //check production logs for the date of production log about to be added.
    public function checkLogDates($productID, $prodDate)
    {
        try {
            $sql = 'SELECT COUNT(*) FROM productionLogs WHERE productID = :productID AND prodDate = :prodDate';
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':productID', $productID, PDO::PARAM_STR);
            $stmt->bindParam(':prodDate', $prodDate);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return ($count > 0);
        } catch (PDOException $e) {
            error_log("Error checking production date: " . $e->getMessage());
            return false;
        }
    }

    //check to see if there is an open production run for the submitted productID
    public function CheckProductionRuns($productID){
        error_log('productionDB_SQL->CheckProductionRuns Called');
        $sql = "SELECT logID, productID, runComplete FROM prodrunlog WHERE productID = :productID AND runComplete = :runComplete";
        $stmt = $this->con->prepare($sql);
        $no = 'no';
        $stmt->bindParam(':productID', $productID , PDO::PARAM_STR);
        $stmt->bindParam(':runComplete',$no, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        error_log('CheckRun $result: '. $result);
        return ($result > 0);

    }
    
    //Passing the production run id and return the material,productIDs, qarejects, purge totals for the production run.
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
            $stmtGetTotals->bindParam(':prodRunID', $prodRunID, PDO::PARAM_INT);
            $stmtGetTotals->execute();
            $result = $stmtGetTotals->fetch(PDO::FETCH_ASSOC);
            if(!$result)throw new Exception("Error: productionDB_SQL->getMaterialTotals for prodRunID logID: " .$prodRunID);
            error_log("Retrieved production totals for end of run and passed to insert function.");
            return $result;
        } catch (PDOException $e) {
            error_log("ERROR: Failed to get production totals for end of run: " . $e->getMessage());
        }
    }

    //This function witll return the material info ffrom the previous production log
    //so that daily used can be filled out on the add productionlog form
    public function getLastMaterialLogForRun($productID)
    {
        $prodRunID = $this->getProdRunID($productID);

        error_log('getLastMaterialLogForRun->productID = ' . $productID);
        error_log('getLastMaterialLogForRun->prodRunID = ' . $prodRunID);

        $prodLogID = $this->getPrevProdLog($prodRunID);
        error_log('getLastMaterialLogForRun->prodLogID = ' . $prodLogID);
        try {
            $sql = 'SELECT * FROM materialLog WHERE prodLogID = :prodLogID';
            $stmt = $this->con->prepare($sql);
            $stmt->bindParam(':prodLogID', $prodLogID,  PDO::PARAM_STR);
            $stmt->execute();
            //$rowCount = $stmt->rowCount();
            //error_log('getLastMaterialLogForRun->row.rowcount: ' . print_r($rowCount));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                error_log("No matching row found for prodLogID: " . $prodLogID);
                return []; // Explicitly return null instead of 1
            }
            return $row;
        } catch (PDOException $e) {
            error_log("ERROR: Failed to get materialLog for the production log: " . $e->getMessage());
        }
    }

    //This will return the logID of the production run no completed based on the part number.
    private function getProdRunID($productID)
    {
        try {
            //get current prodRunID and set $prodRunID
            $sqlGetRunID = 'SELECT logID, productID, runComplete FROM prodrunlog WHERE  productID = :prodID AND runComplete  = "no" ';
            $stmtGetRunID = $this->con->prepare($sqlGetRunID);
            $result = $stmtGetRunID->execute(['prodID' => $productID]);
            if ($result) {
                // Fetch data properly
                $row = $stmtGetRunID->fetch(PDO::FETCH_ASSOC);
                $logID = $row['logID'];
                return $logID;
            } else {
                return 0;
            }
        } catch (PDOException $e) {
            echo 'Error getting prod run ID: ' . $e->getMessage();
        }
    }

    //returns  productionlog ID based on the production Run ID 
    private function getPrevProdLog($prodRunID)
    {
        try {
            $sqlGetPrevLog = "SELECT logID, runLogID FROM productionlogs WHERE runLogID  = :prodRunID ORDER BY logID DESC LIMIT 1";

            $stmtGetPrevLog = $this->con->prepare($sqlGetPrevLog);
            $stmtGetPrevLog->execute(['prodRunID' => $prodRunID]);

            $row = $stmtGetPrevLog->fetch(PDO::FETCH_ASSOC);
            $prevLogID = $row['logID'];

            return $prevLogID;
        } catch (PDOException $e) {
            error_log('Error: Getting Previous log ID for production log insert: ' . $e->getMessage());
        }
    }

    //Returns logID, qaRejects , productID and prodDate so that QA Rejects can be added to a production logs
    private function getProductionlog($productID, $prodDate)
    {
        try {
            $sql = 'SELECT logID, qaRejects,productID, prodDate FROM `productionlogs` WHERE productID = :productID AND prodDate = :prodDate';
            $stmt = $this->con->prepare($sql);
            $stmt->execute([
                'productID' => $productID,
                'prodDate' => $prodDate
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
}
