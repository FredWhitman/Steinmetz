<?php
// File: controllers/ProductionController.php
namespace Production\Controllers;

use Production\Models\ProductionModel;
use Util\Utilities;


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

    public function getOpenRuns()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('getOpenRuns called to fill table');
            $prodRuns = $this->model->getActiveProdRuns();
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch open runs', 'details' => $e->getMessage()]);
        }

        echo json_encode($prodRuns);
    }

    public function getCompletedRuns()
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('getCompletedRuns called to fill table');
            $prodRuns = $this->model->getCompletedProdRuns();
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch completed runs', 'details' => $e->getMessage()]);
        }

        echo json_encode($prodRuns);
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

    public function viewLog($productID, $date)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("viewLog called with these values: {$productID} and {$date}.");
            $log = $this->model->getProductionlog($productID, $date);
            echo json_encode($log);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch production log', 'details' => $e->getMessage()]);
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


    public function addLog($data)
    {
        header('Content-Type: application/json');
        try {
            return $this->model->insertProdLog(
                $data["prodData"],
                $data["materialData"],
                $data["tempData"]
            );
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'message' => 'Failed to add production log.',
                'details' => $e->getMessage()
            ];
        }
    }
}
