const addLogForm = document.getElementById("add-productionLog-form");
//const showAlert = document.getElementById("showAlert");
const addLogModal = new bootstrap.Modal(
  document.getElementById("addProductionModal")
);

//Function to total the lbs of material in hoppers 1-4 and display that value the Blender Total input
document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("addProductionModal");
  modal.addEventListener("shown.bs.modal", function () {
    addBlenderOnBlur(); //Call the addBlenderOnBlur function when modal is displayed.
  });
});

//add New User Ajax Request
addLogForm.addEventListener("submit", async (e) => {
  //prevent form from submitting data to DB
  e.preventDefault();
  console.log("Production log submit button has been clicked!");
  const formData = new FormData(addLogForm);
  //formData.append("addLog", 1);
  //check to make sure the input fields are not empty
  if (!addLogForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    addLogForm.classList.add("was-validated");
    return false;
  } else {
    document.getElementById("add-log-btn").value = "Please Wait...";
    const logInfo = {
      action: "addLog",
      prodLogData: {
        productID: formData.get("partName"),
        prodDate: formData.get("logDate"),
        runStatus: formData.get("prodRun"),
        prevProdLogID: "0",
        runLogID: "0",
        matLogID: "0",
        tempLogID: "0",
        pressCounter: formData.get("pressCounter"),
        startUpRejects: formData.get("startUpRejects"),
        qaRejects: "0",
        purgeLbs: "0",
        Comments: formData.get("commentText"),
      },
      materialData: {
        prodLogID: "0",
        mat1: formData.get("Mat1Name"),
        matUsed1: formData.get("hop1Lbs"),
        mat2: formData.get("Mat2Name"),
        matUsed2: formData.get("hop2Lbs"),
        mat3: formData.get("Mat3Name"),
        matUsed3: formData.get("hop3Lbs"),
        mat4: formData.get("Mat4Name"),
        matUsed4: formData.get("hop4Lbs"),
      },
      tempData: {
        prodLogID: "0",
        bigDryerTemp: formData.get("bigDryerTemp"),
        bigDryerDew: formData.get("bigDryerDew"),
        pressDryerTemp: formData.get("pressDryerTemp"),
        pressDryerDew: formData.get("pressDryerDew"),
        t1: formData.get("t1"),
        t2: formData.get("t2"),
        t3: formData.get("t3"),
        t4: formData.get("t4"),
        m1: formData.get("m1"),
        m2: formData.get("m2"),
        m3: formData.get("m3"),
        m4: formData.get("m4"),
        m5: formData.get("m5"),
        m6: formData.get("m6"),
        m7: formData.get("m7"),
        chillerTemp: formData.get("chiller"),
        moldTemp: formData.get("tcuTemp"),
      },
    };

    const data = await fetch("../src/Classes/productionActions.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(logInfo),
    });

    const response = await data.text();
    showAlert.innerHTML = response;
    document.getElementById("add-log-btn").value = "Add Log";
    addLogForm.reset();
    addLogForm.classList.remove("was-validated");
    addLogModal.hide();
    //calling fetchLast4Weeks inside main.js
    setTimeout(() => {
      //console.log("Refreshing last 4 weeks data...");
      window.fetchLast4Weeks();
    }, 500);
  }
});

//Function to total the lbs of material in hoppers 1-4 and display that value the Blender Total input
function addBlenderOnBlur() {
  var prodRunStatus = document.getElementsByTagName("prodRun");
  let prodStatus = [];

  // Select all radio buttons within the group
  const radioButtons = document.querySelectorAll('input[name="prodRun"]');
  console.log("Radio Button group: " + radioButtons);
  // Loop through the radio buttons and add an event listener to each one
  radioButtons.forEach((radio) => {
    radio.addEventListener("change", function () {
      console.log(`Selected value: ${this.value}`);
      prodStatus = this.value;
    });
  });

  //set const for each hopper and assign it a value the textbox for each hopper
  const hop1 = document.getElementById("hop1Lbs");
  const hop2 = document.getElementById("hop2Lbs");
  const hop3 = document.getElementById("hop3Lbs");
  const hop4 = document.getElementById("hop4Lbs");
  const totalBlended = document.getElementById("BlenderTotals");

  //create a listener on hopper 4 input and trigger script when it no longer has focus
  hop4Lbs.addEventListener("blur", async function () {
    const productID = document.getElementById("partName").value;

    //Convert values to numbers and add them up.
    const sum =
      (Number(hop1.value) || 0) +
      (Number(hop2.value) || 0) +
      (Number(hop3.value) || 0) +
      (Number(hop4.value) || 0);
    totalBlended.value = sum;

    const prodDate = document.getElementById("logDate").value;
    console.log(
      "Production Run Status: " +
        prodStatus +
        " Part Name: " +
        productID +
        " Production Date: " +
        prodDate
    );

    /* Check prodStatus and either copy hopper data to daily hopper data or pull previous log
    and substract current material info from previous log and add to daily usage */
    switch (prodStatus) {
      case "0":
        console.log(
          "Fetching materialLog data from previous log in production for in-process production log"
        );
        console.log("In Progress production log!");
        actionType = "getLastLog";
        break;
      case "2":
        console.log(
          "Fetching materialLog data from previous log in production for end production log"
        );
        console.log("End production log!");
        actionType = "endRun";
        break;
      case "1": //Start Production Run
        console.log("Start production run");

        doStartDailyUsage(
          Number(hop1.value),
          Number(hop2.value),
          Number(hop3.value),
          Number(hop4.value)
        );

        const dH1 = document.getElementById("dHop1").value;
        const dH2 = document.getElementById("dHop2").value;
        const dH3 = document.getElementById("dHop3").value;
        const dH4 = document.getElementById("dHop4").value;
        const dT = document.getElementById("dTotal").value;
        //set the value of a hidden inputbox for later use
        const status = document.getElementById("prodStatus").value;
        status.value = 1;

        dH1.value = Number(hop1.value);
        dH2.value = Number(hop2.value);
        dH3.value = hop3.value;
        dH4.value = hop4.value;
        const dSum =
          (Number(dH1.value) || 0) +
          (Number(dH2.value) || 0) +
          (Number(dH3.value) || 0) +
          (Number(dH4.value) || 0);
        dT.value = validateTotals(dSum);

        console.log("Daily Sum: " + dSum);
        break;
    }

    if (!actionType) return;
    const logData = await fetchPreviousMatLogs(actionType, productID);
    if (!logData) {
      console.error("Failed to retrieve log data.");
      return;
    }

    //extract material data usage values
    const preMatUsed1 = parseFloat(logData.matUsed1) || 0;
    const preMatUsed2 = parseFloat(logData.matUsed2) || 0;
    const preMatUsed3 = parseFloat(logData.matUsed3) || 0;
    const preMatUsed4 = parseFloat(logData.matUsed4) || 0;
    //Calculate daily usage dynamically
    const dailyHop1 = parseFloat(hop1.value) - preMatUsed1;
    const dailyHop2 = parseFloat(hop1.value) - preMatUsed2;
    const dailyHop3 = parseFloat(hop1.value) - preMatUsed3;
    const dailyHop4 = parseFloat(hop1.value) - preMatUsed4;

    if(prodRun === 0 || prodRun === 2){
      //in progress and end of run
      getAndSetDailyUsage(hop1,hop2,hop3,hop4,prevHop1,prevHop4,prevHop4,prevHop4);    
    }else{
      //start
      getAndSetDailyUsage(hop1,hop2,hop3,hop4,0,0,0,0)
    }
    
    console.log(
      `Updated values: preMatUsed1 = ${preMatUsed1}, preMatUsed2 = ${preMatUsed2}, preMatUsed3 = ${preMatUsed3}, preMatUsed4 = ${preMatUsed4}`
    );
  });

  /* // get Production run id, use that to get previsou production log id.
        fetch(
          `../src/classes/productionActions.php?getLastLog=1&productID=${productID}`
        )
          .then((response) => response.text()) //Get raw data
          .then((data) => {
            console.log("Raw reponse from server: ", data); // Debugging output

            //remove any leading numbers or invalid characters before parsing
            const validJson = data.trim().replace(/^[^{]+/, "");

            try {
              const jsonData = JSON.parse(validJson);
              console.log("Parsed JSON: ", jsonData);

              if (!data || data.error) {
                console.error("Error fetching previous log:", data.error);
                return;
              }

              console.log("matUsed1 type: ", typeof jsonData.matUsed1);
              console.log("matUsed1 value before parsing: ", jsonData.matUsed1);

              //subtract current hopper values from previous log and update dHop1 thru 4 values
              const preMatUsed1 =
                jsonData.matUsed1 !== undefined
                  ? parseFloat(jsonData.matUsed1)
                  : null;
              const preMatUsed2 =
                jsonData.matUsed2 !== undefined
                  ? parseFloat(jsonData.matUsed2)
                  : null;
              const preMatUsed3 =
                jsonData.matUsed3 !== undefined
                  ? parseFloat(jsonData.matUsed3)
                  : null;
              const preMatUsed4 =
                jsonData.matUsed4 !== undefined
                  ? parseFloat(jsonData.matUsed4)
                  : null;

              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed1 = : " +
                  preMatUsed1
              );
              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed2 = : " +
                  preMatUsed2
              );
              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed3 = : " +
                  preMatUsed3
              );
              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed4 = : " +
                  preMatUsed4
              );

              let dailyHop1 = parseFloat(hop1.value) - parseFloat(preMatUsed1);
              let dailyHop2 = parseFloat(hop2.value) - parseFloat(preMatUsed2);
              let dailyHop3 = parseFloat(hop3.value) - parseFloat(preMatUsed3);
              let dailyHop4 = parseFloat(hop4.value) - parseFloat(preMatUsed4);

              doStartDailyUsage(
                Number(dailyHop1),
                Number(dailyHop2),
                Number(dailyHop3),
                Number(dailyHop4)
              );
            } catch (error) {
              console.error(
                "prodLogSumbit->Invalid JSON format response:",
                error
              );
            }
          })
          .catch((error) => console.error("Error fetching last log:", error));
        break; */

  /* case "1": //Start Production Run
        console.log("Start production run");
        doStartDailyUsage(
          Number(hop1.value),
          Number(hop2.value),
          Number(hop3.value),
          Number(hop4.value)
        );
        const dH1 = document.getElementById("dHop1").value;
        const dH2 = document.getElementById("dHop2").value;
        const dH3 = document.getElementById("dHop3").value;
        const dH4 = document.getElementById("dHop4").value;
        const dT = document.getElementById("dTotal").value;
        //set the value of a hidden inputbox for later use
        const status = document.getElementById("prodStatus").value;
        status.value = 1;

        dH1.value = Number(hop1.value);
        dH2.value = Number(hop2.value);
        dH3.value = hop3.value;
        dH4.value = hop4.value;
        const dSum =
          (Number(dH1.value) || 0) +
          (Number(dH2.value) || 0) +
          (Number(dH3.value) || 0) +
          (Number(dH4.value) || 0);
        dT.value = validateTotals(dSum);

        console.log("Daily Sum: " + dSum);
        break;

      case "2": //end Production Run
        console.log("End Production Run:");
        console.log("prodLogSubmit->endRun->productID", productID);

        //let productID = document.getElementById("partName").value;

        fetch(
          `../src/classes/productionActions.php?endRun=1&productID=${productID}`
        )
          .then((response) => response.text()) //Get raw data
          .then((data) => {
            console.log("Raw reponse from server: ", data); // Debugging output

            //remove any leading numbers or invalid characters before parsing
            const validJson = data.trim().replace(/^[^{]+/, "");

            try {
              const jsonData = JSON.parse(validJson);
              console.log("Parsed JSON: ", jsonData);

              if (!data || data.error) {
                console.error("Error fetching previous log:", data.error);
                return;
              }

              console.log("matUsed1 type: ", typeof jsonData.matUsed1);
              console.log("matUsed1 value before parsing: ", jsonData.matUsed1);

              //subtract current hopper values from previous log and update dHop1 thru 4 values
              const preMatUsed1 =
                jsonData.matUsed1 !== undefined
                  ? parseFloat(jsonData.matUsed1)
                  : null;
              const preMatUsed2 =
                jsonData.matUsed2 !== undefined
                  ? parseFloat(jsonData.matUsed2)
                  : null;
              const preMatUsed3 =
                jsonData.matUsed3 !== undefined
                  ? parseFloat(jsonData.matUsed3)
                  : null;
              const preMatUsed4 =
                jsonData.matUsed4 !== undefined
                  ? parseFloat(jsonData.matUsed4)
                  : null;

              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed1 = : " +
                  preMatUsed1
              );
              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed2 = : " +
                  preMatUsed2
              );
              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed3 = : " +
                  preMatUsed3
              );
              console.log(
                "prodLogSubmit->addBlenderOnBur->hop4AddEvent->preMatUsed4 = : " +
                  preMatUsed4
              );

              let dailyHop1 = parseFloat(hop1.value) - parseFloat(preMatUsed1);
              let dailyHop2 = parseFloat(hop2.value) - parseFloat(preMatUsed2);
              let dailyHop3 = parseFloat(hop3.value) - parseFloat(preMatUsed3);
              let dailyHop4 = parseFloat(hop4.value) - parseFloat(preMatUsed4);

              doStartDailyUsage(
                Number(dailyHop1),
                Number(dailyHop2),
                Number(dailyHop3),
                Number(dailyHop4)
              );
            } catch (error) {
              console.error(
                "prodLogSumbit->Invalid JSON format response:",
                error
              );
            }
          })
          .catch((error) => console.error("Error fetching last log:", error));
        break;
    }
  }); */
}

function getAndSetDailyUsage(hop1,hop2,hop3,hop4,prevHop1,prevHop2,prevHop3,prevHop4 )
{
  let dHop1 = document.getElementById("dHop1");
  let dHop2 = document.getElementById("dHop2");
  let dHop3 = document.getElementById("dHop3");
  let dHop4 = document.getElementById("dHop4");
  let dTotal = document.getElementById("dTotal");
  let dHop1p = document.getElementById("dHop1p");
  let dHop2p = document.getElementById("dHop2p");
  let dHop3p = document.getElementById("dHop3p");
  let dHop4p = document.getElementById("dHop4p");
  let dTotalp = document.getElementById("dTotalp");

  let cHop1 = Number(hop1.value).toFixed(3);
  let cHop2 = Number(hop2.value).toFixed(3);
  let cHop3 = Number(hop3.value).toFixed(3);
  let cHop4 = Number(hop4.value).toFixed(3);

  dHop1.value = cHop1 - prevHop1;
  dHop2.value = cHop2 - prevHop2;
  dHop3.value = cHop3 - prevHop3;
  dHop4.value = cHop4 - prevHop4;
  dSum = dHop1+dHop2+dHop3+dHop4;
  dTotal.value = dSum.toFixed(3);
  
  dHop1p.value = doPercentage(dSum,Number(dHop1.value));
  dHop2p.value = doPercentage(dSum,Number(dHop2.value));
  dHop3p.value = doPercentage(dSum,Number(dHop3.value));
  dHop4p.value = doPercentage(dSum,Number(dHop4.value));
  dTotalp.value = Number(dHop1p.value)+Number(dHop2p.value)+Number(dHop3p.value)+Number(dHop4p.value);
}

//function to get last materialLog for the prodRun
function fetchPreviousMatLogs(actionType, productID) {
  return fetch(
    `../src/Classes/productionActions.php?${actionType}=1&productID=${productID}`
  )
    .then((response) => response.text())
    .then((data) => {
      //console.log(`Raw response for ${actionType}: `, data); //debugging output
      console.log("Raw response from server: ", data); //Checking to see if data is empty

      //Remove unexpected leading characters before parsing
      //const validJson = data.trim().replace(/^[^{]+/, "");
      if (!data || data.trim() === "") {
        console.error("ERROR: Empty response from server!");
        return;
      }

      try {
        const jsonData = JSON.parse(data.trim());
        console.log("Parsed JSON: ", jsonData);
        return jsonData;
      } catch (error) {
        console.error(`Invalid JSON format for ${actionType}:`, error);
      }
    })
    .catch((error) => {
      console.error(`Error fetching ${actionType} log: `, error);
      throw error;
    });
}

//fills daily Usage and Percentages
function doStartDailyUsage(_hop1, _hop2, _hop3, _hop4) {
  console.log("Hopper values: " + hop1 + " " + hop2 + " " + hop3 + " " + hop4);

  let dHop1 = document.getElementById("dHop1");
  let dHop2 = document.getElementById("dHop2");
  let dHop3 = document.getElementById("dHop3");
  let dHop4 = document.getElementById("dHop4");
  let dTotal = document.getElementById("dTotal");

  dHop1.value = hop1.toFixed(3);
  dHop2.value = hop2.toFixed(3);
  dHop3.value = hop3.toFixed(3);
  dHop4.value = hop4.toFixed(3);
  let dSum =
    (Number(dHop1.value) || 0) +
    (Number(dHop2.value) || 0) +
    (Number(dHop3.value) || 0) +
    (Number(dHop4.value) || 0);
  validateTotals(dSum);
  dSum = Number(dSum.toFixed(3));
  dTotal.value = dSum;
  console.log("Totals: " + dSum);

  let dhop1p = document.getElementById("dHop1p");
  let dhop2p = document.getElementById("dHop2p");
  let dhop3p = document.getElementById("dHop3p");
  let dhop4p = document.getElementById("dHop4p");
  let dtotalp = document.getElementById("dTotalp");

  let dH1p = doPercentage(dSum, dHop1.value);
  let dH2p = doPercentage(dSum, dHop2.value);
  let dH3p = doPercentage(dSum, dHop3.value);
  let dH4p = doPercentage(dSum, dHop4.value);

  dhop1p.value = dH1p;
  dhop2p.value = dH2p;
  dhop3p.value = dH3p;
  dhop4p.value = dH4p;

  let dSump =
    (Number(dH1p) || 0) +
    (Number(dH2p) || 0) +
    (Number(dH3p) || 0) +
    (Number(dH4p) || 0);
  dtotalp.value = dSump.toFixed(0);
}

//calculates and returns percentages
function doPercentage(total, hop) {
  //take hop value and divide by total and then multiply by 100

  let dh1p = (hop / total) * 100;
  dh1p = parseFloat(dh1p.toFixed(2));
  console.log(dh1p);
  return dh1p;
}

function validateTotals(sum) {
  // Regular expression to match numbers with up to 3 decimal places
  const validNumber = /^\d+(\.\d{0,3})?$/;

  // If input doesn't match, truncate it to 3 decimal places
  if (!Number.isNaN(validNumber.test(sum)) && !validNumber.test(sum)) {
    sum = parseFloat(sum).toFixed(3);
  }
}

function validateDecimalInput(event) {
  const value = event.target.value;

  // Regular expression to match numbers with up to 3 decimal places
  const validNumber = /^\d+(\.\d{0,3})?$/;

  // If input doesn't match, truncate it to 3 decimal places
  if (!Number.isNaN(validNumber.test(value)) && !validNumber.test(value)) {
    event.target.value = parseFloat(value).toFixed(3);
  }
}
