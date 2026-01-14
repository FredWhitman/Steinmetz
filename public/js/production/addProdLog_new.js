// FILE: /js/production/addProdLog_new.js

import {
  fetchProductList,
  fetchMaterialList,
  checkIfRunExists,
  checkIfLogExists,
  fetchPreviousMatLogs,
  postProductionLog,
} from "./productionApiClient_new.js";

import {
  populateProductSelect,
  populateMaterialSelects,
  showLoader,
  hideLoader,
  clearAlert,
  showAlertMessage,
  fillDailyUsage,
  fillPercentage,
  updateBlenderTotal,
} from "./productionUiManager_new.js";

import { handleFormSubmit } from "./productionMain_new.js";

// "1" = start, "0" = in-progress, "2" = end
let runMode = null;

// --------------------------------------------------
// Init: load products + materials
// --------------------------------------------------

async function init() {
  try {
    showLoader();
    const [products, materials] = await Promise.all([
      fetchProductList(),
      fetchMaterialList(),
    ]);

    if (!Array.isArray(products) || !Array.isArray(materials)) {
      console.error("Product or Material list failed to load properly.", {
        products,
        materials,
      });
      showAlertMessage(
        "Failed to load products or materials.",
        "showAlert",
        "danger"
      );
      return;
    }

    const selectEl = document.getElementById("partName");
    populateProductSelect(selectEl, products);
    populateMaterialSelects(materials);
  } catch (error) {
    console.error("Error initializing addProdLog page:", error);
    showAlertMessage(
      "Error loading initial data for production log.",
      "showAlert",
      "danger"
    );
  } finally {
    hideLoader();
  }
}

// --------------------------------------------------
// Run status radio handling
// --------------------------------------------------

function onRadioChange(e) {
  runMode = e.target.value;
  validateRunAndLog();
}

// --------------------------------------------------
// Hopper blur: recalc + fetch previous if needed
// --------------------------------------------------

async function onHopperBlur() {
  clearAlert("showAlert");
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
      if (!productID) {
        hideLoader();
        return;
      }

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
      console.error("Error fetching previous material usage:", err);
      showAlertMessage(
        "Failed to fetch previous material usage.",
        "showAlert",
        "danger"
      );
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

// --------------------------------------------------
// Run + Log validation
// --------------------------------------------------

async function validateRunAndLog() {
  clearAlert("showAlert");
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
    console.error("Run/log validation failed:", err);
    showAlertMessage("Validation failed. Please try again.", "showAlert");
  }
}

// --------------------------------------------------
// Helper: compute usage deltas & percentages
// --------------------------------------------------

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

// --------------------------------------------------
// Payload builder for handleFormSubmit
// --------------------------------------------------

function buildProdLogPayload(d) {
  return {
    action: "addLog",
    prodData: {
      productID: d.get("partName"),
      prodDate: d.get("logDate"),
      runStatus: runMode,
      prevProdLogID: "0",
      runLogID: "0",
      matLogID: "0",
      tempLogID: "0",
      pressCounter: d.get("pressCounter"),
      startUpRejects: d.get("startUpRejects"),
      qaRejects: d.get("qaRejects") || "0",
      purgeLbs: d.get("purgeLbs") || "0",
      maxMeltPressure: d.get("maxMeltPress") || "0",
      comments: d.get("commentText"),
    },

    materialData: {
      prodLogID: "0",
      mat1: d.get("Mat1Name"),
      matUsed1: d.get("hop1Lbs"),
      matDailyUsed1: d.get("dHop1"),
      mat2: d.get("Mat2Name"),
      matUsed2: d.get("hop2Lbs"),
      matDailyUsed2: d.get("dHop2"),
      mat3: d.get("Mat3Name"),
      matUsed3: d.get("hop3Lbs"),
      matDailyUsed3: d.get("dHop3"),
      mat4: d.get("Mat4Name"),
      matUsed4: d.get("hop4Lbs"),
      matDailyUsed4: d.get("dHop4"),
    },

    tempData: {
      prodLogID: "0",
      bigDryerTemp: d.get("bigDryerTemp"),
      bigDryerDew: d.get("bigDryerDew"),
      pressDryerTemp: d.get("pressDryerTemp"),
      pressDryerDew: d.get("pressDryerDew"),
      t1: d.get("t1"),
      t2: d.get("t2"),
      t3: d.get("t3"),
      t4: d.get("t4"),
      m1: d.get("m1"),
      m2: d.get("m2"),
      m3: d.get("m3"),
      m4: d.get("m4"),
      m5: d.get("m5"),
      m6: d.get("m6"),
      m7: d.get("m7"),
      chillerTemp: d.get("chiller"),
      moldTemp: d.get("tcuTemp"),
      z1: d.get("z1"),
      z9: d.get("z9"),
    },
  };
}

// --------------------------------------------------
// DOMContentLoaded wiring
// --------------------------------------------------

document.addEventListener("DOMContentLoaded", () => {
  init();

  // Run mode radio changes
  document.querySelectorAll('input[name="prodRun"]').forEach((radio) => {
    radio.addEventListener("change", onRadioChange);
  });

  // Run/log validation on product/date change
  const prodSelect = document.getElementById("partName");
  const dateInput = document.getElementById("logDate");

  if (prodSelect) prodSelect.addEventListener("change", validateRunAndLog);
  if (dateInput) dateInput.addEventListener("change", validateRunAndLog);

  // Hopper blur (only last hopper)
  const hop4 = document.getElementById("hop4Lbs");
  if (hop4) hop4.addEventListener("blur", onHopperBlur);

  // Form submit using shared handler
  handleFormSubmit(
    "add-productionLog-form",
    buildProdLogPayload,
    async (payload) => {
      // We wrap postProductionLog to keep redirect behavior in one place
      const result = await postProductionLog(payload);

      // Use server message if present
      if (result?.message) {
        showAlertMessage(result.message, "showAlert", "success");
        console.log(payload);
      }

      // Redirect after brief delay (same as original)
      setTimeout(() => {
        window.location.href = "../production.php";
      }, 2500);

      return result;
    },
    null // no modal to close on this page
  );
});
