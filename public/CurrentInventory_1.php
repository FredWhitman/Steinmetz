<!DOCTYPE html>

<html>
    <head>
        <title>Steinmetz</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>

<body>
    <?php 
        require_once("InventoryDB.php");
    ?>
    
    <nav class="navbar navbar-expand-sm bg-light">
        <div class="container-fluid bg-primary ">
            <ul class="navbar-nav ">
                <li class="nav-item text-white">
                    <a class="nav-link text-white" href="#">Link 1</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Link 2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#">Link 3</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="container-fluid my-1 bg-dark text-white">
        <h3><p class="text-center">Current Inventory</p></h3>
        <div class="row">
            <div class="col-sm-4">
                <div container>
                <div class="table responsive" style="height:500px; overflow-y: auto">
                <div class="table-wrapper">    
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><p class="text-center">Part Number</p></th>
                                <th><p class="text-center">Part Name</p></th>
                                <th><p class="text-center">Qty in Stock</p></th>
                            </tr>
                        </thead>
                        <?PHP
                        $partInventory = InventoryDB::getInstance()-> get_Parts();
            
                        if(!$partInventory)
                        {
                            exit("There is no part inventory to be found!");
                        }else
                        {
                            while ($row = mysqli_fetch_array($partInventory)) 
                            {
                                echo "<tr><td>" . htmlentities($row['ProductID']) . "</td>";
                                echo "<td>" . htmlentities($row['PartName']) . "</td>";
                                echo "<td>" . htmlentities($row['PartQty']) . "</td></tr>\n";
                            }
                        }
                        ?>
                    </table>    
                </div>
                </div>    
            </div>
            </div>    
            <div class="col-sm-4">
                <div containter>
                <div class="table-responsive" style="height:500px; overflow-y: auto">
                <div class="table-wrapper">    
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><p class="text-center">Material Number</p></th>
                                <th><p class="text-center">Material Name</p></th>
                                <th><p class="text-center">Lbs in Stock</p></th>
                            </tr>
                        </thead>
                        <?PHP
                            $materialInventory = InventoryDB::getInstance()->get_Material();
                            if (!$materialInventory) 
                            {
                                exit("There are no materials to be found." );
                            }else
                            {
                                while ($row = mysqli_fetch_array($materialInventory)) 
                                {
                                    echo "<tr><td>" . htmlentities($row['MaterialPartNumber']) . "</td>";
                                    echo "<td>" . htmlentities($row['MaterialName']) . "</td>";
                                    echo "<td>" . htmlentities($row['lbs']) . "</td></tr>\n";
                                }
                            }
                        ?>
                    </table>    
                </div>
                </div>
                </div>    
            </div>
            <div class ="col md-1">
                <div class="table-responsive" style="height:500px; overflow-y: auto">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><p class="text-center">PFM Part Number</p></th>
                                <th><p class="text-center">PFM Part Name</p></th>
                                <th><p class="text-center">Qty in Stock</p></th>
                            </tr>
                        </thead>
                        <?PHP
                            $pfmInventory = InventoryDB::getInstance()->get_Pfms();
                            if(!$pfmInventory)
                            {
                                exit("There is no pfm inventory to be found!");
                            }else 
                            {                            
                                while($row = mysqli_fetch_array($pfmInventory))
                                {
                                    echo "<tr><td>".($row['PARTNUMBER'])."</td>";
                                    echo "<td>".($row['PARTNAME'])."</td>";
                                    echo "<td>".($row['Qty'])."</td></tr>\n";
                                }
                            }
                        ?>
                    </table>    
                </div> 
            </div>
        </div>
    </div>    
    </body>
</html>
