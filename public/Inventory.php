<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steinmetz Production</title>

    <!-- Bootstrap core CSS -->
    <link href="../resources/vendors/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="../resources/vendors/css/dashboard.css" rel="stylesheet">
    <link href="../resources/css/myCSS.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>
    <!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php'; ?>

    <!-- Add Edit Product start-->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editProductModal">Update Product Details</h1>
                </div>
                <div class="modal-body">
                    <form id="edit-product-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="partName">Product Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="partName" name="p_Part" required></input></div>
                                    <div class="invalid-feedback">Product name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="minQty">Min Quantity</label><input type="number" tabindex="1" class="form-control form-control-sm" id="minQty" name="p_minQty" required></div>
                                    <div class="invalid-feedback">Minimum qauntity required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="boxSkid">Boxes/Skid</label><input type="number" tabindex="1" class="form-control form-control-sm" id="boxSkid" name="p_boxSkid" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="partBox">Part Quantity/Box</label><input type="number" tabindex="1" class="form-control form-control-sm" id="partBox" name="p_partBox" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="partWeight">Part Weight (lbs)</label><input type="number" step=".01" class="form-control form-control-sm" id="partWeight" name="p_partWeight" required></div>
                                <div class="invalid-feedback">weight required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="customer">Customer</label><input type="text" class="form-control form-control-sm" id="customer" name="p_customer" required></div>
                                <div class="invalid-feedback">customer required!</div>
                            </div>
                            <div class="row row-cols-2  pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="partType">Production Type</label><input type="text" class="form-control form-control-sm" id="partType" name="p_partType" required></div>
                                    <div class="invalid-feedback">type required!</div>
                                </div>
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="displayOrder" name="p_displayOrder" required></div>
                                    <div class="invalid-feedback">displayOrder required!</div>
                                </div>
                            </div>
                        </div>
                        <div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Update" class="btn btn-success" id="update-product-btn">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  Edit Product end-->

    <!-- Add Edit Material start-->
    <div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editMaterialModal">Update Material Details</h1>
                </div>
                <div class="modal-body">
                    <form id="edit-material-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="matName">Material Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="matName" name="p_material" required></input></div>
                                    <div class="invalid-feedback">Material name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="minQty">Used for</label><input type="text" tabindex="1" class="form-control form-control-sm" id="productID" name="m_productID" required></div>
                                    <div class="invalid-feedback">Minimum qauntity required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="minLbs">Min. Lbs</label><input type="number" step=".001" class="form-control form-control-sm" id="minLbs" name="m_minLbs" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="customer">Customer</label><input type="text" class="form-control form-control-sm" id="mCustomer" name="m_customer" required></div>
                                <div class="invalid-feedback">customer required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="mDisplayOrder" name="m_displayOrder" required></div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" value="Update" class="btn btn-success" id="update-product-btn">Update Material</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!--  Edit Material end-->

    <!-- Add PFM start-->
    <div class="modal fade" id="editPFMModal" tabindex="-1" aria-labelledby="editPFMModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editPFMModal">Update PFM Details</h1>
                </div>
                <div class="modal-body">
                    <form id="edit-material-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="matName">PFM Part Number</label><input type="text" tabindex="1" class="form-control form-control-sm" id="pNumber" name="pf_number" required></input></div>
                                    <div class="invalid-feedback">Part Number is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="matName">PFM Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="pName" name="pf_name" required></input></div>
                                    <div class="invalid-feedback">Name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="minQty">Used for</label><input type="text" tabindex="1" class="form-control form-control-sm" id="pProductID" name="p_productID" required></div>
                                    <div class="invalid-feedback">product used for required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="minLbs">Min. Qty</label><input type="number" class="form-control form-control-sm" id="pMinQty" name="p_minQty" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="customer">Customer</label><input type="text" class="form-control form-control-sm" id="pCustomer" name="p_customer" required></div>
                                <div class="invalid-feedback">customer required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="pDisplayOrder" name="p_displayOrder" required></div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" value="Update" class="btn btn-success" id="update-product-btn">Update Material</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!--  Edit PFM end-->

    <!-- Table to hold the last 4 weeks of production  -->
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Inventory Data</h4>
                    </div>
                    <!-- <div>
                        <button class="btn btn-primary" type="button" id="loadLotChangeForm" data-bs-toggle="modal" data-bs-target="#addLotChangeModal">Add Lot Change</button>
                        <button class="btn btn-primary" type="button" id="loadPurgeForm" data-bs-toggle="modal" data-bs-target="#addPurgeModal">Add Purge</button>
                        <button class="btn btn-primary" type="button" id="loadQARejectForm" data-bs-toggle="modal" data-bs-target="#addQARejectsModal">Add QA Rejects</button>
                        <button class="btn btn-primary" type="button" id="loadProdLogForm" data-bs-toggle="modal" data-bs-target="#addProductionModal">Add Production Log</button>
                    </div> -->
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div id="showAlert"></div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <!-- <div class="table-responsive"> -->
                        <!-- Table to display our db user list -->
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="3">Products</th>
                                </tr>
                                <tr>
                                    <th>Part Number</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="products">

                            </tbody>
                        </table>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="3">Materials</th>
                                </tr>
                                <tr>
                                    <th>Material Number</th>
                                    <th>Lbs</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="materials">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="3">Purchase Finished Materials</th>
                                </tr>
                                <tr>
                                    <th>Part Number</th>
                                    <th>Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="pfms">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap js -->
    <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>
    <!-- Custom javascript -->
    <script type="text/javascript" src="../resources/js/inventoryController.js"></script>
</body>

</html>