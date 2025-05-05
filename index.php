<?php
// Show PHP errors
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once 'Classes/pfm.php';

$objPFM = new pfm();

// GET


?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Head metas, css, and title -->
        <?php require_once 'includes/head.php'; ?>
    </head>
    <body>
        <!-- Header banner -->
        <?php require_once 'includes/header.php'; ?>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar menu -->
               
                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                    <h1 style="margin-top: 10px">PFM List</h1>
                    <?php
                      if(isset($_GET['updated'])){
                        
                      }
                    ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">PFM ID</th>
                                    <th scope="col">Part Number</th>
                                    <th scope="col">Part Name</th>
                                    <th scope="col">Product ID</th>
                                    <th scope="col">Min Qty</th>
                                    <th scope="col">Amsted PFM</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <?php
                                try{
                                    $query = "SELECT * FROM pfm";
                                    $stmt = $objPFM->runQuery($query);
                                    $stmt->execute();

                                }catch(PDOException $e){
                                    echo 'ERROR! :';
                                    echo $e->getMessage();
                                }
                            ?>

                            <tbody>
                                <?php   if($stmt->rowcount()>0)
                                        {
                                           while($rowPFM = $stmt->fetch(PDO::FETCH_ASSOC))
                                            {
                                ?>
                                <tr>
                                    <td scope="row"><?php print($rowPFM['PFMID']); ?></td>
                                    <td scope="row">
                                        <a href="newPFM.php?edit_pfmID=<?php print($rowPFM['PFMID']); ?>">
                                        <?php print($rowPFM['PartNumber']); ?>
                                        </a>    
                                    </td>
                                    <td scope="row"><?php print($rowPFM['PartName']); ?></td>
                                    <td scope="row"><?php print($rowPFM['ProductID']); ?></td>
                                    <td scope="row"><?php print($rowPFM['MinimumQty']); ?></td>
                                    <td scope="row"><?php print($rowPFM['AmstedPFM']); ?></td>
                                    <td scope="row"><a href="newpfm.php?delete_id=<?php print($rowPFM['PFMID']); ?>"><span data-feather="trash"></span></a></td>
                                </tr>
								<?php }}?>
							</tbody> 	
                        </table>
                    </div>
                </main>
            </div>
        </div>

        <!-- Footer scripts, and functions -->
        <?php require_once 'includes/footer.php'; ?>

        <!-- Custom scripts -->
        <script>
            // JQuery confirmation
            $('.confirmation').on('click', function () {
                return confirm('Are you sure you want do delete this user?');
            });
        </script>
    </body>
</html>