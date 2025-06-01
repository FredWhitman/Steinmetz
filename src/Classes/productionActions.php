<?php
require_once 'fetch_4w_logs.php';
require_once 'util.php';
$db = new Last4Weeks;
$util = new Util;

//Handles Ajax request from qaRejects.js
if(isset($_POST['qaRejects'])){
    $productID = $util->testInput($_POST['qaPart']);
    $prodDate = $util->testInput($_POST['qaLogDate']);
    $rejects = $util->testInput($_POST['rejects']);
    $comments = $util->testInput($_POST['qaComments']);
    if($db->insertQaRejects($productID,$prodDate,$rejects,$comments))
    {
        echo $util->showMessage('success','QA Rejects of been added!');
    }else{
        echo $util->showMessage('danger', 'QA Rejects were not added!');
    }
}

if(isset($_POST['purge'])){
    $productID = $util->testInput($_POST['p_Part']);
    $prodDate = $util->testInput($_POST['p_LogDate']);
    $purge = $util->testInput($_POST['p_purge']);
    
    if($db->addPurge($productID,$prodDate,$purge))
    {
        echo $util->showMessage('success','Purge added to production log!');
    }else{
        echo $util->showMessage('danger', 'Purge was not added ot production log!');
    }
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
