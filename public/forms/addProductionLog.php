<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Production Log</title>

    <!-- Bootstrap core CSS -->
    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/lib/css/dashboard.css" rel="stylesheet">
    <link href="/css/myCSS.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/../steinmetz.ico" type="image/x-icon">

</head>

<body>
    <!--Navbar -->
    <?php require_once '../../includes/steinmetzNavbar.php'; ?>

    <div class="row mt-5">
        <div class="col-lg-12">
            <div id="showAlert"></div>
        </div>
    </div>
    <hr>

    <div class="d-flex justify-content-lg-evenly ">

        <!--  Form for Production log start -->
        <form id="add-productionLog-form" class="needs-validation p-2" novalidate>
            <!-- Log Information -->

            <h3 class="text-center">Add Production Log</h3>

            <div class="d-flex justify-content-center">
                <div class="card pb-1">
                    <div class="card-header">
                        Log Information
                    </div>
                    <div class="card-body">
                        <div class="container text-center">
                            <div class="row row-col-2 pb-1">
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


            <!-- Card for Blender, Daily Usage & production totols-->
            <div class="d-flex">
                <input type="hidden" id="prodStatus" name="productionStatus">
                <div class="row">
                    <div class="col w-50">
                        <div class="card">
                            <div class="card-header">Blender and Daily Usage</div>
                            <div class="card-body">
                                <!-- Header Row -->
                                <div class="d-flex flex-row justify-content-evenly mb-2 g-1">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6"><strong>Material</strong></div>
                                    <div class="col-sm-2"><strong>Lbs for Run</strong></div>
                                    <div class="col-sm-2"></div>
                                </div>
                                <!-- Hoper 1 Row -->
                                <!-- <div class="row"> -->
                                <div class="d-flex flex-row justify-content-evenly  mb-1 g-1">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6">
                                        <div class="input-group">
                                            <label for="Mat1Name" class="input-group-text">Hopper 1</label>
                                            <select class="form-select" type="text" name="Mat1Name" id="Mat1Name" required></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" type="number" step="0.001" name="hop1Lbs" id="hop1Lbs" tabindex="6" required>
                                    </div>
                                    <div class="col-sm-2">

                                    </div>
                                </div>
                                <!-- Hoper 2 Row -->
                                <div class="d-flex flex-row justify-content-evenly  mb-1 g-1">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6">
                                        <div class="input-group sm-1"><label for="Mat2Name" class="input-group-text">Hopper 2</label><select class="form-select" type="text" list="materialNames" name="Mat2Name" id="Mat2Name" required></select></div>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" type="number" step="0.001" name="hop2Lbs" id="hop2Lbs" tabindex="7" required>
                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                </div>
                                <!-- Hoper 3 Row -->
                                <div class="d-flex flex-row justify-content-evenly  mb-1 g-1">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6">
                                        <div class="input-group sm-1"><label for="Mat3Name" class="input-group-text">Hopper 3</label><select class="form-select" type="text" list="materialNames" name="Mat3Name" id="Mat3Name"></select></div>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" type="number" step="0.001" name="hop3Lbs" id="hop3Lbs" tabindex="8">
                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                </div>
                                <!-- Hopper 4 row -->
                                <div class="d-flex flex-row justify-content-evenly  mb-1 g-1">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6">
                                        <div class="input-group sm-1">
                                            <label for="Mat4Name" class="input-group-text">Hopper 4</label>
                                            <select class="form-select" type="text" list="materialNames" name="Mat4Name" id="Mat4Name"></select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <input class="form-control" type="number" step="0.001" name="hop4Lbs" id="hop4Lbs" tabindex="9">
                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                </div>
                                <!-- Totals row -->
                                <div class="d-flex flex-row justify-content-evenly  mb-1 g-1">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6">
                                        <h6 class="text-end">Totals</h6>
                                    </div>
                                    <div class="col-2">
                                        <input class="form-control" type="number" step="0.001" name="totalsBlender" id="BlenderTotals" readonly>
                                    </div>
                                    <div class="col-sm-2"></div>
                                </div>
                                <div class="d-flex flex-row justify-content-evenly  mb-2 g-1"><strong>Daily Usage</strong></div>
                                <div class="d-flex flex-row justify-content-evenly  mb-2 g-1">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-4 d-flex justify-content-center"><strong> Used</strong></div>
                                    <div class="col-sm-1 d-flex justify-content-center"><strong>%</strong></div>
                                    <div class="col-sm-4"></div>
                                </div>
                                <!-- Hoper 1 Row -->
                                <div class="d-flex flex-row justify-content-evenly mb-1 g-1">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-4">
                                        <div class="input-group sm-1">
                                            <label for="dHop1" class="input-group-text">Hopper 1</label>
                                            <input class="form-control" type="number" name="dHop1" id="dHop1" readonly></input>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <input class="form-control" type="text" name="dHop1p" id="dHop1p" readonly>
                                    </div>
                                    <div class="col-sm-4">

                                    </div>
                                </div>
                                <!-- Hopper 2 row -->
                                <div class="d-flex flex-row justify-content-evenly mb-1 g-1">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-4">
                                        <div class="input-group sm-1">
                                            <label for="dHop1" class="input-group-text">Hopper 2</label>
                                            <input class="form-control" type="number" name="dHop2" id="dHop2" readonly></input>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <input class="form-control" type="text" name="dHop2p" id="dHop2p" readonly></input>
                                    </div>
                                    <div class="col-sm-4">

                                    </div>
                                </div>
                                <!-- Hopper 3 row -->
                                <div class="d-flex flex-row justify-content-evenly mb-1 g-1">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-4">
                                        <div class="input-group sm-1">
                                            <label for="dHop3" class="input-group-text">Hopper 3</label>
                                            <input class="form-control" type="number" name="dHop3" id="dHop3" readonly></input>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <input class="form-control" type="text" name="dHop3p" id="dHop3p" readonly></input>
                                    </div>
                                    <div class="col-sm-4">

                                    </div>
                                </div>
                                <!-- Hopper 4 row -->
                                <div class="d-flex flex-row justify-content-evenly mb-1 g-1">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-4">
                                        <div class="input-group sm-1">
                                            <label for="dHop4" class="input-group-text">Hopper 4</label>
                                            <input class="form-control" type="number" name="dHop4" id="dHop4" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <input class="form-control" type="text" name="dHop4p" id="dHop4p" readonly>
                                    </div>
                                    <div class="col-sm-4">
                                    </div>
                                </div>
                                <!-- Totals row -->
                                <div class="d-flex flex-row justify-content-evenly mb-2 g-1">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-3">
                                        <div class="input-group sm-1">
                                            <label for="totalDaily" class="input-group-text">Totals</label>
                                            <input class="form-control" type="number" name="totalDaily" id="dTotal" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <input readonly class="form-control" type="text" name="totalPercent" id="dTotalp" readonly>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col w-50">
                        <div class="card">
                            <div class="card-header">
                                Dryer & Production Information
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-row justify-content-evenly  mb-2 g-1">
                                    <div class="col-sm-3">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group">
                                                <label for="bigDryerTemp" class="input-group-text">Big Dryer</label>
                                                <input class="form-control" type="number" tabindex="10" min="0" max="240" name="bigDryerTemp" id="bigDryerTemp" required></input>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <input class="form-control" type="number" tabindex="11" min="-60" name="bigDryerDew" id="bigDryerDew" required></input>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group">
                                                <label for="Chiller" class="input-group-text">Chiller</label>
                                                <input class="form-control" tabindex="14" type="number" name="chiller" id="chiller" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group">
                                                <label for="Parts" class="input-group-text">Parts</label>
                                                <input class="form-control" tabindex="16" type="number" name="pressCounter" id="pressCounter" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex flex-row justify-content-evenly  mb-2 g-1">
                                    <div class="col-sm-3">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group">
                                                <label for="PressDryerTemp" class="input-group-text">Press Dryer</label>
                                                <input class="form-control" type="number" name="pressDryerTemp" min="0" max="240" tabindex="12" id="PressDryerTemp">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <input class="form-control" type="number" name="pressDryerDew" min="-60" tabindex="13" id="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group">
                                                <label for="TCU" class="input-group-text">TCU</label>
                                                <input class="form-control" tabindex="15" type="number" name="tcuTemp" id="tcuTemp" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group">
                                                <label for="Parts" class="input-group-text">Rejects</label>
                                                <input class="form-control" tabindex="17" type="number" name="startUpRejects" id="startUpRejects" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="d-flex flex-row justify-content-evenly">
                                    <div class="col-md-4">Hot Runner Temps</div>
                                    <div class="col-md-3">Press Data</div>
                                </div> -->
                                <div class="row text-center mb-2">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6 d-flex justify-content-center">
                                        <strong>Hot Runner Temps</strong>
                                    </div>
                                    <div class="col-sm-3 d-flex justify-content-center">
                                        <strong>Press Data</strong>
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
                                <div class="d-flex flex-row justify-content-between">
                                    <div class="col-sm-1">
                                    </div>
                                    <div class="col-sm-2">
                                        <!-- <div class="d-flex flex-column flex-sm-column mb-2 g-1"> -->
                                        <div class="input-group sm-1">
                                            <label for="T1" class="input-group-text">T1</label>
                                            <input class="form-control" maxlength="3" tabindex="18" type="number" name="t1" id="t1" required>
                                        </div>
                                        <!-- </div> -->
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column mb-2 g-1">
                                            <div class="input-group sm-1"><label for="M1" class="input-group-text">M1</label>
                                                <input class="form-control" maxlength="3" type="number" tabindex="22" name="m1" id="m1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column mb-2 g-1">
                                            <div class="input-group sm-1"><label for="M5" class="input-group-text">M5</label>
                                                <input class="form-control" maxlength="3" type="number" tabindex="26" name="m5" id="m5">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column mb-2 g-1">
                                            <div class="input-group sm-1"><label for="z1" class="input-group-text">Z1</label>
                                                <input class="form-control" maxlength="3" type="number" tabindex="29" name="z1" id="z1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                </div>
                                <div class="d-flex flex-row justify-content-between">
                                    <div class="col-sm-1">
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column mb-2 g-1">
                                            <div class="input-group sm-1"><label for="T2" class="input-group-text">T2</label>
                                                <input class="form-control" maxlength="3" tabindex="19" type="number" name="t2" id="t2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column mb-2 g-1">
                                            <div class="input-group sm-1"><label for="M2" class="input-group-text">M2</label>
                                                <input class="form-control" maxlength="3" tabindex="23" type="number" name="m2" id="m2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column mb-2 g-1">
                                            <div class="input-group sm-1"><label for="M6" class="input-group-text">M6</label>
                                                <input class="form-control" maxlength="3" type="number" tabindex="27" name="m6" id="m6">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column mb-2 g-1">
                                            <div class="input-group sm-1"><label for="z9" class="input-group-text">Z9</label>
                                                <input class="form-control" maxlength="3" type="number" tabindex="30" name="z9" id="z9">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                </div>
                                <div class="d-flex flex-row justify-content-between mb-2 g-1">
                                    <div class="col-sm-1">
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group sm-1"><label for="T3" class="input-group-text">T3</label>
                                                <input class="form-control" maxlength="3" tabindex="20" type="number" name="t3" id="t3">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group sm-1"><label for="M3" class="input-group-text">M3</label>
                                                <input class="form-control" type="number" maxlength="3" tabindex="24" name="m3" id="m3">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group sm-1"><label for="M7" class="input-group-text">M7</label>
                                                <input class="form-control" type="number" maxlength="3" tabindex="28" name="m7" id="m7">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group sm-1"><label for="maxPressure" class="input-group-text">Max Pres.</label>
                                                <input class="form-control" type="number" maxlength="3" tabindex="31" name="maxMeltPress" id="maxMeltPress">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                    </div>
                                </div>
                                <div class="d-flex flex-row justify-content-between mb-2 g-1">
                                    <div class="col-sm-1">
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group sm-1"><label for="T4" class="input-group-text">T4</label>
                                                <input class="form-control" type="number" maxlength="3" tabindex="21" name="t4" id="t4">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column">
                                            <div class="input-group sm-1"><label for="M4" class="input-group-text">M4</label>
                                                <input class="form-control" type="number" maxlength="3" tabindex="25" name="m4" id="m4">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column"></div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="d-flex flex-column flex-sm-column"></div>
                                    </div>
                                    <div class="col-sm-2">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">Comments</div>
                            <div class="card-body">
                                <textarea class="form-control" name="commentText" id="commentText" tabindex="32" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container" id="commentSubmit">
                <div class="d-flex justify-content-center p-2">
                    <div class=pe-1><button type="button" id="cancel" data-bs-dismiss="modal" class="btn btn-danger btn-sm">Cancel</button></div>
                    <button type="submit" id="add-log-btn" class="btn btn-success btn-sm">Add Log</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>
    <!-- My custom js -->
    <script type="module" src="/js/addProdLog.js"></script>
</body>

</html>