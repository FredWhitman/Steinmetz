//FILE /js/addProdLog.js

import { fetchProductList, fetchMaterialList } from "./productionApiClient.js";

import {
  populateProductSelect,
  populateMaterialSelects,
  showLoader,
  hideLoader,
} from "./productionUiManager.js";

async function init() {
  try {
    const [products, materials] = await Promise.all([
      fetchProductList(),
      fetchMaterialList(),
    ]);

    if (!Array.isArray(products) || !Array.isArray(materials)) {
      console.error("Product or Material list failed to load properly.");
      return;
    }
    populateProductSelect(products);
    populateMaterialSelects(materials);
  } catch (error) {}
}

document.addEventListener("DOMContentLoaded", () => {
  // Initialize the product select dropdown
  init();
});
