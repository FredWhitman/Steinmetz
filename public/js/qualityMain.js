//FILE /js/qualityMain.js

import {
  renderTables,
  setupEventListener,
  showAlertMessage,
  clearAlert,
  populateProductSelect,
  showLoader,
  hideLoader,
} from "./qualityUiManager.js";

import {
  fetchQualityLogs,
  postQaRejects,
  fetchProductList,
} from "./qualityApiClient.js";

async function init() {
  //load and render tables for OvenLog and QA Rejects
  const data = await fetchQualityLogs();
  if (data) {
    renderTables(data);
    setupEventListener("qaRejectLogs", "qaRejectsLogs");
    setupEventListener("lotChangeLogs", "lotchangelogs");
    setupEventListener("ovenLogs", "ovenlogs");
  }
}

init();

function addQaRejectsFormSubmision() {
  const form = document.getElementById("add-qaReject-form");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const data = new FormData(form);
    const payload = {
      action: "addQaRejects",
      qaRejectData: {
        prodDate: data.get("qaLogDate"),
        productID: data.get("qaPart"),
        rejects: data.get("rejects"),
        comments: data.get("qaComments"),
      },
    };

    try {
      const result = await postQaRejects(payload);
      
      if (result) {
        const alertData = JSON.parse(result.message);
          document.getElementById("showAlert").innerHTML = alertData.html;
        bootstrap.Modal.getInstance(
          document.getElementById("addQARejectsModal")
        ).hide();
      }
      console.log("postQaRejects result: ", result);
      const data = await fetchQualityLogs();
      if (data) {
        renderTables(data);
      }
    } catch (error) {
      console.error("Failed to submit QA Rejects:", error);
    }
  });
}

// 1) When the modal opens, load options
async function onModalShow() {
  showLoader();
  clearAlert();

  try {
    const response = await fetchProductList();
    const products = response.products;
    console.log("🧪 products:", products);

    if (!Array.isArray(products)) {
      showAlertMessage("⚠️ Product list failed to load properly.");
      console.error("products", products);
      return;
    }

    populateProductSelect(products);
  } catch (err) {
    console.error(err);
    showAlertMessage("Unable to load products or materials.");
  } finally {
    hideLoader();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Load data when modal shows
  const addModalEl = document.getElementById("addQARejectsModal");
  addModalEl.addEventListener("show.bs.modal", onModalShow);

  addQaRejectsFormSubmision();
});
