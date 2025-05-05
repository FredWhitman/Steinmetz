<?php 
    session_start();
    include('includes/header.php');
    include( "DB_Files/DBFunctions.php");
    
    $materialNames = getMaterialNames();
    $partNumbers = getPartNumber();
?>

<div class="container-float">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <?php
                if(isset($_SESSION['status']) && $_SESSION['status'] !='')
                {
            ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>DB operation</strong> 
                    <?php echo $_SESSION['status']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
                unset($_SESSION['status']); 
                }
            ?>

            <div class="card mb-3">
                <div class="card-header">
                    <h4>View Lot Changes</h4>
                </div>
                <div class="card-body">
                    <form action="DB_files/viewLotChanges.php" method="POST">
                        <div class="form-group mb-1">
                            <div class="row">
                                <div class="card">
                                    <div class="col">
                                        <label for="materialNumber" class="form-label">Select Material</label>
                                        <input type="text" class="form-control" list="materialNumbers" id ="materialNumber" name ="selectedMaterial"  placeholder="Type to search...">
                                        <datalist id="materialNumbers">
                                            <?php while($row = mysqli_fetch_array($materialNames)):;?>
                                            <option><?php echo $row[0];?> </option>
                                            <?php endwhile;?>  
                                        </datalist>
                                        <label for="partNumber" class="form-label">Select Part</label>
                                        <input type="text" class="form-control" list="partNumbers" id ="partNumber" name ="selectedPart"  placeholder="Type to search...">
                                        <datalist id="partNumbers">
                                            <?php while($row = mysqli_fetch_array($partNumbers)):;?>
                                            <option><?php echo $row[0];?> </option>
                                            <?php endwhile;?>  
                                        </datalist>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-1">
                            <div class="row"> 
                                <div class="card">
                                    <div class="col">
                                        <label for="">Start Date</label>
                                        <input type="date" id="logDate1" name="date1">
                                        <label for="">End Date</label>
                                        <input type="date" id="logDate2" name="date2">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-1">
                            <button type="sumbit" name="viewLotChange"class="btn btn-primary">Get Lot Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>
