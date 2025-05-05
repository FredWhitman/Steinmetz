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
      console.dir(prodStatus);
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
    let prodDate = document.getElementById("logDate");
    console.log(
      "Production Run Status: " +
        prodStatus +
        " Part Name: " +
        partID +
        " Production Date: " +
        prodDate.value
    );

    switch (prodStatus) {
      case "0":
        console.log("In Progress:");

        break;

      case "1":
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

      case "2":
        console.log("End Production Run:");

        break;

      default:
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

function getDailyUsage() {
  const bigDryerTemp = document.getElementById("bigDryerTemp");

  bigDryerTemp.addEventListener("", function () {
    /* TODO: Check radio button for production status if start copy values from blender 
                  and calculate percentage of each hopper.
            TODO: Check radio button and if in progress pull last production record for this run
                  and subtract these values from the current. Display new values and calculate percentages
            TODO: Check radio button and if end pull last production record for the run and fill daily usage.
                  Pull all production logs for entire production run and total all material consumed, number of pads produced and reject
  
          */
  });
}
