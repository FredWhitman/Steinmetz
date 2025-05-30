<?php

require_once 'database.php';

class Last4Weeks extends database{

     // Constructor
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbConnection();
        $this->con = $db;
    }

    public function read4wks(){
        try {
            $sql = 'SELECT * FROM productionLogs WHERE prodDate >= NOW() - INTERVAL 4 WEEK Order By prodDate ASC';
            $stmt = $this->con->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($results);
            return $results;//code...
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

    public function readPrevious($id){
        //this function returns the previous log of the viewed one
        $sql = 'SELECT p.logID, t.*, m.* FROM productionlogs AS p 
                INNER JOIN materiallog AS m ON p.logID = m.prodLogID WHERE p.logID = :id';
                $stmt = $this->con->prepare($sql);
    }


}
