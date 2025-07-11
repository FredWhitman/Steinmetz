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

    public function addQaRejects($data)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->model->insertQaRejects($data);
            if($result['success']){
                $alert = $this->util->showMessage('success', json_decode($result['message']));
            }else{
                $alert = $this->util->showMessage('danger', json_decode($result['message']));
            }
            echo $alert;
        } catch (\Exception $e) {
            http_response_code(500);
            $alert = $this->util->showMessage('danger', 'Unhandled exception: '.$e->getMessage());
            echo $$alert;
        }
    }

    public function getProductList()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $result = $this->model->getProductList();
            $this->log->info("product List for select");
            echo json_encode(['success' => true, 'products' => $result]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
