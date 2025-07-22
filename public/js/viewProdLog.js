//File /js/viewProdLog.js

import { fetchProductList, fetchProductionLog } from "./productionApiClient.js";

import {
  populateProductSelect,
  showLoader,
  hideLoader,
  fillViewLogPage,
} from "./productionUiManager.js";

async function init() {
  try {
    const products = await fetchProductList();
    if (!Array.isArray(products)) {
      console.error("Product list failed to load properly.");
      return;
    }
    populateProductSelect(products);
  } catch (error) {}
}

function getLogFormSubmission() {
  const form = document.getElementById("view-prodLog-form");
  if (!form) {
    console.error("Form not found");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!form.checkValidity()) {
      console.error("Form is invalid");
      return;
    }
    showLoader();
    const data = new FormData(form);

    const payload = {
      action: "viewLog",
      prodData: {
        logDate: data.get("logDate"),
        productID: data.get("partName"),
      },
    };
    try {
      const response = await fetchProductionLog(
        payload.prodData.productID,
        payload.prodData.logDate
      );

      if (!response || response.error) {
        console.error("Error fetching production log:", response?.error);
        return;
      }

      fillViewLogPage(response);

      console.log("Production log data:", response);
      // Here you can process the response data and update the UI accordingly
      // For example, you might want to display the log data in a table or a list
    } catch (error) {
      console.error("Failed to fetch production log:", error);
    } finally {
      hideLoader();
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  // Initialize the product select dropdown
  init();
  getLogFormSubmission();
});
