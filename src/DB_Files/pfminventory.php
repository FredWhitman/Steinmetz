<?Php
require_once 'database.php';

Class pfminventory
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

    public function insert($partNumber,$qty)
    {
        try
        {
            $stmt = $this-con-prepare("INSERT INTO pfminventory (PartNumber, Qty)
                VALUES(:partNumber,:qty)");
            $stmt->bindparam(":partNumber",$partNumber);
            $stmt->bindparam(":qty",$qty);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Update
    public function update($partNumber,$qty)
    {
        try
        {
            $stmt = $this->con->prepare("UPDATE pfminventory SET PartNumber = :partNumber, SET Qty = :qty WHERE PartNumber = :partNumber");
            
            $stmt->bindparam(":partNumber",$partNumber);
            $stmt->bindparam(":qty",$qty);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Delete
    public function delete($partNumber)
    {
        try
        {
            $stmt = $this->con->prepare("DELETE FROM pfminventory WHERE PartNumber = :partNumber");
            $stmt->bindparam(":partNumber",$partNumber);
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