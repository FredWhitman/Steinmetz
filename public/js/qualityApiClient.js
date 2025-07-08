//FILE: /js/qualityApiClient.js

import { showLoader, hideLoader } from "./qualityUiManager.js";

const BASE_URL = "/api/qaDispatcher.php";

//Fetch logs for landing page table
export async function fetchQualityLogs() {
  showLoader();
  try {
    const response = await fetch(`${BASE_URL}?getQaLogs=1`, {
      method: "GET",
    });

    const jsonData = await response.json();
    console.log("Parsed Qa logs: ", jsonData);
    hideLoader();

    return jsonData;
  } catch (error) {
    console.error("Error fetching quality logs: ", error);
    hideLoader();
  }
}

//Fill a form for updating
export async function fetchAndFillUpdateForm(id, table) {
  const url = `${BASE_URL}?update${
    table.charAt(0).toUpperCase() + table.slice(1)
  }=1&id=${id}&table=${table}`;

  console.log("FetchAndFillUpdateForm URL: ", url);
  try {
    const response = await fetch(url);
    const rawText = await response.text();
  } catch (error) {
    console.error("Failed to parse JSON in FetchAndFill UpdateForm: ", error);
  }
}
