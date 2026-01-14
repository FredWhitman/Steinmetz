// FILE: /js/maintenance/maintenanceApiClient_new.js

const BASE_URL = "../api/maintDispatcher.php";

// --- Generic response handler ---
async function handleResponse(res) {
  const text = await res.text();
  let data = null;

  try {
    data = JSON.parse(text);
  } catch {
    // non‑JSON response is allowed, we'll return raw text
  }

  if (!res.ok) {
    const msg = data?.message || `HTTP ${res.status} ${res.statusText}`;
    throw new Error(msg);
  }

  if (data && data.success === false) {
    throw new Error(data.message || "Unknown server error");
  }

  return data ?? text;
}
