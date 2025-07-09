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

  // all good: return parsed JSON (or plain text)
  return data ?? text;
}

export async function postQaRejects(payload){
  const res = await(BASE_URL, {
    method: "POST",
    header: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}

