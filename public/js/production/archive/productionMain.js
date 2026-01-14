//FILE: /js/productionMain.js

import {
  renderTables,
  setupViewEventListener,
  fetchAndFillForm,
  showLoader,
  hideLoader,
  showAlertMessage,
  clearAlert,
  populateProductSelect,
  initAddModalUI,
} from "./productionUiManager.js";

import {
  fetchProdLogs,
  fetchProductList,
  postPurge,
} from "./productionApiClient.js";

async function init() {
  //load & render the landing-page table
  const data = await fetchProdLogs();
  if (data) {
    renderTables(data);
    //listen for clicks inside <table id="last4wks">
    setupViewEventListener("read4wks", "prodLogs", onRowClick);
  }
}

async function onRowClick(id) {
  //fetch that one record and the previousLog and fill form
  await fetchAndFillForm(id, "prodLogs");

  //collect current and previous hopper inputs by ID
  const currentEls = ["vhop1Lbs", "vhop2Lbs", "vhop3Lbs", "vhop4Lbs"].map(
    (id) => document.getElementById(id)
  );
}

init();

async function onPurgeModalShow() {
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

    const selEl1 = document.getElementById("p_PartName");
    populateProductSelect(selEl1, products);
  } catch (error) {
    console.error("Error loading purge modal:", error);
    showAlertMessage("Failed to load purge data.");
  } finally {
    hideLoader();
  }
}

function addPurgeFormSubmission() {
  const form = document.getElementById("add-purge-form");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearAlert();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    showLoader();
    const data = new FormData(form);
    const payload = {
      action: "addPurge",
      productID: data.get("p_PartName"),
      prodDate: data.get("p_LogDate"),
      purgeLbs: data.get("p_purgeLbs"),
    };

    try {
      const result = await postPurge(payload);
      bootstrap.Modal.getInstance(
        document.getElementById("addPurgeModal")
      ).hide();

      showAlertMessage(result.message, "showAlert");
    } catch (err) {
      console.error(err);
      showAlertMessage(
        "Failed to save purge log. Try again.",
        "showAlert",
        "danger"
      );
    } finally {
      const data = await fetchProdLogs();
      if (data) {
        renderTables(data);
      }

      hideLoader();
    }
  });
}
// 5) Bootstrap everything on page load
document.addEventListener("DOMContentLoaded", () => {
  const addPurgeEl = document.getElementById("addPurgeModal");
  addPurgeEl.addEventListener("show.bs.modal", onPurgeModalShow);

  // Bind form submit
  //wireFormSubmission();
  addPurgeFormSubmission();
});
