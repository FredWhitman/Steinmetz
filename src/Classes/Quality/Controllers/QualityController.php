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

    public function getQaRejectLog($id)
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('getQaLog called to fill view log form');
            $qaLog = $this->model->getQaRejectLog($id);
            echo json_encode($qaLog);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch QA Log', 'details' => $e->getMessage()]);
        }
    }

    public function getOvenLog($id)
    {
        ob_clean();
        header('Content-Type: applicatio/json');
        try {
            $this->log->info('Get Oven Log called from QualityController.');
            $ovenLog = $this->model->getOvenLog($id);
            echo json_encode($ovenLog);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch Oven Log', 'details' => $e->getMessage()]);
        }
    }

    public function getLotChange($id)
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('Get Lot Change called from QualityController.');
            $lotChange = $this->model->getLotChange($id);
            echo json_encode($lotChange);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch Lot Change', 'details' => $e->getMessage()]);
        }
    }
    public function addQaRejects($data)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->model->insertQaRejects($data);
            $type = $result['success'] ? 'success' : 'danger';
            $alert = $this->util->showMessage($type, $result['message']);
            echo $alert;
        } catch (\Exception $e) {
            http_response_code(500);
            $alert = $this->util->showMessage('danger', 'Unhandled exception: ' . $e->getMessage());
            echo $alert;
        }
    }

    public function addLotChange($data)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->model->insertLotChange($data);
            $type = $result['success'] ? 'success' : 'danger';
            $alert = $this->util->showMessage($type, $result['message']);
            echo $alert;
        } catch (\Throwable $e) {
            http_response_code(500);
            $alert = $this->util->showMessage('danger', 'Unhandled exception: ' . $e->getMessage());
            echo $alert;
        }
    }

    public function updateOvenLog($data)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->model->updateOvenLog($data);
            $type = $result['success'] ? 'success' : 'danger';
            $alert = $this->util->showMessage($type, $result['message']);
            $this->log->info('QualityController->updateOvenLog $alert: '. print_r($alert, true));
            echo $alert;
        } catch (\Throwable $e) {
            http_response_code(500);
            $alert = $this->util->showMessage('danger', 'Unhandled exception: ' . $e->getMessage());
            echo $alert;
        }
    }

    public function addOvenLog($data)
    {
        header('Content-Type: application/json');
        try {
            $result = $this->model->insertOvenLog($data);
            $type = $result['success'] ? 'success' : 'danger';
            $alertJson = $this->util->showMessage($type, $result['message']);
            echo $alertJson;
        } catch (\Throwable $e) {
            http_response_code(500);
            $alertJson = $this->util->showMessage('danger', 'Unhandled exception: ' . $e->getMessage());
            echo $alertJson;
        }
    }

    public function addMatTransaction($data){
        header('Content-Type: application/json');
        try {
            $result = $this->model->insertTransactions($data);
            $type = $result['success'] ? 'success' : 'danger';
            $alertJson = $this->util->showMessage($type, $result['message']);
            echo $alertJson;
        } catch (\Throwable $th) {
            http_response_code(500);
            $alertJson = $this->util->showMessage('danger', 'Unhandled exception: ' . $e->getMessage());
            echo $alertJson;
        }
    }

    public function getProductList()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $result = $this->model->getProductList();
            $this->log->info("product List for select");
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }



    public function getMaterialList()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $result = $this->model->getMaterialList();
            $this->log->info("material list for select");
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
