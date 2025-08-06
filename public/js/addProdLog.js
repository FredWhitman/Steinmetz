//FILE /js/addProdLog.js

import {
  fetchProductList,
  fetchMaterialList,
  checkIfRunExists,
  checkIfLogExists,
  fetchPreviousMatLogs,
  postProductionLog,
} from "./productionApiClient.js";

import {
  populateProductSelect,
  populateMaterialSelects,
  showLoader,
  hideLoader,
  clearAlert,
  showAlertMessage,
} from "./productionUiManager.js";

// Initialize the product select dropdown
let runMode = null; // "1"=start, "0"=in-progress, "2"=end

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

// 2) Handle run-status radio changes
function onRadioChange(e) {
  runMode = e.target.value;
  validateRunAndLog();
}

// 3) After the last hopper blurs, recalc & fetch previous if needed
async function onHopperBlur() {
  clearAlert();
  updateBlenderTotal();

  // gather current hopper values
  const current = [1, 2, 3, 4].map(
    (i) => parseFloat(document.getElementById(`hop${i}Lbs`).value) || 0
  );
  let usage, percents;

  if (runMode === "1") {
    // Start: previous all zeros
    ({ usage, percents } = computeUsageAndPercents(current, [0, 0, 0, 0]));
  } else if (runMode === "0" || runMode === "2") {
    showLoader();
    try {
      const productID = document.getElementById("partName").value;
      const actionType = runMode === "0" ? "getLastLog" : "endRun";
      const prevData = await fetchPreviousMatLogs(productID, actionType);
      const previous = [
        parseFloat(prevData.matUsed1) || 0,
        parseFloat(prevData.matUsed2) || 0,
        parseFloat(prevData.matUsed3) || 0,
        parseFloat(prevData.matUsed4) || 0,
      ];
      ({ usage, percents } = computeUsageAndPercents(current, previous));
    } catch (err) {
      console.error(err);
      showAlertMessage("Failed to fetch previous material usage.");
      return;
    } finally {
      hideLoader();
    }
  } else {
    // no valid mode selected yet
    return;
  }

  fillDailyUsage(usage);
  fillPercentage(percents);
}
//check for production runs and existing logs
async function validateRunAndLog() {
  clearAlert();
  const productID = document.getElementById("partName").value;
  const logDate = document.getElementById("logDate").value;
  if (!productID || !logDate || !runMode) return;

  try {
    if (runMode === "1") {
      // START: block if run OR log exists
      const [runData, logData] = await Promise.all([
        checkIfRunExists(productID),
        checkIfLogExists(productID, logDate),
      ]);
      if (runData.exists) {
        showAlertMessage(
          "A production run for this product is already active. Please end it before starting a new one.",
          "showAlert",
          "danger"
        );
      } else if (logData.exists) {
        showAlertMessage(
          "A log already exists for this product and date. You cannot start a duplicate log.",
          "showAlert",
          "danger"
        );
      }
    } else if (runMode === "0" || runMode === "2") {
      // IN PROGRESS or END: require a run, then warn if log already exists
      const runData = await checkIfRunExists(productID);
      if (!runData.exists) {
        showAlertMessage(
          "No uncompleted production run found. Please start the run first.",
          "showAlert",
          "danger"
        );
      } else {
        const logData = await checkIfLogExists(productID, logDate);
        if (logData.exists) {
          showAlertMessage(
            "A log already exists for this product and date.",
            "showAlert",
            "danger"
          );
        }
      }
    }
  } catch (err) {
    console.error(err);
    showAlertMessage("Validation failed. Please try again.", "showAlert");
  }
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

// Helper: compute usage deltas & percentages
function computeUsageAndPercents(current, previous) {
  const usage = current.map((c, i) =>
    parseFloat((c - (previous[i] || 0)).toFixed(3))
  );
  const total = usage.reduce((sum, v) => sum + v, 0);
  const percents = usage.map((u) =>
    total ? parseFloat(((u / total) * 100).toFixed(2)) : 0
  );
  return { usage, percents };
}

document.addEventListener("DOMContentLoaded", () => {
  init();
  // Hook radio changes
  document.querySelectorAll('input[name="prodRun"]').forEach((radio) => {
    radio.addEventListener("change", onRadioChange);
  });

  const prodSelect = document.getElementById("partName");
  const dateInput = document.getElementById("logDate");

  prodSelect.addEventListener("change", validateRunAndLog);
  dateInput.addEventListener("change", validateRunAndLog);

  // Hook hopper blur (only needed on last hopper)
  const hop4 = document.getElementById("hop4Lbs");
  if (hop4 && onHopperBlur) hop4.addEventListener("blur", onHopperBlur);
});
