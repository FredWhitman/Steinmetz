// FILE: /js/productionUiManager.js
//
//This will hold function for building tables for the production Landing page
const BASE_URL = "/api/prodDispatcher.php";
const showAlert = document.getElementById("showAlert");

const fieldMappings = {
  prodLogs: {
    productID: "vpartName",
    prodDate: "vlogDate",
    runStatus: "vprodRun",
    mat1: "vMat1Name",
    matUsed1: "vhop1Lbs",
    mat2: "vMat2Name",
    matUsed2: "vhop2Lbs",
    mat3: "vMat3Name",
    matUsed3: "vhop3Lbs",
    mat4: "vMat4Name",
    matUsed4: "vhop4Lbs",
    blenderTotals: "vBlenderTotals",
    bigDryerTemp: "vbigDryerTemp",
    bigDryerDew: "vbigDryerDew",
    pressDryerTemp: "vPressDryerTemp",
    pressDryerDew: "vPressDryerDew",
    chillerTemp: "vChiller",
    moldTemp: "vTCU",
    t1: "vT1",
    t2: "vT2",
    t3: "vT3",
    t4: "vT4",
    m1: "vM1",
    m2: "vM2",
    m3: "vM3",
    m4: "vM4",
    m5: "vM5",
    m6: "vM6",
    m7: "vM7",
    pressCounter: "vPressCounter",
    startUpRejects: "vPressRejects",
    Comments: "vcommentText",
  },
};

// Build HTML for the tables
export function buildProdLogsTable(prodLogs) {
  let html = "";
  prodLogs.forEach((row) => {
    html += `<tr data-id='${row.logID}'>
                <td>${row.productID}</td>
                <td>${row.prodDate}</td>
                <td>${row.pressCounter}</td>
                <td>${row.startUpRejects}</td>
                <td>${row.qaRejects}</td>
                <td>${row.purgeLbs}</td>
                <td>${row.runStatus}</td>
                <td>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" data-bs-toggle ="modal" role="button" data-bs-target="#viewProductionModal">View</a>
                  </td>
            </tr>`;
  });
  return html;
}
/*  
link to open new page log for table   
<a href="/viewProductionLog.php?logID=${row.logID}" target="_blank" class="btn btn-primary btn-sm rounded-pill py-0">View</a>

*/

// Attach a shared event listener for table rows
export function setupViewEventListener(elementId, table) {
  const trigger = document.getElementById(elementId);
  if (!trigger) {
    console.warn(`⚠️ No element found with id "${elementId}"`);
    return;
  }

  trigger.addEventListener("click", (e) => {
    const viewLink = e.target.closest("a.viewLink");
    if (viewLink) {
      e.preventDefault();
      e.stopPropagation();
      const row = e.target.closest("tr");
      const id = row ? row.getAttribute("data-id") : null;
      if (id && id.trim()) {
        // Dispatch to the API client to fill form
        fetchAndFillForm(id.trim(), table);
      }
    }
  });
}

// Function to render tables into the DOM
export function renderTables(prodLogs) {
  document.getElementById("last4wks").innerHTML = buildProdLogsTable(prodLogs);
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

async function fetchAndParseJSON(url) {
  const response = await fetch(url);
  const raw = await response.text();
  return JSON.parse(raw);
}

/*  This function takes data from controller->viewProdLog and fills fields
    on viewProdLogModal.
*/
function fillFormFields(data, mapping) {
  Object.entries(mapping).forEach(([dbKey, formID]) => {
    const element = document.getElementById(formID);
    if (element) {
      element.value = data[dbKey] ?? "";
    } else {
      console.warn(`⚠️ Element not found: ${formID}`);
    }
  });
}

async function fetchPreviousLog(previousLogID) {
  if (!previousLogID) return {};
  const url = `${BASE_URL}?viewProdLogs=1&id=${previousLogID}&table=prodLogs`;
  console.log("🔁 Fetching previous log:", url);
  try {
    const log = await fetchAndParseJSON(url);
    if (log?.error) console.error("Previous log fetch error:", log.error);
    return log;
  } catch (err) {
    console.error("Failed to fetch previous log:", err);
    return {};
  }
}

/*This function takes the current hop1-4 weights, and the previous log in the production 
run subtracts previous values from current to get daily usage, calculates the precentages 
for each used hopper and returns them in three arrays.
*/
function calculateDailyMetrics(current, previous = {}) {
  const parseOrZero = (v) => parseFloat(v) || 0;

  const usage = {
    mat1: parseOrZero(current.matUsed1) - parseOrZero(previous.matUsed1),
    mat2: parseOrZero(current.matUsed2) - parseOrZero(previous.matUsed2),
    mat3: parseOrZero(current.matUsed3) - parseOrZero(previous.matUsed3),
    mat4: parseOrZero(current.matUsed4) - parseOrZero(previous.matUsed4),
  };

  const currentTotal =
    parseFloat(current.matUsed1) +
    parseFloat(current.matUsed2) +
    parseFloat(current.matUsed3) +
    parseFloat(current.matUsed4);

  const el = document.getElementById("vBlenderTotals");
  if (el) el.value = currentTotal.toFixed(3);

  const totalMat = usage.mat1 + usage.mat2 + usage.mat3 + usage.mat4;

  const percentages = {
    mat1: totalMat ? ((usage.mat1 / totalMat) * 100).toFixed(2) : "0",
    mat2: totalMat ? ((usage.mat2 / totalMat) * 100).toFixed(2) : "0",
    mat3: totalMat ? ((usage.mat3 / totalMat) * 100).toFixed(2) : "0",
    mat4: totalMat ? ((usage.mat4 / totalMat) * 100).toFixed(2) : "0",
  };

  return {
    usage,
    percentages,
    totals: {
      dailyMatTotal: totalMat,
      percentTotal: [
        parseFloat(percentages.mat1),
        parseFloat(percentages.mat2),
        parseFloat(percentages.mat3),
        parseFloat(percentages.mat4),
      ].reduce((sum, p) => sum + p, 0),
    },
  };
}

/* this function uses the passed array to set the values of the daily usage hoppers */
function fillDailyUsageFields(usage) {
  document.getElementById("vdHop1").value = usage.mat1.toFixed(3);
  document.getElementById("vdHop2").value = usage.mat2.toFixed(3);
  document.getElementById("vdHop3").value = usage.mat3.toFixed(3);
  document.getElementById("vdHop4").value = usage.mat4.toFixed(3);
}
/*this function uses the passed array to set the values of the daily usage percentages   */
function fillPercentageFields(p) {
  document.getElementById("vdHop1p").value = `${p.mat1}%`;
  document.getElementById("vdHop2p").value = `${p.mat2}%`;
  document.getElementById("vdHop3p").value = `${p.mat3}%`;
  document.getElementById("vdHop4p").value = `${p.mat4}%`;
}

export async function fetchAndFillForm(id, table) {
  const url = `${BASE_URL}?view${capitalize(table)}=1&id=${id}&table=${table}`;
  showLoader();
  console.log("📨 FetchFillForm URL:", url);

  // Let the browser paint the loader before fetching
  await new Promise((r) =>
    requestAnimationFrame(() => requestAnimationFrame(r))
  );

  try {
    const currentLog = await fetchAndParseJSON(url);
    if (!currentLog || currentLog.error) {
      console.error("❌ Current log error:", currentLog?.error);
      return;
    }

    fillFormFields(currentLog, fieldMappings[table]);

    if (table === "prodLogs") {
      const previousLog = await fetchPreviousLog(currentLog.prevProdLogID);
      const metrics = calculateDailyMetrics(currentLog, previousLog);

      fillDailyUsageFields(metrics.usage);
      fillPercentageFields(metrics.percentages);

      const ele = document.getElementById("vdTotalp");
      if (ele) ele.value = metrics.totals.percentTotal;

      const dailyTotalUsage = metrics.totals.dailyMatTotal;

      document.getElementById("vdTotal").value = dailyTotalUsage.toFixed(3);
    }
  } catch (error) {
    console.error("🔥 fetchAndFillForm failed:", error);
  } finally {
    hideLoader();
  }
}
//
// END OF ViewProdLogModal CODE

// —————————————————————————————
// Loader & Alert Utilities
// —————————————————————————————
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

export function populateProductSelect(products) {
  const sel = document.getElementById("partName");
  populateSelect(sel, products, {
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

// —————————————————————————————
// Form Reset & Validation
// —————————————————————————————
export function resetAddModalForm() {
  const form = document.getElementById("add-productionLog-form");
  if (!form) return;
  form.reset();
  form.classList.remove("was-validated");
  clearAlert("showAlert");

  // Clear all readonly calculation fields
  [
    "dHop1",
    "dHop2",
    "dHop3",
    "dHop4",
    "dTotal",
    "dTotalp",
    "BlenderTotals",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
}

// —————————————————————————————
// Field Filling Helpers
// —————————————————————————————
export function populateUsageAndPercents(log, fieldMap) {
  Object.entries(fieldMap).forEach(([key, selector]) => {
    const el = document.querySelector(selector);
    if (el) el.value = log[key] ?? "";
  });
}

export function fillDailyUsage(usage) {
  // usage: [val1, val2, val3?, val4?]
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
  // percentages: [p1, p2, p3?, p4?]
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

// —————————————————————————————
// Totals Calculation (optional helper)
// —————————————————————————————
export function updateBlenderTotal() {
  const hops = [1, 2, 3, 4].map(
    (i) => parseFloat(document.getElementById(`hop${i}Lbs`).value) || 0
  );
  const total = hops.reduce((a, b) => a + b, 0);
  const el = document.getElementById("BlenderTotals");
  if (el) el.value = total.toFixed(3);
}

// —————————————————————————————
// Export a master init for the Add Modal
// —————————————————————————————
export function initAddModalUI({ onRadioChange, onHopperBlur }) {
  const modalEl = document.getElementById("addProductionModal");
  if (!modalEl) return;

  // Reset form on each show
  modalEl.addEventListener("show.bs.modal", resetAddModalForm);

  // Hook radio changes
  document.querySelectorAll('input[name="prodRun"]').forEach((radio) => {
    radio.addEventListener("change", onRadioChange);
  });

  // Hook hopper blur (only needed on last hopper)
  const hop4 = document.getElementById("hop4Lbs");
  if (hop4 && onHopperBlur) hop4.addEventListener("blur", onHopperBlur);
}

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
    '<span class ="fw-bold">Comments: </span>' + data.comments;
  document.getElementById("pl_barrelZones").innerHTML =
    '<span class="fw-bold"> Barrel Temps Z1: </span>' +
    (data.z1 || "Z1: 000 °F") +
    ' °F <span class="fw-bold"> Z9: </span>' +
    (data.z9 || "Z9: 000 °F") +
    " °F";
}
