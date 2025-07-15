//FILE: /js/qualityApiClient.js

import { showLoader, hideLoader } from "./qualityUiManager.js";

const BASE_URL = "/api/qaDispatcher.php";

//Fetch logs for landing page table
export async function fetchQualityLogs() {
  showLoader();
  try {
    const response = await fetch(`${BASE_URL}?action=getQaLogs`, {
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

export async function fetchProductList() {
  const res = await fetch(`${BASE_URL}?action=getProducts`);
  return handleResponse(res);
}

export async function fetchMaterialList() {
  const res = await fetch(`${BASE_URL}?action=getMaterials`);
  return handleResponse(res);
}

export async function postQaRejects(payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    header: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}

export async function postLotChange(payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    header: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}

export async function postOvenLog(payload){
  const res = await fetch(BASE_URL,{
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}

// Fill a form (GET request) for viewing
export async function fetchAndFillViewForm(id, table) {
  const url = `${BASE_URL}?action=getQaRejectLog&id=${id}&table=${table}`;
  console.log("fetchAndFillViewForm URL:", url);
  try {
    const response = await fetch(url);
    const rawText = await response.text();
    const responseData = JSON.parse(rawText);
    if (!responseData || responseData.error) {
      console.error("Error from server:", responseData?.error);
      return;
    }
    // Dynamically fill form fields based on table type.
    const fieldMappings = {
          
    };

    Object.keys(fieldMappings[table]).forEach((dbKey) => {
      const formID = fieldMappings[table][dbKey];
      const element = document.getElementById(formID);
      if (element) {
        element.value = responseData[dbKey] || "";
      } else {
        console.warn(`Element with ID '${formID}' not found!`);
      }
    });
  } catch (error) {
    console.error("Failed to parse JSON in fetchAndFillForm:", error);
  }
}