<?php
// Show PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../src/Classes/Inventory.php';

$inventoryObj = new Inventory();

// GET
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
    <link href="../resources/vendors/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script> -->
    <!-- Custom styles for this template -->
    <link href="../resources/vendors/css/dashboard.css" rel="stylesheet">

</head>

<header>
    <!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php';  ?>
</header>

<body>
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row">
                <div class="col d-flex justify-content-center">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center">Parts List</h3>
                        </div>
                        <div class="card-body">
                            <div class="table responsive-md w-33" style="height:450px; overflow-y: auto">
                                <table class="table table-striped table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Part Number</th>
                                            <th class="text-center">Part Name</th>
                                            <th class="text-center">Qty in Stock</th>
                                    </thead>
                                    <?PHP try {
                                        $parts = $inventoryObj->get_Parts();
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    } ?>
                                    <tbody>
                                        <?php if ($parts != Null) {
                                            while ($rowParts = $parts->fetch(PDO::FETCH_ASSOC)) { ?>
                                                <tr>
                                                    <td class="text-center"><?php print($rowParts["ProductID"]) ?></td>
                                                    <td class="text-center"><?php print($rowParts["PartName"]) ?></td>
                                                    <td class="text-center"><?php if ($rowParts['MinimumQty'] > $rowParts['PartQty']) { ?>
                                                            <strong class="text-danger"><?php print($rowParts["PartQty"]); ?></strong>
                                                        <?php } else print($rowParts["PartQty"]); ?>
                                                    </td>
                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center">Material List</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive w-33" style="height:450px; overflow-y: auto">
                                <table class="table table-striped table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Material Number</th>
                                            <th class="text-center">Material Name</th>
                                            <th class="text-center">Lbs in Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php try {
                                            $material = $inventoryObj->get_Material();
                                        } catch (PDOException $e) {
                                            echo $e->getMessage();
                                        } ?>
                                        <tr><?Php if ($material->rowcount() > 0) {
                                                while ($rowMaterials = $material->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <td class="text-center"><?php print($rowMaterials["MaterialPartNumber"]) ?></td>
                                                    <td class="text-center"><?php print($rowMaterials["MaterialName"]) ?></td>
                                                    <td class="text-center"><?php if ($rowMaterials["Minimumlbs"] > $rowMaterials["lbs"]) { ?>
                                                            <strong class="text-danger"><?php print($rowMaterials["lbs"]); ?> </strong>
                                                        <?php } else print($rowMaterials["lbs"]); ?>
                                                    </td>
                                        </tr><?php }
                                            } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center">PFM List</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="height:450px; overflow-y: auto">
                                <table class="table table-striped table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">PFM Part Number</th>
                                            <th class="text-center">PFM Part Name</th>
                                            <th class="text-center">Qty in Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $pfms = $inventoryObj->get_Pfms(); ?>
                                        <tr><?php if ($pfms->rowcount() > 0) {
                                                while ($rowPfms = $pfms->fetch(PDO::FETCH_ASSOC)) { ?>
                                                    <td class="text-center"><?php print($rowPfms["PARTNUMBER"]) ?></td>
                                                    <td class="text-center"><?php print($rowPfms["PARTNAME"]) ?></td>
                                                    <td class="text-center"><?php if ($rowPfms["MINIMUMQTY"] > $rowPfms["Qty"]) { ?>
                                                            <strong class="text-danger"><?php print($rowPfms["Qty"]); ?> </strong>
                                                        <?php } else print($rowPfms["Qty"]); ?>
                                                    </td>
                                        </tr><?php }
                                            } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</body>
<script type="text/javascript" src="../resources/vendors/js/feather.min.js"></script>
<script>
    feather.replace()
</script>

</html>