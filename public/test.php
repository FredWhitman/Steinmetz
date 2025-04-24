<!doctype html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Steinmetz Inc Inventopry and Maintenance Website">
        <meta name="author" content="Fred Whitman">

        <title>Steinemtz Inc</title>


        <?php while($rowMaterials = $materialObj->fetch(PDO::FETCH_ASSOC)) :;?>
                                    <option><?php print($rowMaterials["MaterialName"]);?> </option>
                                    <?php endwhile;?> 

    $sql = "SELECT MaterialName FROM material";
        if ($res = $conn->query($sql)) 
        {
            /* Check the number of rows that match the SELECT statement */
            if ($res->fetchColumn() > 0) 
            {

            /* Issue the real SELECT statement and work with the results */
            $sql = "SELECT name FROM fruit WHERE calories > 100";

            foreach ($conn->query($sql) as $row) {print "Name: " .  $row['NAME'] . "\n";}
        }
    /* No rows matched -- do something else */
        else { print "No rows matched the query.";}
        }

$res = null;
$conn = null;                            


        <!-- Bootstrap core CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

        <!-- Custom styles for this template -->
        <link href="css/dashboard.css" rel="stylesheet">
        <link href="css/mdb.min.css" rel="stylesheet">
        <script type="text/javascript" src="js/mdb.umd.min.js"></script>
    </head>
    
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top flex-md-nowrap p-0 shadow">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Steinemtz Inc</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">First Dropdown</a>
                                <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li class="dropend">
                                    <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">2nd Dropdown</a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#">Menu item</a></li>
                                        <li><a class="dropdown-item" href="#">Menu item</a></li>
                                        <li class="dropend">
                                        <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">3rd Dropdown</a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#">Menu item</a></li>
                                            <li><a class="dropdown-item" href="#">Menu item</a></li>
                                            <li><a class="dropdown-item" href="#">Menu item</a></li>
                                        </ul>
                                        </li>
                                    </ul>
                                    </li>
                                    <li><a class="dropdown-item" href="#">Another action</a>
                                    
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
        </nav>

    </body>
</html>     