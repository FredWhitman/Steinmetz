//FILE /js/qualityMain.js

import { renderTables, setupEventListener } from "./qualityUiManager.js";

import { fetchQualityLogs } from "./qualityApiClient.js";

async function init() {
  //load and render tables for OvenLog and QA Rejects
  const data = await fetchQualityLogs();
  if (data) {
    renderTables(data);
    setupEventListener("qaRejectLogs", "qaRejectsLogs");
    setupEventListener("lotChangeLogs", "lotchangelogs");
    setupEventListener("ovenLogs", "ovenlogs");
  }
}

init();
