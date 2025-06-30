//productionApiClient.js

import { showLoader, hideLoader } from "./productionUiManager.js";

const BASE_URL = "/api/prodDispatcher.php";

// Fetch last4wks production log data (GET request)
export async function fetchProdLogs() {
  showLoader();
  try {
    const response = await fetch("/api/prodDispatcher.php?read4wks=1");

    if (!response.ok) {
      console.error("API returned error:", response.statusText);
      return;
    }
    const jsonData = await response.json();
    console.log("parsed production log data: ", jsonData);

    hideLoader();
    return jsonData;
  } catch (error) {
    console.error("Error getting production logs: ", error);
  }
}

async function handleResponse(res) {
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  const text = await res.text();
  try {
    return JSON.parse(text);
  } catch {
    return text;
  }
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

export async function fetchPreviousMatLogs(productID, type) {
  // type: "getLastLog" | "endRun"
  const res = await fetch(
    `${BASE_URL}?action=${type}&productID=${encodeURIComponent(productID)}`
  );
  return handleResponse(res);
}

export async function postProductionLog(payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}
