<?php
// FILE: controller/QualityController.php
namespace Quality\Controllers;

require_once __DIR__ . '/../Models/QualityModel.php';

class QualityController
{
    private $model;
    private $log;
    private $util;
    
    public function __construct($model, $util, $log)
    {
        $this->model = $model;
        $this->util = $util;
        $this->log = $log;
    }
    
}

