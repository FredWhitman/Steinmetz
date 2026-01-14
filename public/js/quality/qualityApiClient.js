//FILE: /js/quality/qualityApiClient_new.js

const BASE_URL = "/api/qaDispatcher.php";

async function handleResponse(res) {
  const text = await res.text();
  let data;
  try {
    data = JSON.parse(text);
  } catch {
    data = null;
  }

  if (!res.ok)
    throw new Error(data?.message || `HTTP ${res.status} ${res.statusText}`);
  if (data && data.success === false)
    throw new Error(data.message || "Unknown server error");

  return data ?? text;
}

// Generic GET
export async function apiGet(action) {
  const res = await fetch(`${BASE_URL}?action=${action}`);
  return handleResponse(res);
}

// Generic POST
export async function apiPost(payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}

// Specific endpoints
export const fetchQualityLogs = () => apiGet("getQaLogs");
export const fetchProductList = () => apiGet("getProducts");
export const fetchMaterialList = () => apiGet("getMaterials");
export const fetchPFMList = () => apiGet("getPfms");
export const fetchReceivedShipments = () => apiGet("getReceivedShipments");

// Example POST usage
export const postQaRejects = (payload) =>
  apiPost({ action: "addQaRejects", ...payload });
export const postLotChange = (payload) =>
  apiPost({ action: "addLotChange", ...payload });
export const postOvenLog = (payload) =>
  apiPost({ action: "addOvenLog", ...payload });
export const postUpdateOvenLog = (payload) =>
  apiPost({ action: "updateOvenLog", ...payload });
export const postMatReceived = (payload) =>
  apiPost({ action: "matReceived", ...payload });
export const postPfmReceived = (payload) =>
  apiPost({ action: "pfmReceived", ...payload });
