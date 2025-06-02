<?Php
require_once 'database.php';

class Inventory
{
    private $con;

    // Constructor
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbconnection();
        $this->con = $db;
    }

    //Get Parts Inventory
    public function get_Parts()
    {
        try {
            $stmt = $this->con->prepare("SELECT products.`ProductID`, products.`PartName`,products.`MinimumQty`,products.`customer`,products.`displayOrder`,productinventory.`ProductID`, productinventory.`PartQty` 
                                         FROM products JOIN productinventory ON productinventory.`ProductID` = products.`ProductID` 
                                         WHERE products.`customer` = 'Amsted'ORDER BY products.`displayOrder` ASC");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt;
                echo $stmt->rowCount();
            } else {
                return null;
                echo "$stmt returns NULL";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Get Material Inventory
    public function get_Material()
    {
        try {
            $material = $this->con->prepare("SELECT material.`MaterialPartNumber`, material.`MaterialName`, material.`Minimumlbs`,`materialINVENTORY`.lbs 
                                             FROM Material JOIN `MaterialINVENTORY` ON `MaterialINVENTORY`.`materialpartnumber`=`Material`.materialpartnumber;");
            $material->execute();

            if ($material->rowcount() > 0) {
                return $material;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    // Get inventory of PFMs        
    public function get_Pfms()
    {
        $pfm = $this->con->prepare("SELECT pfm.`PARTNUMBER`, pfm.`PARTNAME`, pfm.`PRODUCTID`,pfm.`MINIMUMQTY`,PFMINVENTORY.`Qty` 
                                    FROM PFM JOIN PFMINVENTORY ON PFMINVENTORY.`partnumber`=pfm.`partnumber`");
        $pfm->execute();
        if ($pfm->rowcount() > 0) {
            return $pfm;
        }
    }

    public function getLogByDate($partNumber, $logDate)
    {
        $log = $this->con->prepare("SELECT * from productionlogs JOIN dailyusage ON dailyusage.`ProductionLogID` = productionlogs.`ProductionID` 
                                    WHERE productionlogs.`ProductID` = " . $partNumber . "AND productionlogs.`ProductionDate` = " . $logDate);

        if ($log->rowcount() > 0) {
            echo "log returned";
            return $log;
        }
    }

    // Redirect URL method
    public function redirect($url)
    {
        header("Location: $url");
    }
}
