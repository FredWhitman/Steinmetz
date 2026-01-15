//FILE: public/js/inventory/inventoryUiManager_new.js

/* -----------------------------------------
   ALERTS
----------------------------------------- */

export function clearAlerts(containerID = "showAlert") {
  const el = document.getElementById(containerID);
  if (el) el.innerHTML = "";
}

export function showAlert(html, containerID = "showAlert") {
  const el = document.getElementById(containerID);
  if (el) el.innerHTML = html;
}

/* -----------------------------------------
   LOADER
----------------------------------------- */
export function showLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.remove("d-none");
}

export function hideLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.add("d-none");
}

/* -----------------------------------------
   SELECT POPULATION
----------------------------------------- */

function populateSelect(
  selectEl,
  items,
  valueKey,
  labelKey,
  includeEmpty = true
) {
  if (!selectEl) return;

  // Clear existing options
  selectEl.innerHTML = includeEmpty
    ? '<option value =""> -- Select --</option>'
    : "";
  items.forEach((item) => {
    const opt = document.createElement("option");
    opt.value = item[valueKey];
    opt.textContent = item[labelKey];
    selectEl.appendChild(opt);
  });
}

export function populateProductSelect(selectEl, products) {
  populateSelect(selectEl, products, "productID", "partName");
}

export function populateMaterialSelect(selectEl, materials) {
  populateSelect(selectEl, materials, "materialID", "matName");
}

/* -----------------------------------------
   TABLE POPULATION
----------------------------------------- */

export function buildProductsTable(products) {
  return products
    .map((row) => {
      const warn =
        row.partQty < row.minQty ? "style='color: red; font-weight:bold;'" : "";
      return `
            <tr data-id= "$row.productID">
                <td><span ${warn}>${row.partName}</span></td>
                <td><span ${warn}>${row.partQty}</span></td>
                <td>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" data-bs-toggle="modal" data-bs-target="#editProductModal">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 updateLink" data-bs-toggle="modal" data-bs-target="#updateProductModal">
                        <i class="bi bi-file-earmark-check"></i>
                    </a>
                </td>
            </tr>
        `;
    })
    .join("");
}

export function buildMaterialsTable(materials) {
  return materials
    .map((row) => {
      const warn =
        row.matLbs < row.minLbs ? "style='color: red; font-weight:bold;'" : "";
      return `
        <tr data-id="row.MatPartNumber}">
            <td><span ${warn}>${row.matName}</span></td>
            <td><span ${warn}>${row.matLbs}</span></td>
            <td>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" data-bs-toggle="modal" data-bs-target="#editMaterialModal">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 updateLink" data-bs-toggle="modal" data-bs-target="#updateMaterialModal">
                    <i class="bi bi-file-earmark-check"></i>
                </a>
            </td>
        </tr>`;
    })
    .join("");
}

export function buildPfmsTable(pfms) {
  return pfms
    .map((row) => {
      const warn =
        row.Qty < row.minQty ? "style='color: red; font-weight:bold;'" : "";
      return `
            <tr data-id="${row.pfmID}">
                <td><span ${warn}>${row.pfmName}</span></td>
                <td><span ${warn}>${row.pfmQty}</span></td>
                <td>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" data-bs-toggle="modal" data-bs-target="#editPfmModal">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 updateLink" data-bs-toggle="modal" data-bs-target="#updatePfmModal">
                        <i class="bi bi-file-earmark-check"></i>
                    </a>
                </td>
            </tr>`;
    })
    .join("");
}

/* -----------------------------------------
   TABLE RENDERING
----------------------------------------- */

export function renderTables({ products, materials, pfms }) {
  document.getElementById("products").innerHTML = buildProductsTable(products);
  document.getElementById("materials").innerHTML =
    buildMaterialsTable(materials);
  document.getElementById("pfms").innerHTML = buildPfmsTable(pfms);
}

/* -----------------------------------------
   SHIPMENTS TABLE
----------------------------------------- */

export function renderShipmentsTable(shipments, productId) {
  const container = document.getElementById("shipments");

  if (!shipments || shipments.length === 0) {
    container.innerHtml =
      "<tr><td colspan='20'> No shipment data available.</td></tr>";
    return;
  }

  const grouped = groupShipmentsByWeek(shipments, productIDs);

  let html = "";
  Object.entries(grouped).forEach(([week, productIDs]) => {
    html += `<tr><td>${week}</td>`;
    productIDs.forEach((pid) => {
      html += `<td>${products[pid] ?? 0}</td>`;
    });
    html += `</tr>`;
  });
  container.innerHTML = html;
}

function groupShipmentsByWeek(shipments, productIDs) {
  const weeks = {};

  shipments.forEach(({ shipWeek, productID, shipQty }) => {
    if (!weeks[shipWeek]) {
      weeks[shipWeek] = {};
      productIDs.forEach((pid) => (weeks[shipWeek][pid] = 0));
    }
    weeks[shipWeek][productID] = shipQty;
  });
  return weeks;
}

/* ---------------------------------------------------------
    FORM POPULATION (EDIT/UPDATE MODALS)
*/
export function populateEditForm(data, mapping) {
  Object.entries(mapping).forEach(([dbKey, formID]) => {
    const el = document.getElementById(formID);
    if (el) el.value = data[dbKey] ?? "";
  });
}
