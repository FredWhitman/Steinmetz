//FILE /js/qualityMain.js

import {
  renderTables,
  setupEventListener,
  showAlertMessage,
  clearAlert,
  populateProductSelect,
  populateMaterialSelect,
  showLoader,
  hideLoader,
} from "./qualityUiManager.js";

import {
  fetchQualityLogs,
  postQaRejects,
  fetchProductList,
  fetchMaterialList,
} from "./qualityApiClient.js";

async function init() {
  //load and render tables for OvenLog and QA Rejects
  const data = await fetchQualityLogs();
  if (data) {
    renderTables(data);
    setupEventListener("qaRejectLogs", "qaRejectsLogs");
    setupEventListener("lotChangeLogs", "lotchangelogs");
    setupEventListener("ovenLogs", "ovenlogs");
  }
}

init();

function addQaRejectsFormSubmision() {
  const form = document.getElementById("add-qaReject-form");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const data = new FormData(form);
    const payload = {
      action: "addQaRejects",
      qaRejectData: {
        prodDate: data.get("qaLogDate"),
        productID: data.get("qaPart"),
        rejects: data.get("rejects"),
        comments: data.get("qaComments"),
      },
    };

    try {
      const result = await postQaRejects(payload);

      if (result) {
        const alertData = JSON.parse(result.message);
        document.getElementById("showAlert").innerHTML = alertData.html;
        bootstrap.Modal.getInstance(
          document.getElementById("addQARejectsModal")
        ).hide();
      }
      console.log("postQaRejects result: ", result);
      const data = await fetchQualityLogs();
      if (data) {
        renderTables(data);
      }
    } catch (error) {
      console.error("Failed to submit QA Rejects:", error);
    }
  });
}

function addLotChangeFormSubmission() {
  const form = document.getElementById("add-lotchange-form");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    const data = new FormData(form);
    const payload = {
      action: "addLotChange",
      lotChangeData: {
        productID: data.get("lc_PartName"),
        MaterialName: data.get("lc_MatName"),
        ChangeDate: data.get("lc_LotDate"),
        ChangeTime: data.get("lc_LotTime"),
        OldLot: data.get("lc_OldLot"),
        NewLot: data.get("lc_NewLot"),
        Comments: data.get("lc_Comments"),
      },
    };

    try {
      const result = await postLotChange(payload);
      if (result) {
        const alertData = JSON.parse(result.message);
        document.getElementById("showAlert").innerHTML = alertData.html;
        bootstrap.Modal.getInstance(
          document.getElementById("addLotChangeModal")
        ).hide();
      }

      console.log("postLotChange result: ", result);
      const data = await fetchQualityLogs();
      if (data) {
        renderTables(data);
      }
    } catch (error) {
      console.error("Failed to submit Lot Change:", error);
    }
  });
}
// 1) When the modal opens, load options
async function onModalShow() {
  showLoader();
  clearAlert();

  try {
    const response = await fetchProductList();
    const products = response.products;
    console.log("🧪 products:", products);

    if (!Array.isArray(products)) {
      showAlertMessage("⚠️ Product list failed to load properly.");
      console.error("products", products);
      return;
    }

    populateProductSelect(products);
  } catch (err) {
    console.error(err);
    showAlertMessage("Unable to load products or materials.");
  } finally {
    hideLoader();
  }
}
//fill selects with list of products and materials
async function onLotChangeModalShow() {
  showLoader();
  clearAlert();

  try {
    const [products, materials] = await Promise.all([
      fetchProductList(),
      fetchMaterialList(),
    ]);

    if (!Array.isArray(products) || !Array.isArray(materials)) {
      showAlertMessage("⚠️ Product or material lists failed to load properly.");
      console.error("products", products);
      console.error("material", materials);
      return;
    }

    const sel = document.getElementById("lc_PartName");
    populateProductSelect(sel, products);
    populateMaterialSelect(materials);
  } catch (err) {
    console.error(err);
    showAlertMessage("Unable to load products or materials.");
  } finally {
    hideLoader();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Load data when modal shows
  const addModalEl = document.getElementById("addQARejectsModal");
  addModalEl.addEventListener("show.bs.modal", onModalShow);

  const addLotModalEl = document.getElementById("addLotChangeModal");
  addLotModalEl.addEventListener("show.bs.modal", onLotChangeModalShow);

  addQaRejectsFormSubmision();
  addLotChangeFormSubmission();
});
