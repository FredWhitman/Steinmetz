// Loader functions
export function showLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.remove("d-none");
}

export function hideLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.add("d-none");
}

//Build HTML for tables
export function buildQaRejectTable(qaRejectLogs) {
  let html = "";
  if (!qaRejectLogs.length) {
    html = `<tr><td colspan = "6" class="text-center">No records found</td></tr>`;
  } else {
    qaRejectLogs.forEach((row) => {
      html += ` <tr data-id="${row.qaRejectLogID}">
                    <td>${row.prodDate}</td>
                    <td>${row.prodLogID}</td>
                    <td>${row.productID}</td>
                    <td>${row.rejects}</td>
                    <td>${row.comments}</td>
                    <td>
                      <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" title="view qa reject" data-bs-toggle="modal" data-bs-target="#viewQaRejectsModal"><i class="bi bi-eye-fill"></i></a>
                    </td>
                </tr>`;
    });
  }

  return html;
}

//build table for oven logs
export function buildOvenLogsTable(ovenLogs) {
  let html = "";

  if (!ovenLogs.length) {
    html = `<tr><td colspan = "10" class="text-center">No records found</td></tr>`;
  } else {
    ovenLogs.forEach((row) => {
      html += ` <tr data-id="${row.ovenLogID}">
                <td>${row.productID}</td>
                <td>${row.inOvenDate}</td>
                <td>${row.inOvenTime}</td>
                <td>${row.inOvenTemp} °</td>
                <td>${row.inOvenInitials}</td>
                <td>${row.outOvenDate}</td>
                <td>${row.outOvenTime}</td>
                <td>${row.outOvenTemp} °</td>
                <td>${row.outOvenInitials}</td>
                <td>
                  <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" title="view ovenLog" data-bs-toggle="modal" data-bs-target="#viewOvenLogModal">
                    <i class="bi bi-eye-fill"></i>
                  </a>
                  <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update ovenLog" data-bs-toggle="modal" data-bs-target="#updateOvenLogModal">
                    <i class="bi bi-file-earmark-check"></i>
                  </a>
                </td>
              </tr>`;
    });
  }
  return html;
}

//build table for lot changes
export function buildLotChangeTable(lotChangeLogs) {
  let html = "";

  if (!lotChangeLogs.length) {
    html = `<tr><td colspan = "8" class="text-center">No records found</td></tr>`;
  } else {
    lotChangeLogs.forEach((row) => {
      html += ` <tr data-id="${row.LotChangeID}">
                  <td>${row.prodLogID}</td>
                  <td>${row.ProductID}</td>
                  <td>${row.matName}</td>
                  <td>${row.ChangeDate}</td>
                  <td>${row.ChangeTime}</td>
                  <td>${row.OldLot}</td>
                  <td>${row.NewLot}</td>
                  <td>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" title="view lotchange" data-bs-toggle="modal" data-bs-target="#viewLotChangeModal">
                      <i class="bi bi-eye-fill"></i>
                    </a>
                    <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update ltochange" data-bs-toggle="modal" data-bs-target="#updateLotChangeModal">
                      <i class="bi bi-file-earmark-check"></i>
                    </a>
                  </td>
               </tr>`;
    });
  }
  return html;
}

// Attach a shared event listener for table rows
export function setupEventListener(elementId, table) {
  document.getElementById(elementId).addEventListener("click", (e) => {
    const viewLink = e.target.closest("a.viewLink");
    const row = viewLink?.closest("tr");
    const id = row?.getAttribute("data-id");

    const editLink = e.target.closest("a.editLink");
    const updateLink = e.target.closest("a.updateLink");

    console.log("target:", e.target);
    console.log("anchor:", e.target.closest("a.viewLink"));
    console.log("row:", e.target.closest("tr"));

    if (editLink) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const id = row ? row.getAttribute("data-id") : null;
      if (id && id.trim()) {
        // Dispatch to the API client to fill form
        import("./qualityApiClient.js").then(({ fetchAndFillForm }) => {
          fetchAndFillForm(id.trim(), table);
        });
      }
    }
    if (updateLink) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const id = row ? row.getAttribute("data-id") : null;
      if (id && id.trim()) {
        import("./qualityApiClient.js").then(({ fetchAndFillUpdateForm }) => {
          fetchAndFillUpdateForm(id.trim(), table);
        });
      }
    }
    if (viewLink && id) {
      e.preventDefault();

      import("./qualityApiClient.js").then(({ fetchAndFillViewForm }) => {
        fetchAndFillViewForm(id.trim(), table);
      });
    }
  });
}

// take passed arrays and render tables
export function renderTables({ qaRejectLogs, ovenLogs, lotChangeLogs }) {
  document.getElementById("qaRejectLogs").innerHTML =
    buildQaRejectTable(qaRejectLogs);
  document.getElementById("ovenLogs").innerHTML = buildOvenLogsTable(ovenLogs);
  document.getElementById("lotChangeLogs").innerHTML =
    buildLotChangeTable(lotChangeLogs);
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

//Clears the showAlert message above the tables
export function clearAlert(containerID = "showAlert") {
  const c = document.getElementById(containerID);
  if (c) c.innerHTML = "";
}

// —————————————————————————————
// Select Population
// —————————————————————————————
function populateSelect(
  selectEl,
  items,
  { valueKey, labelKey, includeEmpty = true }
) {
  if (!selectEl) return;
  selectEl.innerHTML = includeEmpty
    ? `<option value="">– Select –</option>`
    : "";
  items.forEach((item) => {
    const opt = document.createElement("option");
    opt.value = item[valueKey];
    opt.textContent = item[labelKey];
    selectEl.append(opt);
  });
}

export function populateProductSelect(selectEl, products) {
  if (!selectEl) {
    console.warn("🚨 Select element not found!");
    return;
  }

  console.log("qualityUiManager.js->populateProductSelect(products) called");

  populateSelect(selectEl, products, {
    valueKey: "productID",
    labelKey: "partName",
  });
}

export function populateMaterialSelect(materials) {
  const sel = document.getElementById("lc_MatName");
  populateSelect(sel, materials, {
    valueKey: "matPartNumber",
    labelKey: "matName",
  });
}
