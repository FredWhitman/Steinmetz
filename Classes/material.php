<?Php
require_once 'database.php';

Class material
{

    private $con;

    // Constructor
    public function __construct()
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

    public function insert($materialPartNumber,$materialName,$productID,$minLbs,$customer)
    {
        try
        {
            $stmt = $this-con-prepare("INSERT INTO material (MaterialPartNumber, MaterialName, ProductID, MinimumLbs, Customer)
                VALUES(:materialPartNumber,:materialName,:productID,:minLbs,:customer)");
            $stmt->bindparam(":materialPartNumber",$materialPartNumber);
            $stmt->bindparam(":materialName",$materialName);
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":minLbs",$minLbs);
            $stmt->bindparam(":customer",$customer);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Update
    public function update($materialPartNumber,$materialName,$productID,$minLbs,$customer)
    {
        try
        {
            $stmt = $this->con->prepare("UPDATE material SET MaterialPartNumber = : materialPartNumber, SET MaterialName = :materialName, SET ProductID = :productID, SET MinimumLbs = :minLbs, SET customer = :customer WHERE MaterialPartNumber = :materialPartNumber");
            
            $stmt->bindparam(":materialPartNumber",$materialPartNumber);
            $stmt->bindparam(":materialName",$materialName);
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":minLbs",$minLbs);
            $stmt->bindparam(":customer",$customer);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Delete
    public function delete($materialPartNumber)
    {
        try
        {
            $stmt = $this->con->prepare("DELETE FROM material WHERE MaterialPartNumber = :materialPartNumber");
            $stmt->bindparam(":materialPartNumber",$materialPartNumber);
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
