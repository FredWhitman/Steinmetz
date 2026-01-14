//FILE: /js/quality/qualityUiManager_new.js

let materials = [];
let products = [];

async function loadMaterials() {
  try {
    const res = await fetch(`/api/qaDispatcher.php?action=getMaterials`);
    materials = await res.json();
  } catch (err) {
    console.error("Failed to load materials:", err);
    showAlertMessage(
      "Failed to load materials for modals.",
      "showAlert",
      "danger"
    );
  }
}

async function loadProducts() {
  try {
    const res = await fetch(`/api/qaDispatcher.php?action=getProducts`);
    products = await res.json();
  } catch (err) {
    console.error("Failed to load products:", err);
    showAlertMessage(
      "Failed to load products for modals.",
      "showAlert",
      "danger"
    );
  }
}

// Preload products for all modals
loadProducts();

// Preload materials for lot change modals
loadMaterials();

export function showLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.remove("d-none");
}

export function hideLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.add("d-none");
}

export function showAlertMessage(
  message,
  containerID = "showAlert",
  level = "success"
) {
  const container = document.getElementById(containerID);
  if (!container) return;
  container.innerHTML = `
    <div class="alert alert-${level} alert-dismissible fade show" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
}

export function clearAlert(containerID = "showAlert") {
  const c = document.getElementById(containerID);
  if (c) c.innerHTML = "";
}

function buildTable(rows, columns, buildRowFn, emptyMessage) {
  if (rows.length === 0) {
    return `<tr><td colspan="${columns}" class="text-center">${emptyMessage}</td></tr>`;
  }
  return rows.map(buildRowFn).join("");
}

export function renderTables({ qaRejectLogs, ovenLogs, lotChangeLogs }) {
  document.getElementById("qaRejectLogs").innerHTML =
    buildQaRejectTable(qaRejectLogs);
  document.getElementById("ovenLogs").innerHTML = buildOvenLogsTable(ovenLogs);
  document.getElementById("lotChangeLogs").innerHTML =
    buildLotChangeTable(lotChangeLogs);
}

// Generic select population
function populateSelect(selectEl, items, { valueKey, labelKey }) {
  //Convert string ID to element
  if (typeof selectEl === "string") {
    selectEl = document.getElementById(selectEl);
  }

  if (!selectEl) {
    console.error("Select element not found");
    return;
  }

  selectEl.innerHTML = `<option value="">– Select –</option>`;

  items.forEach((item) => {
    const opt = document.createElement("option");
    opt.value = item[valueKey];
    opt.textContent = item[labelKey];
    selectEl.append(opt);
  });
}

export const populateProductSelect = (el, products) =>
  populateSelect(el, products, {
    valueKey: "productID",
    labelKey: "partName",
  });

export const populateMaterialSelect = (el, materials) =>
  populateSelect(el, materials, {
    valueKey: "matPartNumber",
    labelKey: "matName",
  });

export const populatePFMSelect = (el, pfms) =>
  populateSelect(el, pfms, {
    valueKey: "partNumber",
    labelKey: "partName",
  });

export function buildQaRejectTable(qaRejectLogs) {
  const tableHtml = buildTable(
    qaRejectLogs,
    6,
    (row) => `
      <tr>
        <td>${row.prodDate}</td>
        <td>${row.prodLogID}</td>
        <td>${row.productID}</td>
        <td>${row.rejects}</td>
        <td>${row.comments}</td>
        <td>
          <a href="#" 
             class="btn btn-primary btn-sm rounded-pill py-0 viewLink"
             title="View QA Reject"
             data-id="${row.qaRejectLogID}"
             data-bs-toggle="modal" 
             data-bs-target="#viewQaRejectsModal">
             <i class="bi bi-eye-fill"></i>
          </a>
        </td>
      </tr>`,
    "No records found"
  );

  // Attach click handlers after table is rendered
  setTimeout(() => {
    document.querySelectorAll(".viewLink").forEach((link) => {
      link.addEventListener("click", async (e) => {
        e.preventDefault();
        const id = link.dataset.id;
        try {
          const res = await fetch(
            `/api/qaDispatcher.php?action=getQaRejectLog&id=${id}`
          );
          const data = await res.json();
          populateQaRejectModal(data); // fill modal fields
        } catch (err) {
          console.error("Failed to fetch QA Reject Log:", err);
          showAlertMessage(
            "Failed to load QA Reject log.",
            "showAlert",
            "danger"
          );
        }
      });
    });
  }, 0);

  return tableHtml;
}

export function buildOvenLogsTable(ovenLogs) {
  const tableHtml = buildTable(
    ovenLogs,
    13,
    (row) => {
      let timeDiffMinutes = null;
      if (row.inOvenTime && row.outOvenTime) {
        const inTime = new Date(`1970-01-01T${row.inOvenTime}`);
        const outTime = new Date(`1970-01-02T${row.outOvenTime}`);
        timeDiffMinutes = (outTime - inTime) / (1000 * 60);
      }
      const colorClass =
        timeDiffMinutes !== null &&
        (timeDiffMinutes < 960 || timeDiffMinutes > 1440)
          ? "text-danger"
          : "text-success";

      return `
        <tr>
          <td class="${colorClass}"><strong>${row.productID}</strong></td>
          <td>${row.inOvenDate}</td>
          <td>${row.inOvenTime}</td>
          <td>${row.firstShift === 1 ? "&#10003;" : ""}</td>
          <td>${row.secondShift === 1 ? "&#10003;" : ""}</td>
          <td>${row.thirdShift === 1 ? "&#10003;" : ""}</td>
          <td>${row.inOvenTemp} °</td>
          <td>${row.inOvenInitials}</td>
          <td>${row.outOvenDate}</td>
          <td>${row.outOvenTime}</td>
          <td>${row.outOvenTemp} °</td>
          <td>${row.outOvenInitials}</td>
          <td>
            <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewOvenLog"
               title="View Oven Log" data-id="${row.ovenLogID}"
               data-bs-toggle="modal" data-bs-target="#viewOvenLogModal">
               <i class="bi bi-eye-fill"></i>
            </a>
            <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateOvenLog"
               title="Update Oven Log" data-id="${row.ovenLogID}"
               data-bs-toggle="modal" data-bs-target="#updateOvenLogModal">
               <i class="bi bi-file-earmark-check"></i>
            </a>
          </td>
        </tr>`;
    },
    "No records found"
  );

  // Attach click handlers after table is rendered
  setTimeout(() => {
    document.querySelectorAll(".viewOvenLog").forEach((link) => {
      link.addEventListener("click", async (e) => {
        e.preventDefault();
        const id = link.dataset.id;
        try {
          const res = await fetch(
            `/api/qaDispatcher.php?action=getOvenLog&id=${id}`
          );
          const data = await res.json();
          populateOvenLogModal(data); // fill view modal
        } catch (err) {
          console.error("Failed to fetch Oven Log:", err);
          showAlertMessage("Failed to load Oven log.", "showAlert", "danger");
        }
      });
    });

    document.querySelectorAll(".updateOvenLog").forEach((link) => {
      link.addEventListener("click", async (e) => {
        e.preventDefault();
        const id = link.dataset.id;
        try {
          const res = await fetch(
            `/api/qaDispatcher.php?action=getOvenLog&id=${id}`
          );
          const data = await res.json();
          populateUpdateOvenLogModal(data); // fill update modal
        } catch (err) {
          console.error("Failed to fetch Oven Log for update:", err);
          showAlertMessage(
            "Failed to load Oven log for update.",
            "showAlert",
            "danger"
          );
        }
      });
    });
  }, 0);

  return tableHtml;
}

export function buildLotChangeTable(lotChangeLogs) {
  const tableHtml = buildTable(
    lotChangeLogs,
    8,
    (row) => `
      <tr>
        <td>${row.prodLogID}</td>
        <td>${row.ProductID}</td>
        <td>${row.matName}</td>
        <td>${row.ChangeDate}</td>
        <td>${row.ChangeTime}</td>
        <td>${row.OldLot}</td>
        <td>${row.NewLot}</td>
        <td>
          <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLotChange"
             title="View Lot Change" data-id="${row.LotChangeID}"
             data-bs-toggle="modal" data-bs-target="#viewLotChangeModal">
             <i class="bi bi-eye-fill"></i>
          </a>
          <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLotChange"
             title="Update Lot Change" data-id="${row.LotChangeID}"
             data-bs-toggle="modal" data-bs-target="#updateLotChangeModal">
             <i class="bi bi-file-earmark-check"></i>
          </a>
        </td>
      </tr>`,
    "No records found"
  );

  // Attach click handlers after table is rendered
  setTimeout(() => {
    document.querySelectorAll(".viewLotChange").forEach((link) => {
      link.addEventListener("click", async (e) => {
        e.preventDefault();
        const id = link.dataset.id;
        try {
          const res = await fetch(
            `/api/qaDispatcher.php?action=getLotChangeLog&id=${id}`
          );
          const data = await res.json();
          populateLotChangeModal(data, materials); // fill view modal
        } catch (err) {
          console.error("Failed to fetch Lot Change Log:", err);
          showAlertMessage(
            "Failed to load Lot Change log.",
            "showAlert",
            "danger"
          );
        }
      });
    });

    document.querySelectorAll(".updateLotChange").forEach((link) => {
      link.addEventListener("click", async (e) => {
        e.preventDefault();
        const id = link.dataset.id;
        try {
          const res = await fetch(
            `/api/qaDispatcher.php?action=getLotChangeLog&id=${id}`
          );
          const data = await res.json();
          populateUpdateLotChangeModal(data); // fill update modal
        } catch (err) {
          console.error("Failed to fetch Lot Change Log for update:", err);
          showAlertMessage(
            "Failed to load Lot Change log for update.",
            "showAlert",
            "danger"
          );
        }
      });
    });
  }, 0);

  return tableHtml;
}

export function populateQaRejectModal(data) {
  if (!data) return;

  // Map backend JSON fields to modal inputs
  document.getElementById("v_qaPartName").value = data.productID || "";
  document.getElementById("v_logDate").value = data.prodDate || "";
  document.getElementById("v_qaRejects").value = data.rejects || "";
  document.getElementById("v_comment-text").value = data.comments || "";
}

export function populateOvenLogModal(data) {
  if (!data) return;

  document.getElementById("v_olPartName").value = data.productID || "";
  document.getElementById("v_olinOvenDate").value = data.inOvenDate || "";
  document.getElementById("v_olinOvenTime").value = data.inOvenTime || "";
  document.getElementById("v_olinOvenTemp").value = data.inOvenTemp || "";
  document.getElementById("v_olinOvenInitials").value =
    data.inOvenInitials || "";
  document.getElementById("v_olComments").value = data.ovenComments || "";

  // Out oven fields if included
  document.getElementById("v_olOutOvenDate").value = data.outOvenDate || "";
  document.getElementById("v_olOutOvenTime").value = data.outOvenTime || "";
  document.getElementById("v_olOutOvenTemp").value = data.outOvenTemp || "";
  document.getElementById("v_olOutOvenInitials").value =
    data.outOvenInitials || "";
}

export function populateUpdateOvenLogModal(data) {
  if (!data) return;

  document.getElementById("u_olOvenLogID").value = data.ovenLogID || "";
  document.getElementById("u_olPartName").value = data.productID || "";
  document.getElementById("u_olinOvenDate").value = data.inOvenDate || "";
  document.getElementById("u_olinOvenTime").value = data.inOvenTime || "";
  document.getElementById("u_olinOvenTemp").value = data.inOvenTemp || "";
  document.getElementById("u_olinOvenInitials").value =
    data.inOvenInitials || "";
  document.getElementById("u_olComments").value = data.ovenComments || "";

  // Out oven fields if included
  document.getElementById("u_olOutOvenDate").value = data.outOvenDate || "";
  document.getElementById("u_olOutOvenTime").value = data.outOvenTime || "";
  document.getElementById("u_olOutOvenTemp").value = data.outOvenTemp || "";
  document.getElementById("u_olOutOvenInitials").value =
    data.outOvenInitials || "";
}

export function populateLotChangeModal(data, materials) {
  if (!data) return;

  document.getElementById("v_lcPartName").value = data.ProductID || "";

  document.getElementById("v_lclotDate").value = data.ChangeDate || "";
  document.getElementById("v_lcLotTime").value = data.ChangeTime || "";
  document.getElementById("v_lcOldLot").value = data.OldLot || "";
  document.getElementById("v_lcNewLot").value = data.NewLot || "";
  document.getElementById("v_lcComments").value = data.LotComments || "";

  populateMaterialSelect("v_lcMatName", materials);

  document.getElementById("v_lcMatName").value = data.MaterialName || "";
}

export function populateUpdateLotChangeModal(data) {
  if (!data) return;

  document.getElementById("u_lclotDate").value = data.ChangeDate || "";
  document.getElementById("u_lcLotTime").value = data.ChangeTime || "";
  document.getElementById("u_lcOldLot").value = data.OldLot || "";
  document.getElementById("u_lcNewLot").value = data.NewLot || "";
  document.getElementById("u_lcComments").value = data.LotComments || "";

  populateMaterialSelect("u_lcMatName", materials);
  populateProductSelect("u_lcPartName", products);

  document.getElementById("u_lcPartName").value = data.ProductID || "";
  document.getElementById("u_lcMatName").value = data.MaterialName || "";
}
