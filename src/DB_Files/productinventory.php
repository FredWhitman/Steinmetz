<?Php
require_once 'database.php';

Class productInventory
{

    private $con;

    // Constructor
    public function _construct()
    {
        $database = new Database();

        $db = $database->dbconnection();
        $this->con = $db;
    }

    // Execute queries SQL
    public function runQuery($sql)
    {
        $stmt = $this->con->prepare($sql);
        return $stmt;
    }

    public function insert($productID,$partQty,$blenderStartTotal)
    {
        try
        {
            $stmt = $this-con-prepare("INSERT INTO productinventory (ProductID, PartQty, BlenderStartTotal)
                VALUES(:productID,:partQty,:blenderStartTotal)");
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":partQty",$partQty);
            $stmt->bindparam(":amstedPFM",$blenderStartTotal);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Update
    public function update($productID,$partQty,$blenderStartTotal)
    {
        try
        {
            $stmt = $this->con->prepare("UPDATE productinventory SET ProductID = :productID, SET PartQty = :partQty, SET BlenderStartTotal = :blenderStartTotal WHERE ProductID = :productID");
            
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":partQty",$partQty);
            $stmt->bindparam(":amstedPFM",$blenderStartTotal);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Delete
    public function delete($productID)
    {
        try
        {
            $stmt = $this->con->prepare("DELETE FROM productinventory WHERE ProductID = :productID");
            $stmt->bindparam(":productID",$productID);
            //$stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Redirect URL method
    public function redirect($url)
    {
        header("Location: $url");
    }
}

?>