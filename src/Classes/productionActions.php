<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

error_reporting(0);
ini_set('display_errors', 0); // Prevents PHP from printing errors in JSON response

require_once 'productionDB_SQL.php';
require_once 'util.php';
$db = new productionDB;
$util = new Util;

//Handles Ajax request from qaRejects.js
if (isset($_POST['qaRejects'])) {
    $productID = $util->testInput($_POST['qaPart']);
    $prodDate = $util->testInput($_POST['qaLogDate']);
    $rejects = $util->testInput($_POST['rejects']);
    $comments = $util->testInput($_POST['qaComments']);
    if ($db->insertQaRejects($productID, $prodDate, $rejects, $comments)) {
        echo $util->showMessage('success', 'QA Rejects of been added!');
    } else {
        echo $util->showMessage('danger', 'QA Rejects were not added!');
    }
}

if (isset($_POST['purge'])) {
    $productID = $util->testInput($_POST['p_Part']);
    $prodDate = $util->testInput($_POST['p_LogDate']);
    $purge = $util->testInput($_POST['p_purge']);

    if ($db->addPurge($productID, $prodDate, $purge)) {
        echo $util->showMessage('success', 'Purge added to production log!');
    } else {
        echo $util->showMessage('danger', 'Purge was not added ot production log!');
    }
}

//Handles Ajax call to add production log
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    error_log('productionActions->InsertProdLog: ' ."\n" . print_r($data,true));

    if (isset($data["action"]) && $data["action"] === "addLog") {

        if (isset($data["prodLogData"]) && isset($data["materialData"]) && isset($data["tempData"])) {
            $result = $db->insertProdLog($data["prodLogData"], $data["materialData"], $data["tempData"]);

            if ($result["success"]) {
                echo $util->showMessage('success', 'Production, material and temp logs added!');
            } else {
                echo $util->showMessage('danger', 'Logs were not added nor updated!');
            }
        } else {
            echo "Missing required data! Failed to pass log data!";
            http_response_code(400);
        }
    } else {
        echo "Unauthorized request!";
        http_response_code(403); // Forbidden status
    }
}

//Handle getting previous log for in progress log insert
if (isset($_GET['getLastLog'])) {
    header('Content-Type: application/json'); // Set proper header
    ob_clean(); //removes any accidental output
    flush(); //Ensure buffered output gets sent immediately

    //error_log('productionActions->$_GET[productID] =' . $_GET['productID']);
    $productID = $_GET['productID'];
    $log = $db->getLastMaterialLogForRun($_GET['productID']);

    //error_log('Fetched log data: ' . json_encode($log));

    if (!$log || empty($log)) {
        echo json_encode(["error" => "No last log found for productID {$productID} "]);
    } else {
        echo json_encode($log);
    }
    exit();
}


if (isset($_GET['endRun'])) {
    $productID = $_GET['productID'];
    //error_log("productionActions->endRun->$productID");
    header('Content-Type: application/json'); // Set proper header
    ob_clean(); //removes any accidental ouput
    flush();
    error_log('productionActions->endRun->$_GET[productID] =' . $_GET['productID']);
    $log = $db->getLastMaterialLogForRun($productID);

    if (!$log || empty($log)) {
        echo json_encode(["error" => "No last log found for productID {$productID} "]);
    } else {
        echo json_encode($log);
    }
    exit();
}

//Handle AJax read4wks call to fill table
if (isset($_GET['read4wks'])) {

    $records = $db->read4wks();
    $output = '';
    //var_dump('records from productionActions.php: ' . $records);
    //print_r($records);
    if ($records) {
        foreach ($records as $row) {
            //data-id stored the record id so that js can access the value for edit user
            $output .= '<tr data-id=' . $row['logID'] . '>
                            <td>' . $row['productID'] . '</td>
                            <td>' . $row['prodDate'] . '</td>
                            <td>' . $row['pressCounter'] . '</td>
                            <td>' . $row['startUpRejects'] . '</td>
                            <td>' . $row['qaRejects'] . '</td>
                            <td>' . $row['purgeLbs'] . '</td>
                            <td>' . $row['runStatus'] . '</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" data-bs-toggle ="modal" data-bs-target="#viewProductionModal">View</a>
                            </td>
                        </tr>';
        }
        echo $output;
    } else {
        echo '<tr><td colspan="6"> No production in the last 4 weeks found in the database!</td></tr>';
    }
}

//Handle View Log Ajax request from main.js 
if (isset($_GET['view'])) {
    $id = $_GET['id'];
    $log = $db->readOne($id);

    if (!$log) {
        echo json_encode(["error" => "No data found for logID $id"]);
    } else {
        echo json_encode($log);
    }
    exit;
}
if (isset($_GET['checkRun'])){
    header('Content-Type: application/json');
    /* ob_clean();
    flush(); */
    $productID = $_GET['productID'];
    $prodDate = $_GET['logDate'];
    error_log('productionActions->checkRun->productID: ' . $productID . ' prodDate: ' . $prodDate);

    $run = $db->CheckProductionRuns($productID);
    error_log("Is there an open production run for $productID? " . $run);
    echo json_encode(['exists'=>$run]);

    exit();
}

if (isset($_GET['checkLogs'])) {
    header('Content-Type: application/json');
    ob_clean();

    $productID = $_GET['productID'];
    $prodDate = $_GET['logDate'];
    error_log('productionActions->checkLogs->productID: ' . $productID . ' prodDate: ' . $prodDate);

    $present = $db->checkLogDates($productID, $prodDate);
    echo json_encode(['exists' => $present]);
    exit();
}
//Handle get previous log Ajax request from main.js    
if (isset($_GET['previous'])) {
    $id = $_GET['id'];
    $log = $db->readPrevious($id);

    if (!$log) {
        echo json_encode(["error" => "No data found for logID $id"]);
    } else {
        echo json_encode($log);
    }
    exit;
}

//Handle Lot change AJAX requests from logChanges.js
if (isset($_POST['lotChange'])) {
    error_log("productionActions->lotChange triggered!");
}
