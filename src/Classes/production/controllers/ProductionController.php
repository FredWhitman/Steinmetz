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

    public function read4wks(){
        ob_clean();
        header('Conent-Type: application/json');
        $this->log->info('read4wks called to fill table');
        $production = $this->model->read4wks();
        echo json_encode($production);
        exit();
    }
}