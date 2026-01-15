<?php
// File: controllers/ProductionController.php
namespace Production\Controllers;

/* use Production\Models\ProductionModel;
use Util\Utilities; */


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
        //ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('read4wks called to fill table');
            $production = $this->model->read4wks();

            $msg = $this->util->showMessage('success', 'Production logs fetched successfully.');

            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $production
            ]);

            return;
        } catch (\Exception $e) {
            $this->log->error('Error in fetching read4wks: ' . $e->getMessage());

            $msg = $this->util->showMessage('danger', 'Failed to fetch production logs.');

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],

            ]);
            return;
        }
    }

    public function getOpenRuns()
    {
        //ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('getOpenRuns called to fill table');
            $prodRuns = $this->model->getActiveProdRuns();
            $msg = $this->util->showMessage('success', 'Open production runs fetched successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $prodRuns
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error('Error in fetching getOpenRuns: ' . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch open production runs.');

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
            ]);
            return;
        }
    }

    public function getCompletedRuns()
    {
        //ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info('getCompletedRuns called to fill table');
            $prodRuns = $this->model->getCompletedProdRuns();
            $msg = $this->util->showMessage('success', 'Completed production runs fetched successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $prodRuns
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error('Error in fetching getCompletedRuns: ' . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch completed production runs.');

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
            ]);
            return;
        }
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
            $response['previousLog'] = $previousLog;

            $msg = $this->util->showMessage('success', 'Production logs fetched successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $response
            ]);

            return;
        } catch (\Throwable $e) {
            $this->log->error('Error in fetching production log: ' . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch production log.');
            http_response_code(500);

            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
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
            $this->log->info('getProductList called to fill dropdown');

            $products = $this->model->getProductList();
            $msg = $this->util->showMessage('success', 'Product list fetched successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $products
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error('Error in fetching product list: ' . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch product list.');

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
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
            $this->log->info('getMaterialList called to fill dropdown');
            $materials = $this->model->getMaterialList();
            $msg = $this->util->showMessage('success', 'Material list fetched successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $materials
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error('Error in fetching material list: ' . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch material list.');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
        }
    }

    public function checkLogDates($productID, $date)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("checkLogDates called with these values: {$productID} and {$date}.");
            $exists = $this->model->checkLogDates($productID, $date);

            $msg = $this->util->showMessage('success', 'Log date check completed successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => ['exists' => $exists]
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error("Failed to checkLogDate: " . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to check log date.');

            http_response_code(500);
            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
        }
    }

    public function viewLog($productID, $date)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("viewLog called with these values: {$productID} and {$date}.");
            $log = $this->model->getProductionlog($productID, $date);

            $msg = $this->util->showMessage('success', 'Production log fetched successfully.');

            echo json_encode([
                'success' =>  true,
                'type'    =>  $msg['type'],
                'message' =>  $msg['message'],
                'html'    =>  $msg['html'],
                'data' => $log
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error("Failed to fetch production log: " . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch production log.');

            http_response_code(500);

            echo json_encode([
                'success' =>  false,
                'type'    =>  $msg['type'],
                'message' =>  $msg['message'],
                'html'    =>  $msg['html']
            ]);
            return;
        }
    }

    public function checkRun($productID)
    {

        header('Content-Type: application/json');
        try {
            $this->log->info("checkRun called with these values: {$productID}.");
            $exists = $this->model->CheckProductionRuns($productID);
            $msg = $this->util->showMessage('success', 'Production run check completed successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => ['exists' => $exists]
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error("Failed to checkRun: " . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to check production run.');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
        }
    }

    public function getLastLog($productID)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("getLastLog caleed with this value: {$productID}.");
            $matLog = $this->model->getLastMaterialLogForRun($productID);
            $msg = $this->util->showMessage('success', 'Last material log fetched successfully.');

            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $matLog
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error("Failed to fetch last material log: " . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch last material log.');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
        }
    }

    public function getRunProdLogs($runID)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("getRunProdLogs called with this value: {$runID}.");
            $prodLogs = $this->model->getRunProdLogs($runID);
            $msg = $this->util->showMessage('success', 'Production logs for run fetched successfully.');
            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $prodLogs
            ]);
            return;
        } catch (\Throwable $e) {
            $this->log->error("Failed to fetch production logs for run {$runID}: " . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to fetch production logs for run.');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
        }
    }

    public function addLog($data)
    {
        ob_clean();
        header('Content-Type: application/json');
        try {
            $this->log->info("addLog called to insert new production log.");

            $result = $this->model->insertProdLog(
                $data["prodData"],
                $data["materialData"],
                $data["tempData"]
            );

            // Determine success type based on $result if needed
            $type = $result['success'] ? 'success' : 'warning';
            $msg = $this->util->showMessage($type, $result['message']);

            echo json_encode([
                'success' => $result['success'],
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $result['data'] ?? null
            ]);
            return;
        } catch (\Exception $e) {
            $this->log->error("Failed to add production log: " . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to add production log.');

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
        }
    }

    public function addPurge($data)
    {
        header('Content-Type: application/json');
        try {
            $this->log->info("addPurge called with these values: {$data['productID']} and {$data['purgeLbs']}.");

            $result = $this->model->addPurge(
                $data['productID'],
                $data['prodDate'],
                $data['purgeLbs']
            );

            $type = $result['success'] ? 'success' : 'danger';
            $msg = $this->util->showMessage($type, $result['message']);

            echo json_encode([
                'success' => true,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html'],
                'data'    => $result['data'] ?? null
            ]);

            return;
        } catch (\Throwable $e) {
            $this->log->error("Failed to add purge log: " . $e->getMessage());
            $msg = $this->util->showMessage('danger', 'Failed to add purge log.');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'type'    => $msg['type'],
                'message' => $msg['message'],
                'html'    => $msg['html']
            ]);
            return;
        }
    }
}
