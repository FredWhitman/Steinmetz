

   
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
    <div class="modal fade" id="viewProductionModal" tabindex="-1" aria-labelledby="viewProductionModalLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header text-center">
                    <h6 class="text-center">Production Log</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--  Form for Production log start -->
                    <form id="productionLog" action="">
                        <!-- Log Information -->
                         <div class="container" id="logInformation">
                            <div class="card pb-1">
                                <div class="card-header">
                                    Log Information
                                </div>    
                                <div class="card-body">
                                    <div class="container text-center">
                                        <div class="row row-cols-2 pb-1">
                                            <div class="col">
                                                <div class="input-group sm-3"><label class="input-group-text" style="font-size: .75rem" for="partName">Part Name</label><input type="text" tabindex="1" class="form-select form-control-sm" id="vpartName" name="vselectedPart" readonly></div>
                                            </div>
                                            <div class="col">
                                                <div class="col text-center">Production Run Status</div>
                                            </div>
                                        </div>
                                        <div class="row row-col-2">
                                            <div class="col ">
                                                <div class="input-group mb-3"><label class="input-group-text" for="logDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="vlogDate" name="vlog_date" readonly></div>
                                            </div>
                                            <div class="col">
                                                <div class="col text-center"><input type="text" class="form-control" name="vprodRun" id="vprodRun" readonly></div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <!-- Card for Blender and Daily Usage-->   
                        <div class="container"id="vblenderDailyUsage"> <input type="hidden" id="vprodStatus" name="vproductionStatus">
                            <div class="row">
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header">Blender and Daily Usage</div>
                                        <div class="card-body">
                                            <div class="container text-center">
                                                <!-- Header Row -->
                                                <div class="row row-cols-5 mx-auto">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">Material</div>
                                                    <div class="col-2">Lbs for Run</div>
                                                    <div class="col-2">Lbs Used</div>
                                                    <div class="col-2">%</div>
                                                </div>
                                                <!-- Hoper 1 Row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">
                                                        <div class="input-group sm-1">
                                                            <label for="vMat1Name" class="input-group-text">Hopper 1</label>
                                                            <input class="form-control" type="text" name="vselected1Mat" id="vMat1Name" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="vop1" id="vhop1Lbs" tabindex="6" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" name="vhop1LbsDaily" id="vdHop1" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" name="vhop1Percent" id="vdHop1p" readonly>
                                                    </div>
                                                </div>
                                                <!-- Hopper 2 row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">
                                                        <div class="input-group sm-1">
                                                            <label for="vMat2Name" class="input-group-text">Hopper 2</label><inout class="form-control" type="text" name="vselected2Mat" id="vMat2Name" required></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="vhop2" id="vhop2Lbs" tabindex="7" readopnly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" name="vhop2LbsDaily" id="vdHop2" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" name="vhop2Percent" id="vdHop2p" readonly>
                                                    </div>
                                                </div>
                                                <!-- Hopper 3 row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">
                                                        <div class="input-group sm-1"><label for="2Mat3Name" class="input-group-text">Hopper 3</label><input class="form-control" type="text" name="vselected3Mat" id="vMat3Name"></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="hop3" id="hop3Lbs" tabindex="8" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" name="hop3LbsDaily" id="dHop3" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" name="hop3Percent" id="dHop3p" readonly>
                                                    </div>
                                                </div>
                                                <!-- Hopper 4 row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">
                                                        <div class="input-group sm-1">
                                                            <label for="Mat4Name" class="input-group-text">Hopper 4</label>
                                                            <input class="form-control" type="text" name="vselected4Mat" id="vMat4Name" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="vhop4" id="vhop4Lbs" tabindex="9" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1">
                                                            <input class="form-control" type="number" name="vhop4LbsDaily" id="vdHop4" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1"><input class="form-control" type="number" name="vhop4Percent" id="vdHop4p" readonly></div>
                                                    </div>
                                                </div>    
                                                <!-- Totals row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5 d-flex justify-content-end align-items-center">
                                                        <h6 class="text-end">Totals</h6>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="vtotalsBlender" id="vBlenderTotals" oninput="validateInput(event)" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input readonly class="form-control" type="text" name="vtotalDaily" id="vdTotal" oninput="validateDecimalInput(event)" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input readonly class="form-control" type="text" name="vtotalPercent" id="vdTotalp" oninput="validateDecimalInput(event)" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Dryer and Production Information -->
                        <div class="container" id ="vdryerProductionInfo" style="width:31rem;">
                            <div class="card">
                                <!-- TODO: This column will hold the dry information and produciton numbers
                                     TODO: Ensure that only numbers can be entered into fields 
                                -->
                                <div class="card-header">Dryer & Production Information</div>
                                <div class="card-body">
                                    <div class="row row-cols-2">
                                        <div class="col">
                                            <div class="input-group sm-1">
                                                <label for="bigDryerTemp" class="input-group-text" style="font-size: .75rem">Big Dryer</label>
                                                <input class="form-control" style="font-size: .75rem" type="number" tabindex="10" min="70" max="240" name="vbigDryerTemp" id="vbigDryerTemp">
                                                <input class="form-control" style="font-size: .75rem" type="number" tabindex="11" min="-60" max="0" name="vbigDryerDew" id="">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm"><label for="vPressCounter" class="input-group-text">Press Counter</label><input class="form-control form-control-sm" tabindex="14" type="number" name="vpressCount" id="vPressCounter"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-2">
                                        <div class="col">
                                            <div class="input-group sm-1">
                                                <label for="PressDryerTemp" class="input-group-text" style="font-size: .75rem">Press Dryer</label>
                                                <input class="form-control" type="number" style="font-size: .75rem" name="pressDryerTemp" min="70" max="240" tabindex="12" id="PressDryerTemp">
                                                <input class="form-control" style="font-size: .75rem" type="number" name="pressDryerDew" min="-60" max="0" tabindex="13" id="">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="PressRejects" class="input-group-text" style="font-size: .75rem">Press Rejects</label><input class="form-control form-control-sm" style="font-size: .75rem" tabindex="15" type="number" name="rejects" id="PressRejects"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row for Chiller and Hot runner information -->
                        <div class="container" id="chillerHotRunner" style="width:31rem;">
                            <!-- TODO: Add Cooling info text inputs and hotrunner info text input
                                 TODO: make sure that you want to leave the inputs empty if they are empty when they lose focus 
                            -->
                            <div class="card">
                                <div class="card-header">Cooling & HotRunner Information</div>
                                <div class="card-body">
                                    <div class="row row-cols-2 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="Chiller" class="input-group-text">Chiller</label><input class="form-control" tabindex="16" type="number" name="chillerTemp" id="Chiller"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="TCU" class="input-group-text">TCU</label><input class="form-control" tabindex="17" type="number" name="tcuTemp" id="TCU"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text-center"><h7>Hot Runner Temps</h7></div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="T1" class="input-group-text">T1</label><input class="form-control" maxlength="3" tabindex="18" type="number" name="t1Temp" id="T1"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M1" class="input-group-text">M1</label><input class="form-control" maxlength="3" type="number" tabindex="22" name="m1Temp" id="M1"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M5" class="input-group-text">M5</label><input class="form-control" maxlength="3" type="number" tabindex="26" name="m5Temp" id="M5"></div>
                                        </div>
                                    </div>    
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="T2" class="input-group-text">T2</label><input class="form-control" maxlength="3" tabindex="19" type="number" name="t2Temp" id="T2"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M2" class="input-group-text">M2</label><input class="form-control" maxlength="3" tabindex="23" type="number" name="m2Temp" id="M2"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M6" class="input-group-text">M6</label><input class="form-control" maxlength="3" type="number" tabindex="27" name="m6Temp" id="M6"></div>
                                        </div>
                                    </div>    
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="T3" class="input-group-text">T3</label><input class="form-control" maxlength="3" tabindex="20" type="number" name="t3Temp" id="T3"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M3" class="input-group-text">M3</label><input class="form-control" type="number" maxlength="3" tabindex="24" name="m3Temp" id="M3"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M7" class="input-group-text">M7</label><input class="form-control" type="number" maxlength="3" tabindex="28" name="m7Temp" id="M7"></div>
                                        </div>
                                    </div>    
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="T4" class="input-group-text">T4</label><input class="form-control" type="number" maxlength="3" tabindex="21" name="t4Temp" id="T4"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M4" class="input-group-text">M4</label><input class="form-control" type="number" maxlength="3" tabindex="25" name="m4Temp" id="M4"></div>
                                        </div>
                                        <div class="col">

                                        </div>
                                    </div>    
                                    <div class="row row-cols-3">
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <!-- Row for Comment and button -->
                        <div class="container" id="commentSubmit" style="width:31rem;">
                            <!-- TODO: make sure that you want to leave the inputs empty if they are empty when they lose focus 
                                                    -->
                            <div class="card">
                                <div class="card-header">Comments</div>
                                <div class="card-body">
                                    <textarea class="form-control" id="commentText" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center p-2">
                                <div class=pe-1><button type="submit" id="cancel" data-bs-dismiss="modal" class="btn btn-danger btn-sm">Cancel</button></div>
                                <button type="submit" id="addLog" class="btn btn-success btn-sm" onclick="sumbitForm()">Add Log</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </div>

    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Data</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadProdLogForm" data-bs-toggle="modal" data-bs-target="#viewProductionModal">Add Production Log</button>
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
                        <table id = "last4wks" class="table table-striped table-bordered text-center" >
                            <thead>
                                <tr >
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
    <script type="text/javascript" src="../resources/vendors/js/bootstrap.bundle.min.js"></script>
    <!-- My custom js -->
    <script type="text/javascript" src="../resources/js/main.js"></script>

</body>

</html>