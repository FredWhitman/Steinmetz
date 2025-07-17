<?php

declare(strict_types=1);
//FILE:  src/Database/Connection.php
namespace Database;

use PDO;
use PDOException;

class Connection
{
    private $host = "localhost";
    private $dbName = "inventory_db";
    private $username = "root";
    private $password = "";


    /** @var PDO|null */
    private ?PDO $pdo = null;

    /*  
        Returns a singleton PDO instance
    */
    public function getPDO(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8',
            $this->host,
            $this->dbName
        );
        try {
            $this->pdo = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->dbName,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"

                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage(), (int)$e->getCode());
        }
        return $this->pdo;
    }
}
