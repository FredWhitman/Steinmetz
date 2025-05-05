<?php
require_once 'database.php';

Class lotchange
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
    // Insert

    public function insert($material,$part,$changeDate,$changeTime,$oldLot,$newLot,$comments)
    {
        try
        {
            $stmt = $this-con-prepare("INSERT INTO lotchange (MaterialName, ProductID, ChangeDate, ChangeTime, OldLot, NewLot, Comments) VALUES(:material,:part,:changeDate,:changeTime,:oldLot,:newLot,:comments)");
            $stmt->bindparam(":material",$material);
            $stmt->bindparam(":part",$part);
            $stmt->bindparam(":changeDate",$changeDate);
            $stmt->bindparam(":changeTime",$changeTime);
            $stmt->bindparam(":oldLot",$oldLot);
            $stmt->bindparam(":newlot",$newlot);
            $stmt->bindparam(":comments",$comments);
            $stmt->exeute();

            return $stmt;


        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Update
    public function update($lotchangeid,$material,$part,$changeDate,$changeTime,$oldLot,$newLot,$comments)
    {
        try
        {
            $stmt = $this->con->prepare("UPDATE lotchange SET MateralName = :material, SET ProductID = :part, SET ChangeDate = :changeDate, SET ChangeTime = :changeTime, SET OldLot = :oldLot, SET NewLot = :newLot, SET Comments = :comments WHERE LotChangeID = :lotchangeid");
            
            $stmt->bindparam(":material",$material);
            $stmt->bindparam(":part",$part);
            $stmt->bindparam(":changeDate",$changeDate);
            $stmt->bindparam(":changeTime",$changeTime);
            $stmt->bindparam(":oldLot",$oldLot);
            $stmt->bindparam(":newlot",$newlot);
            $stmt->bindparam(":comments",$comments);
            $stmt->bindparam(":lotchangeid",$lotchangeid);
            $stmt->exeute();

            return $stmt;

        }catch(PDOException $e)
        {
            echo $e-getMessage();
        }
    }

    // Delete
    public function delete($lotchangeid)
    {
        try
        {
            $stmt = $this->con->prepare("DELETE FROM lotchange WHERE LotChangeID = :lotchangeid");
            $stmt->bindparam(":lotchangeid",$lotchangeid);
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