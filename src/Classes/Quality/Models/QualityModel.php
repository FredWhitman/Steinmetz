<?php
//FILE: src/Classes/quality/Models/QualityModel.php
declare(strict_types=1);
namespace Quality\Models;

use Psr\Log\LoggerInterface;
use Database\Connection;

class QualityModel
{
    private \PDO $pdo;
    private LoggerInterface $log;

    public function __construct(Connection $dbConnection, LoggerInterface $log)
    {
        $this->pdo = $dbConnection->getPdo();
        $this->log = $log;
    }

}