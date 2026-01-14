// FILE: /js/production/viewProdRunLogs.js

import {
  renderRunProdLogsTable,
  showLoader,
  hideLoader,
  showAlertMessage,
} from "./productionUiManager_new.js";

import { fetchRunProdLogs } from "./productionApiClient_new.js";

async function init() {
  showLoader();

  try {
    // 1. Get runID from URL
    const params = new URLSearchParams(window.location.search);
    const runID = params.get("runID");

    if (!runID) {
      hideLoader();
      showAlertMessage(
        "Missing runID in URL. Cannot load production run logs.",
        "danger"
      );
      return;
    }

    // 2. Fetch logs for this run
    const data = await fetchRunProdLogs(runID);

    // 3. Render table
    if (data && Array.isArray(data)) {
      renderRunProdLogsTable(data);
    } else {
      showAlertMessage("No production logs found for this run.", "warning");
    }
  } catch (err) {
    console.error("Error loading run logs:", err);
    showAlertMessage("Error loading production run logs.", "danger");
  }

  hideLoader();
}

init();
