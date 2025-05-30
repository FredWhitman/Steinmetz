document.addEventListener("DOMContentLoaded", function () {
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
