<!-- /forms/viewProductionRuns.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Run Logs</title>

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
    <!-- Loader -->
    <div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1050;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Run Logs</h4>
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

                                </tr>
                            </thead>
                            <tbody id="runProdLogs">
                                <!-- Table data will be inserted here via JavaScript -->

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
    <script type="module" src="/js/production/viewProdRunLogs.js"></script>
</body>

</html>