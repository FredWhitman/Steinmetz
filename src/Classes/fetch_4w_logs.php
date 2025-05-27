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


}
