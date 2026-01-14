// FILE: /js/production/productionUiManager_new.js

// This file is UI‑only: DOM, rendering, alerts, selects, helpers.
// No fetch calls, no business logic.

// --------------------------------------------------
// Table builders
// --------------------------------------------------

export function buildProdLogsTable(prodLogs) {
  let html = "";
  prodLogs.forEach((row) => {
    html += `<tr data-id='${row.logID}'>
      <td>${row.productID}</td>
      <td>${row.prodDate}</td>
      <td>${row.pressCounter}</td>
      <td>${row.startUpRejects}</td>
      <td>${row.qaRejects}</td>
      <td>${row.purgeLbs} lbs</td>
      <td>${row.runStatus}</td>
      <td>
        <a href="/forms/viewProductionLog.php?productID=${row.productID}&prodDate=${row.prodDate}" 
           target="_blank" 
           class="btn btn-primary btn-sm rounded-pill py-0">
          View
        </a>
      </td>
    </tr>`;
  });
  return html;
}

export function buildRunsNotCompleteTable(prodRunLogs) {
  let html = "";

  if (!prodRunLogs.length) {
    html = `<tr>
      <td colspan="12" class="text-center text-muted">
        There are currently no open production runs.
      </td>
    </tr>`;
  } else {
    prodRunLogs.forEach((row) => {
      html += `<tr data-id='${row.logID}'>
        <td>${row.productID}</td>
        <td>${row.startDate}</td>
        <td>${row.endDate}</td>
        <td>${row.mat1Lbs}</td>
        <td>${row.mat2Lbs}</td>
        <td>${row.mat3Lbs}</td>
        <td>${row.mat4Lbs}</td>
        <td>${row.partsProduced}</td>
        <td>${row.startupRejects}</td>
        <td>${row.qaRejects}</td>
        <td>${row.purgeLbs}</td>
        <td>
          <a href="/forms/viewRunLogs.php?runID=${row.runID ?? row.logID}"
             target="_blank"
             class="btn btn-primary btn-sm rounded-pill py-0">
             View
          </a>
        </td>
      </tr>`;
    });
  }

  return html;
}

export function buildRunsCompleteTable(prodRunLogs) {
  let html = "";

  if (!prodRunLogs.length) {
    html = `<tr>
      <td colspan="12" class="text-center text-muted">
        There are currently no finished production runs.
      </td>
    </tr>`;
  } else {
    prodRunLogs.forEach((row) => {
      html += `<tr data-id='${row.logID}'>
        <td>${row.productID}</td>
        <td>${row.startDate}</td>
        <td>${row.endDate}</td>
        <td>${row.mat1Lbs}</td>
        <td>${row.mat2Lbs}</td>
        <td>${row.mat3Lbs}</td>
        <td>${row.mat4Lbs}</td>
        <td>${row.partsProduced}</td>
        <td>${row.startupRejects}</td>
        <td>${row.qaRejects}</td>
        <td>${row.purgeLbs}</td>
        <td>
          <a href="/forms/viewRunLogs.php?runID=${row.runID ?? row.logID}"
             target="_blank"
             class="btn btn-primary btn-sm rounded-pill py-0">
             View
          </a>
        </td>
      </tr>`;
    });
  }

  return html;
}

export function buildRunProdLogsTable(runProdLogs) {
  let html = "";
  runProdLogs.forEach((row) => {
    html += `<tr data-id='${row.logID}'>
      <td>${row.productID}</td>
      <td>${row.prodDate}</td>
      <td>${row.pressCounter}</td>
      <td>${row.startUpRejects}</td>
      <td>${row.qaRejects}</td>
      <td>${row.purgeLbs} lbs</td>
      <td>${row.runStatus}</td>
    </tr>`;
  });
  return html;
}

// --------------------------------------------------
// Table renderers
// --------------------------------------------------

export function renderTables(prodLogs) {
  const tbody = document.getElementById("last4wks");
  if (tbody) tbody.innerHTML = buildProdLogsTable(prodLogs);
}

export function renderRunsNotCompleteTable(prodRunLogs) {
  const tbody = document.getElementById("runsNotComplete");
  if (tbody) tbody.innerHTML = buildRunsNotCompleteTable(prodRunLogs);
}

export function renderRunsCompleteTable(prodRunLogs) {
  const tbody = document.getElementById("runsFinished");
  if (tbody) tbody.innerHTML = buildRunsCompleteTable(prodRunLogs);
}

export function renderRunProdLogsTable(runProdLogs) {
  const tbody = document.getElementById("runProdLogs");
  if (tbody) tbody.innerHTML = buildRunProdLogsTable(runProdLogs);
}

// --------------------------------------------------
// Loader & Alerts
// --------------------------------------------------

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
  containerID = "alertContainer",
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

// --------------------------------------------------
// Select population
// --------------------------------------------------

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
    console.warn("populateProductSelect: select element not found");
    return;
  }

  populateSelect(selectEl, products, {
    valueKey: "productID",
    labelKey: "partName",
  });
}

export function populateMaterialSelects(materials) {
  [1, 2, 3, 4].forEach((i) => {
    const sel = document.getElementById(`Mat${i}Name`);
    populateSelect(sel, materials, {
      valueKey: "matPartNumber",
      labelKey: "matName",
    });
  });
}

// --------------------------------------------------
// Usage & percent helpers (for addProdLog_new.js)
// --------------------------------------------------

export function fillDailyUsage(usage) {
  usage.forEach((v, idx) => {
    const el = document.getElementById(`dHop${idx + 1}`);
    if (el) el.value = v.toFixed(3);
  });

  const hops = [1, 2, 3, 4].map(
    (i) => parseFloat(document.getElementById(`dHop${i}`).value) || 0
  );
  const total = hops.reduce((a, b) => a + b, 0);
  const ele = document.getElementById("dTotal");
  if (ele) ele.value = total.toFixed(3);
}

export function fillPercentage(percentages) {
  percentages.forEach((p, idx) => {
    const el = document.getElementById(`dHop${idx + 1}p`);
    if (el) el.value = p.toFixed(2);
  });

  const percents = [1, 2, 3, 4].map(
    (i) => parseFloat(document.getElementById(`dHop${i}p`).value) || 0
  );
  const total = percents.reduce((a, b) => a + b, 0);
  const ele = document.getElementById("dTotalp");
  if (ele) ele.value = total.toFixed(2);
}

export function updateBlenderTotal() {
  const hops = [1, 2, 3, 4].map(
    (i) => parseFloat(document.getElementById(`hop${i}Lbs`).value) || 0
  );
  const total = hops.reduce((a, b) => a + b, 0);
  const el = document.getElementById("BlenderTotals");
  if (el) el.value = total.toFixed(3);
}

// --------------------------------------------------
// Usage & percent helpers (for viewProdLog_new.js)
// --------------------------------------------------

export function fillViewLogPage(data) {
  console.log("Filling view log page with data:", data);

  document.getElementById("pl_PartName").innerHTML =
    '<span class="fw-bold">Part Name: </span>' + data.productID;
  document.getElementById("pl_LogDate").innerHTML =
    '<span class="fw-bold"> Date: </span>' + data.prodDate;
  document.getElementById("pl_RunStatus").innerHTML =
    '<span class="fw-bold"> Produciton Status: </span>' + data.runStatus;
  document.getElementById("pl_Hop1Material").innerHTML =
    '<span class="fw-bold"> Hop 1 Mat: </span>' + data.mat1;
  document.getElementById("pl_Hop2Material").innerHTML =
    '<span class="fw-bold"> Hop 2 Mat: </span>' + data.mat2;
  document.getElementById("pl_Hop3Material").innerHTML =
    '<span class="fw-bold"> Hop 3 Mat: </span>' + data.mat3;
  document.getElementById("pl_Hop4Material").innerHTML =
    '<span class="fw-bold"> Hop 4 Mat: </span>' + data.mat4;
  document.getElementById("pl_Hop1Weight").textContent = data.matUsed1;
  document.getElementById("pl_Hop2Weight").textContent = data.matUsed2;
  document.getElementById("pl_Hop3Weight").textContent = data.matUsed3;
  document.getElementById("pl_Hop4Weight").textContent = data.matUsed4;

  document.getElementById("pl_Hop1Daily").textContent = data.matDailyUsed1;
  document.getElementById("pl_Hop2Daily").textContent = data.matDailyUsed2;
  document.getElementById("pl_Hop3Daily").textContent = data.matDailyUsed3;
  document.getElementById("pl_Hop4Daily").textContent = data.matDailyUsed4;

  const blendertotal =
    parseFloat(data.matUsed1) +
      parseFloat(data.matUsed2) +
      parseFloat(data.matUsed3) +
      parseFloat(data.matUsed4) || 0;
  const dailyTotal =
    parseFloat(data.matDailyUsed1) +
      parseFloat(data.matDailyUsed2) +
      parseFloat(data.matDailyUsed3) +
      parseFloat(data.matDailyUsed4) || 0;

  const perHop1 = (parseFloat(data.matUsed1) / blendertotal) * 100 || 0;
  const perHop2 = (parseFloat(data.matUsed2) / blendertotal) * 100 || 0;
  const perHop3 = (parseFloat(data.matUsed3) / blendertotal) * 100 || 0;
  const perHop4 = (parseFloat(data.matUsed4) / blendertotal) * 100 || 0;
  const perTotal = perHop1 + perHop2 + perHop3 + perHop4 || 0;

  document.getElementById("pl_totalDaily").textContent = dailyTotal.toFixed(3);
  document.getElementById("pl_totalMatWeight").textContent =
    blendertotal.toFixed(3);
  document.getElementById("pl_BigDryerTemp").innerHTML =
    '<span class="fw-bold">Big Dryer Temp: </span>' + data.bigDryerTemp + " °F";
  document.getElementById("pl_BigDryerDew").innerHTML =
    '<span class="fw-bold">Big Dryer Dew Point: </span>' + data.bigDryerDew;
  document.getElementById("pl_PressDryerTemp").innerHTML =
    '<span class="fw-bold">Press Dryer Temp: </span>' +
    data.pressDryerTemp +
    " °F";
  document.getElementById("pl_PressDryerDew").innerHTML =
    '<span class="fw-bold">Press Dryer Dew Point: </span>' + data.pressDryerDew;
  document.getElementById("pl_ChillerTemp").innerHTML =
    '<span class="fw-bold">Chiller Temp: </span>' + data.chillerTemp + " °F";
  document.getElementById("pl_MoldTemp").innerHTML =
    '<span class="fw-bold">Mold Temp: </span>' + data.moldTemp + " °F";
  document.getElementById("t1").innerHTML =
    '<span class="fw-bold">T1: </span>' + data.t1 + " °F";
  document.getElementById("t2").innerHTML =
    '<span class="fw-bold">T2: </span>' + data.t2 + " °F";
  document.getElementById("t3").innerHTML =
    '<span class="fw-bold">T3: </span>' + data.t3 + " °F";
  document.getElementById("t4").innerHTML =
    '<span class="fw-bold">T4: </span>' + data.t4 + " °F";
  document.getElementById("m1").innerHTML =
    '<span class="fw-bold">M1: </span>' + data.m1 + " °F";
  document.getElementById("m2").innerHTML =
    '<span class="fw-bold">M2: </span>' + data.m2 + " °F";
  document.getElementById("m3").innerHTML =
    '<span class="fw-bold">M3: </span>' + data.m3 + " °F";
  document.getElementById("m4").innerHTML =
    '<span class="fw-bold">M4: </span>' + data.m4 + " °F";
  document.getElementById("m5").innerHTML =
    '<span class="fw-bold">M5: </span>' + data.m5 + " °F";
  document.getElementById("m6").innerHTML =
    '<span class="fw-bold">M6: </span>' + data.m6 + " °F";
  document.getElementById("m7").innerHTML =
    '<span class="fw-bold">M7: </span>' + data.m7 + " °F";
  document.getElementById("pl_Hop1Percent").innerHTML =
    perHop1.toFixed(2) + " %";
  document.getElementById("pl_Hop2Percent").innerHTML =
    perHop2.toFixed(2) + " %";
  document.getElementById("pl_Hop3Percent").innerHTML =
    perHop3.toFixed(2) + " %";
  document.getElementById("pl_Hop4Percent").innerHTML =
    perHop4.toFixed(2) + " %";
  document.getElementById("pl_totalPercent").innerHTML =
    perTotal.toFixed(0) + " %";
  document.getElementById("pl_PressCounter").innerHTML =
    '<span class ="fw-bold">parts produced: </span>' + data.pressCounter;
  document.getElementById("pl_startRejects").innerHTML =
    '<span class ="fw-bold">Startup Rejects: </span>' + data.startUpRejects;
  document.getElementById("pl_qaRejects").innerHTML =
    '<span class ="fw-bold">QA Rejects: </span>' + data.qaRejects;
  document.getElementById("pl_purge").innerHTML =
    '<span class ="fw-bold">Purge Lbs: </span>' + data.purgeLbs;
  document.getElementById("pl_comments").innerHTML =
    '<span class ="fw-bold">Comments: </span>' + data.Comments;
  document.getElementById("pl_barrelZones").innerHTML =
    '<span class="fw-bold"> Barrel Temps Z1: </span>' +
    (data.z1 || "Z1: 000 °F") +
    ' °F <span class="fw-bold"> Z9: </span>' +
    (data.z9 || "Z9: 000 °F") +
    " °F";
  document.getElementById("pl_maxMelt").innerHTML =
    '<span class="fw-bold">Max Melt Pressure: </span>' +
    data.maxMeltPressure +
    " psi";
}

// --------------------------------------------------
// Generic table row click helper (optional)
// --------------------------------------------------

export function setupViewEventListener(elementId, onRowClick) {
  const trigger = document.getElementById(elementId);
  if (!trigger) {
    console.warn(`setupViewEventListener: No element with id "${elementId}"`);
    return;
  }

  trigger.addEventListener("click", (e) => {
    const row = e.target.closest("tr[data-id]");
    if (!row) return;
    const id = row.getAttribute("data-id");
    if (id && onRowClick) onRowClick(id.trim());
  });
}
