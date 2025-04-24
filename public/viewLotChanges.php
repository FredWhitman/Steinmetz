<?php 
session_start();
require_once 'Classes/products.php';
require_once 'Classes/material.php';

$materialObj = new material();
$productObj = new products();
$materialNames = $materialObj->get_MaterialName();
$partNames = $productObj->get_PartNames();
?>

<!doctype html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Steinmetz Inc Inventopry and Maintenance Website">
        <meta name="author" content="Fred Whitman">

        <title>Steinmetz Inc</title>

        <!-- Bootstrap core CSS -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous"> -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script> -->
        <!-- Custom styles for this template -->
        <link href="css/dashboard.css" rel="stylesheet">
    </head>
    
    <body>
        <!-- Navbar -->
        <?php require_once 'includes/steinmetzNavbar.php';  ?>
        <div class="mt-5">
            <?php if(isset($_SESSION['status']) && $_SESSION['status'] !='') {?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>DB operation</strong><?php echo $_SESSION['status']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php unset($_SESSION['status']); }?>

            <div class="container-fluid d-flex justify-content-center">
                <div class="card" style="width: 55rem;">
                    <div class="card-header"><h5 class="text-center">Lot Change Query</h5></div>
                        <div class="card-body">
                            <div class="row align-items-center ">
                                <form class="form-horizontal"action="viewLotChanges.php" method="POST">
                                    <div class="d-inline-flex justify-content-center">
                                        <div class="d-flex justify-content-center p-1">
                                            <div class="form-group">
                                                <label for="materialName" class="form-label">Select Material</label>
                                                <input type="text" class="form-select" list="material_Names" id ="material_Name" name ="selectedMaterial"  placeholder="Type to search for material ...">
                                                <datalist id="material_Names">
                                                    <?php foreach($materialNames as $row) {  ?> <option> <?php echo $row['MaterialName']; ?> </option> <?php } ?>
                                                </datalist>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center p-1">
                                            <div class="form-group">
                                                <label for="partNumber" class="form-label">Select Part</label>
                                                <input type="text" class="form-select" list="partNumbers" id ="partNumber" name ="selectedPart"  placeholder="select part..">
                                                <datalist id="partNumbers">
                                                    <?php foreach($partNames as $row) {  ?> <option> <?php echo $row['ProductID']; ?> </option> <?php } ?>
                                                </datalist>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center p-1">
                                            <div class="form-group">
                                                <label for="">Start Date</label><input class="form-control" type="date" id="logDate1" name="date1">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center p-1">
                                            <div class="form-group">
                                                <label for="">End Date</label><input class="form-control" type="date" id="logDate2" name="date2">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center p-1">
                                            <div class="form-group">
                                                <button type="sumbit" id="btn_getLotsChanges" name="viewLotChange"class="btn btn-primary">Get Lot Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
            <div class="container-fluid d-flex justify-content-center">
                <div class="card"  style="width: 55rem;">
                    <div class="card-header"><h5 class="text-center">Lot Changes</h5></div>
                        <div class="card-body">
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
                
    </body>
    <script> 
        
    </script>
    <script type="text/javascript" src="js/feather.min.js"></script>
    <script>feather.replace()</script>
</html>