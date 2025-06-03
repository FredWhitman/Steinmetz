const addLogForm = document.getElementById("add-productionLog-form");
//const showAlert = document.getElementById("showAlert");
const addLogModal = new bootstrap.Modal(document.getElementById("addProductionModal"));

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
        purgeLbs: "0",
        Comments: formData.get("commentText"),
      },
      materialData: {
        prodLogID: "0",
        mat1: formData.get("Mat1Name"),
        matused1: formData.get("hop1Lbs"),
        mat2: formData.get("Mat2Name"),
        matused2: formData.get("hop2Lbs"),
        mat3: formData.get("Mat3Name"),
        matused3: formData.get("hop3Lbs"),
        mat4: formData.get("Mat4Name"),
        matused4: formData.get("hop4Lbs"),
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
      //console.dir(prodStatus);
    });
  });

  //set const for each hopper and assign it a value the textbox for each hopper
  const hop1 = document.getElementById("hop1Lbs");
  const hop2 = document.getElementById("hop2Lbs");
  const hop3 = document.getElementById("hop3Lbs");
  const hop4 = document.getElementById("hop4Lbs");
  const totalBlended = document.getElementById("BlenderTotals");

  //create a listener on hopper 4 input and trigger script when it no longer has focus
  hop4Lbs.addEventListener("blur", function () {
    //Convert values to numbers and add them up.
    const sum =
      (Number(hop1.value) || 0) +
      (Number(hop2.value) || 0) +
      (Number(hop3.value) || 0) +
      (Number(hop4.value) || 0);
    totalBlended.value = sum;
    let partID = document.getElementById("partName").value;
    let prodDate = document.getElementById("logDate").value;
    console.log(
      "Production Run Status: " +
        prodStatus +
        " Part Name: " +
        partID +
        " Production Date: " +
        prodDate
    );

    switch (prodStatus) {
      case "0":
        console.log("In Progress:");
        // get Production run id, use that to get previsou production log id.
        console.log("In Progress:");
        let productID = document.getElementById("partName").value;
        fetch(`../src/classes/productionActions.php?getLastLog=1&productID=${productID}`)
          .then((response) => response.json())
          .then((data) => 
          {
            console.log("Fetched Previous Log Data:", data); // Debugging output

            if (!data || data.error) {
              console.error("Error fetching previous log:", data.error);
              return;
            }

            //subtract current hopper values from previous log and update dHop1 thru 4 values
            let preMatUsed1 = parseFloat(data.matUsed1) || 0;
            let preMatUsed2 = parseFloat(data.matUsed2) || 0;
            let preMatUsed3 = parseFloat(data.matUsed3) || 0;
            let preMatUsed4 = parseFloat(data.matUsed4) || 0;
            
            let dailyHop1 = parseFloat(hop1.value) - parseFloat(preMatUsed1);
            let dailyHop2 = parseFloat(hop2.value) - parseFloat(preMatUsed2);
            let dailyHop3 = parseFloat(hop3.value) - parseFloat(preMatUsed3);
            let dailyHop4 = parseFloat(hop4.value) - parseFloat(preMatUsed4);

            // Populate hopper values with retrieved data
  /*           document.getElementById("dHop1").value = dailyHop1;
            document.getElementById("dHop2").value = dailyHop2;
            
            document.getElementById("dHop3").value = (dailyHop3 === "NaN" )? 0:dailyHop3;
            document.getElementById("dHop4").value = dailyHop4;

            let dailyTotal = dailyHop1+dailyHop2+dailyHop3+dailyHop4;
            document.getElementById("dTotal").value = dailyTotal; */
                        
            /* let pDHop1 = doPercentage(dailyTotal,dailyHop1);
            let pDHop2 = doPercentage(dailyTotal,dailyHop2);
            let pDHop3 = doPercentage(dailyTotal,dailyHop3);
            let pDHop4 = doPercentage(dailyTotal,dailyHop4);
            let pdTotal = pDHop1+pDHop2+pDHop3+pDHop4;
 */
            /* console.log("% H1: ", Number(pDHop1));
            console.log("% H2: ", Number(pDHop2));
            console.log("% H3: ", Number(pDHop3));
            console.log("% H4: ", Number(pDHop4));
            console.log("% Total: ", Number(pdTotal));
            document.getElementById("dHop1p").value = pDHop1;
            document.getElementById("dHop2p").value = pDHop2;
            document.getElementById("dHop3p").value = pDHop3;
            document.getElementById("dHop4p").value = pDHop4;
            document.getElementById("dTotalp").value = pdTotal; */
            
            doStartDailyUsage(
          Number(dailyHop1),
          Number(dailyHop2),
          Number(dailyHop3),
          Number(dailyHop4)
        );

            // Validate totals
            /* let totalUsed =
              Number(data.mat1Used) +
              Number(data.mat2Used) +
              Number(data.mat3Used) +
              Number(data.mat4Used);
            document.getElementById("dTotal").value = validateTotals(totalUsed); */
          })
          .catch((error) => console.error("Error fetching last log:", error));
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

      case "2": //end Production Run
        console.log("End Production Run:");
        break;
    }
  });
}

function doStartDailyUsage(hop1, hop2, hop3, hop4) {
  console.log("Hopper values: " + hop1 + " " + hop2 + " " + hop3 + " " + hop4);

  let dHop1 = document.getElementById("dHop1");
  let dHop2 = document.getElementById("dHop2");
  let dHop3 = document.getElementById("dHop3");
  let dHop4 = document.getElementById("dHop4");
  let dTotal = document.getElementById("dTotal");

  dHop1.value = hop1;
  dHop2.value = hop2;
  dHop3.value = hop3;
  dHop4.value = hop4;
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
  dtotalp.value = dSump;
}

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

function getpreviousLog() {}
