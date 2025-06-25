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
    <!--------------------------------------------------------------------------------------------------------------->
    <!-- New production log modal start-->
    <div class="modal fade" id="addProductionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header text-center">
                    <h6 class="text-center">New Production Log</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Alert container -->
                    <div id="alertContainer"></div>
                    <!--  Form for Production log start -->
                    <form id="add-productionLog-form" class="needs-validation p-2" novalidate>
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
                                                <div class="input-group sm-3"><label class="input-group-text" style="font-size: .75rem" for="partName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" list="partNames" id="partName" name="partName" required></select></div>
                                                <div class="invalid-feedback">part name is required!</div>
                                            </div>
                                            <div class="col">
                                                <div class="col text-center">Production Run Status</div>
                                            </div>
                                        </div>
                                        <div class="row row-col-2">
                                            <div class="col ">
                                                <div class="input-group mb-3"><label class="input-group-text" for="logDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="logDate" name="logDate" required></div>
                                            </div>
                                            <div class="col">
                                                <div class="col">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="prodRun" id="start" tabindex="3" value="1" required>Start<label class="form-check-label" for="start"></label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="prodRun" tabindex="4" id="inProgress" value="0">In Progress<label class="form-check-label" for="inProgress"></label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="prodRun" tabindex="5" id="end" value="2">End<label class="form-check-label" for="end"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Card for Blender and Daily Usage-->
                        <div class="container" id="blenderDailyUsage"> <input type="hidden" id="prodStatus" name="productionStatus">
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
                                                            <label for="Mat1Name" class="input-group-text">Hopper 1</label>
                                                            <select class="form-select" type="text" name="Mat1Name" id="Mat1Name" required></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="hop1Lbs" id="hop1Lbs" tabindex="6" oninput="validateDecimalInput(event)" required>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1"><input class="form-control" type="number" name="dHop1" id="dHop1" readonly></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" name="dHop1p" id="dHop1p" readonly>
                                                    </div>
                                                </div>
                                                <!-- Hopper 2 row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">
                                                        <div class="input-group sm-1"><label for="Mat2Name" class="input-group-text">Hopper 2</label><select class="form-select" type="text" list="materialNames" name="Mat2Name" id="Mat2Name" required></select></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="hop2Lbs" id="hop2Lbs" tabindex="7" oninput="validateDecimalInput(event)" required>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1"><input class="form-control" type="number" name="dHop2" id="dHop2" readonly></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm1"><input class="form-control" type="number" name="dHop2p" id="dHop2p" readonly></div>
                                                    </div>
                                                </div>
                                                <!-- Hopper 3 row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">
                                                        <div class="input-group sm-1"><label for="Mat3Name" class="input-group-text">Hopper 3</label><select class="form-select" type="text" list="materialNames" name="Mat3Name" id="Mat3Name"></select></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="hop3Lbs" id="hop3Lbs" tabindex="8" oninput="validateDecimalInput(event)">
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1"><input class="form-control" type="number" name="dHop3" id="dHop3" readonly></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1"><input class="form-control" type="number" name="dHop3p" id="dHop3p" readonly></div>
                                                    </div>
                                                </div>
                                                <!-- Hopper 4 row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5">
                                                        <div class="input-group sm-1">
                                                            <label for="Mat4Name" class="input-group-text">Hopper 4</label>
                                                            <select class="form-select" type="text" list="materialNames" name="Mat4Name" id="Mat4Name"></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="hop4Lbs" id="hop4Lbs" tabindex="9" oninput="validateDecimalInput(event)">
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1">
                                                            <input class="form-control" type="number" name="dHop4" id="dHop4" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1"><input class="form-control" type="number" name="dHop4p" id="dHop4p" readonly></div>
                                                    </div>
                                                </div>
                                                <!-- Totals row -->
                                                <div class="row row-cols-5 mx-auto pb-1">
                                                    <div class="col-1"></div>
                                                    <div class="col-5 d-flex justify-content-end align-items-center">
                                                        <h6 class="text-end">Totals</h6>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control" type="number" step="0.001" name="totalsBlender" id="BlenderTotals" oninput="validateInput(event)" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input readonly class="form-control" type="number" name="totalDaily" id="dTotal" oninput="validateDecimalInput(event)" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input readonly class="form-control" type="number" name="totalPercent" id="dTotalp" oninput="validateDecimalInput(event)" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Dryer and Production Information -->
                        <div class="container" id="dryerProductionInfo" style="width:31rem;">
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
                                                <input class="form-control" style="font-size: .75rem" type="number" tabindex="10" min="0" max="240" name="bigDryerTemp" id="bigDryerTemp" required>
                                                <input class="form-control" style="font-size: .75rem" type="number" tabindex="11" min="-60" name="bigDryerDew" id="bigDryerDew" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm"><label for="PressCounter" class="input-group-text">Press Counter</label><input class="form-control form-control-sm" tabindex="14" type="number" name="pressCounter" id="pessCounter" required></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-2">
                                        <div class="col">
                                            <div class="input-group sm-1">
                                                <label for="PressDryerTemp" class="input-group-text" style="font-size: .75rem">Press Dryer</label>
                                                <input class="form-control" type="number" style="font-size: .75rem" name="pressDryerTemp" min="0" max="240" tabindex="12" id="PressDryerTemp">
                                                <input class="form-control" style="font-size: .75rem" type="number" name="pressDryerDew" min="-60" tabindex="13" id="">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="PressRejects" class="input-group-text" style="font-size: .75rem">Start Up Rejects</label><input class="form-control form-control-sm" style="font-size: .75rem" tabindex="15" type="number" name="startUpRejects" id="startUpRejects" required></div>
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
                                            <div class="input-group sm-1"><label for="Chiller" class="input-group-text">Chiller</label><input class="form-control" tabindex="16" type="number" name="chiller" id="chiller" required></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="TCU" class="input-group-text">TCU</label><input class="form-control" tabindex="17" type="number" name="tcuTemp" id="tcuTemp" required></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text-center">
                                            <h7>Hot Runner Temps</h7>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="t1" class="input-group-text">T1</label><input class="form-control" maxlength="3" tabindex="18" type="number" name="t1" id="t1" required></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M1" class="input-group-text">M1</label><input class="form-control" maxlength="3" type="number" tabindex="22" name="m1" id="m1"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M5" class="input-group-text">M5</label><input class="form-control" maxlength="3" type="number" tabindex="26" name="m5" id="m5"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="T2" class="input-group-text">T2</label><input class="form-control" maxlength="3" tabindex="19" type="number" name="t2" id="t2"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M2" class="input-group-text">M2</label><input class="form-control" maxlength="3" tabindex="23" type="number" name="m2" id="m2"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M6" class="input-group-text">M6</label><input class="form-control" maxlength="3" type="number" tabindex="27" name="m6" id="m6"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="T3" class="input-group-text">T3</label><input class="form-control" maxlength="3" tabindex="20" type="number" name="t3" id="t3"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M3" class="input-group-text">M3</label><input class="form-control" type="number" maxlength="3" tabindex="24" name="m3" id="m3"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M7" class="input-group-text">M7</label><input class="form-control" type="number" maxlength="3" tabindex="28" name="m7" id="m7"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="T4" class="input-group-text">T4</label><input class="form-control" type="number" maxlength="3" tabindex="21" name="t4" id="t4"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="M4" class="input-group-text">M4</label><input class="form-control" type="number" maxlength="3" tabindex="25" name="m4" id="m4"></div>
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
                                    <textarea class="form-control" name="commentText" id="commentText" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center p-2">
                                <div class=pe-1><button type="submit" id="cancel" data-bs-dismiss="modal" class="btn btn-danger btn-sm">Cancel</button></div>
                                <button type="submit" id="add-log-btn" class="btn btn-success btn-sm">Add Log</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- New production log modal end-->
    <!--------------------------------------------------------------------------------------------------------------->
    <!-- View production log modal start -->
    <div class="modal fade" id="viewProductionModal" tabindex="-1" aria-labelledby="viewProductionModalLabel">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header text-center">
                    <h6 class="text-center">Production Log</h6>
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
                        <div class="container" id="vblenderDailyUsage"> <input type="hidden" id="vprodStatus" name="vproductionStatus"><input type="hidden" id="logID" name="logID">
                            <div class="row">
                                <div class="col">
                                    <div class="card">
                                        <div class="card-header">Blender and Daily Usage</div>
                                        <div class="card-body">
                                            <div class="container text-center">
                                                <!-- Header Row -->
                                                <div class="row row-cols-4 mx-auto">
                                                    <div class="col-4">Material</div>
                                                    <div class="col-3">Lbs for Run</div>
                                                    <div class="col-3">Lbs Used</div>
                                                    <div class="col-2">%</div>
                                                </div>
                                                <!-- Hoper 1 Row -->
                                                <div class="row row-cols-4 mx-auto pb-1">
                                                    <div class="col-4">
                                                        <div class="input-group sm-1">
                                                            <label for="vMat1Name" class="input-group-text">Hopper 1</label>
                                                            <input class="form-control" type="text" name="vselected1Mat" id="vMat1Name" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="number" step="0.001" name="vop1" id="vhop1Lbs" tabindex="6" readonly>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="text" name="vhop1LbsDaily" id="vdHop1" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control text-center" type="text" name="vhop1Percent" id="vdHop1p" readonly>
                                                    </div>
                                                </div>
                                                <!-- Hopper 2 row -->
                                                <div class="row row-cols-4 mx-auto pb-1">
                                                    <div class="col-4">
                                                        <div class="input-group sm-1">
                                                            <label for="vMat2Name" class="input-group-text">Hopper 2</label><input class="form-control" type="text" name="vselected2Mat" id="vMat2Name" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="number" step="0.001" name="vhop2" id="vhop2Lbs" tabindex="7" readopnly>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="text" name="vhop2LbsDaily" id="vdHop2" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control text-center" type="text" name="vhop2Percent" id="vdHop2p" readonly>
                                                    </div>
                                                </div>
                                                <!-- Hopper 3 row -->
                                                <div class="row row-cols-4 mx-auto pb-1">
                                                    <div class="col-4">
                                                        <div class="input-group sm-1"><label for="2Mat3Name" class="input-group-text">Hopper 3</label><input class="form-control" type="text" name="vselected3Mat" id="vMat3Name"></div>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="number" step="0.001" name="vhop3" id="vhop3Lbs" tabindex="8" readonly>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="number" name="vhop3LbsDaily" id="vdHop3" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input class="form-control text-center" type="number" name="vhop3Percent" id="vdHop3p" readonly>
                                                    </div>
                                                </div>
                                                <!-- Hopper 4 row -->
                                                <div class="row row-cols-4 mx-auto pb-1">
                                                    <div class="col-4">
                                                        <div class="input-group sm-1">
                                                            <label for="Mat4Name" class="input-group-text">Hopper 4</label>
                                                            <input class="form-control" type="text" name="vselected4Mat" id="vMat4Name" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="number" step="0.001" name="vhop4" id="vhop4Lbs" tabindex="9" readonly>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="input-group sm-1">
                                                            <input class="form-control text-center" type="text" name="vhop4LbsDaily" id="vdHop4" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-2">
                                                        <div class="input-group sm-1"><input class="form-control text-center" type="text" name="vhop4Percent" id="vdHop4p" readonly></div>
                                                    </div>
                                                </div>
                                                <!-- Totals row -->
                                                <div class="row row-cols-4 mx-auto pb-1">

                                                    <div class="col-4 d-flex justify-content-end align-items-center">
                                                        <h6 class="text-end">Totals</h6>
                                                    </div>
                                                    <div class="col-3">
                                                        <input class="form-control text-center" type="text" name="vtotalsBlender" id="vBlenderTotals" readonly>
                                                    </div>
                                                    <div class="col-3">
                                                        <input readonly class="form-control text-center" type="text" name="vtotalDaily" id="vdTotal" readonly>
                                                    </div>
                                                    <div class="col-2">
                                                        <input readonly class="form-control text-center" type="text" name="vtotalPercent" id="vdTotalp" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Dryer and Production Information -->
                        <div class="container" id="vdryerProductionInfo" style="width:31rem;">
                            <div class="card">
                                <!-- TODO: This column will hold the dry information and produciton numbers
                                     TODO: Ensure that only numbers can be entered into fields 
                                -->
                                <div class="card-header">Dryer & Production Information</div>
                                <div class="card-body">
                                    <div class="row row-cols-2">
                                        <div class="col">
                                            <div class="input-group sm-1">
                                                <label for="vbigDryerTemp" class="input-group-text" style="font-size: .75rem">Big Dryer</label>
                                                <input class="form-control" style="font-size: .75rem" type="number" name="vbigDryerTemp" id="vbigDryerTemp" readonly>
                                                <input class="form-control" style="font-size: .75rem" type="number" name="vbigDryerDew" id="vbigDryerDew" readonly>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm"><label for="vPressCounter" class="input-group-text">Press Counter</label><input class="form-control form-control-sm" tabindex="14" type="number" name="vpressCount" id="vPressCounter"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-2">
                                        <div class="col">
                                            <div class="input-group sm-1">
                                                <label for="vPressDryerTemp" class="input-group-text" style="font-size: .75rem">Press Dryer</label>
                                                <input class="form-control" type="number" style="font-size: .75rem" name="vpressDryerTemp" id="vPressDryerTemp">
                                                <input class="form-control" style="font-size: .75rem" type="number" name="vpressDryerDew" id="vPressDryerDew">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vPressRejects" class="input-group-text" style="font-size: .75rem">Press Rejects</label><input class="form-control form-control-sm" style="font-size: .75rem" tabindex="15" type="number" name="vrejects" id="vPressRejects"></div>
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
                                            <div class="input-group sm-1"><label for="vChiller" class="input-group-text">Chiller</label><input class="form-control" tabindex="16" type="number" name="vchillerTemp" id="vChiller"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vTCU" class="input-group-text">TCU</label><input class="form-control" tabindex="17" type="number" name="vtcuTemp" id="vTCU"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text-center">
                                            <h7>Hot Runner Temps</h7>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vT1" class="input-group-text">T1</label><input class="form-control" maxlength="3" tabindex="18" type="number" name="vt1Temp" id="vT1"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vM1" class="input-group-text">M1</label><input class="form-control" maxlength="3" type="number" tabindex="22" name="vm1Temp" id="vM1"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vM5" class="input-group-text">M5</label><input class="form-control" maxlength="3" type="number" tabindex="26" name="vm5Temp" id="vM5"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vT2" class="input-group-text">T2</label><input class="form-control" maxlength="3" tabindex="19" type="number" name="vt2Temp" id="vT2"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vM2" class="input-group-text">M2</label><input class="form-control" maxlength="3" tabindex="23" type="number" name="vm2Temp" id="vM2"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vM6" class="input-group-text">M6</label><input class="form-control" maxlength="3" type="number" tabindex="27" name="vm6Temp" id="vM6"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vT3" class="input-group-text">T3</label><input class="form-control" maxlength="3" tabindex="20" type="number" name="vt3Temp" id="vT3"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vM3" class="input-group-text">M3</label><input class="form-control" type="number" maxlength="3" tabindex="24" name="vm3Temp" id="vM3"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vM7" class="input-group-text">M7</label><input class="form-control" type="number" maxlength="3" tabindex="28" name="vm7Temp" id="vM7"></div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-3 pb-1">
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vT4" class="input-group-text">T4</label><input class="form-control" type="number" maxlength="3" tabindex="21" name="vt4Temp" id="vT4"></div>
                                        </div>
                                        <div class="col">
                                            <div class="input-group sm-1"><label for="vM4" class="input-group-text">M4</label><input class="form-control" type="number" maxlength="3" tabindex="25" name="vm4Temp" id="vM4"></div>
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
                                    <textarea class="form-control" id="vcommentText" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center p-2">
                                <div class=pe-1><button type="submit" id="close" data-bs-dismiss="modal" class="btn btn-danger btn-sm">Close</button></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- View production log modal end -->
    <!--------------------------------------------------------------------------------------------------------------->

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
                                    <div class="input-group sm-3"><label class="input-group-text" for="p_aPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="p_PartName" name="p_Part" required></select></div>
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
                                <div class="input-group sm-3"><label class="input-group-text" for="p_purge">Lbs of purge</label><input type="number" step="0.001" tabindex="1" class="form-control form-control-sm" id="p_purge" name="p_purge" required></div>
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
    <!-- Add Lot Changes to production log start-->
    <div class="modal fade" id="addLotChangeModal" tabindex="-1" aria-labelledby="addLotChangeModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Lot Change</h1>
                </div>
                <div class="modal-body">
                    <form id="add-lotchange-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="lcPartName" name="lcPart" required></select></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Material Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="lcMatName" name="lcMat" required></select></div>
                                    <div class="invalid-feedback">Material is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="lclotDate" name="lcLotDate" required></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">Time</label><input class="form-control" type="time" id="lclotTime" name="lcLotTime" value="09:00" required></div>
                                    <div class="invalid-feedback">Change time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">Old Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="lcOldLot" name="lcOldLot" required></div>
                                <div class="invalid-feedback">Old lot number is required!</div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcNewLot">New Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="lcNewLot" name="lcNewLot" required></div>
                                <div class="invalid-feedback">New lot numger is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="comment-text" name="lcComments"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Add Lot Change" class="btn btn-success" id="add-lotchange-btn">Add Lot Change</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Lot Changes to production log end-->

    <!-- Table to hold the last 4 weeks of production  -->
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Production Data</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadLotChangeForm" data-bs-toggle="modal" data-bs-target="#addLotChangeModal">Add Lot Change</button>
                        <button class="btn btn-primary" type="button" id="loadPurgeForm" data-bs-toggle="modal" data-bs-target="#addPurgeModal">Add Purge</button>
                        <button class="btn btn-primary" type="button" id="loadQARejectForm" data-bs-toggle="modal" data-bs-target="#addQARejectsModal">Add QA Rejects</button>
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
    <script type="module" src="/js/productionMain.js"></script>
</body>

</html>