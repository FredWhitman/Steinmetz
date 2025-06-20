import {
  renderTables,
  setupEditEventListener,
} from "/js/inventoryUiManager.js";

import { fetchProductsMaterialPFM, postData } from "/js/inventoryApiClient.js";

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
    // Optionally, refresh your tables after a successful update.
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
  } catch (error) {
    console.error("Failed to submit form: ", error);
    logToServer("Failed to submit form: ", "ERROR", error);
  }
});
