<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality</title>

    <!-- Bootstrap core CSS -->
    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->

    <link href="/css/qualityTable.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="/../steinmetz.ico" type="image/x-icon">
</head>

<body>
    <!--Navbar -->
    <?php require_once '../../includes/steinmetzNavbar.php'; ?>
    <div id="loader" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1050;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">
        <div class="d-flex flex-column justify-content-center">
            <div class="row text-center">
                <h4 class="text-bg-secondary mt-5 p-3">Production log</h4>
            </div>
            <form id="view-prodLog-form" action="">
                <div class="d-flex flex-row justify-content-around mt-2 mb-0 gap-2">
                    <div class="col"></div>
                    <div class="col-3">
                        <div class="input-group sm-3"><label class="input-group-text" for="partName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="partName" name="partName" required></select></div>
                    </div>
                    <div class="col-3">
                        <div class="input-group mb-3"><label class="input-group-text" for="logDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="logDate" name="logDate" required></div>
                    </div>
                    <div class="col-1">
                        <button class="btn btn-primary form-control" type="submit" id="viewLog">fetch Log</button>
                    </div>
                    <div class="col"></div>
                </div>
            </form>

            <hr>
            <div class="row align-items-center justify-content-center">
                <table class="table w-75 table-bordered">
                    <tbody>
                        <tr>
                            <td class="text-start" id="pl_PartName">Part Name</td>
                            <td class="text-start" colspan="2" id="pl_LogDate">Production Date</td>
                            <td class="text-start" colspan="2" id="pl_RunStatus">Production Run Status</td>
                        </tr>
                        <tr>
                            <td class="bg-dark-subtle">Material</td>
                            <td class="bg-dark-subtle">lbs</td>
                            <td class="bg-dark-subtle">Daily</td>
                            <td class="bg-dark-subtle">%</td>
                            <td class="bg-dark-subtle"></td>
                        </tr>
                        <tr>
                            <td id="pl_Hop1Material">Hop 1 material:</td>
                            <td id="pl_Hop1Weight">Hop 1 mat lbs:</td>
                            <td id="pl_Hop1Daily">Hop 1 daily:</td>
                            <td id="pl_Hop1Percent">Hop 1 %:</td>
                            <td id="pl_PressCounter">parts produced:</td>
                        </tr>
                        <tr>
                            <td id="pl_Hop2Material">Hop 2 material:</td>
                            <td id="pl_Hop2Weight">Hop 2 mat lbs:</td>
                            <td id="pl_Hop2Daily">Hop 2 daily:</td>
                            <td id="pl_Hop2Percent">Hop 2 %:</td>
                            <td id="pl_startRejects">startup rejects:</td>
                        </tr>
                        <tr>
                            <td id="pl_Hop3Material">Hop 3 material:</td>
                            <td id="pl_Hop3Weight">Hop 3 mat lbs:</td>
                            <td id="pl_Hop3Daily">Hop 3 daily:</td>
                            <td id="pl_Hop3Percent">Hop 3 %:</td>
                            <td id="pl_qaRejects">qa rejects:</td>
                        </tr>
                        <tr>
                            <td id="pl_Hop4Material">Hop 4 material:</td>
                            <td id="pl_Hop4Weight">Hop 4 mat lbs:</td>
                            <td id="pl_Hop4Daily">Hop 4 daily:</td>
                            <td id="pl_Hop4Percent">Hop 4 %:</td>
                            <td id="pl_purge">purge total:</td>
                        </tr>
                        <tr>
                            <td class="text-end bg-dark-subtle">Material usage totals:</td>
                            <td id="pl_totalMatWeight">total mat lbs</td>
                            <td id="pl_totalDaily">daily total</td>
                            <td id="pl_totalPercent">total %</td>
                        </tr>
                        <tr>
                            <td class="bg-dark-subtle" colspan="5">Dryer, Chiller & Mold Temp Info</td>
                        </tr>
                        <tr>
                            <td class="" id="pl_BigDryerTemp">BigDryer Temp</td>
                            <td class="" id="pl_BigDryerDew">BigDryer Dew Point</td>
                            <td class="" id="pl_ChillerTemp">Chiller Temp</td>
                            <td colspan="2" rowspan="5">
                                <table class="table table-bordered m-0">
                                    <tr>
                                        <td id="t1">T1</td>
                                        <td id="m1">M1</td>
                                        <td id="m5">M5</td>
                                    </tr>
                                    <tr>
                                        <td id="t2">T2</td>
                                        <td id="m2">M2</td>
                                        <td id="m6">M6</td>
                                    </tr>
                                    <tr>
                                        <td id="t3">T3</td>
                                        <td id="m3">M3</td>
                                        <td id="m7">M7</td>
                                    </tr>
                                    <tr>
                                        <td id="t4">T4</td>
                                        <td id="m4">M4</td>
                                        <td></td>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td id="pl_PressDryerTemp">Press Dryer Temp</td>
                            <td class="" id="pl_PressDryerDew">Press Dryer Dew Point</td>
                            <td class="" id="pl_MoldTemp">Mold Temp</td>
                        </tr>
                        <tr>
                            <td class="bg-dark-subtle text-end">Press data</td>
                            <td id="pl_barrelZones"> Z1: 000° Z9: 000°</td>
                            <td id="pl_maxMelt">Max Melt Press: 0000 psi</td>
                        </tr>
                        <tr style="height: 25px;">

                        </tr>
                        <tr style="height: 25px;">

                        </tr>
                        <tr>
                            <td class=" bg-dark-subtle text-start" colspan="5">Production Log Notes</td>
                        </tr>
                        <tr>
                            <td class="text-start" colspan="5" id="pl_comments">Log Notes</td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <hr>


    <!-- Include your JavaScript files here -->
    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="/js/viewProdLog.js"></script>

</body>

</html>