<?php
session_start();
require_once '../src/Classes/products.php';
require_once '../src/Classes/material.php';

$materialObj = new material();
$productObj = new products();
$materialNames = $materialObj->get_MaterialName();
$partNames = $productObj->get_PartNames();
?>

<datalist id="partNames">
    <?php foreach ($partNames as $row) {  ?> <option> <?php echo $row['ProductID']; ?> </option> <?php } ?>
</datalist>
<datalist id="material_Names">
    <?php foreach ($materialNames as $row) {  ?> <option> <?php echo $row['MaterialName']; ?> </option> <?php } ?>
</datalist>

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
                                        <div class="input-group sm-1"><label for="Material_Name" class="input-group-text">Material</label><input class="form-select" type="text" list="material_Names" name="selectedMaterial" id="Matertial_Name" required></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center p-1">
                                    <div class="form-group">
                                        <div class="input-group sm-3"><label class="input-group-text" style="font-size: .75rem" for="partName">Part Name</label><input type="text" tabindex="1" class="form-select form-control-sm" list="partNames" id="partName" name="selectedPart"></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center p-1">
                                    <div class="form-group">
                                        <div class="input-group mb-3"><label class="input-group-text" for="startDate">Start</label><input class="form-control" type="date" tabindex="2" id="startDate" name="start_date"></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center p-1">
                                    <div class="form-group">
                                        <div class="input-group mb-3"><label class="input-group-text" for="endDate">End</label><input class="form-control" type="date" tabindex="2" id="endDate" name="end_date"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center p-1">
                        <div class="form-group">
                            <button type="sumbit" id="btn_getLotChanges" name="viewLotChange" class="btn btn-dark">Get Lot Changes</button>
                        </div>
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