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
     * getProductList function
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
}




/* 
Routes AJAX calls to relevant logic. You can read the action from POST or the $_GET flag:
    action = addLog
    action = getLastLog
    checkRun = 1
    checkLogs = 1 */
