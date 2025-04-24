<?php
// Show PHP errors
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

require_once 'classes/pfm.php';

$objPFM = new PFM;


//GET
if(isset($_GET['edit_pfmID'])){
    $pfmID = $_GET['edit_pfmID'];
    $stmt = $objPFM->runQuery("SELECT * FROM pfm WHERE PFMID = :pfmID");
    $stmt->execute(array(":pfmID"=> $pfmID));
    $rowPFM = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
    $pfmID = null;
    $rowPFM = null;
}

//POST
if(isset($_POST['btn_save']))
{
    $pfmID=strip_tags($_POST['pfmID']);
    $partNumber = strip_tags($_POST['partNumber']);
    $partName = strip_tags($_POST['partName']);
    $productID = strip_tags($_POST['productID']);
    $minQty = strip_tags($_POST['minQty']);
    $amstedPFM = strip_tags($_POST['amsted']);

    try
    {
        if($pfmID != null)
        {
            if($objPFM->update($pfm,$partName,$partName,$productID,$minQty,$amstedPFM)){
                $objPFM->redirect('index.php?updated');
            }
        }else{
            if($objPFM->insert($partName,$partName,$productID,$minQty,$amstedPFM)){
                $objPFM->redirect('index.php?inserted');
            }else{
                $objPFM->redirect('index.php?error');
            }
        }
    }catch(PDOException $e)
    {
        echo $e->getMessage();
    }
}

?>
<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Steinmetz Inc Inventopry and Maintenance Website">
        <meta name="author" content="Fred Whitman">

        <title>Steinemtz Inc</title>

        <!-- Bootstrap core CSS -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous"> -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script> -->

        <!-- Custom styles for this template -->
        <link href="css/dashboard.css" rel="stylesheet">
        
    </head>
    <body>
        <!-- NavBar -->
        <?php require_once 'includes/steinmetzNavbar.php';  ?>

        <div class="container-fluid mt-6">
            <div class="row">
                <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 style="margin-top: 10px">Add / Edit PFMs</h4>   
                                </div>
                                <div class="card-body">
                                    <p>Required fields are marked with an (*)</p>
                                        <form method="POST">
                                            <div class="form-group">
                                                <label for="pfmID">PFM ID</label>
                                                <input class="form-control" type="text" name="pfmID" id="pfmID" value="<?php if($pfmID != null){print($rowPFM['PFMID']);}else{} ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="partNumber">Part Number *</label>
                                                <input class="form-control" type="text" name="partNumber" id="partNumber" value="<?php if($pfmID != null){print($rowPFM['PartNumber']);}else{} ?>" required maxlength="50">
                                            </div>
                                            <div class="form-group">
                                                <label for="partName">Part Name *</label>
                                                <input class="form-control" type="text" name="partName" id="partName" value="<?php if($pfmID != null){print($rowPFM['PartName']) ;}else{}?>" required maxlength="25">
                                            </div>
                                            <div class="form-group">
                                                <label for="productID">Product Used For *</label>
                                                <input class="form-control" type="text" name="productID" id="productID" value="<?php if($pfmID != null){print($rowPFM['ProductID']);}else{} ?>" required maxlength="25">
                                            </div>
                                            <div class="form-group">
                                                <label for="minQty">Minimum Qty</label>
                                                <input class="form-control" type="text" name="minQty" id="MinQty" value="<?php if($pfmID != null){print($rowPFM['MinimumQty']) ;}else{}?>">
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <div class="container">
                                                    <div class="row">
                                                        <label for="amstedPFM">Amsted PFM *</label>
                                                        <div class="col">
                                                            <input class ="form-check-input" type="radio" name="amsted" id="yes" value="1"<?php if($pfmID != null){if($rowPFM['AmstedPFM']== 1){ echo 'checked';};}else{} ?>>
                                                            <label for="amsted">Yes</label>
                                                        </div>
                                                        <div class="col">
                                                            <input class ="form-check-input" type="radio" name="amsted" id="no" value="0" <?php if($pfmID != null){if($rowPFM['AmstedPFM'] == 0){ echo 'checked';};}else{} ?>>
                                                            <label for="amsted">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input class ="btn btn-primary mb-2" type="sumbit" name="btn_Save" value="Save">
                                            </div>
                                        </form>
                                </div>    
                            </div>    
                        </div>    
                    </div>
                </main>
            </div>
        </div>

        <!-- Footer scripts, and functions -->
        <script type="text/javascript" src="js/feather.min.js"></script>
        <script>feather.replace()</script>
    </body>
</html>
