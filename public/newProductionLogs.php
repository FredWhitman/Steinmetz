<!doctype html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Steinmetz Inc Inventopry and Maintenance Website">
        <meta name="author" content="Fred Whitman">

        <title>Steinmetz Inc</title>

        <!-- Bootstrap core CSS -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous"> -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script> -->
        <!-- Custom styles for this template -->
        <link href="css/dashboard.css" rel="stylesheet">
        
    </head>
    
    <body>
        <!--Navbar -->
        <?php require_once 'includes/steinmetzNavbar.php';  ?>

        <div class="container-fluid"> 
            <div class="mt-5">
                <div class="row">
                    <div class="col d-flex justify-content-center">
                        <div class="card" >
                            <div class="card-header"><h4 class="text-center">New Production Log</h4></div>
                            <div class="card-body">
                                <div class="container">
                                <!-- TODO: check to make sure date selected is within a normal range or throw message
                                     TODO: Add radio buttons for production run status 
                                     TODO: If radio button for start run is selected copy blender data to daily usage data
                                     TODO: if in progress radio button is selected pull previous log and subtract values from blender data to fill daily usage data
                                     TODO: if end of run is selected pull all data for production and add production run data to database
                                           -->
                                    <div class="row">
                                        <div class="card">
                                            <table>
                                                <tr>
                                                    <td><div class="input-group mb-3"><label class="input-group-text" for="partName">Part Name</label>
                                                    <input type="text" class="form-select" list="partNames" id="partName" name="selectedPart"></td></div>
                                                    <td> </td>
                                                    <td class="text-center">Production Run Status</td>
                                                </tr>

                                                <tr>
                                                    <td><div class="input-group mb-3"><label class="input-group-text" for="logDate">Production Date</label><input class="form-control" type="date" id="logDate" name="log_date"></div></td>
                                                    <td> </td>
                                                    <td>
                                                        <table>
                                                            <tr>
                                                                <td><label for="">start</label></td>
                                                                <td class="text-center"><input class ="form-check-input" type="radio" name="prodRun" id="startRun" value="1"></td>
                                                                <td><label for="">in progress</label></td>
                                                                <td class="text-center"><input class ="form-check-input" type="radio" name="prodRun" id="inProgressRun" value="0"></td>
                                                                <td><label for="">end</label></td>
                                                                <td class="text-center"><input class ="form-check-input" type="radio" name="prodRun" id="endRun" value="0"></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr><td></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="card" style="width: 25rem;">
                                                <div class="card-header">Blender</div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <table>
                                                                <thead><tr><th class="text-center">Material</th><th class="text-center">Lbs for Run</th></tr></thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><div class="input-group sm-1"><label for="Mat1Name" class="input-group-text">Hopper 1</label><input class="form-select" type="text" list="materialNames" name="selected1Mat" id="Mat1Name"></div></td>
                                                                        <td><input class="form-control" type="text" name="hop1Lbs" id=""></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><div class="input-group sm-1"><label for="Mat2Name" class="input-group-text">Hopper 2</label><input class="form-select" type="text" list="materialNames" name="selected2Mat" id="Mat2Name"></div></td>
                                                                        <td><input class="form-control" type="text" name="hop2Lbs" id=""></td>
                                                                    </tr>
                                                                    <tr>
                                                                    <td><div class="input-group sm-1"><label for="Mat3Name" class="input-group-text">Hopper 3</label><input class="form-select" type="text" list="materialNames" name="selected3Mat" id="Mat3Name"></div></td>
                                                                        <td><input class="form-control" type="text" name="hop3Lbs" id=""></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><div class="input-group sm-1"><label for="Mat4Name" class="input-group-text">Hopper 4</label><input class="form-select" type="text" list="materialNames" name="selected4Mat" id="Mat4Name"></div></td>
                                                                        <td><input class="form-control" type="text" name="hop4Lbs" id=""></td>
                                                                    </tr>
                                                                    <tr>

                                                                    <td><div class="input-group sm-1"><label for="BlenderTotals" class="input-group-text">Blender Totals</label><input class="form-control"type="text" name="totalsBlender" id="BlenderTotals"></div></td>   
                                                                    
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
                                                                <thead><tr><th class="text-center">Lbs Used</th><th class="text-center">%</th></tr></thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><input class="form-control" type="text" name="hop1LbsDaily" id=""></td>
                                                                        <td><input class="form-control" type="text" name="hop1Percent" id=""></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><input class="form-control" type="text" name="hop2LbsDaily" id=""></td>
                                                                        <td><input class="form-control" type="text" name="hop2Percent" id=""></td> 
                                                                    </tr>
                                                                    <tr>
                                                                        <td><input class="form-control" type="text" name="hop3LbsDaily" id=""></td>
                                                                        <td><input class="form-control" type="text" name="hop3Percent" id=""></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><input class="form-control" type="text" name="hop4LbsDaily" id=""></td>
                                                                        <td><input class="form-control" type="text" name="hop4Percent" id=""></td>
                                                                    </tr>
                                                                    <tr><td class="text-end">Daily Usage Total</td><td><input readonly class="form-control"type="text" name="totalDaily" id=""></td></tr>
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

                                            <div class="card">
                                            <!-- TODO: This column will hold the dry information and produciton numbers
                                                 TODO: Ensure that only numbers can be entered into fields 
                                            -->
                                                <div class="card-header">Dryer & Production Information</div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col">
                                                            <table>
                                                                <tr>
                                                                    <td><div class="input-group sm-1"><label for="bigDryerTemp" class="input-group-text">Big Dryer</label><input class="form-control" type="text" name="bigDryerTemp" id="bigDryerTemp"></div></td>
                                                                    <td><input class="form-control" type="text" name="bigDryerDew" id=""></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div class="input-group sm-1"><label for="PressDryerTemp" class="input-group-text">Press Dryer</label><input class="form-control" type="text" name="pressDryerTemp" id="PressDryerTemp"></div></td>
                                                                    <td><input class="form-control" type="text" name="pressDryerDew" id=""></td>
                                                                </tr>
                                                            </table>
                                                        </div>    
                                                        <div class="col">
                                                            <table>
                                                                <tr>
                                                                    <td><div class="input-group sm"><label for="PressCounter" class="input-group-text">Press Counter</label><input class="form-control form-control-sm" type="text" name="pressCount" id="PressCounter"></div></td>
                                                                </tr>
                                                                <tr>
                                                                <td><div class="input-group sm-1"><label for="PressRejects" class="input-group-text">Press Rejects</label><input class="form-control form-control-sm" type="text" name="rejects" id="PressRejects"></div></td>
                                                                </tr>
                                                            </table>
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
                                                            <td><div class="input-group sm-1"><label for="Chiller" class="input-group-text">Chiller</label><input class="form-control" type="text" name="chillerTemp" id="Chiller"></div></td>
                                                            <td><div class="input-group sm-1"><label for="TCU" class="input-group-text">TCU</label><input class="form-control" type="text" name="tcuTemp" id="TCU"></div></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <div class="row">
                                                        <table>
                                                        <tr>
                                                            <td><div class="input-group sm-1"><label for="T1" class="input-group-text">T1</label><input class="form-control" type="text" name="t1Temp" id="T1"></div></td>
                                                            <td><div class="input-group sm-1"><label for="M1" class="input-group-text">M1</label><input class="form-control" type="text" name="m1Temp" id="M1"></div></td>
                                                            <td><div class="input-group sm-1"><label for="M5" class="input-group-text">M5</label><input class="form-control" type="text" name="m5Temp" id="M5"></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td><div class="input-group sm-1"><label for="T2" class="input-group-text">T2</label><input class="form-control" type="text" name="t2Temp" id="T2"></div></td>
                                                            <td><div class="input-group sm-1"><label for="M2" class="input-group-text">M2</label><input class="form-control" type="text" name="m2Temp" id="M2"></div></td>
                                                            <td><div class="input-group sm-1"><label for="M6" class="input-group-text">M6</label><input class="form-control" type="text" name="m6Temp" id="M6"></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td><div class="input-group sm-1"><label for="T3" class="input-group-text">T3</label><input class="form-control" type="text" name="t3Temp" id="T3"></div></td>
                                                            <td><div class="input-group sm-1"><label for="M3" class="input-group-text">M3</label><input class="form-control" type="text" name="m3Temp" id="M3"></div></td>
                                                            <td><div class="input-group sm-1"><label for="M7" class="input-group-text">M7</label><input class="form-control" type="text" name="m7Temp" id="M7"></div></td>
                                                        </tr>
                                                        <tr>
                                                            <td><div class="input-group sm-1"><label for="T4" class="input-group-text">T4</label><input class="form-control" type="text" name="t4Temp" id="T4"></div></td>
                                                            <td><div class="input-group sm-1"><label for="M4" class="input-group-text">M4</label><input class="form-control" type="text" name="m4Temp" id="M4"></div></td>  
                                                        </tr>
                                                    </table>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Row for Comment and button -->
                                    <div class="row">
                                        <!-- TODO: Add Comment box and sumbit button -->
                                        <div class="col">
                                            <!-- TODO: Add Cooling info text inputs and hotrunner info text input
                                                 TODO: make sure that you want to leave the inputs empty if they are empty when they lose focus 
                                            -->
                                            <div class="card">
                                                <div class="card-header">Comments</div>
                                                    <div class="card-body">

                                                 </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="js/feather.min.js"></script>
    <script>feather.replace()</script>
</html>