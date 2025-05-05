<?php
session_start();
$con = mysqli_connect("localhost","root","","inventory_db");

if(isset($_POST['viewLotChange']))
{
    if(empty($_POST['selectedMaterial']))
    {
        $_SESSION['status'] = "ERROR: A material must be selected before this query will run!";
    }
    if(empty($_POST['selectedPart']))
    {
        $_SESSION['status'] = "ERROR: A part must be selected before this query will run!";
    }
    
    $selectedMaterial = $_POST['selectedMaterial'];
    $selectedPart =$_POST['selectedPart'];
    $startdate =$_POST['date1'];
    $enddate =$_POST['date2'];

    $query = "SELECT MaterialName, ChangeDate , OldLot, NewLot, Comments From Lotchange where ChangeDate BETWEEN ? AND ? AND MaterialName = ? ";
       
    $parameters =[$startdate,$enddate,$selectedMaterial];
    $con->execute_query($query,$parameters);

    $_SESSION['status'] = "Lot Change acquired successfully.";
    $_SESSION['lotchanges'] =$query;
    header("location: ../index.php");


}

?>
