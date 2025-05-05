<?Php
require_once 'database.php';

Class materialinventory
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

    public function insert($materialPartNumber,$lbs)
    {
        try
        {
            $stmt = $this-con-prepare("INSERT INTO materialinventory (MaterialPartNumber, Lbs)
                VALUES(:materialPartNumber,:lbs)");
            $stmt->bindparam(":materialPartNumber",$materialPartNumber);
            $stmt->bindparam(":lbs",$lbs);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Update
    public function update($materialPartNumber,$lbs)
    {
        try
        {
            $stmt = $this->con->prepare("UPDATE materialinventory SET MaterialPartNumber = :materialPartNumber, SET Lbs = :lbs WHERE MaterialPartNumber = :materialPartNumber");
            
            $stmt->bindparam(":materialPartNumber",$materialPartNumber);
            $stmt->bindparam(":lbs",$lbs);
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
            $stmt = $this->con->prepare("DELETE FROM materialinventory WHERE MaterialPartNumber = :materialPartNumber");
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