<?Php
require_once 'database.php';

Class products
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

    //Get Part Names
    public Function get_PartNames(){
        try {
            $stmt = $this->con->prepare("SELECT ProductID FROM products");
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {echo $e->getMessage();}
    }

    public function insert($productID,$partName,$minQty,$boxesSkid,$partsBox,$partWeight,$displayOrder,$customer,$productionType)
    {
        try
        {
            $stmt = $this->con->prepare("INSERT INTO products (ProductID, PartName, MinimumQty, BoxesPerSkid, PartsPerBox, PartWeight, displayOrder,customer,productionType)
                VALUES(:productID,:partName,:minQty,:boxesSkid,:partsBox,:partWeight,:displayOder,:customer,:productionType)");
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":partName",$partName);
            $stmt->bindparam(":minQty",$minQty);
            $stmt->bindparam(":boxesSkid",$boxesSkid);
            $stmt->bindparam(":partsBox",$partsBox);
            $stmt->bindparam(":partWeight",$partWeight);
            $stmt->bindparam(":displayOrder",$displayOrder);
            $stmt->bindparam(":customer",$customer);
            $stmt->bindparam(":productionType",$productionType);
            $stmt->exeCute();

            return $stmt;


        }catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    // Update
    public function update($productID,$partName,$minQty,$boxesSkid,$partsBox,$partWeight,$displayOrder,$customer,$productionType)
    {
        try
        {
            $stmt = $this->con->prepare("UPDATE products SET ProductID = :productID, SET PartName = :partName, SET MinimumQty = :minQty, SET BoxesPerSkid = :boxesSkid, SET PartsPerBox = :partsBox, SET PartWeight = :partWeight, SET displayOrder = :displayOrder, SET customer = :customer, SET productionType = : productionType WHERE ProductID = :ProductID");
            
            $stmt->bindparam(":productID",$productID);
            $stmt->bindparam(":partName",$partName);
            $stmt->bindparam(":minQty",$minQty);
            $stmt->bindparam(":boxesSkid",$boxesSkid);
            $stmt->bindparam(":partsBox",$partsBox);
            $stmt->bindparam(":partWeight",$partWeight);
            $stmt->bindparam(":displayOrder",$displayOrder);
            $stmt->bindparam(":customer",$customer);
            $stmt->bindparam(":productionType",$productionType);
            $stmt->execute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    // Delete
    public function delete($productID)
    {
        try
        {
            $stmt = $this->con->prepare("DELETE FROM products WHERE ProductID = :productid");
            $stmt->bindparam(":productid",$productid);
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
