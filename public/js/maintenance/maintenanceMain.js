//FILE: /js/maintenance/maintenanceMain_new.js

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
