//FILE /js/production/addProdLog.js

import {
  fetchProdLogs,
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
  fillDailyUsage,
  fillPercentage,
  updateBlenderTotal,
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
    const selectEl = document.getElementById("partName");
    populateProductSelect(selectEl, products);
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

  console.log("Hopper blur event triggered");

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

      console.log("Usage:", usage);
      console.log("Percents:", percents);
    } catch (err) {
      console.error(err);
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

// 4) Form submit → validate, POST, finalize (if needed), and refresh
function wireFormSubmission() {
  const form = document.getElementById("add-productionLog-form");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearAlert();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    showLoader();
    const data = new FormData(form);
    // Build payload
    const payload = {
      action: "addLog",
      prodData: {
        productID: data.get("partName"),
        prodDate: data.get("logDate"),
        runStatus: runMode,
        prevProdLogID: "0",
        runLogID: "0",
        matLogID: "0",
        tempLogID: "0",
        pressCounter: data.get("pressCounter"),
        startUpRejects: data.get("startUpRejects"),
        qaRejects: data.get("qaRejects") || "0",
        purgeLbs: data.get("purgeLbs") || "0",
        maxMeltPressure: data.get("maxMeltPress") || "0",
        comments: data.get("commentText"),
      },

      // material usage
      materialData: {
        prodLogID: "0",
        mat1: data.get("Mat1Name"),
        matUsed1: data.get("hop1Lbs"),
        matDailyUsed1: data.get("dHop1"),
        mat2: data.get("Mat2Name"),
        matUsed2: data.get("hop2Lbs"),
        matDailyUsed2: data.get("dHop2"),
        mat3: data.get("Mat3Name"),
        matUsed3: data.get("hop3Lbs"),
        matDailyUsed3: data.get("dHop3"),
        mat4: data.get("Mat4Name"),
        matUsed4: data.get("hop4Lbs"),
        matDailyUsed4: data.get("dHop4"),
      },

      // temps
      tempData: {
        prodLogID: "0",
        bigDryerTemp: data.get("bigDryerTemp"),
        bigDryerDew: data.get("bigDryerDew"),
        pressDryerTemp: data.get("pressDryerTemp"),
        pressDryerDew: data.get("pressDryerDew"),
        t1: data.get("t1"),
        t2: data.get("t2"),
        t3: data.get("t3"),
        t4: data.get("t4"),
        m1: data.get("m1"),
        m2: data.get("m2"),
        m3: data.get("m3"),
        m4: data.get("m4"),
        m5: data.get("m5"),
        m6: data.get("m6"),
        m7: data.get("m7"),
        chillerTemp: data.get("chiller"),
        moldTemp: data.get("tcuTemp"),
        z1: data.get("z1"),
        z9: data.get("z9"),
      },
    };

    try {
      const result = await postProductionLog(payload);

      showAlertMessage(result.message, "showAlert", "success");

      setTimeout(() => {
        window.location.href = "../production.php";
      }, 2500); // Redirect after 2.5 seconds
    } catch (err) {
      console.error(err);
      showAlertMessage(
        "Failed to save production log. Try again.",
        "showAlert",
        "danger"
      );
    } finally {
      hideLoader();
    }
  });
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

  // Bind form submit
  wireFormSubmission();
});
