// inventoryApiClient.js

import { showLoader, hideLoader } from "./inventoryUiManager.js";

// Adjust your base URL/path if needed.
const BASE_URL = "/api/dispatcher.php";

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

// Fill a form for updating (GET request) - similar logic as above:
export async function fetchAndFillUpdateForm(id, table) {
  const url = `${BASE_URL}?update${
    table.charAt(0).toUpperCase() + table.slice(1)
  }=1&id=${id}&table=${table}`;
  console.log("fetchAndFillUpdateForm URL:", url);
  try {
    const response = await fetch(url);
    const rawText = await response.text();
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
      // Add material and pfm mappings if needed.
    };
    Object.keys(fieldMappings[table]).forEach((dbKey) => {
      const formID = fieldMappings[table][dbKey];
      const element = document.getElementById(formID);
      if (element) {
        element.value = responseData[dbKey] || "";
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
    const responseBody = await response.text();
    return responseBody;
  } catch (error) {
    console.error("Error in postData:", error);
    throw error;
  }
}
