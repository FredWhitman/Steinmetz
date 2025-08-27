import {
  renderRunsNotCompleteTable,
  renderRunsCompleteTable,
  showLoader,
  hideLoader,
} from "./productionUiManager.js";

import {
  fetchProdRunsNotComplete,
  fetchProdRunsCompleted,
} from "./productionApiClient.js";

async function init() {
  //load & render the landing-page table
  showLoader();
  const data = await fetchProdRunsCompleted();
  if (data) {
    renderRunsCompleteTable(data);
  }

  const notCompleteData = await fetchProdRunsNotComplete();
  if (notCompleteData) {
    renderRunsNotCompleteTable(notCompleteData);
  }
  hideLoader();
}

init();
