<!-- /forms/viewProductionRuns.php -->
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
    <link rel="icon" href="../steinmetz.ico" type="image/x-icon">

</head>

<body>
    <!--Navbar -->
    <?php require_once '../../includes/steinmetzNavbar.php'; ?>



    <!-- Table to hold the last 4 weeks of production  -->
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Run Data</h4>
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
                    <div><strong>Open Production Runs</strong></div>
                    <div class="table-container-scroll">
                        <!-- <div class="table-responsive"> -->
                        <!-- Table to display our db user list -->
                        <table id="getProdRunsNotComplete" class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th>Product</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Hop 1</th>
                                    <th>Hop 2</th>
                                    <th>Hop 3</th>
                                    <th>Hop 4</th>
                                    <th>Parts Produced</th>
                                    <th>Startup Rejects</th>
                                    <th>QA Rejects</th>
                                    <th>Purge lbs</th>
                                </tr>
                            </thead>
                            <tbody id="runsNotComplete">
                                <!-- Data will be populated here by JavaScript -->

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div><strong>Finished Production Runs</strong></div>
                    <div class="table-container-scroll">
                        <!-- <div class="table-responsive"> -->
                        <!-- Table to display our db user list -->
                        <table id="getProdRunsFinished" class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th>Product</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Hop 1</th>
                                    <th>Hop 2</th>
                                    <th>Hop 3</th>
                                    <th>Hop 4</th>
                                    <th>Parts Produced</th>
                                    <th>Startup Rejects</th>
                                    <th>QA Rejects</th>
                                    <th>Purge lbs</th>
                                </tr>
                            </thead>
                            <tbody id="runsFinished">
                                <!-- Data will be populated here by JavaScript -->

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>

    <!-- My custom js -->
    <script type="module" src="/js/viewProdRuns.js"></script>
</body>

</html>