<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steinmetz Production</title>

    <!-- Bootstrap core CSS -->
    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/lib/css/dashboard.css" rel="stylesheet">
    <link href="/css/myCSS.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="steinmetz.ico" type="image/x-icon">

</head>

<body>
    <!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php'; ?>
    <!-- Add New Product start-->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModal">
        <div class="modal-dialog modal-sm " style="max-width: 32%">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addProductModal">Add Product</h1>
                </div>
                <div class="modal-body">
                    <form id="edit-product-form" class="needs-validation p-2" novalidate>
                        <div class="d-flex flex-column g-1 ">
                            <input type="hidden" name="productID" id="hiddenProductID" />

                            <div class="row-sm-8 mb-1">
                                <div class="input-group sm-3"><label class="input-group-text" for="partName">Product Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="add_ProductID" name="add_ProductID" required></input></div>
                                <div class="invalid-feedback">Product name is required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="row-sm-8">
                                    <div class="d-flex flex-column flex-sm-column">
                                        <div class="input-group sm-3">
                                            <label class="input-group-text" for="customer">Customer</label>
                                            <input type="text" class="form-control form-control-sm" id="add_Customer" name="add_Customer" required>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">customer required!</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-evenly mb-1 g-1">
                            <div class="col-sm-6">
                                <div class="input-group sm-3"><label class="input-group-text" for="minQty">Min Qty</label><input type="number" tabindex="1" class="form-control form-control-sm" id="add_MinQty" name="add_MinQty" required></div>
                                <div class="invalid-feedback">Minimum qauntity required!</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="partWeight">Part (lbs)</label>
                                    <input type="number" step=".001" class="form-control form-control-sm" id="add_PartWeight" name="add_PartWeight">
                                </div>
                                <div class="invalid-feedback">weight required!</div>
                            </div>

                        </div>
                        <div class="d-flex flex-row justify-content-evenly mb-1 g-1">
                            <div class="col-sm-6 g-1">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="partBox">Part Qty/Box</label>
                                    <input type="number" tabindex="1" class="form-control form-control-sm" id="add_PartsBox" name="add_PartsBox">
                                </div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="col-sm-6 g-1">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="boxSkid">Boxes/Skid</label><input type="number" tabindex="1" class="form-control form-control-sm" id="add_BoxSkid" name="add_BoxSkid">
                                </div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-evenly mb-1 g-1">
                            <div class="col-sm-7">
                                <div class="input-group sm-3">
                                    <label class="input-group-text" for="partType">Product Type</label>
                                    <select type="text" class="form-control form-control-sm" id="add_PartType" name="add_PartType" required></select>
                                </div>
                                <div class="invalid-feedback">type required!</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="add_DisplayOrder" name="add_DisplayOrder"></div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>
                        </div>

                        <div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" value="add" class="btn btn-success" id="add-product-btn">Add Product</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  Add New Product end-->

    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Data</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadProdLogForm" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div id="showAlert"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <!-- Table to display our db user list -->
                        <table id="last4wks" class="table table-striped table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Part Number</th>
                                    <th>Production Date</th>
                                    <th>Parts Produced</th>
                                    <th>Start Up Rejects</th>
                                    <th>QA Rejects</th>
                                    <th>Purge</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="weeks">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>
    <!-- Custom javascript -->
    <script type="module" src="/js/inventoryMain.js"></script>

</body>

</html>