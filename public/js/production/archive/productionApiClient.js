//FILE   js/productionApiClient.js

import { showLoader, hideLoader } from "./productionUiManager.js";

const BASE_URL = "../api/prodDispatcher.php";

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

export async function fetchProdRunsCompleted() {
  showLoader();
  try {
    const response = await fetch("/api/prodDispatcher.php?getCompletedRuns=1");

    if (!response.ok) {
      console.error("API returned error:", response.statusText);
      return;
    }
    const jsonData = await response.json();
    console.log("parsed completed production runs data: ", jsonData);

    hideLoader();
    return jsonData;
  } catch (error) {
    console.error("Error getting completed production runs: ", error);
  }
}

export async function fetchProdRunsNotComplete() {
  showLoader();
  try {
    const response = await fetch("/api/prodDispatcher.php?getOpenRuns=1");

    if (!response.ok) {
      console.error("API returned error:", response.statusText);
      return;
    }
    const jsonData = await response.json();
    console.log("parsed not completed production runs data: ", jsonData);

    hideLoader();
    return jsonData;
  } catch (error) {
    console.error("Error getting not completed production runs: ", error);
  }
}

export async function fetchRunProdLogs(runID) {
  showLoader();
  try {
    const response = await fetch(
      `/api/prodDispatcher.php?getRunProdLogs=${encodeURIComponent(runID)}`
    );
    if (!response.ok) {
      console.error("API returned error:", response.statusText);
      return;
    }
    const jsonData = await response.json();
    console.log(`parsed production logs for runID ${runID}: `, jsonData);
    hideLoader();
    return jsonData;
  } catch (error) {
    console.error(`Error getting production logs for runID ${runID}: `, error);
  }
}

async function handleResponse(res) {
  //read raw text
  const text = await res.text();

  //try to parse JSON, otherwise leave text
  let data;
  try {
    data = JSON.parse(text);
  } catch {
    data = null;
  }

  // HTTP error?
  if (!res.ok) {
    //check body for message and use it
    const msg = data?.message || `HTTP ${res.status} ${res.statusText}`;
    throw new Error(msg);
  }

  // business-logic error?
  if (data && data.success === false) {
    //sever returned {success: false, message:"..."}
    throw new Error(data.message || "Unknown server error");
  }

  console.log("Raw response text:", text);
  // all good: return parsed JSON (or plain text)
  return data ?? text;
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
  console.log("Server response:", res);
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
