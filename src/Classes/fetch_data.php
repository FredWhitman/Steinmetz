<?php
require_once 'database.php';


class DB extends database
{


    // Constructor
    public function __construct()
    {
        $database = new Database();
        $db = $database->dbconnection();
        $this->con = $db;
    }

    // get data to fill material select component
    public function getMaterialNames()
    {
        try {
            $sql = "SELECT MaterialPartNumber, MaterialName FROM material";
            $stmt = $this->con->query($sql);
            $materialNames = $stmt->fetchall(PDO::FETCH_ASSOC);

            $sql2 = "SELECT ProductID, PartName FROM products";
            $stmt2 = $this->con->query($sql2);
            $partNames = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            // Send JSON response
            header("Content-Type: application/json");
            ob_clean(); //clears any accident output before JSON
            echo json_encode(
                [
                    "materials" => $materialNames,
                    "partNames" => $partNames
                ]
            );

            exit;
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
            echo $e->getMessage();
        }
    }
}

$db = new DB();
$db->getMaterialNames();
