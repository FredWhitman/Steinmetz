const addQARejectForm = document.getElementById("add-qaReject-form");
const addQARejectModal = new bootstrap.Modal(document.getElementById("addQARejectsModal"));
const showAlert = document.getElementById("showAlert");

document.addEventListener("DOMContentLoaded", function () {
  //console.log("Triggering fetchLast4Weeks from qaRejects.js.......");
  //console.log("Checking for fetchLast4Weeks:", typeof window.fetchLast4Weeks);
  if(typeof window.fetchLast4Weeks === 'function'){
    window.fetchLast4Weeks(); //Call function from main.js
  }else{
    console.error("Error: fetchLast4Weeks is not defined!");
  }


  const modal = document.getElementById("addQARejectsModal"); //set variable for modal webform

  //add listener to modal to trigger when displayed
  // This function will display the add Log form and fetch data from the db to fill the select options for material
  //and part name.
  modal.addEventListener("shown.bs.modal", function () {
    
      // Runs when modal is visible
      fetch("../src/classes/fetch_data.php")
        .then((response) => response.json())
        .then((data) => {
          
          //console.log("Fetched Data: ", data); //checking for data
          //console.log("Fetched Part Names:", data.partNames);
          
          if (!data.materials || !data.partNames) {
            console.error("Error: Json response missing expect keys.");
            return; //stop execution
          }
          
          //console.log("Checking data before population:", JSON.stringify(data.partNames, null, 2));

          //Populate PartName Select component
          let select = document.getElementById("qaPartName");

          //console.log(`Checking existence of ${select}: `, select);

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
              //option.style.fontSize = "0.75rem"; ///change font size of dropdown
              select.appendChild(option);

             // console.log("Added Option:", option);
            });
          } else {
            console.warn(`Select element '${select}' not found.`);
          }
        })
        .catch((error) => console.error("Error fetching data:", error));
  });
});


//add qa rejects Ajax Request
addQARejectForm.addEventListener("submit", async (e) => {
  //prevent form from submitting data to DB
  e.preventDefault();
  //console.log("Add button clicked:");

  const formData = new FormData(addQARejectForm);
  formData.append("qaRejects", 1);

  //check to make sure the input fields are not empty
  if (!addQARejectForm.checkValidity()) {
    //e.preventDefault();
    e.stopPropagation();
    addQARejectForm.classList.add("was-validated");
    return false;
  } 
  
    document.getElementById("add-qaReject-btn").value = "Please Wait...";
    const data = await fetch("../src/Classes/productionActions.php", {
      method: "POST",
      body: formData,
    });

    const response = await data.text();
    //console.log("Server Response: ", response); //Debug ouput

    showAlert.innerHTML = response;
    document.getElementById("add-qaReject-btn").value = "Add Rejects";
    addQARejectForm.reset();
    addQARejectForm.classList.remove("was-validated");
    addQARejectModal.hide();


    //calling fetchLast4Weeks inside main.js
    setTimeout(() =>{
      //console.log("Refreshing last 4 weeks data...");
      window.fetchLast4Weeks();
    },500);
    
    //console.log("Checking Table Before Refresh:", document.getElementById("weeks").innerHTML);

    
  }
);

