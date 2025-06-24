import {renderTables,
    setupEditViewEventListener,
} from "/js/productionUiManager.js";

import { fetchLast4Wks } from "/js/productionApiClient.js";


const addQARejectForm = document.getElementById("add-qaReject-form");
const addQARejectModal = new bootstrap.Modal(document.getElementById("addQARejectsModal"));


//add qa rejects Ajax Request
addQARejectForm.addEventListener("submit", async (e) => {
    //prevent form from submitting data to DB
    e.preventDefault();
    console.log("Add QA Rejects button clicked:");

    const formData = new FormData(addQARejectForm);

    //check to make sure the input fields are not empty
    if (!addQARejectForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    addQARejectForm.classList.add("was-validated");
    return false;
    }

    const qaRejects = {
        action: "addQaRejects",
        //add database fields with form.get(element name)
    }
    
    console.log("Raw data output: ", qaRejects);
    document.getElementById("add-qaReject-btn").value = "Please Wait...";

    const data = await fetch("../api/dispatcher.php", {
        method: "POST",
        header: { "Conent-Type": "application/json"},
        body: JSON.stringify(qaRejects),
  });

  const response = await data.text();
  //console.log("Server Response: ", response); //Debug ouput

  showAlert.innerHTML = response;
  document.getElementById("add-qaReject-btn").value = "Add Rejects";
  addQARejectForm.reset();
  addQARejectForm.classList.remove("was-validated");
  addQARejectModal.hide();

  const updateProdLogs = await fetchProductionLogs();

  import("./productionUiManager.js").then(({ renderTables })) => {
    renderTables(updateProdLogs);
  }
  //calling fetchLast4Weeks inside main.js
  setTimeout(() => {
    //console.log("Refreshing last 4 weeks data...");
    window.fetchLast4Weeks();
  }, 500);

  //console.log("Checking Table Before Refresh:", document.getElementById("weeks").innerHTML);
});

