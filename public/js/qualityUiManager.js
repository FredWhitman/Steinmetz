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
    html = `<tr><td colspan = "5" class="text-center">No records found</td></tr>`;
  } else {
    qaRejectLogs.forEach((row) => {
      html += ` <tr data-id="${row.qaRejectID}">
                    <td>${row.prodDate}</td>
                    <td>${row.prodLog}</td>
                    <td>${row.productID}</td>
                    <td>${row.rejects}</td>
                    <td>
                      <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" title="view qa reject" data-bs-toggle="modal" data-bs-target="#viewQaRejectLog"><i class="bi bi-eye-fill"></a>
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
    html = `<tr><td colspan = "5" class="text-center">No records found</td></tr>`;
  } else {
    ovenLogs.forEach((row) => {
      html += ` <tr data-id="${row.ovenLogID}">
                <td>${row.productID}</td>
                <td>${row.inOvenDate}</td>
                <td>${row.inOvenTime}</td>
                <td>${row.inOvenInitials}</td>
                <td>${row.outOvenDate}</td>
                <td>${row.outOvenTime}</td>
                <td>${row.outOvenInitials}</td>
                <td>
                  <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" title="view ovenLog" data-bs-toggle="modal" data-bs-target="#viewOvenLogModal"><i class="bi bi-eye-fill"></a>
                  <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update ovenLog" data-bs-toggle="modal" data-bs-target="#updateOvenLogModal"><i class="bi bi-file-earmark-check"></a>
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
    html = `<tr><td colspan = "5" class="text-center">No records found</td></tr>`;
  } else {
    lotChangeLogs.forEach((row) => {
      html += ` <tr data-id="${row.LotChangeID}">
                  <td>${row.prodLogID}</td>
                  <td>${row.productID}</td>
                  <td>${row.MaterialName}</td>
                  <td>${row.ChangeDate}</td>
                  <td>${row.ChangeTime}</td>
                  <td>${row.OldLot}</td>
                  <td>${row.NewLot}</td>
                  <td>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" title="view ovenLog" data-bs-toggle="modal" data-bs-target="#viewLotChangeModal"><i class="bi bi-eye-fill"></a>
                    <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update ovenLog" data-bs-toggle="modal" data-bs-target="#updateLotChangeModal"><i class="bi bi-file-earmark-check"></a>
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
    const editLink = e.target.closest("a.editLink");
    const updateLink = e.target.closest("a.updateLink");

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
    if (viewLink) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const id = row ? row.getAttribute("data-id") : null;
      if (id && id.trim()) {
        import(".qualityApiClient.js").then(({ fetchAndFillViewForm }) => {
          fetchAndFillViewForm(id.trim(), table);
        });
      }
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
