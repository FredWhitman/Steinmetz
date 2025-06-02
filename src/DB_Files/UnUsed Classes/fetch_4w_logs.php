<?php

require_once 'database.php';

class Last4Weeks extends database
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
            $sql = 'SELECT * FROM productionLogs WHERE prodDate >= NOW() - INTERVAL 4 WEEK Order By prodDate ASC';
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
            $row = $this->getProductionlog($productID,$prodDate);

            if(!$row){
                error_log("Error:  nothing was returned for previous log!");
                $this->con->rollback(); //revert changes
                return false;
            }

            $prodLogID = $row['logID'];
            $prevRejects =$row['qaRejects'];

            $newTotal = $prevRejects+$rejects;
            //Insert info into qaRejects Table
            $sqlInsert = "INSERT INTO qarejects (prodDate,prodLogID,productID,rejects,comments) 
                            VALUES (:prodDate,:prodLogID,:productID,:rejects,:comments)";
            $stmtInsert = $this->con->prepare($sqlInsert);
            $stmtInsert->bindParam(":prodDate",$prodDate,PDO::PARAM_STR);
            $stmtInsert->bindParam(":prodLogID",$prodLogID,PDO::PARAM_STR);
            $stmtInsert->bindParam(":productID",$productID,PDO::PARAM_INT);
            $stmtInsert->bindParam(":rejects",$rejects,PDO::PARAM_INT);
            $stmtInsert->bindParam(":comments",$comments,PDO::PARAM_INT);
            $stmtInsert->execute();

            //Update productionLogs table

            $sqlUpdate = 'UPDATE productionlogs SET qaRejects =  qaRejects + :rejects WHERE logID = :prodLogID';
            $stmtUpdate= $this->con->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":rejects",$rejects,PDO::PARAM_INT);
            $stmtUpdate->bindParam(":prodLogID",$prodLogID,PDO::PARAM_INT);
            $stmtUpdate->execute();

            if ($stmtUpdate->rowCount()===0) {
                $this->con->rollback();
                error_log("Transaction Failed: QA Rejects were not added and productionlogs qarejects was not updated.");
                return false;
            }else{
                //Commit transaction
                $this->con->commit();
                error_log("Transaction successful: QA Rejects added and productionlogs qarejects updated.");
                return true; 
            }
        } catch (PDOException $e) {
            $this->con->rollback();
            error_log("QA Rejects Transaction failed: " .$e->getMessage());

        }
    }

    public function addPurge($productID, $prodDate, $purge)
    {
        try {
            $this->con->beginTransaction();
            $row = $this->getProductionlog($productID,$prodDate);

            if(!$row){
                error_log("Error:  nothing was returned for previous log!");
                $this->con->rollback(); //revert changes
                return false;
            }

            $prodLogID = $row['logID'];

            //Update productionLogs table

            $sqlUpdate = 'UPDATE productionlogs SET purgelbs =  purgelbs + :purge WHERE logID = :prodLogID';
            $stmtUpdate= $this->con->prepare($sqlUpdate);
            $stmtUpdate->bindParam(":purge",$purge,PDO::PARAM_STR);
            $stmtUpdate->bindParam(":prodLogID",$prodLogID,PDO::PARAM_INT);
            $stmtUpdate->execute();

            if($stmtUpdate->rowCount()=== 0){
                error_log("Transaction Failed: productionlogs purge update.");
                $this->con->rollback();
                return false;
            }else{
                //Commit transaction
                $this->con->commit();
                error_log("Transaction successful: productionlogs purge updated.");
                return true;
            }
        } catch (PDOException $e) {
            echo error_log('Error adding purge to production log: ' . $e->getMessage());
        }
    }

    private function getProductionlog($productID, $prodDate){
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
