<?php
session_start();

require_once '../src/Classes/products.php';
require_once '../src/Classes/viewData.php';
$productObj = new products();
$data = new viewData();

$partNames = $productObj->get_PartNames();

?>

<datalist id="partNames">
  <?php foreach ($partNames as $row) {  ?> <option> <?php echo $row['ProductID']; ?> </option> <?php } ?>
</datalist>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Steinmetz Inc Inventopry and Maintenance Website">
  <meta name="author" content="Fred Whitman">

  <title>Steinmetz Inc</title>

  <!-- Bootstrap core CSS -->
  <link href="../resources/vendors/css/bootstrap.min.css" rel="stylesheet">
  <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>

  <!-- Custom styles for this template -->
  <link href="../resources/vendors/css/dashboard.css" rel="stylesheet">
  <link href="../resources/css/myCSS.css" rel="stylesheet">
</head>

<body>
  <?php require_once '../includes/steinmetzNavbar.php';  ?>
  <div class="mt-5">
    <?php if (isset($_SESSION['status']) && $_SESSION['status'] != '') { ?>
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>DB operation</strong><?php echo $_SESSION['status']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php unset($_SESSION['status']);
    } ?>

    <div class="container-fluid d-flex justify-content-center">
      <div class="card" style="width: 55rem;">
        <div class="card-header">
          <h5 class="text-center">Production Log Query</h5>
        </div>
        <div class="card-body">
          <div class="row align-items-center">
            <form class="form-horizontal" action="" method="POST">
              <div class="d-inline-flex justify-content-center">
                <div class="d-flex justify-content-center p-1">
                  <div class="form-group">
                    <div class="input-group sm-3"><label class="input-group-text" style="font-size: .75rem" for="partName">Part Name</label><input type="text" tabindex="1" class="form-select form-control-sm" list="partNames" id="partName" name="selectedPart"></div>
                  </div>
                </div>
                <div class="d-flex justify-content-center p-1">
                  <div class="form-group">
                    <div class="input-group sm-3"><label class="input-group-text" for="prodDate">Production Log</label><input class="form-control" type="date" tabindex="2" id="prodDate" name="prod_date"></div>
                  </div>
                </div>
                <div class="d-flex justify-content-center p-1">
                  <div class="form-group">
                    <button class="btn btn-dark btn-sm" type="submit" id="btn_getProdLog" name="getProdLog">Search</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="card-footer">
          <div class="d-flex justify-content-center p-1">
            <div class="form-group">

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid d-flex justify-content-center">
      <div class="card" style="width: 55rem;">
        <div class="card-header">
          <h5 class="text-center">Production Log</h5>
        </div>
        <div class="card-body">
          <!-- TODO:  Setup table for production log information -->
        </div>
        <div class="card-footer">

        </div>
      </div>
    </div>
</body>

</html>