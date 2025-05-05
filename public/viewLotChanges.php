<?php
session_start();
require_once '../src/Classes/products.php';
require_once '../src/Classes/material.php';

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

    <!-- Bootstrap core CSS and JS-->
    <link href="../resources/vendors/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>

    <!-- Custom styles for this template -->
    <link href="../resources/vendors/css/dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/myCSS.css">

</head>

<body>
    <!-- Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php';  ?>
    <div class="mt-5">
        <?php if (isset($_SESSION['status']) && $_SESSION['status'] != '') { ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>DB operation</strong><?php echo $_SESSION['status']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php unset($_SESSION['status']);
        } ?>

        <div class="container-fluid d-flex justify-content-center">
            <div class="card" style="width: 55rem;">
                <div class="card-header">
                    <h5 class="text-center">Lot Change Query</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center ">
                        <form class="form-horizontal" action="viewLotChanges.php" method="POST">
                            <div class="d-inline-flex justify-content-center">
                                <div class="d-flex justify-content-center p-1">
                                    <div class="form-group">
                                        <label for="materialName" class="form-label">Select Material</label>
                                        <input type="text" class="form-select" list="material_Names" id="material_Name" name="selectedMaterial" placeholder="Type to search for material ...">
                                        <datalist id="material_Names">
                                            <?php foreach ($materialNames as $row) {  ?> <option> <?php echo $row['MaterialName']; ?> </option> <?php } ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center p-1">
                                    <div class="form-group">
                                        <label for="partNumber" class="form-label">Select Part</label>
                                        <input type="text" class="form-select" list="partNumbers" id="partNumber" name="selectedPart" placeholder="select part..">
                                        <datalist id="partNumbers">
                                            <?php foreach ($partNames as $row) {  ?> <option> <?php echo $row['ProductID']; ?> </option> <?php } ?>
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
                                        <button type="sumbit" id="btn_getLotsChanges" name="viewLotChange" class="btn btn-dark">Get Lot Changes</button>
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
        <div class="card" style="width: 55rem;">
            <div class="card-header">
                <h5 class="text-center">Lot Changes</h5>
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
    </div>
    </div>

</body>
<script>

</script>
<script type="text/javascript" src="../resources/vendors/js/feather.min.js"></script>
<script>
    feather.replace()
</script>

</html>