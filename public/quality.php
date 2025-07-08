<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality</title>

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


    <!-- Add QA Rejects to production log start-->
    <div class="modal fade" id="addQARejectsModal" tabindex="-1" aria-labelledby="addQARejectsModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addQARejectsModal">QA Rejects</h1>
                </div>
                <div class="modal-body">
                    <form id="add-qaReject-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="qaPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="qaPartName" name="qaPart" required></select></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="qaLogDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="logDate" name="qaLogDate" required></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="qaRejects">Reject Quantity</label><input type="number" tabindex="1" class="form-control form-control-sm" id="qaRejects" name="rejects" required></div>
                                <div class="invalid-feedback">Number of rejects is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="comment-text" name="qaComments"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Add Rejects" class="btn btn-success" id="add-qaReject-btn">Add Rejects</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add QA Rejects to production log end-->



    <!-- Table to hold the last 4 weeks of production  -->
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Quality</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadProductForm" data-bs-toggle="modal" data-bs-target="#addLotChange">Add Lot Chage</button>
                        <button class="btn btn-primary" type="button" id="loadQARejectForm" data-bs-toggle="modal" data-bs-target="#addQARejectsModal">Add QA Rejects</button>
                        <button class="btn btn-primary" type="button" id="loadMaterialForm" data-bs-toggle="modal" data-bs-target="#receiveMaterial">Receive Material</button>
                    </div>
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
                        <!-- Table to display QA Reject Logs -->
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="5">QA Reject Logs</th>
                                </tr>
                                <tr>
                                    <th>Production Date</th>
                                    <th>Production Log</th>
                                    <th>Part Number</th>
                                    <th>Rejects</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="qaRejectLogs">

                            </tbody>
                        </table>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <!--  Table to display Oven Logs-->
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="8">Oven Logs</th>
                                </tr>
                                <tr>
                                    <th>Part Number</th>
                                    <th>In Date</th>
                                    <th>In Time</th>
                                    <th>In Initials</th>
                                    <th>Out Date</th>
                                    <th>Out Time</th>
                                    <th>Out Initials</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ovenLogs">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <!-- Table to display Lot change logs -->
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="8">Lot Change Logs</th>
                                </tr>
                                <tr>
                                    <th>Production Log Id</th>
                                    <th>Product ID</th>
                                    <th>Material Name</th>
                                    <th>Change Date</th>
                                    <th>Change Time</th>
                                    <th>Old Lot #</th>
                                    <th>New Lot #</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="lotChangeLogs">

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
    <script type="module" src="/js/qualityMain.js"></script>
</body>

</html>