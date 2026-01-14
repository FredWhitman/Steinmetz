<?php
// File: src/Classes/Production/Models/UpdateProduction.php

namespace Production\Models;

class UpdateProduction
{
    private $pdo;
    private $log;
    private GetProduction $get;
    private InsertProduction $insert;

    public function __construct($pdo, $log, GetProduction $get, InsertProduction $insert)
    {
        $this->pdo = $pdo;
        $this->log = $log;
        $this->get = $get;
        $this->insert = $insert;
    }

    //-------------------------------------------------
    //  ------- Inventory Updates ---------------------
    //-------------------------------------------------

    /**
     * updatePFMInventory
     *
     * @param  mixed $partNumber
     * @param  mixed $amount
     * @param  mixed $operator
     * @return void
     */
    public function updatePFMInventory($partNumber, $amount, $operator)
    {
        $oldStock = $this->get->getPFMQty($partNumber);
        $sql = 'UPDATE pfmInventory SET qty = :newQty WHERE partNumber = :partNumber';
        ($operator === '+') ? $newQty = $amount + $oldStock : $newQty = $oldStock - $amount;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':partNumber', $partNumber, \PDO::PARAM_STR);
        $stmt->bindParam(':newQty', $newQty, \PDO::PARAM_INT);

        if (!$stmt->execute()) throw new \Exception("Failed to update {$partNumber}.");
    }

    /**
     * updateProductInventory
     *
     * @param  mixed $productID
     * @param  mixed $transAmount
     * @param  mixed $oldStock
     * @param  mixed $operator
     * @return void
     */
    public function updateProductInventory($productID, $qty, $operator)
    {
        $op = $op = ($operator === "+") ? '+' : '-';
        $sql = "UPDATE productInventory SET partQty = partQty {$op} :qty WHERE productID = :productID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':qty', $qty, \PDO::PARAM_INT);
        $stmt->bindParam(':productID', $productID, \PDO::PARAM_STR);

        if (!$stmt->execute()) throw new \Exception("Failed to update product inventory for {$productID}.");
    }

    /**
     * updateMaterialInventory
     *
     * @param  mixed $matPartNumber
     * @param  mixed $matLbs
     * @param  mixed $oldStock
     * @param  mixed $operator
     * @return void
     */
    public function updateMaterialInventory($materialData, $operator)
    {
        $prodLogID = $materialData['prodLogID'];
        $op = ($operator === "+") ? '+' : '-';

        $matData = $materialData;

        $transMaterialData = array(
            "action" => 'insertProdLog',
            "inventoryID" => "",
            "inventoryType" => 'material',
            "prodLogID" => $prodLogID,
            "oldStockCount" => "",
            "transAmount" => "",
            "transType" => "production log",
            "transComment" => "",
        );

        /*  loop throw materials and update material inventory
            Insert trans record for each material */
        for ($i = 1; $i <= 4; $i++) {

            $matId = $matData["mat{$i}"];
            $used = floatval($matData["matDailyUsed{$i}"] ?? 0);

            if (!$matId || $used <= 0) continue;

            $oldMatStock = $this->get->getMaterialLbs($matId);
            $transMaterialData['oldStockCount'] = $oldMatStock;
            $transMaterialData['inventoryID'] = $matId;
            $transMaterialData['transAmount'] = $used;

            $sql = "UPDATE materialinventory 
                    SET matLbs = matlbs {$op} :matLbs 
                    WHERE matPartNumber = :matPartNumber";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':matLbs', $used, \PDO::PARAM_STR);
            $stmt->bindParam(':matPartNumber', $matId, \PDO::PARAM_STR);

            if (!$stmt->execute()) throw new \Exception("Failed to update material inventory for {$matId}.");

            $this->insert->insertTrans($transMaterialData);
        }
    }

    //-------------------------------------------------
    //--------------- Log Linking ---------------------
    //-------------------------------------------------
    /**
     * updateMatLogProdLogID
     *
     * @param  mixed $materialData
     * @return void
     */
    public function updateMatLogProdLogID($materialData)
    {

        $sql = 'UPDATE materiallog SET prodLogID = :prodLogID WHERE matLogID = :logID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':prodLogID', $materialData['prodLogID'], \PDO::PARAM_INT);
        $stmt->bindParam(':logID', $materialData['logID'], \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $this->log->error('material log update failed: ' . print_r($stmt->errorInfo(), true));
            throw new \Exception("Material log update failed.");
        }
    }

    /**
     * updateTempLogProdLogID
     *
     * @param  mixed $tempData
     * @return void
     */
    public function updateTempLogProdLogID($tempData)
    {
        $sql = 'UPDATE tempLog SET prodLogID = :prodLogID WHERE tempLogID = :logID';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':logID', $tempData['logID'], \PDO::PARAM_INT);
        $stmt->bindParam(':prodLogID', $tempData['prodLogID'], \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $this->log->error('temp log update failed: ' . print_r($stmt->errorInfo(), true));
            throw new \Exception("Production log insert failed.");
        }
    }

    //-------------------------------------------------
    //  ------- Production Run Updates ----------------
    //-------------------------------------------------

    /**
     * updateProductionRun
     *
     * @param  mixed $prodRunID
     * @param  mixed $runComplete
     * @return void
     */
    public function updateProductionRun($prodRunID)
    {
        $this->log->info("updateProductionRun called with prodRunID: {$prodRunID}");
        $totals = $this->get->getMaterialTotals($prodRunID);

        if (!$totals) throw new \Exception('Failed to get production run totals from getMaterialTotals');
        $sql = "UPDATE prodrunlog 
                    SET 
                        mat1Lbs = :mat1Lbs, 
                        mat2Lbs = :mat2Lbs, 
                        mat3Lbs = :mat3Lbs, 
                        mat4Lbs = :mat4Lbs, 
                        partsProduced = :produced, 
                        startUpRejects= :startUpRejects, 
                        qaRejects=:qaRejects,
                        purgelbs = :purge 
                    WHERE logID = :prodRunID";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':mat1Lbs', $totals['matUsed1'], \PDO::PARAM_STR);
        $stmt->bindParam(':mat2Lbs', $totals['matUsed2'], \PDO::PARAM_STR);
        $stmt->bindParam(':mat3Lbs', $totals['matUsed3'], \PDO::PARAM_STR);
        $stmt->bindParam(':mat4Lbs', $totals['matUsed4'], \PDO::PARAM_STR);
        $stmt->bindParam(':produced', $totals['totalPressCounter'], \PDO::PARAM_STR);
        $stmt->bindParam(':startUpRejects', $totals['totalStartUpRejects'], \PDO::PARAM_STR);
        $stmt->bindParam(':qaRejects', $totals['totalQARejects'], \PDO::PARAM_STR);
        $stmt->bindParam(':purge', $totals['totalPurgeLbs'], \PDO::PARAM_STR);
        $stmt->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_STR);

        $result = $stmt->execute();
        if (!$result) throw new \Exception('Failed to update production run log.');
    }

    public function markProductionRunCompleted($prodRunID)
    {
        $this->log->info("markProductionRunCompleted called for prodRunID: {$prodRunID}");

        // Get the last production date from productionlog 
        $sqlLastDate = "SELECT prodDate 
                        FROM productionlogs 
                        WHERE logID = :prodRunID 
                        ORDER BY prodDate DESC, logID 
                        DESC LIMIT 1 ";

        $stmtLast = $this->pdo->prepare($sqlLastDate);
        $stmtLast->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_INT);
        $stmtLast->execute();
        $row = $stmtLast->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \Exception("Failed to fetch lastProdDate for prodRunID {$prodRunID}");
        }

        $lastProdDate = $row['prodDate'];

        // Mark the run as completed 
        $sql = "UPDATE prodrunlog 
                SET runComplete = 'yes', endDate = :endDate 
                WHERE logID = :prodRunID";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':endDate', $lastProdDate, \PDO::PARAM_STR);
        $stmt->bindParam(':prodRunID', $prodRunID, \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            throw new \Exception("Failed to mark production run completed for prodRunID {$prodRunID}");
        }

        $this->log->info("Production run {$prodRunID} marked as completed.");
    }

    public function recalcRunPurgeTotal($prodRunID)
    {
        $sql = "UPDATE prodrunlog pr 
                SET pr.purgeLbs = ( 
                SELECT COALESCE(SUM(pl.purgeLbs), 0) 
                FROM productionlogs pl WHERE pl.runLogID = pr.logID ) WHERE pr.logID = :prodRunID";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':prodRunID', $prodRunID, \PDO::PARAM_INT);

        return $stmt->execute();
    }
}
