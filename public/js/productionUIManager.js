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
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" data-bs-toggle ="modal" data-bs-target="#viewProductionModal">View</a>
                </td>
            </tr>`;
  });
  return html;
}

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

function fillDailyUsageFields(usage) {
  document.getElementById("vdHop1").value = usage.mat1.toFixed(3);
  document.getElementById("vdHop2").value = usage.mat2.toFixed(3);
  document.getElementById("vdHop3").value = usage.mat3.toFixed(3);
  document.getElementById("vdHop4").value = usage.mat4.toFixed(3);
}

function fillPercentageFields(p) {
  document.getElementById("vdHop1p").value = `${p.mat1}%`;
  document.getElementById("vdHop2p").value = `${p.mat2}%`;
  document.getElementById("vdHop3p").value = `${p.mat3}%`;
  document.getElementById("vdHop4p").value = `${p.mat4}%`;
}

export async function fetchAndFillForm(id, table) {
  const url = `${BASE_URL}?view${capitalize(table)}=1&id=${id}&table=${table}`;
  console.log("📨 FetchFillForm URL:", url);

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
      const dailyTotalUsage =
        metrics.usage.mat1 +
        metrics.usage.mat2 +
        metrics.usage.mat3 +
        metrics.usage.mat4;
      const el = document.getElementById("vdTotal");
      if (el) el.value = dailyTotalUsage.toFixed(3);
    }
  } catch (error) {
    console.error("🔥 fetchAndFillForm failed:", error);
  }
}

/* export function calculateDailyUsage(currentHoppers, previousValues) {
  console.log("🔄 Calculating daily usage...");

  const usageElements = ["dHop1", "dHop2", "dHop3", "dHop4"].map((id) =>
    document.getElementById(id)
  );
  const percentageElements = ["dHop1p", "dHop2p", "dHop3p", "dHop4p"].map(
    (id) => document.getElementById(id)
  );
  const dTotal = document.getElementById("dTotal");
  const dTotalp = document.getElementById("dTotalp");

  let usageTotals = currentHoppers.map((hop, i) => {
    const current = parseFloat(hop.value) || 0;
    const previous = parseFloat(previousValues[i]) || 0;
    const delta = current - previous;
    usageElements[i].value = delta.toFixed(3);
    return delta;
  });

  const total = usageTotals.reduce((sum, val) => sum + val, 0);
  dTotal.value = total.toFixed(3);

  let percentages = usageTotals.map((val) =>
    total ? parseFloat(((val / total) * 100).toFixed(2)) : 0
  );

  percentages.forEach((pct, i) => (percentageElements[i].value = pct));
  dTotalp.value = percentages.reduce((sum, val) => sum + val, 0).toFixed(2);
}
 */
