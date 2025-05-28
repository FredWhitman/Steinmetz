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

//Edit User Ajax request
//This code gets the id from the dynamic created table.

tbody.addEventListener("click",(e) => {

  if(e.target && e.target.matches("a.viewLink")){
    e.preventDefault();
    //find the data-id class tag in dynamiclly created table from productionAction.php get-read check
    let id = e.target.closest("tr").getAttribute("data-id");
    console.log("Production log ID: " + id);
    viewUser(id);
  }
});

const viewUser = async (id)=>{
  //
  const data = await fetch(`../src/Classes/productionActions.php?view=1&id=${id}`,{
    method: 'GET'
  });
  const response = await data.json();
  console.log("Fetched Data for logID:", response); // Debugging outp
  console.log("Available Data Keys:", Object.keys(response));

  if(response.error){
    console.log("Error in viewUser:" + response.error);
  }else{
    const modal = new bootstrap.Modal(document.getElementById("viewProductionModal"));
    modal.show(); //Ensures modal is open first

  
    setTimeout(()=> { //delay assigning values
      document.getElementById("logID").value = response.logID;
      document.getElementById("vpartName").value = response.productID;
      document.getElementById("vlogDate").value = response.prodDate;
      document.getElementById("vprodRun").value = response.runStatus;
      document.getElementById("vMat1Name").value = response.mat1;
      document.getElementById("vhop1Lbs").value = response.matUsed1;
      document.getElementById("vMat2Name").value = response.mat2;
      document.getElementById("vhop2Lbs").value = response.matUsed2;
      document.getElementById("vMat3Name").value = response.mat3;
      if(response.matUsed3 === null){
          document.getElementById("vhop3Lbs").value =  0;
      }else{
        document.getElementById("vhop3Lbs").value =  response.matUsed3;
      };
      document.getElementById("vMat4Name").value = response.mat4;
      if(response.matUsed4 === null){
          document.getElementById("vhop4Lbs").value =  0;
      }else{
        document.getElementById("vhop4Lbs").value =  response.matUsed4;
      };
      let total = response.matUsed1+response.matUsed2+response.matUsed3+response.matUsed4;
      document.getElementById("vBlenderTotals");
    },500); //wait for modal elements to load
  }
  
}


 //columns returned: logID,productID,prodDate,runStatus,preProdLogID,runLogID,matLogID,tempLogID,pressCounter,startUpRejects,purgeLbs,
        //  Comments, bigDryerTemp,bigDryerDew,pressDryerTemp,pressDryerDew,t1,t2,t3,t4,m1,m2,m3,m4,m5,m6,m7,chillerTemp,moldTemp,mat1,matUsed1,mat2,
        //  matUsed2,mat3,matUsed3,mat4,matUsed4,