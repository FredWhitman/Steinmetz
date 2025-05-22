<?php
require_once 'database.php';


class DB extends database
{

    // get data to fill material select component
    public function getMaterialNames()
    {
        try {
            $sql = "SELECT MaterialName FROM material";
            $stmt = $this->con->query($sql);
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);

            // Send JSON response
            header("Content-Type: application/json");
            echo json_encode($result);
            var_dump($result);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
