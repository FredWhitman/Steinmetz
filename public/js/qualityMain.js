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
  postLotChange,
  postOvenLog,
  postUpdateOvenLog,
  fetchProductList,
  fetchMaterialList,
} from "./qualityApiClient.js";

async function init() {
  //load and render tables for OvenLog and QA Rejects
  const data = await fetchQualityLogs();
  if (data) {
    renderTables(data);
    setupEventListener("qaRejectLogs", "qaRejectsLogs");
    setupEventListener("lotChangeLogs", "lotChangeLogs");
    setupEventListener("ovenLogs", "ovenLogs");
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
        console.log("Raw result:", result);
        const alertData = result;
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
      document.getElementById("showAlert").innerHtml;
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
        const alertData = result;
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

function addOvenLogFormSubmission() {
  //create form value
  const form = document.getElementById("add-ovenlog-form");
  //add listener to form that catches submit event
  form.addEventListener("submit", async (e) => {
    //prevent form submission
    e.preventDefault();
    //check validity of form
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }

    //store form data in array
    const data = new FormData(form);
    //capture form data
    const payload = {
      action: "addOvenLog",
      ovenLogData: {
        productID: data.get("ol_PartName"),
        inOvenDate: data.get("ol_inOvenDate"),
        inOvenTime: data.get("ol_inOvenTime"),
        inOvenTemp: data.get("ol_inOvenTemp"),
        inOvenInitials: data.get("ol_inOvenInitials"),
        ovenComments: data.get("ol_Comments"),
      },
    };
    console.log(JSON.stringify(payload, null, 2));
    try {
      const result = await postOvenLog(payload);
      console.log("RAW result: ", result);
      console.log("Alert HTML: ", result?.html);

      if (result) {
        const alertData = result;
        document.getElementById("showAlert").innerHTML = alertData.html;
        bootstrap.Modal.getInstance(
          document.getElementById("addOvenLogModal")
        ).hide();
      }
      console.log("postOvenLog result: ", result);
      const data = await fetchQualityLogs();
      if (data) {
        renderTables(data);
      }
    } catch (error) {
      console.error("Failed to submit oven log: ", error);
    }
  });
}

function updateOvenLogFormSubmission() {
  const form = document.getElementById("update-ovenlog-form");
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!form.checkValidity()) {
      form.classList.add("was-validated");
      return;
    }
    const data = new FormData(form);
    const payload = {
      action: "updateOvenLog",
      ovenlog: {
        ovenLogID: data.get("u_olOvenLogID"),
        outOvenDate: data.get("u_olOutOvenDate"),
        outOvenTime: data.get("u_olOutOvenTime"),
        outOvenTemp: data.get("u_olOutOvenTemp"),
        outOvenInitials: data.get("u_olOutOvenInitials"),
        ovenComments: data.get("u_olComments"),
      },
    };

    console.log(JSON.stringify(payload, null, 2));
    
    try {
      const result = await postUpdateOvenLog(payload);
      console.log("RAW result: ", result);
      console.log("Alert HTML: ", result?.html);

      if (result) {
        const alertData = result;
        document.getElementById("showAlert").innerHTML = alertData.html;
        bootstrap.Modal.getInstance(
          document.getElementById("updateOvenLogModal")
        ).hide();
      }
      console.log("postUpdateOvenLog result: ", result);
      const data = await fetchQualityLogs();
      if (data) {
        renderTables(data);
      }
    } catch (error) {
      console.error("Failed to update oven log: ", error);
    }
  });
}

// 1) When the modal opens, load options
async function onModalShow() {
  showLoader();
  clearAlert();

  try {
    const response = await fetchProductList();
    //const products = response.products;
    console.log("🧪 products:", response);

    if (!Array.isArray(response)) {
      showAlertMessage("⚠️ Product list failed to load properly.");
      console.error("products", response);
      return;
    }

    const sel = document.getElementById("qaPartName");
    populateProductSelect(sel, response);
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

async function onOvenLogModalShow() {
  //Fill productID select
  showLoader();
  clearAlert();
  try {
    const [products] = await Promise.all([fetchProductList()]);

    if (!Array.isArray(products)) {
      showAlertMessage(
        "⚠️ Product or material lists failed to load properly.",
        "showAlert",
        "danger"
      );
      console.error("products", products);
      return;
    }
    const sel = document.getElementById("ol_PartName");
    populateProductSelect(sel, products);
  } catch (error) {
    console.error(error);
    showAlertMessage("Unable to load product list!", "showAlert", "danger");
  } finally {
    hideLoader();
  }
}

async function onUpdateLotModalShow(){
  showLoader();
  try {
    const [products, materials] = await Promise.all([
      fetchProductList(),
      fetchMaterialList(),
    ]);

    if (!Array.isArray(products) || !Array.isArray(materials)) {
      showAlertMessage(
        "⚠️ Product or material lists failed to load properly.",
        "showAlert",
        "danger"
      );
      console.error("products", products);
      console.error("materials", materials);
      return;
    }  
    const sel = document.getElementById("u_lcPartName");
    const selectEl = document.getElementById("u_lcMatName");
    populateProductSelect(sel, products);
    populateMaterialSelect(selectEl, materials);

  } catch (error) {
    console.error(error);
    showAlertMessage("Unable to load product list!", "showAlert", "danger");
  }finally{
    hideLoader();
  }
}
document.addEventListener("DOMContentLoaded", () => {
  // Load data when modal shows
  const addModalEl = document.getElementById("addQARejectsModal");
  addModalEl.addEventListener("show.bs.modal", onModalShow);

  const addLotModalEl = document.getElementById("addLotChangeModal");
  addLotModalEl.addEventListener("show.bs.modal", onLotChangeModalShow);

  const addOvenLogModalEl = document.getElementById("addOvenLogModal");
  addOvenLogModalEl.addEventListener("show.bs.modal", onOvenLogModalShow);

  const updateLotModal = document.getElementById("updateLotChangeModal");
  updateLotModal.addEventListener("show.bs.modal", onUpdateLotModalShow);


  addQaRejectsFormSubmision();
  addLotChangeFormSubmission();
  addOvenLogFormSubmission();
  updateOvenLogFormSubmission();
});
