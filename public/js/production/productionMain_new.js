// FILE: /js/production/productionMain_new.js

import {
  renderTables,
  showLoader,
  hideLoader,
  showAlertMessage,
  clearAlert,
  populateProductSelect,
} from "./productionUiManager_new.js";

import {
  fetchProdLogs,
  fetchProductList,
  postPurge,
} from "./productionApiClient_new.js";

// --------------------------------------------------
// Generic form handler
// --------------------------------------------------

export function handleFormSubmit(formId, buildPayload, postFn, modalId) {
  const form = document.getElementById(formId);
  if (!form) {
    console.warn(`handleFormSubmit: No form with id "${formId}"`);
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearAlert("showAlert");

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const data = new FormData(form);
    const payload = buildPayload(data);

    try {
      showLoader();
      const result = await postFn(payload);

      // Use HTML from server if present (your showMessage helper returns this)
      if (result?.html) {
        document.getElementById("showAlert").innerHTML = result.html;
      } else if (result?.message) {
        showAlertMessage(result.message, "showAlert", "success");
      }

      if (modalId) {
        const modalEl = document.getElementById(modalId);
        if (modalEl) {
          const instance = bootstrap.Modal.getInstance(modalEl);
          if (instance) instance.hide();
        }
      }

      // Refresh tables after successful submit
      const logs = await fetchProdLogs();
      if (logs) renderTables(logs);
    } catch (error) {
      console.error(`Failed to submit form "${formId}":`, error);
      showAlertMessage(
        error.message || "Submission failed.",
        "showAlert",
        "danger"
      );
    } finally {
      hideLoader();
    }
  });
}

// --------------------------------------------------
// Generic modal setup
// --------------------------------------------------

function setupModalShow(modalId, configs) {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) {
    console.warn(`setupModalShow: No modal with id "${modalId}"`);
    return;
  }

  modalEl.addEventListener("show.bs.modal", async () => {
    showLoader();
    clearAlert("showAlert");

    try {
      const results = await Promise.all(configs.map((c) => c.fetchFn()));

      configs.forEach((c, i) => {
        const data = results[i];
        if (Array.isArray(data)) {
          const el = document.getElementById(c.elId);
          c.populateFn(el, data);
        } else {
          console.error(`Failed to load ${c.name}:`, data);
          showAlertMessage(`Failed to load ${c.name}.`, "showAlert", "danger");
        }
      });
    } catch (error) {
      console.error(`Failed to load data for modal "${modalId}":`, error);
      showAlertMessage("Failed to load modal data.", "showAlert", "danger");
    } finally {
      hideLoader();
    }
  });
}

// --------------------------------------------------
// Page init
// --------------------------------------------------

async function initLanding() {
  try {
    showLoader();
    const data = await fetchProdLogs();
    if (data) {
      renderTables(data);
      // If you later want row‑click behavior, wire it here:
      // setupViewEventListener("read4wks", (id) => { ... });
    }
  } catch (error) {
    console.error("Error initializing production landing page:", error);
    showAlertMessage("Failed to load production logs.", "showAlert", "danger");
  } finally {
    hideLoader();
  }
}

// --------------------------------------------------
// Bootstrapping
// --------------------------------------------------

document.addEventListener("DOMContentLoaded", () => {
  // 1) Landing page table
  initLanding();

  // 2) Purge modal: preload product list when modal opens
  setupModalShow("addPurgeModal", [
    {
      name: "product list",
      elId: "p_PartName",
      fetchFn: fetchProductList,
      populateFn: populateProductSelect,
    },
  ]);

  // 3) Purge form submission
  handleFormSubmit(
    "add-purge-form",
    (d) => ({
      action: "addPurge",
      productID: d.get("p_PartName"),
      prodDate: d.get("p_LogDate"),
      purgeLbs: d.get("p_purgeLbs"),
    }),
    postPurge,
    "addPurgeModal"
  );
});
