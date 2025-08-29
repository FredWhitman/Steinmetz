<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Steinmetz Inc</title>

    <!-- Bootstrap core CSS -->
    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <!-- <link href="/lib/css/dashboard.css" rel="stylesheet">
    <link href="/css/myCSS.css" rel="stylesheet"> -->
    <!-- <link href="/css/qualityTable.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="steinmetz.ico" type="image/x-icon">
</head>

<body>
    <?php require_once '../includes/steinmetzNavbar.php';  ?>

    <div class="container-fluid mt-6">
        <div class="d-flex flex-row justify-content-center mt-5">
            <div class="col-sm-6 text-center">
                <img src="/SteinmetzLogo.png" alt="Steinmetz Logo" class="img-fluid" style="max-width: 300px;">
            </div>
        </div>
        <div class="d-flex flex-row justify-content-center mt-3">
            <div class="col-sm-12 text-center">
                <p class="fs-5">Please login</p>
            </div>
        </div>

        <div class="d-flex flex-row justify-content-center mt-3">
            <div class="col-sm-4">
                <form method="POST" action="/loginHandler.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
</body>

</html>