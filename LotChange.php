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
            <div class="card mt-3">
                <div class="card-header">
                    <h4>Add Lot Change</h4>
                </div>
                <div class="card-body">
                    
                    <form action="DB_files/addLotChange.php" method="POST">
                        <div class="form-group mb-1">
                            <label for="materialNumber" class="form-label">Select Material</label>
                            <input type="text" class="form-control" list="materialNumbers" id ="materialNumber" name ="selectedMaterial"  placeholder="Type to search...">
                                <datalist id="materialNumbers">
                                    <?php while($row = mysqli_fetch_array($materialNames)):;?>
                                    <option><?php echo $row[0];?> </option>
                                    <?php endwhile;?>  
                                </datalist>
                        </div>
                        <div class="form-group mb-1">
                            <label for="partNumber" class="form-label">Select Part</label>
                            <input type="text" class="form-control" list="partNumbers" id ="partNumber" name ="selectedPart"  placeholder="Type to search...">
                                <datalist id="partNumbers">
                                    <?php while($row = mysqli_fetch_array($partNumbers)):;?>
                                    <option><?php echo $row[0];?> </option>
                                    <?php endwhile;?>  
                                </datalist>
                        </div>
                        <div class="form-group mb-1">
                            <label for="">Change Date</label>
                            <input type="date" id="logDate" name="date1">
                        </div>
                        <div class="form-group mb-1">
                            <label for="">Time</label>
                            <input type="time" name = "time" class="form-control">
                        </div>
                        
                        <div class="form-group mb-1">
                            <label for="">Old Lot Number: </label>
                                <input type="oldLotNumber" class="form-control" name = "oldLot" placeholder="old lot number">
                        </div>
                        <div class="form-group mb-1">
                            <label for="">New Lot Number: </label>
                            <input type="newLotNumber" class="form-control" name = "newLot" placeholder="new lot number">
                        </div>
                        <div class="form-group mb-1">
                            <div class="form-floating">
                                <textarea class="form-control" name = "comments" placeholder="Leave a comment here" id="floatingTextarea"></textarea>
                                <label for="floatingTextarea">Comments</label>
                            </div>
                        </div>
                        <div class="form-group mb-1">
                            <button type="sumbit" name="addLotChange"class="btn btn-primary">Add Lot Change</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php');?>
