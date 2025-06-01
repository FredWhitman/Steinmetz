const addPurgeForm = document.getElementById("add-purge-form");
const modal = document.getElementById("addPurgeModal"); //set variable for modal webform
const addPurgeModal = new bootstrap.Modal(document.getElementById("addPurgeModal"));

document.addEventListener("DOMContentLoaded", function () 
{
  if(typeof window.fetchLast4Weeks === 'function'){
    window.fetchLast4Weeks(); //Call function from main.js
  }else{
    console.error("Error: fetchLast4Weeks is not defined!");
  }
  modal.addEventListener("shown.bs.modal", function () {
    
      // Runs when modal is visible
      fetch("../src/classes/fetch_data.php")
        .then((response) => response.json())
        .then((data) => {
          
          if (!data.materials || !data.partNames) {
            console.error("Error: Json response missing expect keys.");
            return; //stop execution
          }
          
          //Populate PartName Select component
          let select = document.getElementById("p_PartName");

            if (select) 
            {
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


//add purge Ajax Request
addPurgeForm.addEventListener("submit", async (e) => 
{
    //prevent form from submitting data to DB
    e.preventDefault();
    console.log("Add button clicked:");
    const formData = new FormData(addPurgeForm);
    formData.append("purge", 1);

        //check to make sure the input fields are not empty
        if (!addPurgeForm.checkValidity()) 
        {
            //e.preventDefault();
            e.stopPropagation();
            addPurgeForm.classList.add("was-validated");
            return false;
        } 
  
        document.getElementById("add-purge-btn").value = "Please Wait...";
        const data = await fetch("../src/Classes/productionActions.php", {
        method: "POST",
        body: formData,
        });

        const response = await data.text();
        //console.log("Server Response: ", response); //Debug ouput

        showAlert.innerHTML = response;
        document.getElementById("add-purge-btn").value = "Add Purge";
        addPurgeForm.reset();
        addPurgeForm.classList.remove("was-validated");
        addPurgeModal.hide();


        //calling fetchLast4Weeks inside main.js
        setTimeout(() =>{
        //console.log("Refreshing last 4 weeks data...");
        window.fetchLast4Weeks();
        },500);
    
    //console.log("Checking Table Before Refresh:", document.getElementById("weeks").innerHTML);
})

