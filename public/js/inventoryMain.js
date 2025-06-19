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
