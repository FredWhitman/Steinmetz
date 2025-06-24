// FILE: /js/productionUiManager.js
//
//This will hold function for building tables for the production Landing page

const showAlert = document.getElementById("showAlert"); 

// Build HTML for the tables
export function buildProdLogsTable(prodLogs) {
  let html = "";
  prodLogs.forEach((row) => {
    // Highlight if below minimum quantity
    let colorStyle =
      row.partQty < row.minQty ? "style='color:red;font-weight: bold;'" : "";
    html += `<tr data-id="${row.productID}">
               <td><span ${colorStyle}>${row.productID}</span></td>
               <td><span ${colorStyle}>${row.partQty}</span></td>
               <td>
                 <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" title="edit product" data-bs-toggle="modal" data-bs-target="#editProductModal"><i class="bi bi-pencil"></i></a>
                 <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" title="update product qty" data-bs-toggle="modal" data-bs-target="#updateProductModal"><i class="bi bi-file-earmark-check"></i></a>
               </td>
             </tr>`;
  });
  return html;
}

// Function to render tables into the DOM
export function renderTables({ prodLogs }) {
    document.getElementById("products").innerHTML = buildProdLogsTable(prodLogs);
}