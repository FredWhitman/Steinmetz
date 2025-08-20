// inventoryApiClient.js

import { showLoader, hideLoader } from "./inventoryUiManager.js";

const BASE_URL = "/api/dispatcher.php";

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

// Fetch inventory data (GET request)
export async function fetchProductsMaterialPFM() {
  showLoader();
  try {
    const start = performance.now();
    const response = await fetch(`${BASE_URL}?getInventory=1`, {
      method: "GET",
    });
    const jsonData = await response.json();
    console.log("Parsed inventory data:", jsonData);
    console.log("Fetch duration:", performance.now() - start);
    hideLoader();
    return jsonData;
  } catch (error) {
    console.error("Error fetching inventory:", error);
    hideLoader();
  }
}

// Fill a form (GET request) for editing
export async function fetchAndFillForm(id, table) {
  const url = `${BASE_URL}?edit${
    table.charAt(0).toUpperCase() + table.slice(1)
  }=1&id=${id}&table=${table}`;
  console.log("fetchAndFillForm URL:", url);
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
      products: {
        productID: "hiddenProductID",
        partName: "partName",
        minQty: "minQty",
        boxesPerSkid: "boxSkid",
        partsPerBox: "partBox",
        partWeight: "partWeight",
        customer: "customer",
        productionType: "partType",
        displayOrder: "displayOrder",
      },
      materials: {
        matPartNumber: "h_matPartNumber",
        matName: "matName",
        productID: "productID",
        minLbs: "minLbs",
        matCustomer: "mCustomer",
        displayOrder: "mDisplayOrder",
      },
      pfms: {
        pfmID: "h_pfmID",
        partNumber: "pNumber",
        partName: "pName",
        productID: "pProductID",
        minQty: "pMinQty",
        customer: "pCustomer",
        displayOrder: "pDisplayOrder",
      },
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

// Fill a form for updating (GET request)
export async function fetchAndFillUpdateForm(id, table) {
  const url = `${BASE_URL}?update${
    table.charAt(0).toUpperCase() + table.slice(1)
  }=1&id=${id}&table=${table}`;
  console.log("fetchAndFillUpdateForm URL:", url);
  try {
    const response = await fetch(url);
    const rawText = await response.text();
    console.log("Raw server response: ", rawText);

    const responseData = JSON.parse(rawText);
    if (!responseData || responseData.error) {
      console.error("Error from server:", responseData?.error);
      return;
    }
    // Field mappings for update may be different.
    const fieldMappings = {
      products: {
        productID: "h_productID",
        partName: "pPartName",
        partQty: "pStock",
      },
      materials: {
        matPartNumber: "h_matPartNumber",
        matName: "umMatName",
        matLbs: "umMatLbs",
      },
      pfms: {
        pfmID: "h_pfmID",
        partNumber: "h_partNumber",
        partName: "uPfmName",
        Qty: "uPfmStock",
      },

      // Add pfm mappings if needed.
    };
    const record = responseData[table] || responseData; // fallback if data is flat

    Object.keys(fieldMappings[table]).forEach((dbKey) => {
      const formID = fieldMappings[table][dbKey];
      const element = document.getElementById(formID);
      if (element) {
        element.value = record[dbKey] ?? ""; // safe fallback
        console.log(`${formID} populated with:`, element.value);
        console.log("Using table:", table);
        console.log("Resolved record keys:", Object.keys(record));
      } else {
        console.warn(`Element with ID '${formID}' not found in update form!`);
      }
    });
  } catch (error) {
    console.error("Failed to parse JSON in fetchAndFillUpdateForm:", error);
  }
}

// Function to handle a POST request submission:
export async function postData(productData) {
  try {
    const response = await fetch(BASE_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(productData),
    });
    return await handleResponse(response);
  } catch (error) {
    console.error("Error in postData:", error);
    throw error;
  }
}
