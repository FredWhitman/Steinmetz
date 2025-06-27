<?php

namespace Production\Controllers;

// File: controllers/ProductionController.php

require_once __DIR__ . '/../models/ProductionModel.php';

class ProductionController
{
    private $model;
    private $util;
    private $log;

    public function __construct($model, $util, $log)
    {
        $this->model = $model;
        $this->util  = $util;
        $this->log   = $log;
        $this->log->info("Controller logger test", ['file' => __FILE__]);
    }

    public function read4wks()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('read4wks called to fill table');
            $production = $this->model->read4wks();
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch production logs', 'details' => $e->getMessage()]);
        }

        echo json_encode($production);
    }

    public function viewProdLogs($id)
    {
        header('Content-Type: application/json');

        try {
            $log = $this->model->readOne($id);
            $prevProdLogID = $log['prevProdLogID'];

            $this->log->info("prevLog ID: " . $log['prevProdLogID']);
            $previousLog = $this->model->readPrevious($prevProdLogID);

            $response = $log;
            $repsonse['previousLog'] = $previousLog;

            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch production', 'details' => $e->getMessage()]);
        };
    }

    /**
     * getProductList function return an array of productIDs and partNames
     *
     * @return void  a list of products
     */
    public function getProductList()
    {
        header('Content-Type: application/json');

        try {
            $products = $this->model->getProductList();
            echo json_encode($products);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch product list', 'details' => $e->getMessage()]);
        }
    }

    /**
     * getMaterialList() function return an array of matPartNumber and matName
     *
     * @return void
     */
    public function getMaterialList()
    {
        header('Content-Type: application/json');
        try {
            $materials = $this->model->getMaterialList();
            echo json_encode($materials);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch material list', 'details' => $e->getMessage()]);
        }
    }

    public function checkLogDates($productID, $date)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("checkLogDates called with these values: {$productID} and {$date}.");
            $exists = $this->model->checkLogDates($productID, $date);
            echo json_encode(['exists' => $exists]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to checkLogDate', 'details' => $e->getMessage()]);
        }
    }

    public function checkRun($productID)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("checkRun called with these values: {$productID}.");
            $exists = $this->model->CheckProductionRuns($productID);
            echo json_encode(['exists' => $exists]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to checkRun', 'details' => $e->getMessage()]);
        }
    }

    public function getLastLog($productID)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("getLastLog caleed with this value: {$productID}.");
            $matLog = $this->model->getLastMaterialLogForRun($productID);
            echo json_encode($matLog);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch last material log.', 'details: ' => $e->getMessage()]);
        }
    }

    public function addLog($payload)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("📦 Received payload for addLog()", ['payload' => $payload]);
            // Parse flat POST payload into structured inputs
            $prodData = [
                'productID' => $payload['productID'],
                'prodDate' => $payload['prodDate'],
                'runStatus' => $payload['runStatus'],
                'prevProdLogID' => $payload['prevProdLogID'],
                'runLogID' => $payload['runLogID'],
                'matLogID' => $payload['matLogID'],
                'tempLogID' => $payload['tempLogID'],
                'pressCounter' => $payload['pressCounter'],
                'startUpRejects' => $payload['startUpRejects'],
                'qaRejects' => $payload['qaRejects'],
                'purgeLbs' => $payload['purgeLbs'],
                'comments' => $payload['comments'],
            ];

            $materialData = [
                'mat1' => $payload['materials'][0]['id'],
                'matUsed1' => $payload['materials'][0]['used'],
                'mat2' => $payload['materials'][1]['id'],
                'matUsed2' => $payload['materials'][1]['used'],
                'mat3' => $payload['materials'][2]['id'],
                'matUsed3' => $payload['materials'][2]['used'],
                'mat4' => $payload['materials'][3]['id'],
                'matUsed4' => $payload['materials'][3]['used'],
            ];

            $temp = $payload['temperatures'];
            $tempData = [
                'bigDryerTemp' => $temp['bigDryerTemp'],
                'bigDryerDew' => $temp['bigDryerDew'],
                'pressDryerTemp' => $temp['pressDryerTemp'],
                'pressDryerDew' => $temp['pressDryerDew'],
                't1' => $temp['t1'],
                't2' => $temp['t2'],
                't3' => $temp['t3'],
                't4' => $temp['t4'],
                'm1' => $temp['m1'],
                'm2' => $temp['m2'],
                'm3' => $temp['m3'],
                'm4' => $temp['m4'],
                'm5' => $temp['m5'],
                'm6' => $temp['m6'],
                'm7' => $temp['m7'],
                'chillerTemp' => $temp['chiller'],
                'moldTemp' => $temp['tcuTemp'],
            ];

            $result = $this->model->insertProdLog($prodData, $materialData, $tempData);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add production log.', 'details: ' => $e->getMessage()]);
        }
    }
}
/* 
Routes AJAX calls to relevant logic. You can read the action from POST or the $_GET flag:
    action = addLog
    action = getLastLog
    checkRun = 1
    checkLogs = 1 */
