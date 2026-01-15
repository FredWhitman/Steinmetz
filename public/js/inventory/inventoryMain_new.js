//FILE: public/js/inventory/inventoryMain_new.js

import {
  getInventory,
  getProducts,
  getShipments,
  getRecordForEdit,
  getRecordForUpdate,
  addProduct,
  addMaterial,
  addPFM,
  addShipment,
  editProduct,
  editMaterial,
  editPFM,
  updateProduct,
  updateMaterial,
  updatePFM,
} from "./inventoryApiClient_new.js";

import {
  renderTables,
  renderShipmentsTable,
  populateProductSelect,
  populateMaterialSelect,
  populateEditForm,
  populateUpdateForm,
  showAlert,
  clearAlerts,
  showLoader,
  hideLoader,
} from "./inventoryUiManager_new.js";

/*-----------------------------------------
    INITIALIZATION
-----------------------------------------*/
async function init() {
  await loadInventory();

  //Setup Table click handlers
  setupTableClickHandlers("products", "products");
  setupTableClickHandlers("materials", "materials");
  setupTableClickHandlers("pfm", "pfm");

  //Modal show handlers
  setupModalShowHandlers();
}

async function loadInventory() {
  showLoader();
  try {
    const data = await getInventory();
    renderTables(data);
  } catch (error) {
    showAlert(`<div class="alert alert-danger">${error.message}</div>`);
  } finally {
    hideLoader();
  }
}

/*-----------------------------------------
    TABLE CLICK HANDLERS (edit/update)
-----------------------------------------*/

function setupTableClickHandlers(elementId, table) {
  document.getElementById(elementId).addEventListener("click", async (e) => {
    const editLink = e.target.closest("a.editLink");
    const updateLink = e.target.closest("a.updateLink");

    const row = e.target.closest("tr");
    const id = row?.getAttribute("data-id");
    if (!id) return;

    if (editLink) {
      e.preventDefault();
      await handleEditClick(id, table);
    }
    if (updateLink) {
      e.preventDefault();
      await handleUpdateClick(id, table);
    }
  });
}

async function handleEditModal(id, table) {
  try {
    const data = await getRecordForEdit(id, table);
    const mapping = getEditFieldMapping(table);
    populateEditForm(data, mapping);
  } catch (err) {
    showAlert(`<div class="alert alert-danger">${err.message}</div>`);
  }
}

async function handleUpdateModal(id, table) {
  try {
    const data = await getRecordForUpdate(id, table);
    const mapping = getUpdateFieldMapping(table);
    populateUpdateForm(data, mapping);
  } catch (err) {
    showAlert(`<div class="alert alert-danger">${err.message}</div>`);
  }
}

/* -----------------------------------------
   FIELD MAPPINGS
----------------------------------------- */

function getEditFieldMapping(table) {
  return {
    products: {
      productID: "hiddenProductID",
      partName: "partName",
      minQty: "minQty",
      boxesPerSkid: "boxSkid",
      partsPerBox: "partBox",
      partWeight: "partWeight",
      customer: "customer",
      productType: "partType",
      displayOrder: "displayOrder",
    },
    materials: {
      matPartNumber: "h_matPartNumber",
      matName: "matName",
      productID: "productID",
      minLbs: "minLbs",
      matCustomer: "mCustomer",
      matSupplier: "m_matSupplier",
      matPriceLbs: "m_priceLbs",
      displayOrder: "mDisplayOrder",
    },
    pfms: {
      pfmID: "h_pfmID",
      partNumber: "pNumber",
      partName: "pName",
      productID: "pProductID",
      minQty: "pMinQty",
      customer: "pCustomer",
      displayOrder: "pDisplayOrder",
    },
  }[table];
}

function getUpdateFieldMapping(table) {
  return {
    products: {
      productID: "h_productID",
      partName: "pPartName",
      partQty: "pStock",
    },
    materials: {
      matPartNumber: "h_matPartNumber",
      matName: "umMatName",
      matLbs: "umMatLbs",
    },
    pfms: {
      pfmID: "h_pfmID",
      partNumber: "h_partNumber",
      partName: "uPfmName",
      Qty: "uPfmStock",
    },
  }[table];
}

/* ----------------------------------------------------
        MODAL SHOW HANDLERS
-----------------------------------------------------*/
function setupModalHandlers() {
  document
    .getElementById("addMaterialModal")
    ?.addEventListener("show.bs.modal", loadProductsForMaterial);

  document
    .getElementById("addPFMModal")
    ?.addEventListener("show.bs.modal", loadProductsforPFM);
  document
    .getElementById("addShipmentModal")
    ?.addEventListener("show.bs.modal", loadProductsForShipment);
}

async function loadProductsForMaterial() {
  const products = await getProducts();
  populateProductSelect(document.getElementById("productID"), products);
}

async function loadProductsforPFM() {
  const products = await getProducts();
  populateProductSelect(document.getElementById("pProductID"), products);
}

async function loadProductsForShipment() {
  const products = await getProducts();
  populateProductSelect(document.getElementById("shipProductID"), products);
}

/* ----------------------------------------------------------------
        FORM SUBMISSIONS
-----------------------------------------------------------------*/

function setupFormHandler(formId, submitFn) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearAlerts();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const formData = Object.fromEntries(new FormData(form).entried());

    try {
      const response = await submitFn(formData);
      showAlert(response.html);
      form.reset();
      form.classList.remove("was-validated");
      await loadInventory();
    } catch (err) {
      showAlert('div class="alert alert-danger">${err.message}</div>');
    }
  });
}

/* -------------------------------------------------------------------
        REGISTER FORM HANDLERS
--------------------------------------------------------------------*/

setupFormHandler("addProductForm", addProduct);
setupFormHandler("addMaterialForm", addMaterial);
setupFormHandler("addPFMForm", addPFM);
setupFormHandler("addShipmentForm", addShipment);

setupFormHandler("editProductForm", editProduct);
setupFormHandler("editMaterialForm", editMaterial);
setupFormHandler("editPFMForm", editPFM);

setupFormHandler("updateProductForm", updateProduct);
setupFormHandler("updateMaterialForm", updateMaterial);
setupFormHandler("updatePfmForm", updatePFM);

/* -----------------------------------------------------------------
        START
------------------------------------------------------------------*/

document.addEventListener("DOMContentLoaded", init);
