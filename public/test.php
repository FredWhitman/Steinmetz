<div class="card" style="width: 30rem;">
    <div class="card-header">Blender</div>
    <div class="card-body">
        <div class="row row-cols-2">
            <div class="col">
                <div class="text-center">Material</div> 
            </div>
            <div class="col">
                <div class="text-center">Lbs for Run</div>
            </div>
        </div>
        <div class="row row-cols-2">
            <div class="col">
                <div class="input-group sm-1"><label for="Mat1Name" class="input-group-text">Hopper 1</label><select class="form-select" type="text" name="selected1Mat" id="Mat1Name" required></div>
            </div>
            <div class="col">
                <input class="form-control" type="number" step="0.001" name="hop1" id="hop1Lbs" tabindex="6" oninput="validateDecimalInput(event)" required></div>
            </div>
        </div>
        <div class="row row-cols-2">
            <div class="col">
                <div class="input-group sm-1"><label for="Mat2Name" class="input-group-text">Hopper 2</label><select class="form-select" type="text" list="materialNames" name="selected2Mat" id="Mat2Name" required></div>
            </div>
            <div class="col">
                <input class="form-control" type="number" step="0.001" name="hop2" id="hop2Lbs" tabindex="7" oninput="validateDecimalInput(event)" required>
            </div>
        </div>
        <div class="row row-cols-2">
            <div class="col">
                <div class="input-group sm-1"><label for="Mat3Name" class="input-group-text">Hopper 3</label><select class="form-select" type="text" list="materialNames" name="selected3Mat" id="Mat3Name"></div>
            </div>
            <div class="col">
                <input class="form-control" type="number" step="0.001" name="hop3" id="hop3Lbs" tabindex="8" oninput="validateDecimalInput(event)">
            </div>
        </div>
        <div class="row row-cols-2">
            <div class="col">
                <div class="input-group sm-1"><label for="Mat4Name" class="input-group-text">Hopper 4</label><select class="form-select" type="text" list="materialNames" name="selected4Mat" id="Mat4Name"></div>
            </div>
            <div class="col">
                <input class="form-control" type="number" step="0.001" name="hop4" id="hop4Lbs" tabindex="9" oninput="validateDecimalInput(event)">
            </div>
        </div>
        <div class="row row-cols-2">
            <div class="col">

            </div>
            <div class="col">
                <div class="input-group sm-1"><label for="BlenderTotals" class="input-group-text">Blender Totals</label><input class="form-control" type="number" step="0.001" name="totalsBlender" id="BlenderTotals" oninput="validateInput(event)" readonly></div>
            </div>
        </div>    
    </div>
</div>