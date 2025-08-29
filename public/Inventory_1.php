<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>

    <!-- Bootstrap core CSS -->
    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/lib/css/dashboard.css" rel="stylesheet">
    <link href="/css/myCSS.css" rel="stylesheet">
    <link rel="stylesheet" href="https://www.devwares.com/docs/contrast/javascript/sections/timepicker/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="steinmetz.ico" type="image/x-icon">

</head>

<body>
    <!--Navbar -->
    <?php require_once '../includes/steinmetzNavbar.php'; ?>
    <div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1050;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Add New Product start-->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModal">
        <div class="modal-dialog modal-sm " style="max-width: 35%">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addProductModal">Add New Product</h1>
                </div>
                <div class="modal-body">
                    <form id="add-product-form" class="needs-validation p-2" novalidate>
                        <div class="d-flex flex-column g-1 ">
                            <input type="hidden" name="productID" id="hiddenProductID" />
                            <div class="row-sm-8 mb-1">
                                <div class="input-group sm-3">
                                    <label class="input-group-text" for="partName">Product Name</label>
                                    <input type="text" tabindex="1" class="form-control form-control-sm" id="add_ProductID" name="add_ProductID" required></input>
                                </div>
                                <div class="invalid-feedback">Product name is required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="row-sm-8">
                                    <div class="d-flex flex-column flex-sm-column">
                                        <div class="input-group sm-3">
                                            <label class="input-group-text" for="customer">Customer</label>
                                            <input type="text" class="form-control form-control-sm" tabindex="2" id="add_Customer" name="add_Customer" required>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">customer required!</div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-7">
                                <div class="input-group sm-3"><label class="input-group-text" for="minQty">Min Qty</label>
                                    <input type="number" tabindex="3" class="form-control form-control-sm" id="add_MinQty" name="add_MinQty" required>
                                </div>
                                <div class="invalid-feedback">Minimum qauntity required!</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="partWeight">Part (lbs)</label>
                                    <input type="number" step=".001" tabindex="4" class="form-control form-control-sm" id="add_PartWeight" name="add_PartWeight">
                                </div>
                                <div class="invalid-feedback">weight required!</div>
                            </div>

                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-7">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="partBox">Part Qty/Box</label>
                                    <input type="number" tabindex="4" class="form-control form-control-sm" id="add_PartsBox" name="add_PartsBox">
                                </div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="boxSkid">Boxes/Skid</label>
                                    <input type="number" tabindex="5" class="form-control form-control-sm" id="add_BoxSkid" name="add_BoxSkid">
                                </div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-7">
                                <div class="input-group sm-3">
                                    <label class="input-group-text" for="partType">Product Type</label>
                                    <select type="text" class="form-control form-control-sm" tabindex="6" id="add_PartType" name="add_PartType" required>
                                        <option value="" disabled selected>--Select type--</option>
                                        <option value="Cast">Cast</option>
                                        <option value="Injection">Injection</option>
                                        <option value="Machined">Machined</option>
                                    </select>
                                </div>
                                <div class="invalid-feedback">type required!</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group sm-3">
                                    <label class="input-group-text" for="displayOrder">Display Order</label>
                                    <input type="number" class="form-control form-control-sm" tabindex="7" id="add_DisplayOrder" name="add_DisplayOrder">
                                </div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>
                        </div>


                        <div class="d-flex flex-row justify-content-center mb-1 g-2">
                            <div class="col-sm-4">
                                <button type="submit" value="add" class="btn btn-success" id="add-product-btn">Add Product</button>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  Add New Product end-->

    <!-- Add New Material start-->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModal">
        <div class="modal-dialog modal-sm " style="max-width: 45%">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addMaterialModal">Add New Material</h1>
                </div>
                <div class="modal-body">
                    <form id="add-material-form" class="needs-validation p-2" novalidate>
                        <input type="hidden" name="hiddenMatPartNumber" id="hiddenMatPartNumber" />
                        <div class="d-flex flex-column g-1 ">
                            <div class="row pb-1">
                                <div class="col">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_matPartNumber">Mat Part #</label>
                                        <input type="text" tabindex="1" class="form-control form-control-sm" id="add_matPartNumber" name="add_matPartNumber" required></input>
                                    </div>
                                    <div class="invalid-feedback">Mat part number is required!</div>
                                </div>
                                <div class="col">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_MatPartName">Mat Name</label>
                                        <input type="text" class="form-control form-control-sm" tabindex="2" id="add_matPartName" name="add_matPartName" required>
                                    </div>
                                    <div class="invalid-feedback">mat name required!</div>
                                </div>
                            </div>
                            <div class="row pb-2"><!-- Customer and Supplier -->
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_MatCustomer">Customer</label>
                                        <input type="text" class="form-control form-control-sm" tabindex="3" id="add_matCustomer" name="add_matCustomer" required>
                                    </div>
                                </div>
                                <div class="invalid-feedback">customer required!</div>
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_MatSupplier">Supplier</label>
                                        <input type="text" class="form-control form-control-sm" tabindex="4" id="add_matSupplier" name="add_matSupplier" required>
                                    </div>
                                    <div class="invalid-feedback">Supplier required!</div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-7">
                                <div class="input-group sm-3"><label class="input-group-text" for="add_matProduct">Product</label>
                                    <select type="text" tabindex="5" class="form-control form-control-sm" id="add_matProduct" name="add_matProduct" required></select>
                                </div>
                                <div class="invalid-feedback">Product required!</div>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="add_minLbs">Min (lbs)</label>
                                    <input type="number" class="form-control form-control-sm" tabindex="6" id="add_minLbs" name="add_minLbs" required>
                                </div>
                                <div class="invalid-feedback">min lbs required!</div>
                            </div>

                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-7">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="add_MatPricLbs">Price/Lbs</label>
                                    <input type="number" tabindex="7" class="form-control form-control-sm" id="add_matPriceLbs" name="add_matPriceLbs">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label>
                                    <input type="number" tabindex="8" class="form-control form-control-sm" id="add_matDisplayOrder" name="add_matDisplayOrder">
                                </div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">

                                <textarea type="text" tabindex="9" class="form-control form-control-sm" id="add_matComments" placeholder="Comments" name="add_matComments"></textarea>
                            </div>
                        </div>
                        <div class="d-flex flex-row justify-content-center mb-1 g-2">
                            <div class="col-sm-4">
                                <button type="submit" value="add" class="btn btn-success" id="add-material-btn">Add Material</button>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  Add New Material end-->

    <!-- Add New PFM start-->
    <div class="modal fade" id="addPFMModal" tabindex="-1" aria-labelledby="addPFMModal">
        <div class="modal-dialog modal-sm " style="max-width: 45%">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addPFMModal">Add New PFM</h1>
                </div>
                <div class="modal-body">
                    <form id="add-pfm-form" class="needs-validation p-2" novalidate>
                        <input type="hidden" name="hiddenPfmID" id="hiddenPfmID" />
                        <div class="d-flex flex-column g-1 ">
                            <div class="row pb-1"><!-- Part Number & Part Name -->
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_pfmPartNumber">PFM Part #</label>
                                        <input type="text" tabindex="1" class="form-control form-control-sm" id="add_pfmPartNumber" name="add_pfmPartNumber" required></input>
                                    </div>
                                    <div class="invalid-feedback">Mat part number is required!</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_pfmPartName">PFM Name</label>
                                        <input type="text" tabindex="2" class="form-control form-control-sm" id="add_pfmPartName" name="add_pfmPartName" required>
                                    </div>
                                    <div class="invalid-feedback">PFM name required!</div>
                                </div>
                            </div>
                            <div class="row pb-1"><!-- Customer and ProductID -->
                                <div class="col-sm-6">
                                    <div class="input-group sm-3">
                                        <label class="input-group-text" for="add_MatCustomer">Customer</label>
                                        <input type="text" tabindex="3" class="form-control form-control-sm" id="add_pfmCustomer" name="add_pfmCustomer" required>
                                    </div>
                                    <div class="invalid-feedback">customer required!</div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="input-group sm-3"><label class="input-group-text" for="add_pfmProductID">Product</label>
                                        <select type="text" tabindex="4" class="form-control form-control-sm" id="add_pfmProductID" name="add_pfmProductID" required></select>
                                    </div>
                                    <div class="invalid-feedback">Product required!</div>
                                </div>
                            </div>
                        </div>
                        <div class="row pb-3"><!-- Min Qty & Display Order -->
                            <div class="col-sm-2"></div>

                            <div class="col-sm-4">
                                <div class="input-group sm-2">
                                    <label class="input-group-text" for="add_minLbs">Min Qty</label>
                                    <input type="number" tabindex="5" class="form-control form-control-sm" id="add_pfmMinQty" name="add_pfmMinQty" required>
                                </div>
                                <div class="invalid-feedback">min PFM Qty required!</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label>
                                    <input type="number" tabindex="6" class="form-control form-control-sm" id="add_pfmDisplayOrder" name="add_pfmDisplayOrder">
                                </div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                        <div class="d-flex flex-row justify-content-center mb-1 g-2"> <!-- Buttons -->
                            <div class="col-sm-3"></div>
                            <div class="col-sm-2">
                                <button type="submit" value="add" class="btn btn-success" id="add-pfm-btn">Add PFM</button>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  Add New PFM end-->

    <!-- Edit Product start-->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editProductModal">Update Product Details</h1>
                </div>
                <div class="modal-body">
                    <form id="edit-product-form" class="needs-validation p-2" novalidate>
                        <div class="mb-3">
                            <input type="hidden" name="productID" id="hiddenProductID" />
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="partName">Product Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="partName" name="p_Part" required></input></div>
                                    <div class="invalid-feedback">Product name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="minQty">Min Quantity</label><input type="number" tabindex="1" class="form-control form-control-sm" id="minQty" name="p_minQty" required></div>
                                    <div class="invalid-feedback">Minimum qauntity required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="boxSkid">Boxes/Skid</label><input type="number" tabindex="1" class="form-control form-control-sm" id="boxSkid" name="p_boxSkid" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="partBox">Part Quantity/Box</label><input type="number" tabindex="1" class="form-control form-control-sm" id="partBox" name="p_partBox" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="partWeight">Part Weight (lbs)</label><input type="number" step=".001" class="form-control form-control-sm" id="partWeight" name="p_partWeight" required></div>
                                <div class="invalid-feedback">weight required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="customer">Customer</label><input type="text" class="form-control form-control-sm" id="customer" name="p_customer" required></div>
                                <div class="invalid-feedback">customer required!</div>
                            </div>
                            <div class="row row-cols-2  pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="partType">Production Type</label><input type="text" class="form-control form-control-sm" id="partType" name="p_partType" required></div>
                                    <div class="invalid-feedback">type required!</div>
                                </div>
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="displayOrder" name="p_displayOrder" required></div>
                                    <div class="invalid-feedback">displayOrder required!</div>
                                </div>
                            </div>
                        </div>
                        <div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Update" class="btn btn-success" id="update-product-btn">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  Edit Product end-->

    <!-- Edit Material start-->
    <div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editMaterialModal">Update Material Details</h1>
                </div>
                <form id="edit-material-form" class="needs-validation p-2" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="m_matPartNumber" id="h_matPartNumber" />
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="matName">Material Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="matName" name="m_material" required></input></div>
                                    <div class="invalid-feedback">Material name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="minQty">Used for</label><input type="text" tabindex="1" class="form-control form-control-sm" id="productID" name="m_productID" required></div>
                                    <div class="invalid-feedback">Minimum qauntity required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="minLbs">Min. Lbs</label><input type="number" step=".001" class="form-control form-control-sm" id="minLbs" name="m_minLbs" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="matSupplier">Mat. Supplier</label><input type="text" class="form-control form-control-sm" id="m_matSupplier" name="m_matSupplier" required></div>
                                <div class="invalid-feedback">type required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="minLbs">Price/Lbs</label><input type="number" step=".01" class="form-control form-control-sm" id="m_priceLbs" name="m_priceLbs" required></div>
                                <div class="invalid-feedback">qauntity required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="customer">Customer</label><input type="text" class="form-control form-control-sm" id="mCustomer" name="m_customer" required></div>
                                <div class="invalid-feedback">customer required!</div>
                            </div>
                            <div class="row pb-2">
                                <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="mDisplayOrder" name="m_displayOrder" required></div>
                                <div class="invalid-feedback">displayOrder required!</div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" value="Update" class="btn btn-success" id="update-material-btn">Update Material</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--  Edit Material end-->

    <!-- edit PFM start-->
    <div class="modal fade" id="editPFMModal" tabindex="-1" aria-labelledby="editPFMModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editPFMModal">Edit PFM Details</h1>
                </div>
                <form id="edit-pfm-form" class="needs-validation p-2" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="p_pfmID" id="h_pfmID" />
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="pNumber">PFM Part Number</label><input type="text" tabindex="1" class="form-control form-control-sm" id="pNumber" name="pf_Number" required></input></div>
                                    <div class="invalid-feedback">Part Number is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="pName">PFM Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="pName" name="pf_Name" required></input></div>
                                    <div class="invalid-feedback">Name is required!</div>
                                </div>
                            </div>
                            <div class="row row-cols-2 pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="minQty">Used for</label><input type="text" tabindex="1" class="form-control form-control-sm" id="pProductID" name="pf_productID" required></div>
                                    <div class="invalid-feedback">product used for required!</div>
                                </div>
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="minLbs">Min. Qty</label><input type="number" class="form-control form-control-sm" id="pMinQty" name="pf_minQty" required></div>
                                    <div class="invalid-feedback">qauntity required!</div>
                                </div>
                            </div>
                            <div class="row row-cols-2 pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="customer">Customer</label><input type="text" class="form-control form-control-sm" id="pCustomer" name="pf_customer" required></div>
                                    <div class="invalid-feedback">customer required!</div>
                                </div>
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="displayOrder">Display Order</label><input type="number" class="form-control form-control-sm" id="pDisplayOrder" name="pf_displayOrder" required></div>
                                    <div class="invalid-feedback">displayOrder required!</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" value="Update" class="btn btn-success" id="update-pfm-btn">Update PFM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--  Edit PFM end-->

    <!-- Update product iventory start-->
    <div class="modal fade" id="updateProductModal" tabindex="-1" aria-labelledby="updateProductModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editPFMModal">Update product inventory</h1>
                </div>
                <form id="update-product-form" class="needs-validation p-2" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="p_productID" id="h_productID" />
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="pPartName">Part Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="pPartName" name="p_partName" readonly></input></div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="pStock">Current Stock</label><input type="number" tabindex="1" class="form-control form-control-sm" id="pStock" name="p_Stock" readonly></input></div>
                                    <div class="invalid-feedback">Name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="pAmount">Change amount</label><input type="number" tabindex="1" class="form-control form-control-sm" id="pAmount" name="p_Amount" required></div>
                                    <div class="invalid-feedback">amount required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="invQty" id="add" tabindex="3" value="+" required>Add<label class="form-check-label" for="add"></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="invQty" tabindex="4" id="subtract" value="-">Subtract<label class="form-check-label" for="subtract"></label>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <textarea class="form-control" name="p_commentText" id="commentText" rows="5" placeholder="Comments"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" value="Update" class="btn btn-success" id="update-product-btn">Update Product Qty</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Update product inventory end -->

    <!-- Update material iventory start-->
    <div class="modal fade" id="updateMaterialModal" tabindex="-1" aria-labelledby="updateMaterialModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Update material inventory</h1>
                </div>
                <form id="update-material-form" class="needs-validation p-2" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="u_matPartNumber" id="h_matPartNumber" />
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="uMatName">Material Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="umMatName" name="um_MatName" readonly></input></div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="umStock">Current Stock</label><input type="number" step=".001" tabindex="1" class="form-control form-control-sm" id="umMatLbs" name="um_MatLbs" readonly></input></div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="umAmount">Change amount</label><input type="number" step="0.001" tabindex="1" class="form-control form-control-sm" id="umAmount" name="um_Amount" required></div>
                                    <div class="invalid-feedback">amount required!</div>
                                </div>
                            </div>
                            <div class="row row-cols-2 pb-2">
                                <div class="col">
                                    <input class="form-check-input" type="radio" name="mInvQty" id="add" tabindex="3" value="+" required>Add<label class="form-check-label" for="add"></label>
                                </div>
                                <div class="col">
                                    <input class="form-check-input" type="radio" name="mInvQty" tabindex="4" id="subtract" value="-">Subtract<label class="form-check-label" for="subtract"></label>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <textarea class="form-control" name="um_CommentText" id="MCommentText" rows="5" placeholder="Comments"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" value="Update" class="btn btn-success" id="update-material-btn">Update Material Weight</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Update material inventory end -->

    <!-- Update pfm iventory start-->
    <div class="modal fade" id="updatePfmModal" tabindex="-1" aria-labelledby="updatePfmModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Update PFM inventory</h1>
                </div>
                <form id="update-pfm-form" class="needs-validation p-2" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="u_pfmID" id="h_pfmID" />
                            <input type="hidden" name="u_partNumber" id="h_partNumber" />
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="uPfName">PFM Name</label><input type="text" tabindex="1" class="form-control form-control-sm" id="uPfmName" name="upf_pfmName" readonly></input></div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="uPfmStock">Current Stock</label><input type="number" tabindex="1" class="form-control form-control-sm" id="uPfmStock" name="u_PfmStock" readonly></input></div>
                                    <div class="invalid-feedback">Name is required!</div>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <div class="col">
                                    <div class="input-group sm-3"><label class="input-group-text" for="pAmount">Change amount</label><input type="number" tabindex="1" class="form-control form-control-sm" id="upfAmount" name="upf_Amount" required></div>
                                    <div class="invalid-feedback">amount required!</div>
                                </div>
                            </div>
                            <div class="row row-cols-2 pb-2">
                                <div class="col">
                                    <input class="form-check-input" type="radio" name="pfInvQty" id="add" tabindex="3" value="+" required>Add<label class="form-check-label" for="add"></label>
                                </div>
                                <div class="col">
                                    <input class="form-check-input" type="radio" name="pfInvQty" tabindex="4" id="subtract" value="-">Subtract<label class="form-check-label" for="subtract"></label>
                                </div>
                            </div>
                            <div class="row pb-2">
                                <textarea class="form-control" name="pfm_CommentText" id="pfmCommentText" rows="5" placeholder="Comments"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" value="Update" class="btn btn-success" id="update-pfm-btn">Update PFM Qty</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Update pfm inventory end -->

    <!-- Table to hold the last 4 weeks of production  -->
    <div class="container-fluid">
        <div class="mt-5">
            <div class="row mt-2">
                <div class="col-lg-12 d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <h4 class="text-primary">Inventory Data</h4>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="button" id="loadProductForm" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>
                        <button class="btn btn-primary" type="button" id="loadMaterialForm" data-bs-toggle="modal" data-bs-target="#addMaterialModal">Add New Material</button>
                        <button class="btn btn-primary" type="button" id="loadPFMForm" data-bs-toggle="modal" data-bs-target="#addPFMModal">Add New PFM</button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div id="showAlert"></div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <!-- <div class="table-responsive"> -->
                        <!-- Table to display our db user list -->
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="3">Products</th>
                                </tr>
                                <tr>
                                    <th>Part Number</th>
                                    <th>Qty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="products">

                            </tbody>
                        </table>
                        <!-- </div> -->
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="3">Materials</th>
                                </tr>
                                <tr>
                                    <th>Material Number</th>
                                    <th>Lbs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="materials">

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="table-container-scroll">
                        <table class="table table-striped table-bordered text-center">
                            <thead class="sticky-header">
                                <tr>
                                    <th colspan="3">Purchase Finished Materials</th>
                                </tr>
                                <tr>
                                    <th>Part Number</th>
                                    <th>Qty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pfms">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap js -->
    <script type="text/javascript" src="/lib/js/bootstrap.bundle.min.js"></script>
    <!-- Custom javascript -->
    <script type="module" src="/js/inventoryMain.js"></script>

</body>

</html>