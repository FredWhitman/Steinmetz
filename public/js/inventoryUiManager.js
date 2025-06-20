// uiManager.js

// DOM elements
const tbodyProducts = document.getElementById("products");
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
const showAlert = document.getElementById("showAlert"); // Adjust according to your markup

// Loader functions
export function showLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.remove("d-none");
}

export function hideLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.add("d-none");
}

// Build HTML for the tables
export function buildProductsTable(products) {
  let html = "";
  products.forEach((row) => {
    // Highlight if below minimum quantity
    let colorStyle =
      row.partQty < row.minQty ? "style='color:red;font-weight: bold;'" : "";
    html += `<tr data-id="${row.productID}">
               <td><span ${colorStyle}>${row.productID}</span></td>
               <td><span ${colorStyle}>${row.partQty}</span></td>
               <td>
                 <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" title="edit product" data-bs-toggle="modal" data-bs-target="#editProductModal"><i class="bi bi-pencil"></i></a>
                 <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update product qty" data-bs-toggle="modal" data-bs-target="#updateProductModal"><i class="bi bi-file-earmark-check"></i></a>
               </td>
             </tr>`;
  });
  return html;
}

export function buildMaterialsTable(materials) {
  let html = "";
  materials.forEach((row) => {
    let colorStyle =
      row.matLbs < row.minLbs ? "style='color:red; font-weight:bold;'" : "";
    html += `<tr data-id="${row.matPartNumber}">
               <td><span ${colorStyle}>${row.matName}</span></td>
               <td><span ${colorStyle}>${row.matLbs}</span></td>
               <td>
                 <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" title="edit material" data-bs-toggle="modal" data-bs-target="#editMaterialModal"><i class="bi bi-pencil"></i></a>
                 <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update material lbs" data-bs-toggle="modal" data-bs-target="#updateInventoryModal"><i class="bi bi-file-earmark-check"></i></a>
               </td>
             </tr>`;
  });
  return html;
}

export function buildPfmsTable(pfms) {
  let html = "";
  pfms.forEach((row) => {
    let colorStyle =
      row.Qty < row.minQty ? "style='color:red;font-weight:bold;'" : "";
    html += `<tr data-id="${row.pfmID}">
               <td><span ${colorStyle}>${row.partName}</span></td>
               <td><span ${colorStyle}>${row.Qty}</span></td>
               <td>
                 <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" title="edit pfm" data-bs-toggle="modal" data-bs-target="#editPFMModal"><i class="bi bi-pencil"></i></a>
                 <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update pfm qty" data-bs-toggle="modal" data-bs-target="#updatePFMModal"><i class="bi bi-file-earmark-check"></i></a>
               </td>
             </tr>`;
  });
  return html;
}

// Attach a shared event listener for table rows
export function setupEditEventListener(elementId, table) {
  document.getElementById(elementId).addEventListener("click", (e) => {
    const editLink = e.target.closest("a.editLink");
    const updateLink = e.target.closest("a.updateLink");
    if (editLink) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const id = row ? row.getAttribute("data-id") : null;
      if (id && id.trim()) {
        // Dispatch to the API client to fill form
        import("./inventoryApiClient.js").then(({ fetchAndFillForm }) => {
          fetchAndFillForm(id.trim(), table);
        });
      }
    }
    if (updateLink) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const id = row ? row.getAttribute("data-id") : null;
      if (id && id.trim()) {
        import("./inventoryApiClient.js").then(({ fetchAndFillUpdateForm }) => {
          fetchAndFillUpdateForm(id.trim(), table);
        });
      }
    }
  });
}

// Function to render tables into the DOM
export function renderTables({ products, materials, pfms }) {
  document.getElementById("products").innerHTML = buildProductsTable(products);
  document.getElementById("materials").innerHTML =
    buildMaterialsTable(materials);
  document.getElementById("pfms").innerHTML = buildPfmsTable(pfms);
}
