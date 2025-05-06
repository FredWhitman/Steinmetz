<?php
    
    require_once '../src/Classes/products.php';
    require_once '../src/Classes/viewData.php';
    $productObj = new products();
    $data = new viewData();

    //$partNames = $productObj->get_PartNames();

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
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous"> -->
    <link href="../resources/vendors/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script> -->
    <!-- Custom styles for this template -->
    <link href="../resources/vendors/css/dashboard.css" rel="stylesheet">
    <link href="../resources/css/myCSS.css" rel="stylesheet">
</head>

    <body>
        <?php require_once '../includes/steinmetzNavbar.php';  ?>

      <div class="container-fluid d-inline-flex justify-content-center mt-5">
        <div class="card" style="width: 55rem;">
          <div class="card-header text-center">Search Production Logs</div>
            <div class="card-body justify-content-center">
              <form class="form-horizontal align-items-center">
                <div class="row align-items-center">
                  <div class="input-group sm-3"><label class="input-group-text" style="font-size: .75rem" for="partName">Part Name</label><input type="text" tabindex="1" class="form-select form-control-sm" list="partNames" id="partName" name="selectedPart"></div>
                  <div class="input-group sm-3"><label class="input-group-text" for="prodDate">Production Log</label><input class="form-control" type="date" tabindex="2" id="prodDate" name="prod_date"></div>
                  <button class="btn btn-dark btn-sm" type="submit">Submit</button>
                </div>
              </form>  
            </div>
          </div>
        </div>  
      </div>
    
      <div class="container-fluid d-flex justify-content-center">
        <div class="card">
          <div class="card-header">Production Data</div>
            <div class="card-body">

            </div>
          </div>
      </div>  
 
    </body>
</html>