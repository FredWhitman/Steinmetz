<?php

declare(strict_types=1);

namespace Production\Models;

use Psr\Log\LoggerInterface;
use Production\Utilities\ProductionMessage;
use Database\Connection;

class ProductionModel
{
    private \PDO $pdo;
    private LoggerInterface $log;
    private ProductionMessage $util;

    private GetProduction $get;
    private InsertProduction $insert;
    private UpdateProduction $update;

    public function __construct(Connection $dbConnection, LoggerInterface $log, ProductionMessage $util)
    {
        $this->pdo  = $dbConnection->getPDO();
        $this->log  = $log;
        $this->util = $util;

        // ---------------------------------------------------------
        // 1. Create GetProduction (no dependencies)
        // ---------------------------------------------------------
        $this->get = new GetProduction($this->pdo, $this->log);

        // ---------------------------------------------------------
        // 2. Create InsertProduction WITHOUT UpdateProduction
        //    (we inject UpdateProduction afterward)
        // ---------------------------------------------------------
        $this->insert = new InsertProduction($this->pdo, $this->log, $this->get, null);

        // ---------------------------------------------------------
        // 3. Create UpdateProduction WITH InsertProduction
        // ---------------------------------------------------------
        $this->update = new UpdateProduction($this->pdo, $this->log, $this->get, $this->insert);

        // ---------------------------------------------------------
        // 4. Inject UpdateProduction back into InsertProduction
        //    (this avoids circular constructor injection)
        // ---------------------------------------------------------
        $this->insert->setUpdateProduction($this->update);
    }

    // ---------------------------------------------------------
    // PUBLIC API — called by your controller
    // ---------------------------------------------------------

    /**
     * Insert a production log and handle all related operations.
     */
    public function insertProdLog($prodData, $materialData, $tempData)
    {
        // Step 1: Insert the production log + mat logs + temp logs
        $result = $this->insert->insertProdLog($prodData, $materialData, $tempData);

        if (!$result || empty($result['prodRunID'])) {
            return [
                'success' => false,
                'message' => 'InsertProduction::insertProdLog failed.'
            ];
        }

        $prodRunID = $result['prodRunID'];
        $runStatus = $result['runStatus'] ?? null;

        // Step 2: Update production run totals
        $this->update->updateProductionRun($prodRunID);

        // Step 3: If run ended, mark it completed
        if ($runStatus === 'end') {
            $this->update->markProductionRunCompleted($prodRunID);
        }

        return [
            'success' => true,
            'prodRunID' => $prodRunID,
            'prodLogID' => $result['prodLogID'] ?? null,
            'message' => 'Production log inserted successfully.'
        ];
    }

    public function addPurge($productID, $prodDate, $purgeLbs)
    {
        return $this->insert->addPurge($productID, $prodDate, $purgeLbs);
    }

    // ---------------------------------------------------------
    // READ OPERATIONS — passthrough to GetProduction
    // ---------------------------------------------------------

    public function getActiveProdRuns()
    {
        return $this->get->getActiveProdRuns();
    }

    public function getCompletedProdRuns()
    {
        return $this->get->getCompletedProdRuns();
    }

    public function getProdRunID($productID)
    {
        return $this->get->getProdRunID($productID);
    }

    public function getMaterialTotals($prodRunID)
    {
        return $this->get->getMaterialTotals($prodRunID);
    }

    public function read4wks()
    {
        return $this->get->read4wks();
    }

    public function readOne($id)
    {
        return $this->get->readOne($id);
    }

    public function readPrevious($id)
    {
        return $this->get->readPrevious($id);
    }

    public function getProductList()
    {
        return $this->get->getProductList();
    }

    public function getMaterialList()
    {
        return $this->get->getMaterialList();
    }

    public function CheckProductionRuns($productID)
    {
        return $this->get->CheckProductionRuns($productID);
    }

    public function checkLogDates($prodRunID, $prodDate)
    {
        return $this->get->checkLogDates($prodRunID, $prodDate);
    }

    public function getLastMaterialLogForRun($prodRunID)
    {
        return $this->get->getLastMaterialLogForRun($prodRunID);
    }

    public function getProductionlog($productID, $prodDate)
    {
        return $this->get->getProductionlog($productID, $prodDate);
    }

    public function getRunProdLogs($prodRunID)
    {
        return $this->get->getRunProdLogs($prodRunID);
    }

    // ---------------------------------------------------------
    // UTILITIES PASSTHROUGH
    // ---------------------------------------------------------

    public function recalcRunPurgeTotal($prodRunID)
    {
        return $this->update->recalcRunPurgeTotal($prodRunID);
    }
}
