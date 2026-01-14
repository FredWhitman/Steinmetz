<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production</title>

    <!-- Bootstrap core CSS -->
    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/lib/css/dashboard.css" rel="stylesheet">
    <link href="/css/myCSS.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/assets/steinmetz.ico" type="image/x-icon">

</head>

<body>
    <!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php'; ?>

    <!-- Loader -->
    <div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1050;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!--------------------------------------------------------------------------------------------------------------->
    <!-- Add purge to production log start -->
    <div class="modal fade" id="addPurgeModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addPurgeModal">Add Purge</h1>
                </div>
                <div class="modal-body">
                    <form id="add-purge-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="p_PartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="p_PartName" name="p_PartName" required></select></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="p_LogDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="logDate" name="p_LogDate" required></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="p_purge">Lbs of purge</label><input type="number" step="0.001" tabindex="1" class="form-control form-control-sm" id="p_purgeLbs" name="p_purgeLbs" required></div>
                                <div class="invalid-feedback">Lbs of purge is required!</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Add Purge" class="btn btn-success" id="add-purge-btn">Add Purge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add purge to production log end -->
    <!--------------------------------------------------------------------------------------------------------------->

    <!-- Table to hold the last 4 weeks of production  -->
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Data</h4>
                    </div>
                    <div>

                        <a class="btn btn-primary" href="/forms/viewProductionRuns.php" role="button">View Production Runs</a>
                        <a class="btn btn-primary" href="/forms/viewProductionLog.php" role="button">View log</a>

                        <button class="btn btn-primary" type="button" id="loadPurgeForm" data-bs-toggle="modal" data-bs-target="#addPurgeModal">Add Purge</button>
                        <a class="btn btn-primary" href="/forms/addProductionLog.php" role="button">Add Production Log</a>

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
                    <div class="table-container-scroll">
                        <!-- <div class="table-responsive"> -->
                        <!-- Table to display our db user list -->
                        <table id="read4wks" class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th>Part Number</th>
                                    <th>Production Date</th>
                                    <th>Parts Produced</th>
                                    <th>Start Up Rejects</th>
                                    <th>QA Rejects</th>
                                    <th>Purge</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="last4wks">

                            </tbody>
                        </table>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>
    <!-- My custom js -->
    <script type="module" src="/js/production/productionMain_new.js"></script>
</body>

</html>