// FILE: /js/productionUiManager.js
//
//This will hold function for building tables for the production Landing page

const showAlert = document.getElementById("showAlert");

// Loader functions
export function showLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.remove("d-none");
}

export function hideLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.add("d-none");
}

// Build HTML for the tables
export function buildProdLogsTable(prodLogs) {
  let html = "";
  prodLogs.forEach((row) => {
    html += `<tr data-id='${row.logID}'>
                <td>${row.productID}</td>
                <td>${row.prodDate}</td>
                <td>${row.pressCounter}</td>
                <td>${row.startUpRejects}</td>
                <td>${row.qaRejects}</td>
                <td>${row.purgeLbs}</td>
                <td>${row.runStatus}</td>
                <td>
                    <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" data-bs-toggle ="modal" data-bs-target="#viewProductionModal">View</a>
                </td>
            </tr>`;
  });
  return html;
}

// Attach a shared event listener for table rows
export function setupViewEventListener(elementId, table) {
  const trigger = document.getElementById(elementId);
  if (!trigger) {
    console.warn(`⚠️ No element found with id "${elementId}"`);
    return;
  }

  trigger.addEventListener("click", (e) => {
    const viewLink = e.target.closest("a.viewLink");
    if (viewLink) {
      e.preventDefault();
      const row = e.target.closest("tr");
      const id = row ? row.getAttribute("data-id") : null;
      if (id && id.trim()) {
        // Dispatch to the API client to fill form
        import("./productionApiClient.js").then(({ fetchAndFillForm }) => {
          fetchAndFillForm(id.trim(), table);
        });
      }
    }
  });
}

// Function to render tables into the DOM
export function renderTables(prodLogs) {
  document.getElementById("last4wks").innerHTML = buildProdLogsTable(prodLogs);
}

export function calculateDailyUsage(currentHoppers, previousValues) {
  console.log("🔄 Calculating daily usage...");

  const usageElements = ["dHop1", "dHop2", "dHop3", "dHop4"].map(id =>
    document.getElementById(id)
  );
  const percentageElements = ["dHop1p", "dHop2p", "dHop3p", "dHop4p"].map(id =>
    document.getElementById(id)
  );
  const dTotal = document.getElementById("dTotal");
  const dTotalp = document.getElementById("dTotalp");

  let usageTotals = currentHoppers.map((hop, i) => {
    const current = parseFloat(hop.value) || 0;
    const previous = parseFloat(previousValues[i]) || 0;
    const delta = current - previous;
    usageElements[i].value = delta.toFixed(3);
    return delta;
  });

  const total = usageTotals.reduce((sum, val) => sum + val, 0);
  dTotal.value = total.toFixed(3);

  let percentages = usageTotals.map(val =>
    total ? parseFloat(((val / total) * 100).toFixed(2)) : 0
  );

  percentages.forEach((pct, i) => (percentageElements[i].value = pct));
  dTotalp.value = percentages.reduce((sum, val) => sum + val, 0).toFixed(2);
}
