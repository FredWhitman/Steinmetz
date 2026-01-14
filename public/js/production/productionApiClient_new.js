// FILE: /js/production/productionApiClient_new.js

const BASE_URL = "../api/prodDispatcher.php";

async function handleResponse(response) {
  const json = await response.json();
  // Display alert HTML if provided
  const alertContainer = document.getElementById("alerts");
  if (alertContainer && json.html) {
    alertContainer.innerHTML = json.html;
  }

  // Throw an error if the server indicates failure
  if (!json.success) {
    throw new Error(json.message || "Server returned an error");
  }
  // Return the data payload (or the whole object if no data field)
  return json.data ?? json;
}

// --- Reads / GETs ---

export async function fetchProdLogs() {
  const res = await fetch("/api/prodDispatcher.php?read4wks=1");
  return handleResponse(res);
}

export async function fetchProdRunsCompleted() {
  const res = await fetch("/api/prodDispatcher.php?getCompletedRuns=1");
  return handleResponse(res);
}

export async function fetchProdRunsNotComplete() {
  const res = await fetch("/api/prodDispatcher.php?getOpenRuns=1");
  return handleResponse(res);
}

export async function fetchRunProdLogs(runID) {
  const res = await fetch(
    `/api/prodDispatcher.php?action=getRunProdLogs&runID=${encodeURIComponent(
      runID
    )}`
  );
  return handleResponse(res);
}

export async function fetchProductList() {
  const res = await fetch(`${BASE_URL}?action=getProducts`);
  return handleResponse(res);
}

export async function fetchMaterialList() {
  const res = await fetch(`${BASE_URL}?action=getMaterials`);
  return handleResponse(res);
}

export async function checkIfLogExists(productID, date) {
  const res = await fetch(
    `${BASE_URL}?action=checkIfLogExists&productID=${encodeURIComponent(
      productID
    )}&date=${encodeURIComponent(date)}`
  );
  return handleResponse(res);
}

export async function checkIfRunExists(productID) {
  const res = await fetch(
    `${BASE_URL}?action=checkRun&productID=${encodeURIComponent(productID)}`
  );
  return handleResponse(res);
}

export async function fetchProductionLog(productID, date) {
  const res = await fetch(
    `${BASE_URL}?action=viewLog&productID=${encodeURIComponent(
      productID
    )}&date=${encodeURIComponent(date)}`
  );
  return handleResponse(res);
}

/**
 * type: "getLastLog" | "endRun"
 */
export async function fetchPreviousMatLogs(productID, type) {
  const res = await fetch(
    `${BASE_URL}?action=${type}&productID=${encodeURIComponent(productID)}`
  );
  return handleResponse(res);
}

// --- Writes / POSTs ---

export async function postProductionLog(payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}

export async function postPurge(payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}
