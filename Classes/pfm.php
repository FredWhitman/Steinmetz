<?Php
require_once 'database.php';

Class pfm
{

    private $con;

    // Constructor
    public function __construct()
    {
        $database = new database();

        $db = $database->dbconnection();
        $this->con = $db;
    }

    // Execute queries SQL
    public function runQuery($sql){
        $stmt = $this->con->prepare($sql);
        return $stmt;
      }

    public function insert($partNumber,$partName,$productID,$minQty,$amstedPFM)
    {
        try
        {
            $stmt = $this->con->prepare("INSERT INTO pfm (PartNumber, PartName, ProductID, MinimumQty, AmstedPFM)
                VALUES(:partNumber,:materialName,:productID,:minQty,:amstedPFM)");
            $stmt->bindparam(":partNumber",$partNumber);
            $stmt->bindparam(":partName",$partName);
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":minQty",$minQty);
            $stmt->bindparam(":amstedPFM",$amstedPFM);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    // Update
    public function update($pfmID, $partNumber,$partName,$productID,$minQty,$amstedPFM)
    {
        try
        {
            $stmt = $this->con->prepare("UPDATE pfm SET PartNumber = :partNumber, SET PartName = :partName, SET ProductID = :productID, SET MinimumQty = :minQty, SET AmstedPFM = :amstedPFM WHERE PFMID = :pfmID");
            
            $stmt->bindparam(":pfmID",$pfmID);
            $stmt->bindparam(":partNumber",$partNumber);
            $stmt->bindparam(":partName",$partName);
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":minQty",$minQty);
            $stmt->bindparam(":amstedPFM",$amstedPFM);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    // Delete
    public function delete($pfmID)
    {
        try
        {
            $stmt = $this->con->prepare("DELETE FROM pfm WHERE PFMID = :pfmID");
            $stmt->bindparam(":pfmID",$pfmID);
            //$stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    // Redirect URL method
    public function redirect($url)
    {
        header("Location: $url");
    }
}

?>