<?Php
require_once 'database.php';

class viewData
{
    private $con;

    // Constructor
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbconnection();
        $this->con = $db;
    }

    public function getLogByDate($partNumber, $logDate)
    {

        try 
        {
            $stmt = $this->con->prepare("SELECT * FROM productionlogs JOIN dailyusage ON dailyusage.`ProductionLogID` = productionlogs.`ProductionID` WHERE productionlogs.`ProductID` = " . $partNumber . " AND productionlogs.`ProductionDate` = " .$logDate);
            $stmt->execute();
            $rowCount = $stmt->rowCount();

            //if Statement to return 
            return ($rowCount !== 0 )? $stmt : null;

            /*if ($stmt->rowCount() > 0){return $stmt;} else {return null;echo "$stmt returns NULL";}*/

        } catch (PDOException $e) 
        {
            echo $e->getMessage();
        }
    }

    


}
?>