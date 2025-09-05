<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Shipments</title>

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
    <!-- Loader -->
    <div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1050;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <!--------------------------------------------------------------------------------------------------------------->

    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Shipment Data</h4>
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
                <div class="col-md-7">
                    <div class="table-container-scroll">
                        <!-- <div class="table-responsive"> -->
                        <!-- Table to display our db user list -->
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="11">Weekly Shipments</th>
                                </tr>
                                <tr>
                                    <th>Week of</th>
                                    <th>10601</th>
                                    <th>10454</th>
                                    <th>WE-5525</th>
                                    <th>10471</th>
                                    <th>10457</th>
                                    <th>10522A</th>
                                    <th>98-1-10881</th>
                                    <th>98-1-10835</th>
                                    <th>98-1-10702</th>
                                    <th>98-1-11020</th>
                                </tr>
                            </thead>
                            <tbody id="shipments">

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
    <!-- Custom javascript -->
    <script type="module" src="/js/inventory/viewShipments.js"></script>
</body>

</html>