<?php
//Database connection information

class database
{
    private $host = "localhost";
    private $dbName = "inventory_db";
    private $username = "root";
    private $password = "";


    public $con;

    //Method for returing protect connection
    public function dbConnection()
    {
        $this->con = null;
        try {
            $this->con = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ));
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->con;
    }
}
