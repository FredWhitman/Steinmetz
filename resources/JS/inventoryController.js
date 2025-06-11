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
    html += `<tr data-id="${row.productID}">
              <td><span ${colorStyle}> ${row.productID} </span></td>
              <td><span ${colorStyle}> ${row.partQty} </span></td>
              <td>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" style = "font-size: 10px; data-bs-placement = "top" title = "edit product" data-bs-toggle = "modal" data-bs-target="#editProductModal"><i class = "bi bi-pencil"></i></a>
                <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" style="font-size: 10px;" data-bs-placement="top" title= "update product qty" data-bs-toggle="modal" data-bs-target="#updateInventoryModal"><i class="bi bi-file-earmark-check"></i></a>
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

    html += `<tr data-id="${row.matPartNumber}">
              <td><span ${colorStyle}> ${row.matName}</span></td>
              <td><span ${colorStyle}> ${row.matLbs}</span></td>
              <td>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" style = "font-size: 10px; data-bs-placement = "top" title = "edit material" data-bs-toggle = "modal" data-bs-target="#editMaterialModal"><i class = "bi bi-pencil"></i></a>
                <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" style="font-size: 10px;" data-bs-placement="top" title= "update material lbs" data-bs-toggle="modal" data-bs-target="#updateInventoryModal"><i class="bi bi-file-earmark-check"></i></a>
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
    html += `<tr data-id="${row.pFMID}">
              <td><span ${colorStyle}>${row.partName}</span></td>
              <td><span ${colorStyle}>${row.Qty}</span></td>
              <td>
                <a href="#" class="btn btn-primary btn-sm rounded-pill py-0 editLink" style = "font-size: 10px; data-bs-placement = "top" title = "edit pfm" data-bs-toggle = "modal" data-bs-target="#editPFMModal"><i class = "bi bi-pencil"></i></a>
                <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" style="font-size: 10px;" data-bs-placement="top" title= "update pfm qty" data-bs-toggle="modal" data-bs-target="#updatePFMModal"><i class="bi bi-file-earmark-check"></i></a>
              </td>
              </tr>`;
  }
  return html;
}

/* document.getElementById("products").addEventListener("click", (e) => {
  if (e.target.closest("a.editLink")) {
    e.preventDefault();
    let rowElement = e.target.closest("tr"); // Get the parent row
    let id = rowElement ? rowElement.getAttribute("data-id") : null;
    let table = "products";

    console.log("extracted ID: ", id);
    console.log("Table: ", table);
    if (id && id.trim() !== "") {
      editProduct(id.trim(), table);
    } else {
      console.error("ERROR: `data-id` is missing or incorrect!");
    }
  }
}); */

const setupEditEventListener = (elementId, table) => {
  document.getElementById(elementId).addEventListener("click", (e) => {
    if (e.target.closest("a.editLink")) {
      e.preventDefault();
      let rowElement = e.target.closest("tr");
      let id = rowElement ? rowElement.getAttribute("data-id") : null;

      console.log("Extracted ID:", id);
      console.log("Extraced Table:", table);

      if (id && id.trim() !== "") {
        fetchAndFillForm(id.trim(), table);
      } else {
        console.error("ERROR: `data-id` is missing or incorrect!");
      }
    }
  });
};

// Apply the function to both tables
setupEditEventListener("products", "products");
setupEditEventListener("materials", "materials");
setupEditEventListener("pfms", "pfms");

const fetchAndFillForm = async (id, table) => {
  console.log("Fetching record:", id, table);

  let url = `../src/classes/inventoryActions.php?edit${
    table.charAt(0).toUpperCase() + table.slice(1)
  }=1&id=${id}&table=${table}`;
  const response = await fetch(url);
  const rawText = await response.text();
  console.log("RAW server response:", rawText);

  try {
    const responseData = JSON.parse(rawText);
    console.log("Parsed response:", responseData);

    if (!responseData || responseData.error) {
      console.error("Error from server:", responseData.error);
      return;
    }

    // Map fields dynamically based on table type,
    // first variable is db field name second is the element name
    const fieldMappings = {
      products: {
        partName: "partName",
        minQty: "minQty",
        boxesPerSkid: "boxSkid",
        partsPerBox: "partBox",
        partweight: "partWeight",
        customer: "customer",
        productionType: "partType",
        displayOrder: "displayOrder",
      },
      materials: {
        matName: "matName",
        productID: "productID",
        minLbs: "minLbs",
        customer: "mCustomer",
        displayOrder: "mDisplayOrder",
      },
      pfms: {
        partNumber: "pNumber",
        partName: "pName",
        productID: "pProductID",
        minQty: "pMinQty",
        customer: "pCustomer",
        displayOrder: "pDisplayOrder",
      },
    };

    Object.keys(fieldMappings[table]).forEach((dbKey) => {
      let formID = fieldMappings[table][dbKey];
      let element = document.getElementById(formID);
      if (element) {
        element.value = responseData[dbKey] || "";
      } else {
        console.warn(`Element with ID '${formID}'  not found!`);
      }
    });
  } catch (error) {
    console.error("Failed to parse JSON:", error);
  }
};

/* const editProduct = async (id, table) => {
  console.log("id: ", id);
  console.log("table: ", table);
  let url = `../src/classes/inventoryActions.php?editProduct=1&id=${id}&table=${table}`;
  //console.log("Fetching: ", url);
  const response = await fetch(url);
  const rawText = await response.text();
  console.log("RAW server response: ", rawText);

  try {
    const responseData = JSON.parse(rawText);
    console.log("Parsed response: ", responseData);

    if (!responseData || responseData.error) {
      console.error("Error from server: ", responseData.error);
      return;
    }
    document.getElementById("partName").value = responseData["partName"] || "";
    document.getElementById("minQty").value = responseData["minQty"] || "";
    document.getElementById("boxSkid").value =
      responseData["boxesPerSkid"] || "";
    document.getElementById("partBox").value =
      responseData["partsPerBox"] || "";
    document.getElementById("partWeight").value =
      responseData["partWeight"] || "";
    document.getElementById("customer").value = responseData["customer"] || "";
    document.getElementById("partType").value =
      responseData["productionType"] || "";
    document.getElementById("displayOrder").value =
      responseData["displayOrder"] || "";
  } catch (error) {
    console.error("Failed to parse JSON: ", error);
  }
};

document.getElementById("materials").addEventListener("click", (e) => {
  if (e.target.closest("a.editLink")) {
    e.preventDefault();
    let rowElement = e.target.closest("tr"); // Get the parent row
    let id = rowElement ? rowElement.getAttribute("data-id") : null;
    let table = "materials";

    console.log("extracted ID: ", id);
    console.log("Table: ", table);

    if (id && id.trim() !== "") {
      editMaterials(id.trim(), table);
    } else {
      console.error("ERROR: `data-id` is missing or incorrect!");
    }
  }
});

const editMaterials = async (id, table) => {
  console.log("id: ", id);
  console.log("table: ", table);
  let url = `../src/classes/inventoryActions.php?editMaterial=1&id=${id}&table=${table}`;
  //console.log("Fetching: ", url);
  const response = await fetch(url);
  const rawText = await response.text();
  console.log("RAW server response: ", rawText);

  try {
    const responseData = JSON.parse(rawText);
    console.log("Parsed response: ", responseData);

    if (!responseData || responseData.error) {
      console.error("Error from server: ", responseData.error);
      return;
    }

    document.getElementById("matName").value = responseData["matName"] || "";
    document.getElementById("productID").value =
      responseData["productID"] || "";
    document.getElementById("minLbs").value = responseData["minLbs"] || "";
    document.getElementById("mCustomer").value = responseData["customer"] || "";
    document.getElementById("mDisplayOrder").value =
      responseData["displayOrder"] || "";
  } catch (error) {
    console.error("Failed to parse JSON: ", error);
  }
};
 */
