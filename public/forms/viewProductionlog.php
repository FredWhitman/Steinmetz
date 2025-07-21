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
    <link rel="icon" href="steinmetz.ico" type="image/x-icon">
</head>

<body>
    <div class="d-flex flex-row justify-content-center">
        <div class="row row-cols-3 pb-2">
            <h1 class="mt-5">Production log</h1>
            <div class="col">
                <div class="input-group sm-3"><label class="input-group-text" for="qaPartName">Part Name</label><select type="text" tabindex="1" class="form-select form-control-sm" id="pl_PartName" name="pl_PartName" required></select></div>
                <div class="invalid-feedback">Part name is required!</div>
            </div>
            <div class="col">
                <div class="input-group sm-3"><label class="input-group-text" for="qaLogDate">Production Date</label><input class="form-control" type="date" tabindex="2" id="pl_LogDate" name="pl_LogDate" required></div>
                <div class="invalid-feedback">Production date is required!</div>
            </div>
            <div class="col">
                <a class="btn btn-primary btn-sm " href="#" role="button">Fetch Log</a>
            </div>
        </div>
    </div>
    <hr>

    <!-- Include your JavaScript files here -->
    <script src=""></script>

</body>

</html>