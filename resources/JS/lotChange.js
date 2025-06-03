const addLotChangeForm = document.getElementById("add-lotchange-form");
const addLotChangeModal = new bootstrap.Modal(
  document.getElementById("addLotChangeModal")
);

document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("addLotChangeModal"); //set variable for modal webform
  //add listener to modal to trigger when displayed
  // This function will display the add Log form and fetch data from the db to fill the select options for material
  //and part name.
  modal.addEventListener("shown.bs.modal", function () {
    // Runs when modal is visible
    fetch("../src/classes/fetch_data.php")
      .then((response) => response.json())
      .then((data) => {
        //console.log("Fetched Data: ", data); //checking for data

        if (!data.materials || !data.partNames) {
          console.error("Error: Json response missing expect keys.");
          return; //stop execution
        }

        //console.log("Materials Array: ", data.materials);
        //console.log("PartNames Array: ", data.partNames);

        let materialSelects = ["lcMatName"];
        let partSelect = ["lcPartName"];

        materialSelects.forEach((selectId) => {
          let select = document.getElementById(selectId);
          //console.log("Checking existence of ${selectId}: ", select);

          if (select) {
            // Ensure select exists before modifying
            //console.log("Populating Material Select: ", selectId); //check to make sure select components actually exist
            select.innerHTML = ""; // Clear existing options

            let blankOption = document.createElement("option");
            blankOption.value = ""; //empty value
            blankOption.textContent = "-- Select Material --"; //placeholder
            select.appendChild(blankOption);

            data.materials.forEach((item) => {
              //console.log(`Adding Option: ${item.MaterialName}`);
              let option = document.createElement("option");
              option.value = item.MaterialPartNumber;
              option.textContent = item.MaterialName;
              select.appendChild(option);
            });
          } else {
            console.warn(`Select element '${selectId}' not found.`);
          }
        });
        //Populate PartName Select component
        partSelect.forEach((selectId) => {
          let select = document.getElementById(selectId);
          if (select) {
            //console.log("Populating Part select: ", selectId);
            select.innerHTML = ""; //make sure its empty

            //create blank entry
            let blankOption = document.createElement("option");
            blankOption.value = "";
            blankOption.textContent = "-- Select Part Name --";
            select.appendChild(blankOption);

            data.partNames.forEach((item) => {
              let option = document.createElement("option");
              option.value = item.ProductID;
              option.textContent = item.PartName;
              select.appendChild(option);
            });
          } else {
            console.warn(`Select element '${selectId}' not found.`);
          }
        });
      })
      .catch((error) => console.error("Error fetching data:", error));
  });
});

addLotChangeForm.addEventListener("submit", async (e) => {
  //prevent form from submitting data to DB
  e.preventDefault();

  const formData = new FormData(addLotChangeForm);
  formData.append("lotChange", 1);

  //check to make sure the input fields are not empty
  if (!addLotChangeForm.checkValidity()) {
    //e.preventDefault();
    e.stopPropagation();
    addLotChangeForm.classList.add("was-validated");
    return false;
  }

  document.getElementById("add-lotchange-btn").value = "Please Wait...";
  const data = await fetch("../src/Classes/productionActions.php", {
    method: "POST",
    body: formData,
  });

  const response = await data.text();
  //console.log("Server Response: ", response); //Debug ouput

  showAlert.innerHTML = response;
  document.getElementById("add-lotchange-btn").value = "Add Lot Change";
  addLotChangeForm.reset();
  addLotChangeForm.classList.remove("was-validated");
  addLotChangeModal.hide();

  //calling fetchLast4Weeks inside main.js
  setTimeout(() => {
    //console.log("Refreshing last 4 weeks data...");
    window.fetchLast4Weeks();
  }, 500);
});
