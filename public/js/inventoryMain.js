import {
  renderTables,
  setupEditEventListener,
  hideLoader,
  showLoader,
  populateProductSelect,
  showAlertMessage,
  clearAlert,
} from "/js/inventoryUiManager.js";

import {
  fetchProductsMaterialPFM,
  postData,
  fetchProductList,
} from "/js/inventoryApiClient.js";

// Initialize the page: fetch data and render the tables.
async function init() {
  const data = await fetchProductsMaterialPFM();
  if (data) {
    renderTables(data);
    setupEditEventListener("products", "products");
    setupEditEventListener("materials", "materials");
    setupEditEventListener("pfms", "pfms");
  }
}
init();

// Example: attach a submit listener for the edit product form.
const editProductForm = document.getElementById("edit-product-form");
const editProductModal = new bootstrap.Modal(
  document.getElementById("editProductModal")
);
const editMaterialForm = document.getElementById("edit-material-form");
const editMaterialModal = new bootstrap.Modal(
  document.getElementById("editMaterialModal")
);
const editPFMForm = document.getElementById("edit-pfm-form");
const editPFMModal = new bootstrap.Modal(
  document.getElementById("editPFMModal")
);

const updateProductForm = document.getElementById("update-product-form");
const updateProductModal = new bootstrap.Modal(
  document.getElementById("updateProductModal")
);

const updateMaterialForm = document.getElementById("update-material-form");
const updateMaterialModal = new bootstrap.Modal(
  document.getElementById("updateMaterialModal")
);

const updatePfmForm = document.getElementById("update-pfm-form");
const updatePfmModal = new bootstrap.Modal(
  document.getElementById("updatePfmModal")
);
const addProductForm = document.getElementById("add-product-form");
const addProductModal = new bootstrap.Modal(
  document.getElementById("addProductModal")
);

const addMaterialForm = document.getElementById("add-material-form");
const addMaterialModal = new bootstrap.Modal(
  document.getElementById("addMaterialModal")
);

const addPFMForm = document.getElementById("add-pfm-form");
const addPFMModal = new bootstrap.Modal(document.getElementById("addPFMModal"));

const addShipmentForm = document.getElementById("add-shipment-form");
const addShipmentModal = new bootstrap.Modal(
  document.getElementById("addShipmentModal")
);

async function onMaterialModalShow() {
  showLoader();
  clearAlert();
  try {
    const products = await fetchProductList();
    if (!Array.isArray(products)) {
      showAlertMessage("⚠️ Product list failed to load properly.");
      console.error("products", products);
      return;
    }
    console.log("🧪 products:", products);

    const selEl1 = document.getElementById("add_matProduct");
    populateProductSelect(selEl1, products);
  } catch (error) {
    console.error("Error loading purge modal:", error);
    showAlertMessage("Failed to load purge data.");
  } finally {
    hideLoader();
  }
}
async function onReceiveModalShow() {
  showLoader();
  clearAlert();
  try {
    const products = await fetchProductList();
    if (!Array.isArray(products)) {
      showAlertMessage("⚠️ Product list failed to load properly.");
      console.error("products", products);
      return;
    }
    console.log("🧪 products:", products);

    const selEl1 = document.getElementById("add_shipProductID");
    populateProductSelect(selEl1, products);
  } catch (error) {
    console.error("Error receive material modal:", error);
    showAlertMessage("Failed to product data.");
  } finally {
    hideLoader();
  }
}

async function onPFMModalShow() {
  showLoader();
  clearAlert();
  try {
    const products = await fetchProductList();
    if (!Array.isArray(products)) {
      showAlertMessage("⚠️ Product list failed to load properly.");
      console.error("products", products);
      return;
    }
    console.log("🧪 products:", products);

    const selEl2 = document.getElementById("add_pfmProductID");
    populateProductSelect(selEl2, products);
  } catch (error) {
    console.error("Error loading purge modal:", error);
    showAlertMessage("Failed to load purge data.");
  } finally {
    hideLoader();
  }
}

addProductForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(addProductForm);
  const productData = {
    action: "addProduct",
    productID: formData.get("add_ProductID"),
    partName: formData.get("add_ProductID"),
    minQty: formData.get("add_MinQty"),
    boxesPerSkid: formData.get("add_BoxSkid"),
    partPerBox: formData.get("add_PartsBox"),
    partWeight: formData.get("add_PartWeight"),
    displayOrder: formData.get("add_DisplayOrder"),
    customer: formData.get("add_Customer"),
    productionType: formData.get("add_PartType"),
  };

  try {
    const responseText = await postData(productData);
    document.getElementById("showAlert").innerHTML = responseText;
    addProductForm.reset();
    addProductModal.hide();

    const addProduct = await fetchProductsMaterialPFM();
    renderTables(addProduct);
  } catch (error) {}
});

addMaterialForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(addMaterialForm);
  const materialData = {
    action: "addMaterial",
    matPartNumber: formData.get("add_matPartNumber"),
    matName: formData.get("add_matPartName"),
    productID: formData.get("add_matProduct"),
    minLbs: formData.get("add_minLbs"),
    matCustomer: formData.get("add_matCustomer"),
    matSupplier: formData.get("add_matSupplier"),
    matPriceLbs: formData.get("add_matPriceLbs"),
    comments: formData.get("add_matComments"),
    displayOrder: formData.get("add_matDisplayOrder"),
  };
  try {
    const responseText = await postData(materialData);
    document.getElementById("showAlert").innerHTML = responseText;
    addMaterialForm.reset();
    addMaterialModal.hide();
    const addMaterial = await fetchProductsMaterialPFM();
    renderTables(addMaterial);
  } catch (error) {}
});

addShipmentForm.addEventListener("submit", async (e) => {
  e.preventDefault(); // Prevent default form submission
  const formData = new FormData(addShipmentForm);
  const shipmentData = {
    action: "addShipment",
    productID: formData.get("add_shipProductID"),
    shipQty: formData.get("add_ShipQty"),
    shipWeek: formData.get("add_shipDate"),
  };

  try {
    const responseText = await postData(shipmentData);
    document.getElementById("showAlert").innerHTML = responseText;
    addShipmentForm.reset(); // Reset the form fields after submission
    addShipmentModal.hide(); // Hide the modal after submission
    const updatedInventory = await fetchProductsMaterialPFM(); // Fetch updated inventory data
    renderTables(updatedInventory); // Re-render the tables with updated data
  } catch (error) {
    console.error("Failed to submit shipment form:", error);
  }
});

addPFMForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(addPFMForm);
  const pfmData = {
    action: "addPFM",
    partNumber: formData.get("add_pfmPartNumber"),
    partName: formData.get("add_pfmPartName"),
    productID: formData.get("add_pfmProductID"),
    minQty: formData.get("add_pfmMinQty"),
    customer: formData.get("add_pfmCustomer"),
    displayOrder: formData.get("add_pfmDisplayOrder"),
  };
  try {
    const responseText = await postData(pfmData);
    document.getElementById("showAlert").innerHTML = responseText;
    addPFMForm.reset();
    addPFMModal.hide();
    const addPfm = await fetchProductsMaterialPFM();
    renderTables(addPfm);
  } catch (error) {}
});

editProductForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(editProductForm);
  const productData = {
    action: "editProduct",
    products: {
      productID: formData.get("productID"),
      partName: formData.get("p_Part"),
      minQty: formData.get("p_minQty"),
      boxesPerSkid: formData.get("p_boxSkid"),
      partsPerBox: formData.get("p_partBox"),
      partWeight: formData.get("p_partWeight"),
      customer: formData.get("p_customer"),
      displayOrder: formData.get("p_displayOrder"),
      productionType: formData.get("p_partType"),
    },
  };

  try {
    const responseText = await postData(productData);
    document.getElementById("showAlert").innerHTML = responseText;
    editProductForm.reset();
    editProductModal.hide();

    const updatedInventory = await fetchProductsMaterialPFM();
    import("./inventoryUiManager.js").then(({ renderTables }) => {
      renderTables(updatedInventory);
    });
  } catch (error) {
    console.error("Failed to submit editProduct form:", error);
  }
});

editMaterialForm.addEventListener("submit", async (e) => {
  console.log("submit edit Material button was clicked!");
  //prevent form from submitting data to DB
  e.preventDefault();
  //console.log("Edit Product submit button has been clicked!");
  const formData = new FormData(editMaterialForm);

  //check to make sure the input fields are not empty
  if (!editMaterialForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    editMaterialForm.classList.add("was-validated");
    return false;
  }

  const materialData = {
    action: "editMaterial",
    materials: {
      matPartNumber: formData.get("m_matPartNumber"),
      matName: formData.get("m_material"),
      productID: formData.get("m_productID"),
      minLbs: formData.get("m_minLbs"),
      matCustomer: formData.get("m_customer"),
      matSupplier: formData.get("m_matSupplier"),
      matPriceLbs: formData.get("m_priceLbs"),
      displayOrder: formData.get("m_displayOrder"),
    },
  };

  console.log("Raw data output: ", materialData);

  const data = await fetch("/api/dispatcher.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(materialData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;
    editMaterialForm.reset();
    editMaterialForm.classList.remove("was-validated");
    editMaterialModal.hide();

    //Wait for updated data to be fetched
    const updatedInventory = await fetchProductsMaterialPFM();
    //Optionally re-render table or refresh the dom here
    import("./inventoryUiManager.js").then(({ renderTables }) => {
      renderTables(updatedInventory);
    });
  } catch (error) {
    console.error("Failed to submit form: ", error);
  }
});

editPFMForm.addEventListener("submit", async (e) => {
  console.log("submit edit pfm button clicked");

  e.preventDefault();
  const formData = new FormData(editPFMForm);

  //check to make sure the input fields are not empty
  if (!editPFMForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    editPFMForm.classList.add("was-validated");
    return false;
  }

  const pfmData = {
    action: "editPFM",
    pfm: {
      pfmID: formData.get("p_pfmID"),
      partNumber: formData.get("pf_Number"),
      partName: formData.get("pf_Name"),
      productID: formData.get("pf_productID"),
      minQty: formData.get("pf_minQty"),
      customer: formData.get("pf_customer"),
      displayOrder: formData.get("pf_displayOrder"),
    },
  };

  console.log("Raw data output: ", pfmData);

  const data = await fetch("/api/dispatcher.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(pfmData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;
    editPFMForm.reset();
    editPFMForm.classList.remove("was-validated");
    editPFMModal.hide();

    //Wait for updated data to be fetched
    const updatedInventory = await fetchProductsMaterialPFM();
    //Optionally re-render table or refresh the dom here
    import("./inventoryUiManager.js").then(({ renderTables }) => {
      renderTables(updatedInventory);
    });
  } catch (error) {
    console.error("Failed to submit form: ", error);
    logToServer("Failed to submit form: ", "ERROR", error);
  }
});

updateProductForm.addEventListener("submit", async (e) => {
  console.log("submit update button clicked");

  e.preventDefault();
  const formData = new FormData(updateProductForm);

  if (!updateProductForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    updateProductForm.classList.add("was-validated");
    return false;
  }

  const productData = {
    action: "updateProduct",
    productID: formData.get("p_productID"),
    partName: formData.get("p_partName"),
    partQty: formData.get("p_Stock"),
    changeAmount: formData.get("p_Amount"),
    comments: formData.get("p_commentText"),
    operator: formData.get("invQty"),
  };

  console.log("Raw data output: ", productData);
  const data = await fetch("/api/dispatcher.php", {
    method: "POST",
    headers: { "Conent-Type": "application/json" },
    body: JSON.stringify(productData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;

    updateProductForm.reset();
    updateProductForm.classList.remove("was-validated");
    updateProductModal.hide();

    //Wait for updated data to be fetched
    const updatedInventory = await fetchProductsMaterialPFM();
    //Optionally re-render table or refresh the dom here
    import("./inventoryUiManager.js").then(({ renderTables }) => {
      renderTables(updatedInventory);
    });
  } catch (error) {
    console.error("Failed to submit form: ", error);
  }
});

updateMaterialForm.addEventListener("submit", async (e) => {
  console.log("submit material update button clicked");

  e.preventDefault();

  const hiddenInput = updateMaterialForm.querySelector(
    '[name="u_matPartNumber"]'
  );
  console.log("Hidden input from form scope:", hiddenInput);
  console.log("Hidden input value at submit:", hiddenInput?.value);

  const formData = new FormData(updateMaterialForm);

  if (!updateMaterialForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    updateMaterialForm.classList.add("was-validated");
    return false;
  }
  console.log(
    "Actual DOM value:",
    document.getElementById("h_matPartNumber").value
  );
  const materialData = {
    action: "updateMaterial",
    matPartNumber: document.getElementById("h_matPartNumber").value,
    matLbs: formData.get("um_MatLbs"),
    changeAmount: formData.get("um_Amount"),
    comments: formData.get("um_CommentText"),
    operator: formData.get("mInvQty"),
  };

  console.log("matPartNumber:", formData.get("u_matPartNumber"));
  console.log("matLbs:", formData.get("um_MatLbs"));

  console.log("RAW data outpput: ", materialData);
  const data = await fetch("/api/dispatcher.php", {
    method: "POST",
    headers: { "Conent-Type": "application/json" },
    body: JSON.stringify(materialData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;
    updateMaterialForm.reset();
    updateMaterialForm.classList.remove("was-validated");
    updateMaterialModal.hide();

    //Wait for updated data to be fetched
    const updatedInventory = await fetchProductsMaterialPFM();
    //Optionally re-render table or refresh the dom here
    import("./inventoryUiManager.js").then(({ renderTables }) => {
      renderTables(updatedInventory);
    });
  } catch (error) {
    console.error("Failed to submit update Material qty.");
  }
});

updatePfmForm.addEventListener("submit", async (e) => {
  console.log("submit pfm update button click");
  e.preventDefault();

  const formData = new FormData(updatePfmForm);

  if (!updatePfmForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    updatePfmForm.classList.add("was-validated");
    return false;
  }

  const pfmData = {
    action: "updatePfm",
    pfmID: document.getElementById("h_pfmID").value,
    partNumber: document.getElementById("h_partNumber").value,
    Qty: formData.get("u_PfmStock"),
    changeAmount: formData.get("upf_Amount"),
    comments: formData.get("pfm_CommentText"),
    operator: formData.get("pfInvQty"),
  };

  const data = await fetch("/api/dispatcher.php", {
    method: "POST",
    headers: { "Conent-Type": "application/json" },
    body: JSON.stringify(pfmData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;

    updatePfmForm.reset();
    updatePfmForm.classList.remove("was-validated");
    updatePfmModal.hide();

    //Wait for updated data to be fetched
    const updatedInventory = await fetchProductsMaterialPFM();
    //Optionally re-render table or refresh the dom here
    import("./inventoryUiManager.js").then(({ renderTables }) => {
      renderTables(updatedInventory);
    });
  } catch (error) {
    console.error("submit pfm update pfm qty.");
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const addMaterialEl = document.getElementById("addMaterialModal");
  addMaterialEl.addEventListener("show.bs.modal", onMaterialModalShow);

  const addPFMEl = document.getElementById("addPFMModal");
  addPFMEl.addEventListener("show.bs.modal", onPFMModalShow);

  const receiveMaterialEl = document.getElementById("addShipmentModal");
  receiveMaterialEl.addEventListener("show.bs.modal", onReceiveModalShow);
});
