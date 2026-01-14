// FILE: /js/production/viewProdLog_new.js

import {
  fetchProductList,
  fetchProductionLog,
} from "./productionApiClient_new.js";

import {
  populateProductSelect,
  showLoader,
  hideLoader,
  clearAlert,
  showAlertMessage,
  fillViewLogPage,
} from "./productionUiManager_new.js";

import { handleFormSubmit } from "./productionMain_new.js";

// --------------------------------------------------
// Initialize product dropdown
// --------------------------------------------------

async function initProductSelect() {
  try {
    showLoader();
    const products = await fetchProductList();

    if (!Array.isArray(products)) {
      console.error("Product list failed to load properly:", products);
      showAlertMessage("Failed to load product list.", "showAlert", "danger");
      return;
    }

    const selectEl = document.getElementById("partName");
    populateProductSelect(selectEl, products);
  } catch (error) {
    console.error("Error loading product list:", error);
    showAlertMessage("Error loading product list.", "showAlert", "danger");
  } finally {
    hideLoader();
  }
}

// --------------------------------------------------
// Payload builder for handleFormSubmit
// --------------------------------------------------

function buildViewLogPayload(d) {
  return {
    productID: d.get("partName"),
    logDate: d.get("logDate"),
  };
}

// --------------------------------------------------
// Wrapper for fetchProductionLog to match handleFormSubmit signature
// --------------------------------------------------

async function fetchLogWrapper(payload) {
  const { productID, logDate } = payload;

  const response = await fetchProductionLog(productID, logDate);

  if (!response || response.error) {
    showAlertMessage(
      "No production log found for the selected product and date.",
      "showAlert",
      "danger"
    );
    throw new Error("Production log not found");
  }

  fillViewLogPage(response);
  return response;
}

// --------------------------------------------------
// Auto-load log from URL parameters
// --------------------------------------------------

async function autoLoadFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  const productID = urlParams.get("productID");
  const logDate = urlParams.get("prodDate");

  if (!productID || !logDate) return;

  showLoader();
  try {
    const response = await fetchProductionLog(productID, logDate);

    if (!response || response.error) {
      console.error("Error fetching production log by URL:", response?.error);
      showAlertMessage(
        "Failed to load production log from URL.",
        "showAlert",
        "danger"
      );
      return;
    }

    fillViewLogPage(response);
  } catch (error) {
    console.error("Failed to auto-load production log:", error);
    showAlertMessage("Error loading production log.", "showAlert", "danger");
  } finally {
    hideLoader();
  }
}

// --------------------------------------------------
// DOMContentLoaded
// --------------------------------------------------

document.addEventListener("DOMContentLoaded", () => {
  initProductSelect();

  // Form submission using shared handler
  handleFormSubmit(
    "view-prodLog-form",
    buildViewLogPayload,
    fetchLogWrapper,
    null // no modal to close
  );

  // Auto-load if URL contains productID & prodDate
  autoLoadFromURL();
});
