//FILE /js/qualityMain.js

import { renderTables, setupEventListener } from "./qualityUiManager.js";

import { fetchQualityLogs, postQaRejects } from "./qualityApiClient.js";

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
        //db column: data.get("element id"),
        prodDate: data.get(""),
        productID: data.get(""),
        rejects: data.get(""),
        comments: data.get(""),
      },
    };

    try {
      const result = await postQaRejects(payload);
      if (result) {
        bootstrap.Modal.getInstance(
          document.getElementById("addQARejectsModal")
        ).hide();
      }
      console.log("postQaRejects result: ".result);
      const data = await fetchQualityLogs();
      if (data) {
        renderTables(data);
      }
    } catch (error) {
      console.error("Failed to submit QA Rejects:", error);
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  // Load data when modal shows
  const addModalEl = document.getElementById("addQARejectsModal");
  addModalEl.addEventListener("show.bs.modal", onModalShow);

  addQaRejectsFormSubmision();
});
