<?php
// FILE: controller/QualityController.php
namespace Quality\Controllers;


use Quality\Models\QualityModel;
use Util\Utilities;

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

    public function getQALogs()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('getQALogs called to fill tables.');
            $qaLogs = $this->model->getQALogs();
            echo json_encode($qaLogs);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch QA Logs', 'details' => $e->getMessage()]);
        }
    }
}
