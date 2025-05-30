<?php
require_once 'fetch_4w_logs.php';

$db = new Last4Weeks;

if (isset($_GET['read4wks']))
{

    $records = $db->read4wks();
    $output ='';
    //var_dump('records from productionActions.php: ' . $records);
    //print_r($records);
    if($records)
    {
        foreach ($records AS $row) 
        {
            //data-id stored the record id so that js can access the value for edit user
            $output .= '<tr data-id='. $row['logID']. '>
                            <td>'. $row['productID'] .'</td>
                            <td>'. $row['prodDate'] .'</td>
                            <td>'. $row['pressCounter'] .'</td>
                            <td>'. $row['startUpRejects'] .'</td>
                            <td></td>
                            <td>'. $row['purgeLbs'] .'</td>
                            <td>'. $row['runStatus'] .'</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" data-bs-toggle ="modal" data-bs-target="#viewProductionModal">View</a>
                            </td>
                        </tr>';
        }
        echo $output;
    }else{
        echo '<tr><td colspan="6"> No production in the last 4 weeks found in the database!</td></tr>';
    }
}


//Handle View Log Ajax request from main.js 
if (isset($_GET['view'])) {
    $id =$_GET['id'];
    $log = $db->readOne($id);

    if (!$log) {
        echo json_encode(["error" => "No data found for logID $id"]);
    } else {
        echo json_encode($log);
    }
    exit;
}

 //Handle get previous log Ajax request from main.js    
if(isset($_GET['previous']))
{
    $id = $_GET['id'];
    $log = $db->readPrevious($id);

    if(!$log){
        echo json_encode(["error" => "No data found for logID $id"]);
    }else{
      echo json_encode($log);
    }   
    exit;
}

