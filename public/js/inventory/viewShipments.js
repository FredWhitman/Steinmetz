import {
  hideLoader,
  showLoader,
  renderShipmentsTable,
} from "../inventoryUiManager.js";

import { fetchShipments } from "../inventoryApiClient.js";

async function init() {
  showLoader();

  const data = await fetchShipments();
  if (data) {
    renderShipmentsTable(data);
  }

  hideLoader();
}

init();
