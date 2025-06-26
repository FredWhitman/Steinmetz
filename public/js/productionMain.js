//FILE: /js/productionMain.js

import {
  renderTables,
  setupViewEventListener,
  fetchAndFillForm,
} from "./productionUiManager.js";

import { fetchProdLogs } from "./productionApiClient.js";

/* calculateDailyUsage(
  [hop1, hop2, hop3, hop4],
  [prevHop1, prevHop2, prevHop3, prevHop4]
);
 */

async function init() {
  //load & render the landing-page table
  const data = await fetchProdLogs();
  if (data) {
    renderTables(data);
    //listen for clicks inside <table id="last4wks">
    setupViewEventListener("read4wks", "prodLogs", onRowClick);
  }
}

async function onRowClick(id) {
  //fetch that one record and the previousLog and fill form
  await fetchAndFillForm(id, "prodLogs");

  //collect current and previous hopper inputs by ID
  const currentEls = ["vhop1Lbs", "vhop2Lbs", "vhop3Lbs", "vhop4Lbs"].map(
    (id) => document.getElementById(id)
  );
}

init();

/* 
Hook up event listeners and control flow:
  initializeAddModal()
  on modal show → call loadProductAndMaterialOptions()
  Form onsubmit handler → validate + send to productionApiClient
  Handle radio changes and call calculation logic 
  
  
  // src/js/productionMain.js

import {
  fetchProductList,
  fetchMaterialList,
  checkIfRunExists,
  checkIfLogExists,
  fetchPreviousMatLogs,
  postProductionLog,
  finalizeProductionRun
} from "./productionApiClient.js";

import {
  showLoader,
  hideLoader,
  showAlertMessage,
  clearAlert,
  populateProductSelect,
  populateMaterialSelects,
  resetAddModalForm,
  updateBlenderTotal,
  fillDailyUsageFields,
  fillPercentageFields,
  initAddModalUI
} from "./productionUiManager.js";

let runMode = null;  // "1"=start, "0"=in-progress, "2"=end

// 1) When the modal opens, load options
async function onModalShow() {
  showLoader();
  clearAlert();
  try {
    const [products, materials] = await Promise.all([
      fetchProductList(),
      fetchMaterialList()
    ]);
    populateProductSelect(products);
    populateMaterialSelects(materials);
  } catch (err) {
    console.error(err);
    showAlertMessage("Unable to load products or materials.");
  } finally {
    hideLoader();
  }
}

// 2) Handle run-status radio changes
function onRadioChange(e) {
  runMode = e.target.value;
  clearAlert();

  const productID = document.getElementById("partName").value;
  const logDate   = document.getElementById("logDate").value;
  if (!productID || !logDate) return;

  // Prevent duplicate "Start" runs
  if (runMode === "1") {
    checkIfRunExists(productID, logDate)
      .then(data => {
        if (data.exists) {
          showAlertMessage(
            "A production run for this product is already active. Please end it first."
          );
        }
      })
      .catch(err => console.error(err));
  }
  // Prevent missing "In Progress" logs
  else if (runMode === "0") {
    checkIfLogExists(productID, logDate)
      .then(data => {
        if (!data.exists) {
          showAlertMessage(
            "No existing in-progress log found. Please start the run first."
          );
        }
      })
      .catch(err => console.error(err));
  }
}

// Helper: compute usage deltas & percentages
function computeUsageAndPercents(current, previous) {
  const usage = current.map((c, i) => parseFloat((c - (previous[i]||0)).toFixed(3)));
  const total = usage.reduce((sum, v) => sum + v, 0);
  const percents = usage.map(u => total ? parseFloat(((u/total)*100).toFixed(2)) : 0);
  return { usage, percents };
}

// 3) After the last hopper blurs, recalc & fetch previous if needed
async function onHopperBlur() {
  clearAlert();
  updateBlenderTotal();

  // gather current hopper values
  const current = [1,2,3,4].map(i => 
    parseFloat(document.getElementById(`hop${i}Lbs`).value) || 0
  );
  let usage, percents;

  if (runMode === "1") {
    // Start: previous all zeros
    ({ usage, percents } = computeUsageAndPercents(current, [0,0,0,0]));
  } else if (runMode === "0" || runMode === "2") {
    showLoader();
    try {
      const productID = document.getElementById("partName").value;
      const actionType = runMode === "0" ? "getLastLog" : "endRun";
      const prevData = await fetchPreviousMatLogs(productID, actionType);
      const previous = [
        parseFloat(prevData.matUsed1)||0,
        parseFloat(prevData.matUsed2)||0,
        parseFloat(prevData.matUsed3)||0,
        parseFloat(prevData.matUsed4)||0
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

  fillDailyUsageFields(usage);
  fillPercentageFields(percents);
}

// 4) Form submit → validate, POST, finalize (if needed), and refresh
function wireFormSubmission() {
  const form = document.getElementById("add-productionLog-form");
  form.addEventListener("submit", async e => {
    e.preventDefault();
    clearAlert();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    showLoader();
    const data = new FormData(form);
    // Build payload exactly how your PHP expects it:
    const payload = {
      productID:    data.get("partName"),
      prodDate:     data.get("logDate"),
      runStatus:    runMode,
      prevProdLogID: "0",    // PHP can overwrite if needed
      runLogID:     "0",
      matLogID:     "0",
      tempLogID:    "0",
      pressCounter: data.get("pressCounter"),
      startUpRejects: data.get("startUpRejects"),
      qaRejects:    data.get("qaRejects")  || "0",
      purgeLbs:     data.get("purgeLbs")   || "0",
      comments:     data.get("commentText"),

      // material usage
      materials: [
        { id: data.get("Mat1Name"), used: data.get("hop1Lbs") },
        { id: data.get("Mat2Name"), used: data.get("hop2Lbs") },
        { id: data.get("Mat3Name"), used: data.get("hop3Lbs") },
        { id: data.get("Mat4Name"), used: data.get("hop4Lbs") }
      ],

      // temps
      temperatures: {
        bigDryerTemp: data.get("bigDryerTemp"),
        bigDryerDew:  data.get("bigDryerDew"),
        pressDryerTemp: data.get("pressDryerTemp"),
        pressDryerDew:  data.get("pressDryerDew"),
        t1: data.get("t1"), t2: data.get("t2"),
        t3: data.get("t3"), t4: data.get("t4"),
        m1: data.get("m1"), m2: data.get("m2"),
        m3: data.get("m3"), m4: data.get("m4"),
        m5: data.get("m5"), m6: data.get("m6"),
        m7: data.get("m7"),
        chiller: data.get("chiller"),
        tcuTemp: data.get("tcuTemp")
      }
    };

    try {
      const result = await postProductionLog(payload);
      // If ending a run, finalize roll-up
      if (runMode === "2" && result.runLogID) {
        await finalizeProductionRun(result.runLogID);
      }
      // close, reset, refresh
      bootstrap.Modal.getInstance(
        document.getElementById("addProductionModal")
      ).hide();
      window.fetchLast4Weeks();
    } catch (err) {
      console.error(err);
      showAlertMessage("Failed to save production log. Try again.");
    } finally {
      hideLoader();
    }
  });
}

// 5) Bootstrap everything on page load
document.addEventListener("DOMContentLoaded", () => {
  // Initialize UI bindings
  initAddModalUI({
    onRadioChange,
    onHopperBlur
  });

  // Load data when modal shows
  const addModalEl = document.getElementById("addProductionModal");
  addModalEl.addEventListener("show.bs.modal", onModalShow);

  // Bind form submit
  wireFormSubmission();
});

  */

