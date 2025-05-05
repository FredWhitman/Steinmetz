<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top flex-md-nowrap p-0 shadow">
    <div class="container-fluid">
        <a class="navbar-brand" href="./index.php">Steinmetz Inc</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-auto-close="outside" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Inventory</a>
                    <ul class="dropdown-menu">
                        <li class="dropend">
                            <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" href="#">Add</a>
                            <ul class="dropdown-menu">
                                <li><a href="" class="dropdown-item">New Product</a></li>
                                <li><a href="#" class="dropdown-item">New Material</a></li>
                                <li><a href="./newPFM.php" class="dropdown-item">New PFM</a></li>
                            </ul>
                        </li>
                        <li class="dropend">
                            <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" href="#">Edit</a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="dropdown-item">Product Inventory</a></li>
                                <li><a href="#" class="dropdown-item">Material Inventory</a></li>
                                <li><a href="#" class="dropdown-item">PFM Inventory</a></li>
                            </ul>
                        </li>
                        <li class="dropend">
                            <a class="dropdown-item dropdown-toggle" data-bs-toggle="dropdown" href="#">View</a>
                            <ul class="dropdown-menu">
                                <li><a href="currentinventory.php" class="dropdown-item">Current Inventory</a></li>
                                <li><a href="" class="dropdown-item">Product List</a></li>
                                <li><a href="" class="dropdown-item">Material List</a></li>
                                <li><a href="pfmlist.php" class="dropdown-item">PFM List</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-auto-close="outside" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Lot Changes</a>
                    <ul class="dropdown-menu">
                        <li><a href="" class="dropdown-item">Add Lot Change</a></li>
                        <li><a href="#" class="dropdown-item">Edit Lot CHange</a></li>
                        <li><a href="viewLotChanges.php" class="dropdown-item">View Lot Change</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-auto-close="outside" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Production</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href=".\newProductionLogs.php" class="dropdown-item">Add Production Log</a>
                            <a href="" class="dropdown-item">Edit Production Log</a>
                            <a href="" class="dropdown-item">View Production Log</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>