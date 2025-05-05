<?php
// Show PHP errors
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

//GET

//POST

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
                                                <label for="partNumber">Part Number *</label>
                                                <input class="form-control" type="text" name="partNumber" id="partNumber" value="" required maxlength="50">
                                            </div>
                                            <div class="form-group">
                                                <label for="partName">Part Name *</label>
                                                <input class="form-control" type="text" name="partName" id="partName" value="" required maxlength="25">
                                            </div>
                                            <div class="form-group">
                                                <label for="productID">Product Used For *</label>
                                                <input class="form-control" type="text" name="productID" id="productID" value="" required maxlength="25">
                                            </div>
                                            <div class="form-group">
                                                <label for="minQty">Minimum Qty</label>
                                                <input class="form-control" type="text" name="minQty" id="MinQty" value="">
                                            </div>
                                            <div class="form-group">
                                                <label for="amstedPFM">Amsted PFM *</label>
                                                <input class="form-control" type="text" name="amstedPFM" id="amstedPFM" value="" required>
                                            </div>
                                            <input class ="btn btn-primary mb-2" type="button" name="btn_Save" value="Save">
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
