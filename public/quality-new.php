<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality</title>

    <!-- Bootstrap core CSS -->
    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <!-- <link href="/lib/css/dashboard.css" rel="stylesheet">
    <link href="/css/myCSS.css" rel="stylesheet"> -->
    <link href="/css/qualityTable.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="steinmetz.ico" type="image/x-icon">
</head>

<body>
    <!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php'; ?>


    <!-- LOADER -->
    <div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1050;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Add QA Rejects to production log start-->
    <div class="modal fade" id="addQARejectsModal" tabindex="-1" aria-labelledby="addQARejectsModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addQARejectsModal">QA Rejects</h1>
                </div>
                <div class="modal-body">
                    <form id="add-qaReject-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="qaPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="qaPartName" name="qaPart" required></select></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="qaLogDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="logDate" name="qaLogDate" required></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="qaRejects">Reject Quantity</label><input type="number" tabindex="1" class="form-control form-control-sm" id="qaRejects" name="rejects" required></div>
                                <div class="invalid-feedback">Number of rejects is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="comment-text" name="qaComments"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Add Rejects" class="btn btn-success" id="add-qaReject-btn">Add Rejects</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add QA Rejects to production log end-->

    <!-- View QA Rejects to production log start-->
    <div class="modal fade" id="viewQaRejectsModal" tabindex="-1" aria-labelledby="viewQaRejectsModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addQARejectsModal">QA Rejects</h1>
                </div>
                <div class="modal-body">
                    <form id="view-qaReject-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="qaPartName">Part Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="v_qaPartName" name="v_qaPart" readonly></input></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="qaLogDate">Production Date</label><input class="form-control" type="text" tabindex="2" id="v_logDate" name="v_qaLogDate" readonly></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="qaRejects">Reject Quantity</label><input type="number" tabindex="1" class="form-control form-control-sm" id="v_qaRejects" name="v_rejects" readonly></div>
                                <div class="invalid-feedback">Number of rejects is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="v_comment-text" name="v_qaComments"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- View QA Rejects to production log end-->

    <!-- Add Lot Changes start-->
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
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="lc_PartName" name="lc_PartName" required></select></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Material Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="lc_MatName" name="lc_MatName" required></select></div>
                                    <div class="invalid-feedback">Material is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="lc_lotDate" name="lc_LotDate" required></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">Time</label><input class="form-control" type="time" id="lc_lotTime" name="lc_LotTime" value="09:00" required></div>
                                    <div class="invalid-feedback">Change time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">Old Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="lc_OldLot" name="lc_OldLot" required></div>
                                <div class="invalid-feedback">Old lot number is required!</div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcNewLot">New Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="lc_NewLot" name="lc_NewLot" required></div>
                                <div class="invalid-feedback">New lot numger is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="lc_Comments" name="lc_Comments"></textarea>
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
    <!-- Add Lot Changes end-->

    <!-- View Lot Changes start-->
    <div class="modal fade" id="viewLotChangeModal" tabindex="-1" aria-labelledby="viewLotChangeModal">
        <div class="modal-dialog modal-sm-1">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Lot Change</h1>
                </div>
                <div class="modal-body">
                    <form id="view-lotchange-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Part Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="v_lcPartName" name="v_lcPartName" readonly></input></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Material Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="v_lcMatName" name="v_lcMatName" readonly></select></div>
                                    <div class="invalid-feedback">Material is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="v_lclotDate" name="v_lcLotDate" readonly></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">Time</label><input class="form-control" type="time" id="v_lclotTime" name="v_lcLotTime" value="09:00" readonly></div>
                                    <div class="invalid-feedback">Change time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">Old Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="v_lcOldLot" name="v_lcOldLot" readonly></div>
                                <div class="invalid-feedback">Old lot number is required!</div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcNewLot">New Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="v_lcNewLot" name="v_lcNewLot" readonly></div>
                                <div class="invalid-feedback">New lot numger is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="v_lcComments" name="v_lcComments" readonly></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- View Lot Changes end-->

    <!-- Update Lot Changes start-->
    <div class="modal fade" id="updateLotChangeModal" tabindex="-1" aria-labelledby="updateLotChangeModal">
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
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="u_lcPartName" name="u_lcPartName" required></select></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcPartName">Material Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="u_lcMatName" name="u_lcMatName" required></select></div>
                                    <div class="invalid-feedback">Material is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="u_lclotDate" name="u_lclotDate" required></div>
                                    <div class="invalid-feedback">Production date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">Time</label><input class="form-control" type="time" id="u_lcLotTime" name="u_lcLotTime" value="09:00" required></div>
                                    <div class="invalid-feedback">Change time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">Old Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="u_lcOldLot" name="u_lcOldLot" required></div>
                                <div class="invalid-feedback">Old lot number is required!</div>
                            </div>
                            <div class="row">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcNewLot">New Lot</label><input type="text" tabindex="1" class="form-control form-control-sm" id="u_lcNewLot" name="u_lcNewLot" required></div>
                                <div class="invalid-feedback">New lot numger is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="u_lcComments" name="u_lcComments"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Update Lot Change" class="btn btn-success" id="update-lotchange-btn">Update Lot Change</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Update Lot Changes end-->


    <!-- Add Oven Logs start-->
    <div class="modal fade" id="addOvenLogModal" tabindex="-1" aria-labelledby="addOvenLogModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Oven Log</h1>
                </div>
                <div class="modal-body">
                    <form id="add-ovenlog-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="olPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="ol_PartName" name="ol_PartName" required></select></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">In Oven Date</label><input class="form-control" type="date" tabindex="2" id="ol_inOvenDate" name="ol_inOvenDate" required></div>
                                    <div class="invalid-feedback">In Oven date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Time</label><input class="form-control" type="time" id="ol_inOvenTime" name="ol_inOvenTime" value="09:00" required></div>
                                    <div class="invalid-feedback">In Oven time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Temp</label><input class="form-control" type="number" id="ol_inOvenTemp" name="ol_inOvenTemp" required></div>
                                    <div class="invalid-feedback">In Oven temp is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">In Initials</label><input type="text" tabindex="1" class="form-control form-control-sm" id="ol_inOvenInitials" name="ol_inOvenInitials" required></div>
                                <div class="invalid-feedback">In Initials is required!</div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="ol_Comments" name="ol_Comments"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Add Lot Change" class="btn btn-success" id="add-ovenlog-btn">Add OvenLog</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Oven Logs end-->

    <!-- View Oven Logs start-->
    <div class="modal fade" id="viewOvenLogModal" tabindex="-1" aria-labelledby="viewOvenLogModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Oven Log</h1>
                </div>
                <div class="modal-body">
                    <form id="view-ovenlog-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="olPartName">Part Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="v_olPartName" name="v_olPartName" readonly></input></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">In Oven Date</label><input class="form-control" type="date" tabindex="2" id="v_olinOvenDate" name="v_olinOvenDate" readonly></div>
                                    <div class="invalid-feedback">In Oven date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Time</label><input class="form-control" type="time" id="v_olinOvenTime" name="v_olinOvenTime" value="09:00" readonly></div>
                                    <div class="invalid-feedback">In Oven time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Temp</label><input class="form-control" type="number" id="v_olinOvenTemp" name="v_olinOvenTemp" readonly></div>
                                    <div class="invalid-feedback">In Oven temp is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">In Initials</label><input type="text" tabindex="1" class="form-control form-control-sm" id="v_olinOvenInitials" name="v_olinOvenInitials" readonly></div>
                                <div class="invalid-feedback">In Initials is required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">out Oven Date</label><input class="form-control" type="date" tabindex="2" id="v_olOutOvenDate" name="v_olOutOvenDate" readonly></div>
                                    <div class="invalid-feedback">Out Oven date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Time</label><input class="form-control" type="time" id="v_olOutOvenTime" name="v_olOutOvenTime" value="09:00" readonly></div>
                                    <div class="invalid-feedback">In Oven time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Temp</label><input class="form-control" type="number" id="v_olOutOvenTemp" name="v_olOutOvenTemp" readonly></div>
                                    <div class="invalid-feedback">In Oven temp is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">In Initials</label><input type="text" tabindex="1" class="form-control form-control-sm" id="v_olOutOvenInitials" name="v_olOutOvenInitials" readonly></div>
                                <div class="invalid-feedback">In Initials is required!</div>
                            </div>
                        </div>
                        <div_v> <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="v_olComments" name="v_olComments" readonly></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!-- View Oven Logs end-->

    <!-- View Oven Logs start-->
    <div class="modal fade" id="updateOvenLogModal" tabindex="-1" aria-labelledby="updateOvenLogModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Oven Log</h1>
                </div>
                <div class="modal-body">
                    <form id="update-ovenlog-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <input type="hidden" id="u_olOvenLogID" name="u_olOvenLogID">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="olPartName">Part Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="u_olPartName" name="u_olPartName" readonly></input></div>
                                    <div class="invalid-feedback">Part name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">In Oven Date</label><input class="form-control" type="date" tabindex="2" id="u_olinOvenDate" name="u_olinOvenDate" readonly></div>
                                    <div class="invalid-feedback">In Oven date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Time</label><input class="form-control" type="time" id="u_olinOvenTime" name="u_olinOvenTime" value="09:00" readonly></div>
                                    <div class="invalid-feedback">In Oven time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Temp</label><input class="form-control" type="number" id="u_olinOvenTemp" name="u_olinOvenTemp" readonly></div>
                                    <div class="invalid-feedback">In Oven temp is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">In Initials</label><input type="text" tabindex="1" class="form-control form-control-sm" id="u_olinOvenInitials" name="u_olinOvenInitials"></div>
                                <div class="invalid-feedback">In Initials is required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcLotDate">out Oven Date</label><input class="form-control" type="date" tabindex="2" id="u_olOutOvenDate" name="u_olOutOvenDate"></div>
                                    <div class="invalid-feedback">Out Oven date is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Time</label><input class="form-control" type="time" id="u_olOutOvenTime" name="u_olOutOvenTime" value="09:00"></div>
                                    <div class="invalid-feedback">In Oven time is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="lcaTime">In Oven Temp</label><input class="form-control" type="number" id="u_olOutOvenTemp" name="u_olOutOvenTemp"></div>
                                    <div class="invalid-feedback">In Oven temp is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="lcOldLot">In Initials</label><input type="text" tabindex="1" class="form-control form-control-sm" id="u_olOutOvenInitials" name="u_olOutOvenInitials"></div>
                                <div class="invalid-feedback">In Initials is required!</div>
                            </div>
                        </div>
                        <div_v> <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="u_olComments" name="u_olComments"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" value="Update Lot Change" class="btn btn-success" id="update-ovenlog-btn">Update OvenLog</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <!-- View Oven Logs end-->

    <!-- Add Material Receive start-->
    <div class="modal fade" id="addMaterialReceivedModal" tabindex="-1" aria-labelledby="addMaterialReceivedModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Oven Log</h1>
                </div>
                <div class="modal-body">
                    <form id="add-matreceived-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="mr_MatPartNumber">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="mr_MatPartNumber" name="mr_MatPartNumber" required></select></div>
                                    <div class="invalid-feedback">Material Part Nnumber is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="mr_matReceiveDate">Received Date</label><input class="form-control" type="date" tabindex="2" id="mr_matReceiveDate" name="mr_matReceiveDate" required></div>
                                    <div class="invalid-feedback">Received date is required!</div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Comments</label>
                            <textarea class="form-control" type="text" id="mr_Comments" name="mr_Comments"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Add Mat Received" class="btn btn-success" id="add-matreceived-btn">Add Materiald Shipment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Material Received end-->

    <!-- Table to hold the last 4 weeks of production  -->
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h3 class="text-primary">Quality</h3>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadOvenLogForm" data-bs-toggle="modal" data-bs-target="#addOvenLogModal">Add Oven Log</button>
                        <button class="btn btn-primary" type="button" id="loadProductForm" data-bs-toggle="modal" data-bs-target="#addLotChangeModal">Add Lot Change</button>
                        <button class="btn btn-primary" type="button" id="loadQARejectForm" data-bs-toggle="modal" data-bs-target="#addQARejectsModal">Add QA Rejects</button>
                        <button class="btn btn-primary" type="button" id="loadMaterialForm" data-bs-toggle="modal" data-bs-target="#addMaterialReceivedModal">Receive Material</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div id="showAlert"></div>
                </div>
            </div>
            <div class="d-flex flex-column justify-content-around sm-3 caption-top gap-1">
                <div class="table-container">
                    <!-- Table to display QA Reject Logs -->
                    <table class="table caption-top table-striped table-bordered text-center w-100">
                        <thead class="table-primary">
                            <tr>
                                <th colspan="6">QA Reject Logs</th>
                            </tr>
                            <tr>
                                <th scope="col">Production Date</th>
                                <th scope="col">Production Log</th>
                                <th scope="col">Part Number</th>
                                <th scope="col">Rejects</th>
                                <th scope="col">Comments</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="qaRejectLogs">
                            <!-- Dynamic data table here -->
                        </tbody>
                    </table>
                </div>
                <div class="table-container">
                    <!--  Table to display Oven Logs-->
                    <table class="table table-striped table-bordered text-center w-100">
                        <thead class="table-primary">
                            <tr>
                                <th colspan="10">Oven Logs</th>
                            </tr>
                            <tr class="table-primary">
                                <th>Part Number</th>
                                <th>In Date</th>
                                <th>In Time</th>
                                <th>In Temp</th>
                                <th>In Initials</th>
                                <th>Out Date</th>
                                <th>Out Time</th>
                                <th>Out Temp</th>
                                <th>Out Initials</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ovenLogs">

                        </tbody>
                    </table>
                </div>
                <div class="table-container">
                    <!-- Table to display Lot change logs -->
                    <table class="table table-striped table-bordered text-center">
                        <thead class="sticky-priamry">
                            <tr>
                                <th colspan="8">Lot Change Logs</th>
                            </tr>
                            <tr">
                                <th>Production Log Id</th>
                                <th>Product ID</th>
                                <th>Material Name</th>
                                <th>Change Date</th>
                                <th>Change Time</th>
                                <th>Old Lot #</th>
                                <th>New Lot #</th>
                                <th>Actions</th>
                                </tr>
                        </thead>
                        <tbody id="lotChangeLogs">

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>
    <!-- Custom javascript -->
    <script type="module" src="/js/qualityMain.js"></script>
</body>

</html>