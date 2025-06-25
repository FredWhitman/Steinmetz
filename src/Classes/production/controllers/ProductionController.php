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
            $this->log->info("prevLog ID: " . $log['PreviousProductionID']);
            $previousLog = $this->model->readPrevious($id);
            
            $response = $log;
            $repsonse['previousLog'] = $previousLog;

            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch production', 'details' => $e->getMessage()]);
        };
    }
}