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

</head>
<body>
<!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php'; ?>
    





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
                        <button class="btn btn-primary" type="button" id="loadMaterialForm" data-bs-toggle="modal" data-bs-target="#addQARejects">Add QA Rejects</button>
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
                
            </div>
        </div>
    </div>

    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>
    <!-- Custom javascript -->
    <script type="module" src="/js/inventoryMain.js"></script>    
</body>
</html>