// File: public/js/inventory/inventoryApiClient_new.js

const BASE_URL = "/api/dispatcher.php";

/**
 * Unified JSON handler (Production-style)
 */
async function handleResponse(response) {
  const raw = await response.text();

  let data = null;
  try {
    data = JSON.parse(raw);
  } catch {
    throw new Error(
      `Invalid JSON response from server. Raw output: ${raw.substring(
        0,
        200
      )}...`
    );
  }

  if (!response.ok) {
    const msg =
      data?.message || `HTTP ${response.status} ${response.statusText}`;
    throw new Error(msg);
  }

  if (data.success === false) {
    throw new Error(data.message || "Server returned an error.");
  }

  return data;
}

/* ---------------------------------------------------------
   GET REQUESTS
--------------------------------------------------------- */

/**
 * Fetch full inventory (products, materials, pfms)
 */
export async function getInventory() {
  const res = await fetch(`${BASE_URL}?action=getInventory`);
  return handleResponse(res);
}

/**
 * Fetch product list for dropdowns
 */
export async function getProducts() {
  const res = await fetch(`${BASE_URL}?action=getProducts`);
  return handleResponse(res);
}

/**
 * Fetch shipments table data
 */
export async function getShipments() {
  const res = await fetch(`${BASE_URL}?action=getShipments`);
  return handleResponse(res);
}

/**
 * Fetch record for edit modal
 */
export async function getRecordForEdit(id, table) {
  const url = `${BASE_URL}?action=getRecordForEdit&id=${id}&table=${table}`;
  const res = await fetch(url);
  return handleResponse(res);
}

/**
 * Fetch record for update modal
 */
export async function getRecordForUpdate(id, table) {
  const url = `${BASE_URL}?action=getRecordForUpdate&id=${id}&table=${table}`;
  const res = await fetch(url);
  return handleResponse(res);
}

/*
---------------------------------------------------------
   POST REQUESTS
--------------------------------------------------------- */

/**
 * Generic POST wrapper
 */
async function post(action, payload) {
  const res = await fetch(BASE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action, ...payload }),
  });
  return handleResponse(res);
}

/*------------------------- ADD --------------------------------*/

export function addProduct(data) {
  return post("addProduct", data);
}

export function addMaterial(data) {
  return post("addMaterial", data);
}

export function addPFM(data) {
  return post("addPFM", data);
}

export function addShipment(data) {
  return post("addShipment", data);
}

/*------------------------- EDIT --------------------------------*/
export function editProduct(data) {
  return post("editProduct", data);
}

export function editMaterial(data) {
  return post("editMaterial", data);
}

export function editPFM(data) {
  return post("editPFM", data);
}

/*------------------------- UPDATE --------------------------------*/

export function updateProduct(data) {
  return post("updateProduct", data);
}

export function updateMaterial(data) {
  return post("updateMaterial", data);
}

export function updatePFM(data) {
  return post("updatePFM", data);
}
