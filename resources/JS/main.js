const tbody = document.querySelector("tbody"); //Set the tbody to display last 4 weeks of production

document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("addProductionModal"); //set variable for modal webform

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
              option.value = item.MaterialName;
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

//Fetch last 4 weeks of production logs Ajax request
window.fetchLast4Weeks = async function () {
  //console.log("fetchLast4Weeks() is being called");
  const data = await fetch("../src/classes/productionActions.php?read4wks=1", {
    method: "GET",
  });

  const response = await data.text();
  //console.log("Fetched Data:", response); // Debugging output
  document.getElementById("weeks").innerHTML = response;
};

fetchLast4Weeks();

//View log Ajax request
//This code gets the id from the dynamic created table.

tbody.addEventListener("click", (e) => {
  if (e.target && e.target.matches("a.viewLink")) {
    e.preventDefault();
    //find the data-id class tag in dynamiclly created table from productionAction.php get-read check
    let id = e.target.closest("tr").getAttribute("data-id");
    //console.log("Production log ID: " + id);
    viewLog(id);
  }
});

const viewLog = async (id) => {
  //
  const data = await fetch(
    `../src/Classes/productionActions.php?view=1&id=${id}`,
    {
      method: "GET",
    }
  );

  const response = await data.json();
  //console.log("Fetched Data for logID:", response); // Debugging outp
  //console.log("Available Data Keys:", Object.keys(response));

  if (response.error) {
    console.log("Error in viewLog:" + response.error);
  } else {
    const modal = new bootstrap.Modal(
      document.getElementById("viewProductionModal")
    );
    modal.show(); //Ensures modal is open first

    setTimeout(() => {
      //delay assigning values
      //Blender Info
      document.getElementById("logID").value = response.logID;
      document.getElementById("vpartName").value = response.productID;
      document.getElementById("vlogDate").value = response.prodDate;
      document.getElementById("vprodRun").value = response.runStatus;
      document.getElementById("vMat1Name").value = response.mat1;
      document.getElementById("vhop1Lbs").value = response.matUsed1;
      document.getElementById("vMat2Name").value = response.mat2;
      document.getElementById("vhop2Lbs").value = response.matUsed2;
      document.getElementById("vMat3Name").value = response.mat3;
      response.matUsed3 === null
        ? (document.getElementById("vhop3Lbs").value = 0)
        : (document.getElementById("vhop3Lbs").value = response.matUsed3);
      document.getElementById("vMat4Name").value = response.mat4;
      response.matUsed4 === null
        ? (document.getElementById("vhop4Lbs").value = 0)
        : (document.getElementById("vhop4Lbs").value = response.matUsed4);

      let total =
        response.matUsed1 +
        response.matUsed2 +
        response.matUsed3 +
        response.matUsed4;
      //console.log("Blender total: " + total);
      document.getElementById("vBlenderTotals").value = total;

      //Dryer Info//
      document.getElementById("vbigDryerTemp").value = response.bigDryerTemp;
      document.getElementById("vbigDryerDew").value = response.bigDryerDew;
      document.getElementById("vPressDryerTemp").value =
        response.pressDryerTemp;
      document.getElementById("vPressDryerDew").value = response.pressDryerDew;

      //Cooling and Hotrunner info
      document.getElementById("vChiller").value = response.chillerTemp;
      document.getElementById("vTCU").value = response.moldTemp;
      document.getElementById("vT1").value = response.t1;
      document.getElementById("vT2").value = response.t2;
      document.getElementById("vT3").value = response.t3;
      document.getElementById("vT4").value = response.t4;
      document.getElementById("vM1").value = response.m1;
      document.getElementById("vM2").value = response.m2;
      document.getElementById("vM3").value = response.m3;
      document.getElementById("vM4").value = response.m4;
      document.getElementById("vM5").value = response.m5;
      document.getElementById("vM6").value = response.m6;
      document.getElementById("vM7").value = response.m7;

      //Produced Parts and startup Rejects comments
      document.getElementById("vPressCounter").value = response.pressCounter;
      document.getElementById("vPressRejects").value = response.startUpRejects;
      document.getElementById("vcommentText").value = response.Comments;
      let prevID = response.prevProdLogID;

      getLog(prevID);
    }, 500); //wait for modal elements to load
  }
};

const getLog = async (id) => {
  const data = await fetch(
    `../src/Classes/productionActions.php?previous=1&id=${id}`,
    {
      method: "GET",
    }
  );
  const response = await data.json();
  //console.log("Fetched Data for previous log:", response);
  //current values of the hopppers
  let cMat1Used = parseFloat(document.getElementById("vhop1Lbs").value) || 0;
  let cMat2Used = parseFloat(document.getElementById("vhop2Lbs").value) || 0;
  let cMat3Used = parseFloat(document.getElementById("vhop3Lbs").value) || 0;
  let cMat4Used = parseFloat(document.getElementById("vhop4Lbs").value) || 0;
  //console.log("Current Hopper 1 Used:", cMat1Used);

  //previous log in the production run's hoppers
  let pMat1Used = parseFloat(response.matUsed1) || 0;
  let pMat2Used = parseFloat(response.matUsed2) || 0;
  let pMat3Used = parseFloat(response.matUsed3) || 0;
  let pMat4Used = parseFloat(response.matUsed4) || 0;
  console.log("Previous Hopper 1 Used:", pMat1Used);

  //getting daily usage
  let dMat1Used = parseFloat(cMat1Used - pMat1Used) || 0;
  let dMat2Used = parseFloat(cMat2Used - pMat2Used) || 0;
  let dMat3Used = parseFloat(cMat3Used - pMat3Used) || 0;
  let dMat4Used = parseFloat(cMat4Used - pMat4Used) || 0;
  let dTotal = dMat1Used + dMat2Used + dMat3Used + dMat4Used;

  ///calculate percentage
  let pHop1 = (dMat1Used / dTotal) * 100;
  let pHop2 = (dMat2Used / dTotal) * 100;
  let pHop3 = (dMat3Used / dTotal) * 100;
  let pHop4 = (dMat4Used / dTotal) * 100;
  let pTotal = pHop1 + pHop2 + pHop3 + pHop4;

  //Add data to elements
  document.getElementById("vdHop1").value = dMat1Used.toFixed(3);
  document.getElementById("vdHop1p").value = pHop1.toFixed(2);
  document.getElementById("vdHop2").value = dMat2Used.toFixed(3);
  document.getElementById("vdHop2p").value = pHop2.toFixed(2);
  document.getElementById("vdHop3").value = dMat3Used.toFixed(3);
  document.getElementById("vdHop3p").value = pHop3.toFixed(2);
  document.getElementById("vdHop4").value = dMat4Used.toFixed(3);
  document.getElementById("vdHop4p").value = pHop4.toFixed(2);
  document.getElementById("vdTotal").value = dTotal.toFixed(3);
  document.getElementById("vdTotalp").value = pTotal.toFixed(2);
};
