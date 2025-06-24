import { renderTables, setupViewEventListener } from "./productionUiManager.js";

import { fetchProdLogs } from "./productionApiClient.js";

async function init() {
  const data = await fetchProdLogs();
  if (data) {
    renderTables(data);
    setupViewEventListener("read4wks", "prodLogs");
  }
}
init();
