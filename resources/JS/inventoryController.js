const tbodyProducts = document.getElementById("products"); //Set the tbody to display last 4 weeks of production

//Fetch inventory logs Ajax request
window.fetchProductsMaterialPFM = async function () {
  try {
    const response = await fetch(
      "../src/classes/inventoryActions.php?getInventory=1",
      {
        method: "GET",
      }
    );
    const jsonData = await response.json();
    console.log("Parsed inventory data: ", jsonData);
    //jsonData contains three arrays:
    //jsonData.products, jsonData.materials, jsonData.pfms

    //Build HTML for each table
    let productsHTML = buildProductsTable(jsonData.products);
    let materialsHTML = buildMaterialsTable(jsonData.materials);
    let pfmsHTML = buildPfmsTable(jsonData.pfms);

    //inject the HTML into the repsective containers
    document.getElementById("products").innerHTML = productsHTML;
    document.getElementById("materials").innerHTML = materialsHTML;
    document.getElementById("pfms").innerHTML = pfmsHTML;
  } catch (error) {
    console.error("Error fetching inventory: ", error);
  }
};

fetchProductsMaterialPFM();

function buildProductsTable(products) {
  let html = "";
  for (const row of products) {
    let colorStyle =
      row.partQty < row.minQty ? "style='color:red;font-weight: bold;'" : "";
    html += `<tr data-id='$row['productID']'>
              <td><span ${colorStyle}> ${row.productID} </span></td>
              <td><span ${colorStyle}> ${row.partQty} </span></td>
              <td>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" style="font-size: 10px;" data-bs-toggle ="modal" data-bs-target="#editProductModal">Edit Product</a>
                <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" style="font-size: 10px;" data-bs-toggle ="modal" data-bs-target="#updateInventoryModal">Update Qty</a>
              </td>
              </tr>`;
  }
  return html;
}

function buildMaterialsTable(materials) {
  let html = "";
  for (const row of materials) {
    let colorStyle =
      row.matLbs < row.minLbs ? "style='color:red; font-weight: bold;'" : "";

    html += `<tr>
              <td><span ${colorStyle}> ${row.matName}</span></td>
              <td><span ${colorStyle}> ${row.matLbs}</span></td>
              <td>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" style = "font-size: 10px; data-bs-placement = "top" title = "edit product" data-bs-toggle = "modal" data-bs-target="#editProductModal"><i class = "bi bi-pencil"></i></a>
                
                <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" style="font-size: 10px;" data-bs-placement="top" title= "update product qty" data-bs-toggle="modal" data-bs-target="#updateInventoryModal"><i class="bi bi-file-earmark-check"></i></a>
              </td>
              </tr>`;
  }
  return html;
}

function buildPfmsTable(pfms) {
  let html = "";
  for (const row of pfms) {
    let colorStyle =
      row.Qty < row.minQty ? "style='color:red; font-weight: bold;'" : "";
    html += `<tr>
              <td><span ${colorStyle}>${row.partName}</span></td>
              <td><span ${colorStyle}>${row.Qty}</span></td>
              <td>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" style="font-size: 10px; data-bs-toggle ="modal" data-bs-target="#editProductModal">Edit Product</a>
                <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" style="font-size: 10px; data-bs-toggle ="modal" data-bs-target="#updateInventoryModal">Update Qty</a>

              </td>
              </tr>`;
  }
  return html;
}

tbodyProducts.addEventListener("click", (e) => {
  if (e.target && e.target.matches("a.editLink")) {
    e.preventDefault();
    //find the data-id class tag in dynamiclly created table from action.php get-read check

    let rowElement = e.target.closest("tr"); // Get the parent row
    let id = rowElement ? rowElement.getAttribute("data-id") : null;
    let table = "products";

    console.log("extracted ID: ", id);
    console.log("Table: ", table);
    if (id && id.trim() !== "") {
      editUser(id.trim(), table);
    } else {
      console.error("ERROR: `data-id` is missing of incorrect!");
    }
  }
});

const editUser = async (id, table) => {
  //

  console.log("id: ", id);
  console.log("table: ", table);
  let url = `../src/classes/inventoryActions.php?editProduct=1&id=${id}&type=${table}`;
  console.log("Fetching: ", url);
  const response = await fetch(url);
  const rawText = await response.text();
  console.log("RAW server response: ", rawText);

  //const response = await data.json();
  /*  document.getElementById("id").value = response.id;
  document.getElementById("fname").value = response.fname;
  document.getElementById("lname").value = response.lname;
  document.getElementById("userEmail").value = response.email;
  document.getElementById("phone").value = response.phone; */
};
