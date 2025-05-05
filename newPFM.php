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
        <!-- Head metas, css, and title -->
        <?php require_once 'includes/head.php'; ?>
    </head>
    <body>
        <!-- Header banner -->
        <?php require_once 'includes/header.php'; ?>
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar menu -->
                <?php require_once 'includes/sidebar.php'; ?>
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
                                                <input class="form-control" type="text" name="pfmID" id="pfmID" value="<?php print($rowPFM['PFMID']) ?>" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="partNumber">Part Number *</label>
                                                <input class="form-control" type="text" name="partNumber" id="partNumber" value="<?php print($rowPFM['PartNumber']) ?>" required maxlength="50">
                                            </div>
                                            <div class="form-group">
                                                <label for="partName">Part Name *</label>
                                                <input class="form-control" type="text" name="partName" id="partName" value="<?php print($rowPFM['PartName']) ?>" required maxlength="25">
                                            </div>
                                            <div class="form-group">
                                                <label for="productID">Product Used For *</label>
                                                <input class="form-control" type="text" name="productID" id="productID" value="<?php print($rowPFM['ProductID']) ?>" required maxlength="25">
                                            </div>
                                            <div class="form-group">
                                                <label for="minQty">Minimum Qty</label>
                                                <input class="form-control" type="text" name="minQty" id="MinQty" value="<?php print($rowPFM['MinimumQty']) ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="amstedPFM">Amsted PFM *</label>
                                                <input class ="form-control" type="radio" name="amsted" id="yes" value="1">
                                                <label for="amsted">Yes</label>
                                                <input class ="form-control" type="radio" name="amsted" id="no" value="0">
                                                <label for="amsted">No</label>
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
        <?php require_once 'includes/footer.php'; ?>
    </body>
</html>
