<?php
// Show PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../src/Classes/pfm.php';

$objPFM = new pfm();

// GET

?>

<!doctype html>
<html lang="en"  data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Steinmetz Inc Inventopry and Maintenance Website">
    <meta name="author" content="Fred Whitman">

    <title>Steinemtz Inc</title>

    <!-- Bootstrap core CSS -->
    <link href="../resources/vendors/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>

    <!-- Custom styles for this template -->
    <link href="../resources/vendors/css/dashboard.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php';  ?>
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row">
                <div class="col d-flex justify-content-center">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="text-center">PFM List</h2>
                        </div>
                        <div class="card-body">
                            <?php if (isset($_GET['updated'])) {
                            } ?>
                            <div class="table-responsive" style="height:550px; overflow-y: auto">
                                <table class="table table-striped table-bordered table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">PFM ID</th>
                                            <th class="text-center">Part Number</th>
                                            <th class="text-center">Part Name</th>
                                            <th class="text-center">Product ID</th>
                                            <th class="text-center">Min Qty</th>
                                            <th class="text-center">Amsted PFM</th>
                                            <th class="text-center"></th>
                                        </tr>
                                    </thead>
                                    <?php try {
                                        $query = "SELECT * FROM pfm";
                                        $stmt = $objPFM->runQuery($query);
                                        $stmt->execute();
                                    } catch (PDOException $e) {
                                        echo 'ERROR! :';
                                        echo $e->getMessage();
                                    } ?>
                                    <tbody>
                                        <?php if ($stmt->rowcount() > 0) {
                                            while ($rowPFM = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                                <tr>
                                                    <td class="text-center"><?php print($rowPFM['PFMID']); ?></td>
                                                    <td class="text-center"><a href="newPFM.php?edit_pfmID=<?php print($rowPFM['PFMID']); ?>"><?php print($rowPFM['PartNumber']); ?></a></td>
                                                    <td class="text-center"><?php print($rowPFM['PartName']); ?></td>
                                                    <td class="text-center"><?php print($rowPFM['ProductID']); ?></td>
                                                    <td class="text-center"><?php print($rowPFM['MinimumQty']); ?></td>
                                                    <td class="text-center"><?php print($rowPFM['AmstedPFM']); ?></td>
                                                    <td class="text-center"><a href="pfmlist.php?delete_id=<?php print($rowPFM['PFMID']); ?>"><span data-feather="trash"></span></a></td>
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