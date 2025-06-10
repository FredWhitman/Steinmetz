//Fetch last 4 weeks of production logs Ajax request
window.fetchProductsMaterialPFM = async function () {
  try {
    const response =  await fetch("../src/classes/inventoryActions.php?getInventory=1",
      {
        method: "GET",
      });
    const jsonData = await response.json();
    console.log("Parsed inventory data: ", jsonData);
    //jsonData contains three arrays:
    //jsonData.products, jsonData.materials, jsonData.pfms

    //Build HTML for each table
    let productsHTML = buildProductsTable(jsonData.products);
    let materialsHTML = buildProductsTable(jsonData.materials);
    let pfmsHTML = buildProductsTable(jsonData.pfms);

    //inject the HTML into the repsective containers
    document.getElementById("products").innerHTML = productsHTML;
    document.getElementById("materials").innerHTML = materialsHTML;
    document.getElementById("pfms").innerHTML = pfmsHTML;

  } catch (error) {
     console.error("Error fetching inventory: ", error);
  }
};

fetchProductsMaterialPFM();

function buildProductsTable(products){
  let html = "";
  for(const row of products){
    html += `<tr>
              <td>${row.productID}</td>
              <td>${row.partQty}</td>
              <td></td>
              </tr>`;
  }
  return html;
}

function buildMaterialsTable(materials){
  let html = "";
  for(const row of materials){
    html += `<tr>
              <td>${row.matName}</td>
              <td>${row.matLbs}</td>
              <td></td>
              </tr>`;
  }
  return html;
}

function buildProductsTable(pfms){
  let html = "";
  for(const row of pfms){
    html += `<tr>
              <td>${row.PartName}</td>
              <td>${row.Qty}</td>
              <td></td>
              </tr>`;
  }
  return html;
}