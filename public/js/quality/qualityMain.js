//FILE: /js/quality/qualityMain_new.js

import {
  renderTables,
  showLoader,
  hideLoader,
  showAlertMessage,
  clearAlert,
  populateProductSelect,
  populateMaterialSelect,
  populatePFMSelect,
} from "./qualityUiManager.js";

import {
  fetchQualityLogs,
  fetchProductList,
  fetchMaterialList,
  fetchPFMList,
  postQaRejects,
  postLotChange,
  postOvenLog,
  postUpdateOvenLog,
  postMatReceived,
  postPfmReceived,
} from "./qualityApiClient.js";

// Generic form handler
function handleFormSubmit(formId, buildPayload, postFn, modalId) {
  const form = document.getElementById(formId);
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const data = new FormData(form);
    const payload = buildPayload(data);

    try {
      showLoader();
      const result = await postFn(payload);
      if (result?.html)
        document.getElementById("showAlert").innerHTML = result.html;
      bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();

      const logs = await fetchQualityLogs();
      if (logs) renderTables(logs);
    } catch (err) {
      console.error(`Failed to submit ${formId}:`, err);
      showAlertMessage("Submission failed.", "showAlert", "danger");
    } finally {
      hideLoader();
    }
  });
}

function setupModalShow(modalId, configs) {
  const modalEl = document.getElementById(modalId);
  modalEl.addEventListener("show.bs.modal", async () => {
    showLoader();
    clearAlert();
    try {
      const results = await Promise.all(configs.map((c) => c.fetchFn()));
      configs.forEach((c, i) => {
        if (Array.isArray(results[i])) {
          c.populateFn(document.getElementById(c.elId), results[i]);
        } else {
          showAlertMessage(
            `⚠️ Failed to load ${c.name}`,
            "showAlert",
            "danger"
          );
        }
      });
    } catch (err) {
      console.error(`Failed to load data for ${modalId}:`, err);
      showAlertMessage("Failed to load data.", "showAlert", "danger");
    } finally {
      hideLoader();
    }
  });
}

//add-qaReject-form call handleFormSubmit function and pass form name, payload
// builder, post function, and modal ID
handleFormSubmit(
  "add-qaReject-form",
  (d) => ({
    qaRejectData: {
      prodDate: d.get("qaLogDate"),
      productID: d.get("qaPart"),
      rejects: d.get("rejects"),
      comments: d.get("qaComments"),
    },
  }),
  postQaRejects,
  "addQARejectsModal"
);

//add-lotchange-form
handleFormSubmit(
  "add-lotchange-form",
  (d) => ({
    lotChangeData: {
      productID: d.get("lc_PartName"),
      MaterialName: d.get("lc_MatName"),
      ChangeDate: d.get("lc_LotDate"),
      ChangeTime: d.get("lc_LotTime"),
      OldLot: d.get("lc_OldLot"),
      NewLot: d.get("lc_NewLot"),
      Comments: d.get("lc_Comments"),
    },
  }),
  postLotChange,
  "addLotChangeModal"
);

//add-ovenlog-form
handleFormSubmit(
  "add-ovenlog-form",
  (d) => ({
    ovenLogData: {
      productID: d.get("ol_PartName"),
      inOvenDate: d.get("ol_inOvenDate"),
      firstShift: d.get("ol_1stShift") ? 1 : 0,
      secondShift: d.get("ol_2ndShift") ? 1 : 0,
      thirdShift: d.get("ol_3rdShift") ? 1 : 0,
      inOvenTime: d.get("ol_inOvenTime"),
      inOvenTemp: d.get("ol_inOvenTemp"),
      inOvenInitials: d.get("ol_inOvenInitials"),
      ovenComments: d.get("ol_Comments"),
    },
  }),
  postOvenLog,
  "addOvenLogModal"
);

//update-ovenlog-form
handleFormSubmit(
  "update-ovenlog-form",
  (d) => ({
    ovenlog: {
      ovenLogID: d.get("u_olOvenLogID"),
      productID: d.get("u_olPartName"),
      inOvenDate: d.get("u_olinOvenDate"),
      inOvenTime: d.get("u_olinOvenTime"),
      inOvenTemp: d.get("u_olinOvenTemp"),
      inOvenInitials: d.get("u_olinOvenInitials"),
      outOvenDate: d.get("u_olOutOvenDate"),
      outOvenTime: d.get("u_olOutOvenTime"),
      outOvenTemp: d.get("u_olOutOvenTemp"),
      outOvenInitials: d.get("u_olOutOvenInitials"),
      ovenComments: d.get("u_olComments"),
    },
  }),
  postUpdateOvenLog,
  "updateOvenLogModal"
);

//add-matreceived-form
handleFormSubmit(
  "add-matreceived-form",
  (d) => ({
    matTransData: {
      inventoryID: d.get("mr_matPartNumber"),
      inventoryType: "material",
      inventoryLogID: 0,
      matPartNumber: d.get("mr_matPartNumber"),
      deliveryDate: d.get("mr_matReceivedDate"),
      lbsReceived: d.get("mr_lbsReceived"),
      transType: "received",
      transComment: d.get("mr_Comments"),
    },
  }),
  postMatReceived,
  "addMaterialReceivedModal"
);

//add-pfmreceived-form
handleFormSubmit(
  "add-pfmreceived-form",
  (d) => ({
    pfmReceivedData: {
      partNumber: d.get("pr_partNumber"),
      pfmReceivedDate: d.get("pr_pfmReceivedDate"),
      qtyReceived: d.get("pr_qtysReceived"),
      comments: d.get("pr_Comments"),
    },
  }),
  postPfmReceived,
  "addPfmReceivedModal"
);

setupModalShow("addQARejectsModal", [
  {
    fetchFn: fetchProductList,
    populateFn: populateProductSelect,
    elId: "qaPartName",
    name: "products",
  },
]);

setupModalShow("addLotChangeModal", [
  {
    fetchFn: fetchProductList,
    populateFn: populateProductSelect,
    elId: "lc_PartName",
    name: "products",
  },
  {
    fetchFn: fetchMaterialList,
    populateFn: populateMaterialSelect,
    elId: "lc_MatName",
    name: "materials",
  },
]);

setupModalShow("addOvenLogModal", [
  {
    fetchFn: fetchProductList,
    populateFn: populateProductSelect,
    elId: "ol_PartName",
    name: "products",
  },
]);

setupModalShow("addMaterialReceivedModal", [
  {
    fetchFn: fetchMaterialList,
    populateFn: populateMaterialSelect,
    elId: "mr_matPartNumber",
    name: "materials",
  },
]);

setupModalShow("addPfmReceivedModal", [
  {
    fetchFn: fetchPFMList,
    populateFn: populatePFMSelect,
    elId: "pr_partNumber",
    name: "pfms",
  },
]);

// Initial page load
(async function init() {
  try {
    showLoader();
    const logs = await fetchQualityLogs();
    if (logs) renderTables(logs);
  } catch (err) {
    console.error("Init failed:", err);
    showAlertMessage("Failed to load initial data.", "showAlert", "danger");
  } finally {
    hideLoader();
  }
})();
