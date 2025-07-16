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

export async function postOvenLog(payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  return handleResponse(res);
}

// Fill a form (GET request) for viewing
export async function fetchAndFillViewForm(id, table) {
  const tableKeyMap = {
    qaRejectsLogs: "qaRejectLog",
    ovenLogs: "ovenLog",
    lotChangeLogs: "lotChangeLog",
  };

  const fieldMappings = {
    qaRejectLog: {
      prodDate: "v_logDate",
      productID: "v_qaPartName",
      rejects: "v_qaRejects",
      comments: "v_comment-text",
    },
    ovenLog: {
      productID: "v_olPartName",
      inOvenDate: "v_olinOvenDate",
      inOvenTime: "v_olinOvenTime",
      inOvenTemp: "v_olinOvenTemp",
      inOvenInitials: "v_olinOvenInitials",
      outOvenDate: "v_olOutOvenDate",
      outOvenTime: "v_olOutOvenTime",
      outOvenTemp: "v_olOutOvenTemp",
      outOvenInitials: "v_olOutOvenInitials",
      ovenComments: "v_olComments",
    },
    lotChangeLog: {
      ProductID: "v_lcPartName",
      MaterialName: "v_lcMatName",
      ChangeDate: "v_lclotDate",
      ChangeTime: "v_lclotTime",
      OldLot: "v_lcOldLot",
      NewLot: "v_lcNewLot",
      Comments: "v_lcComments",
    },
  };

  const lookupKey = tableKeyMap[table];
  const fieldMap = fieldMappings[lookupKey];

  if (!fieldMap) {
    console.warn(`❌ No field mapping found for table: ${table}`);
    return;
  }

  const url = `${BASE_URL}?action=get${
    lookupKey.charAt(0).toUpperCase() + lookupKey.slice(1)
  }&id=${id}&table=${table}`;
  console.log("fetchAndFillViewForm URL:", url);
  try {
    const response = await fetch(url);
    const rawText = await response.text();
    const responseData = JSON.parse(rawText);

    if (!responseData || responseData.error) {
      console.error("Error from server:", responseData?.error);
      return;
    }
    console.log("responseData:", responseData);
    // Dynamically fill form fields based on table type.

    console.log("Form field:", document.getElementById("v_qaPartName"));

    Object.entries(fieldMap).forEach(([dbKey, formID]) => {
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

//Fill a form for updating
export async function fetchAndFillUpdateForm(id, table) {
  const tableKeyMap = {
    qaRejectsLogs: "qaRejectLog",
    ovenLogs: "ovenLog",
    lotChangeLogs: "lotChangeLog",
  };

  const fieldMappings = {
    qaRejectLog: {
      prodDate: "u_logDate",
      productID: "u_qaPartName",
      rejects: "u_qaRejects",
      comments: "u_comment-text",
    },
    ovenLog: {
      productID: "u_olPartName",
      inOvenDate: "u_olinOvenDate",
      inOvenTime: "u_olinOvenTime",
      inOvenTemp: "u_olinOvenTemp",
      inOvenInitials: "u_olinOvenInitials",
      outOvenDate: "u_olOutOvenDate",
      outOvenTime: "u_olOutOvenTime",
      outOvenTemp: "u_olOutOvenTemp",
      outOvenInitials: "u_olOutOvenInitials",
      ovenComments: "u_olComments",
    },
    lotChangeLog: {
      ProductID: "u_lcPartName",
      MaterialName: "u_lcMatName",
      ChangeDate: "u_lclotDate",
      ChangeTime: "u_lcLotTime",
      OldLot: "u_lvOldLot",
      NewLot: "u_lcNewLot",
      Comments: "u_lcComments",
    },
  };

  const lookupKey = tableKeyMap[table];
  const fieldMap = fieldMappings[lookupKey];

  if (!fieldMap) {
    console.warn(`❌ No field mapping found for table: ${table}`);
    return;
  }

  const url = `${BASE_URL}?action = update${
    lookupKey.charAt(0).toUpperCase() + lookupKey.slice(1)
  }=1&id=${id}&table=${table}`;

  console.log("FetchAndFillUpdateForm URL: ", url);
  try {
    const response = await fetch(url);
    const rawText = await response.text();
    const responseData = JSON.parse(rawText);

    if (!responseData || responseData.error) {
      console.error("Error from server:", responseData?.error);
      return;
    }
    console.log("responseData:", responseData);
    // Dynamically fill form fields based on table type.

    Object.entries(fieldMap).forEach(([dbKey, formID]) => {
      const element = document.getElementById(formID);

      if (element) {
        element.value = responseData[dbKey] || "";
      } else {
        console.warn(`Element with ID '${formID}' not found!`);
      }
    });
  } catch (error) {
    console.error("Failed to parse JSON in FetchAndFill UpdateForm: ", error);
  }
}
