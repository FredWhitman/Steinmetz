<?

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steinmetz Production</title>

    <!-- Bootstrap core CSS -->
    <link href="../resources/vendors/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="../resources/vendors/css/dashboard.css" rel="stylesheet">
    <link href="../resources/css/myCSS.css" rel="stylesheet">
</head>

<body>
    <!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php'; ?>
    <!-- New production log modal start-->
    <div class="modal fade" id="addProductionModal" tabindex="-1" aria-labelledby="addProductionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style=" width: 55rem;">
            <div class="modal-content ">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addProductionModalLabel">New Production Log</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!--  Form for Production log start -->
                    <form id="productionLog" action="">
                        <div class="container">
                            <!-- TODO: check to make sure date selected is within a normal range or throw message
                            TODO: if in progress radio button is selected pull previous log and subtract values from blender data to fill daily usage data
                            TODO: if end of run is selected pull all data for production and add production run data to database
                            -->
                            <div class="row">
                                <div class="card align-middle">
                                    <div class="row row-cols-2">
                                        <div class="col">
                                            <div class="input-group sm-3"><label class="input-group-text" style="font-size: .75rem" for="partName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" list="partNames" id="partName" name="selectedPart"></div>
                                            <datalist id="partNames"><?php foreach ($partNames as $row) { ?><option><?php echo $row['ProductID']; ?></option><?php } ?></datalist>
                                        </div>
                                        <div class="col text-center">
                                            Production Run Status
                                        </div>
                                        <div class="col ">
                                            <div class="input-group mb-3"><label class="input-group-text" for="logDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="logDate" name="log_date"></div>
                                        </div>
                                        <div class="col text-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="prodRun" id="start" tabindex="3" value="1">Start<label class="form-check-label" for="start"></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="prodRun" tabindex="4" id="inProgress" value="0">In Progress<label class="form-check-label" for="inProgress"></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="prodRun" tabindex="5" id="end" value="2">End<label class="form-check-label" for="end"></label>
                                            </div>
                                            <div class="form-check-input"><input type="hidden" id="prodStatus" name="productionStatus"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="card" style="width: 30rem;">
                                    <div class="card-header">Blender</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Material</th>
                                                        <th class="text-center">Lbs for Run</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="input-group sm-1"><label for="Mat1Name" class="input-group-text">Hopper 1</label><select class="form-select" type="text" name="selected1Mat" id="Mat1Name" required></div>
                                                        </td>

                                                        <td><input class="form-control" type="number" step="0.001" name="hop1" id="hop1Lbs" tabindex="6" oninput="validateDecimalInput(event)" required></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="input-group sm-1"><label for="Mat2Name" class="input-group-text">Hopper 2</label><select class="form-select" type="text" list="materialNames" name="selected2Mat" id="Mat2Name" required></div>
                                                        </td>
                                                        <td><input class="form-control" type="number" step="0.001" name="hop2" id="hop2Lbs" tabindex="7" oninput="validateDecimalInput(event)" required></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="input-group sm-1"><label for="Mat3Name" class="input-group-text">Hopper 3</label><select class="form-select" type="text" list="materialNames" name="selected3Mat" id="Mat3Name"></div>
                                                        </td>
                                                        <td><input class="form-control" type="number" step="0.001" name="hop3" id="hop3Lbs" tabindex="8" oninput="validateDecimalInput(event)"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="input-group sm-1"><label for="Mat4Name" class="input-group-text">Hopper 4</label><select class="form-select" type="text" list="materialNames" name="selected4Mat" id="Mat4Name"></div>
                                                        </td>
                                                        <td><input class="form-control" type="number" step="0.001" name="hop4" id="hop4Lbs" tabindex="9" oninput="validateDecimalInput(event)"></td>
                                                    </tr>
                                                    <tr>

                                                        <td>
                                                            <div class="input-group sm-1"><label for="BlenderTotals" class="input-group-text">Blender Totals</label><input class="form-control" type="number" step="0.001" name="totalsBlender" id="BlenderTotals" oninput="validateInput(event)" readonly></div>
                                                        </td>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">Daily Usage</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Lbs Used</th>
                                                        <th class="text-center">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input class="form-control" type="number" name="hop1LbsDaily" id="dHop1" readonly></td>
                                                        <td><input class="form-control" type="number" name="hop1Percent" id="dHop1p" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input class="form-control" type="number" name="hop2LbsDaily" id="dHop2" readonly></td>
                                                        <td><input class="form-control" type="number" name="hop2Percent" id="dHop2p" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input class="form-control" type="number" name="hop3LbsDaily" id="dHop3" readonly></td>
                                                        <td><input class="form-control" type="number" name="hop3Percent" id="dHop3p" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td><input class="form-control" type="number" name="hop4LbsDaily" id="dHop4" readonly></td>
                                                        <td><input class="form-control" type="number" name="hop4Percent" id="dHop4p" readonly></td>
                                                    </tr>
                                                    <tr>

                                                        <td>
                                                            <div class="input-group sm-1"><label for="dTotal" class="input-group-text">Total</label><input readonly class="form-control" type="text" name="totalDaily" id="dTotal" oninput="validateDecimalInput(event)" readonly></div>
                                                        </td>
                                                        <td>
                                                            <div class="input-group sm-1"><label for="dTotalp" class="input-group-text">%</label><input readonly class="form-control" type="text" name="totalPercent" id="dTotalp" oninput="validateDecimalInput(event)" readonly></div>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row for Dry Information, Cooling Information Hot Runner Information and Production Numbers -->
                        <div class="row">
                            <div class="col">
                                <div class="card" style=" width: 30rem;">
                                    <!-- TODO: This column will hold the dry information and produciton numbers
                                                 TODO: Ensure that only numbers can be entered into fields 
                                            -->
                                    <div class="card-header">Dryer & Production Information</div>
                                    <div class="card-body">
                                        <div class="row row-cols-2">
                                            <div class="col">
                                                <div class="input-group sm-1">
                                                    <label for="bigDryerTemp" class="input-group-text" style="font-size: .75rem">Big Dryer</label>
                                                    <input class="form-control" style="font-size: .75rem" type="number" tabindex="10" min="70" max="240" name="bigDryerTemp" id="bigDryerTemp">
                                                    <input class="form-control" style="font-size: .75rem" type="number" tabindex="11" min="-60" max="0" name="bigDryerDew" id="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="input-group sm"><label for="PressCounter" class="input-group-text">Press Counter</label><input class="form-control form-control-sm" tabindex="14" type="text" name="pressCount" id="PressCounter"></div>
                                            </div>
                                            <div class="col">
                                                <div class="input-group sm-1">
                                                    <label for="PressDryerTemp" class="input-group-text" style="font-size: .75rem">Press Dryer</label>
                                                    <input class="form-control" type="number" style="font-size: .75rem" name="pressDryerTemp" min="70" max="240" tabindex="12" id="PressDryerTemp">
                                                    <input class="form-control" style="font-size: .75rem" type="number" name="pressDryerDew" min="-60" max="0" tabindex="13" id="">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="input-group sm-1"><label for="PressRejects" class="input-group-text" style="font-size: .75rem">Press Rejects</label><input class="form-control form-control-sm" style="font-size: .75rem" tabindex="15" type="text" name="rejects" id="PressRejects"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <!-- TODO: Add Cooling info text inputs and hotrunner info text input
                                                 TODO: make sure that you want to leave the inputs empty if they are empty when they lose focus 
                                            -->
                                <div class="card">
                                    <div class="card-header">Cooling & HotRunner Information</div>
                                    <div class="card-body">
                                        <div class="row">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="Chiller" class="input-group-text">Chiller</label><input class="form-control" tabindex="16" type="text" name="chillerTemp" id="Chiller"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="TCU" class="input-group-text">TCU</label><input class="form-control" tabindex="17" type="text" name="tcuTemp" id="TCU"></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="T1" class="input-group-text">T1</label><input class="form-control" maxlength="3" tabindex="18" type="text" name="t1Temp" id="T1"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="M1" class="input-group-text">M1</label><input class="form-control" maxlength="3" type="text" tabindex="22" name="m1Temp" id="M1"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="M5" class="input-group-text">M5</label><input class="form-control" maxlength="3" type="text" tabindex="26" name="m5Temp" id="M5"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="T2" class="input-group-text">T2</label><input class="form-control" maxlength="3" tabindex="19" type="text" name="t2Temp" id="T2"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="M2" class="input-group-text">M2</label><input class="form-control" maxlength="3" tabindex="23" type="text" name="m2Temp" id="M2"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="M6" class="input-group-text">M6</label><input class="form-control" maxlength="3" type="text" tabindex="27" name="m6Temp" id="M6"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="T3" class="input-group-text">T3</label><input class="form-control" maxlength="3" tabindex="20" type="text" name="t3Temp" id="T3"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="M3" class="input-group-text">M3</label><input class="form-control" type="text" maxlength="3" tabindex="24" name="m3Temp" id="M3"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="M7" class="input-group-text">M7</label><input class="form-control" type="text" maxlength="3" tabindex="28" name="m7Temp" id="M7"></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="T4" class="input-group-text">T4</label><input class="form-control" type="text" maxlength="3" tabindex="21" name="t4Temp" id="T4"></div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group sm-1"><label for="M4" class="input-group-text">M4</label><input class="form-control" type="text" maxlength="3" tabindex="25" name="m4Temp" id="M4"></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row for Comment and button -->
                        <div class="row">
                            <div class="col">
                                <!-- TODO: make sure that you want to leave the inputs empty if they are empty when they lose focus 
                                            -->
                                <div class="card">
                                    <div class="card-header">Comments</div>
                                    <div class="card-body">
                                        <textarea class="form-control" id="commentText" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center"><button type="submit" id="addLog" class="btn btn-dark mb-3" onclick="sumbitForm()">Add Log</button></div>
                </div>
            </div>
            </form>
            <!--  Form for Production log end -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary">Save changes</button>
        </div>
    </div>
    </div>
    </div>

    <!-- New production log modal end-->


    <div class="container-fluid">
        <div class="mt-5">

            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Data</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadProdLogForm" data-bs-toggle="modal" data-bs-target="#addProductionModal">Add Production Log</button>
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
                <div class="col_lg-12">
                    <div class="table_responsive">
                        <!-- Table to display our db user list -->
                        <table class="table table-striped table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>E-mail</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap js -->
    <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>
    <!-- My custom js -->
    <script type="text/javascript" src="../resources/js/main.js"></script>
    <script type="text/javascript" src="../resources/js/prodLogSubmit.js"></script>
</body>

</html>