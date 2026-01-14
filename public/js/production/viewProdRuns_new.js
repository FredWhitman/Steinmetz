// FILE: /js/production/viewProdRuns_new.js

import {
  renderRunsNotCompleteTable,
  renderRunsCompleteTable,
  showLoader,
  hideLoader,
  clearAlert,
  showAlertMessage,
} from "./productionUiManager_new.js";

import {
  fetchProdRunsNotComplete,
  fetchProdRunsCompleted,
} from "./productionApiClient_new.js";

// Attach click listeners to View buttons
function attachViewButtonListeners() {
  document.querySelectorAll(".view-run-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      const runID = e.currentTarget.getAttribute("data-runid");
      if (!runID) return;

      window.location.href = `/forms/viewRunLogs.php?runID=${runID}`;
    });
  });
}

async function init() {
  clearAlert("showAlert");
  showLoader();

  try {
    const finished = await fetchProdRunsCompleted();
    if (Array.isArray(finished)) {
      renderRunsCompleteTable(finished);
    } else {
      showAlertMessage(
        "Failed to load finished production runs.",
        "showAlert",
        "danger"
      );
    }

    const open = await fetchProdRunsNotComplete();
    if (Array.isArray(open)) {
      renderRunsNotCompleteTable(open);
    } else {
      showAlertMessage(
        "Failed to load open production runs.",
        "showAlert",
        "danger"
      );
    }

    // Attach listeners AFTER rendering
    attachViewButtonListeners();
  } catch (err) {
    console.error("Error loading production runs:", err);
    showAlertMessage(
      "Error loading production run data.",
      "showAlert",
      "danger"
    );
  } finally {
    hideLoader();
  }
}

document.addEventListener("DOMContentLoaded", init);
