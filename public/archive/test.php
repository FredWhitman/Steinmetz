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
    <!-- Add New PFM start-->
    <div class="modal fade" id="addPFMModal" tabindex="-1" aria-labelledby="addPFMModal">
        <div class="modal-dialog modal-sm " style="max-width: 45%">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addPFMModal">Add New PFM</h1>
                </div>
                <div class="modal-body">
                    <form id="add-material-form" class="needs-validation p-2" novalidate>
                        <input type="hidden" name="hiddenPfmID" id="hiddenPfmID" />
                        <div class="d-flex flex-column g-1 ">
                            <div class="row pb-1"><!-- Part Number & Part Name -->
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_pfmPartNumber">PFM Part #</label>
                                        <input type="text" tabindex="1" class="form-control form-control-sm" id="add_pfmPartNumber" name="add_pfmPartNumber" required></input>
                                    </div>
                                    <div class="invalid-feedback">Mat part number is required!</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_pfmPartName">PFM Name</label>
                                        <input type="text" class="form-control form-control-sm" id="add_pfmPartName" name="add_MatPartName" required>
                                    </div>
                                    <div class="invalid-feedback">PFM name required!</div>
                                </div>
                            </div>
                            <div class="row pb-1"><!-- Customer and ProductID -->
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_MatCustomer">Customer</label>
                                        <input type="text" class="form-control form-control-sm" id="add_MatCustomer" name="add_MatCustomer" required>
                                    </div>
                                    <div class="invalid-feedback">customer required!</div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group sm-3"><label class="input-group-text" for="add_pfmProductID">Product</label><select type="text" tabindex="1" class="form-control form-control-sm" id="add_PfmroductID" name="add_PfmProductID" required></select></div>
                                    <div class="invalid-feedback">Product required!</div>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-3"><!-- Min Qty & Display Order -->
                            <div class="col-sm-2"></div>

                            <div class="col-sm-4">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="add_minLbs">Min Qty</label>
                                    <input type="number" class="form-control form-control-sm" id="add_minPfmQty" name="add_minPfmQty" required>
                                </div>
                                <div class="invalid-feedback">min PFM Qty required!</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="add_DisplayOrder" name="add_DisplayOrder"></div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                        <div class="d-flex flex-row justify-content-center mb-1 g-2"> <!-- Buttons -->
                            <div class="col-sm-3"></div>
                            <div class="col-sm-2">
                                <button type="submit" value="add" class="btn btn-success" id="add-pfm-btn">Add PFM</button>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  Add New PFM end-->

    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Data</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadProdLogForm" data-bs-toggle="modal" data-bs-target="#addPFMModal">Add PFM</button>
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