<?php
session_start();
$con = mysqli_connect("localhost","root","","inventory_db");

if(isset($_POST['addLotChange']))
{
    $selectedMaterial = $_POST['selectedMaterial'];
    $selectedPart =$_POST['selectedPart'];
    $changedate =$_POST['date1'];
    $time =$_POST['time'];
    $oldlot = $_POST['oldLot'];
    $newlot = $_POST['newLot'];
    $comments = $_POST['comments'];
    
    $query ="INSERT INTO lotchange (MaterialName, ProductID, ChangeDate, changeTime, OldLot, NewLot, Comments) VALUES(?,?,?,?,?,?,?)";

    $parameters = [$selectedMaterial, $selectedPart, $changedate,$time,$oldlot,$newlot,$comments];        
    $con->execute_query($query,$parameters);

    $_SESSION['status'] = "Lot Change added successfully.";
    header("location: ../lotChange.php");
}

?>