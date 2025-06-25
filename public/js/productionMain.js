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
