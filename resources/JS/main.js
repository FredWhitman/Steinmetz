const tbody =  document.querySelector('tbody'); //Set the tbody to display last 4 weeks of production


document.addEventListener("DOMContentLoaded", function () {
  
  const modal = document.getElementById("addProductionModal");//set variable for modal webform
  

  //add listener to modal to trigger function when displayed

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

        let materialSelects = ["Mat1Name", "Mat2Name", "Mat3Name", "Mat4Name"];
        let partSelect = ["partName"];

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
            //select.dispatchEvent(new Event("change"));
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
              option.style.fontSize = "0.75rem"; ///change font size of dropdown
              select.appendChild(option);
            });
           fetchLast4Weeks(); 
          } else {
            console.warn(`Select element '${selectId}' not found.`);
          }
        });
      })
      .catch((error) => console.error("Error fetching data:", error));
  });
});

//Fetch ALl users Ajax request
const fetchLast4Weeks = async()=>{
  const data = await fetch('../src/Classes/productionActions.php?read4wks=1',{
    method: "GET",
  });

  console.log('fetchLast4Weeks Ajax called!');
  const response = await data.text();
  tbody.innerHTML = response;
};

fetchLast4Weeks();




