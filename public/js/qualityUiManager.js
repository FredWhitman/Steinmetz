
// Loader functions
export function showLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.remove("d-none");
}

export function hideLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.add("d-none");
}

//Build HTML for tables
export function buildQaRejectTable(qaRejectLogs){
    let html = "";
    qaRejectLogs.forEach((row) => {
        html +=` <tr data-id="${row.qaRejectID}">
                    <td>${row.prodDate}</td>
                    <td>${row.prodLog}</td>
                    <td>${row.productID}</td>
                    <td>${row.rejects}</td>
                    <td>
                        <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 viewLink" title="view qa reject" data-bs-toggle="modal" data-bs-target="#viewQaRejectLog"><i class="bi-pencil"></a>
                    </td>
                </tr>`;
    });
    return html;
}

//build table for lot changes


//build table for oven logs