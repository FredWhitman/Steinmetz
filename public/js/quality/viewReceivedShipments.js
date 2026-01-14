//FILE: public/js/quality/viewReceivedShipments.js

import {
  hideLoader,
  showLoader,
  renderReceivedShipmentsTable,
} from "../qualityUiManager.js";

import { fetchReceivedShipments } from "../qualityApiClient.js";

async function init() {
  showLoader();

  const data = await fetchReceivedShipments();
  if (data) {
    renderReceivedShipmentsTable(data);
  }

  hideLoader();
}

init();
