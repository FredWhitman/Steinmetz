const tbodyProducts = document.getElementById("products"); //Set the tbody to display last 4 weeks of production

const editProductForm = document.getElementById("edit-product-form");
const editProductModal = new bootstrap.Modal(
  document.getElementById("editProductModal")
);

const editMaterialForm = document.getElementById("edit-material-form");
const editMaterialModal = new bootstrap.Modal(
  document.getElementById("editMaterialModal")
);

const editPFMForm = document.getElementById("edit-pfm-form");
const editPFMModal = new bootstrap.Modal(
  document.getElementById("editPFMModal")
);

const updateProductForm = document.getElementById("update-product-form");
const updateProductModal = new bootstrap.Modal(
  document.getElementById("updateProductModal")
);

function showLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.remove("d-none");
}

function hideLoader() {
  const loader = document.getElementById("loader");
  if (loader) loader.classList.add("d-none");
}

//Fetch inventory logs Ajax request
window.fetchProductsMaterialPFM = async function () {
  showLoader();
  try {
    const start = performance.now();
    const response = await fetch(
      "../src/classes/inventoryActions.php?getInventory=1",
      {
        method: "GET",
      }
    );
    const jsonData = await response.json();
    console.log("Parsed inventory data: ", jsonData);
    console.log("Fetch duration: ", performance.now() - start);
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

    console.log("Render duration: ", performance.now() - start);

    setTimeout(() => {
      hideLoader();
    }, 0);
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
                <a href="#" class="btn btn-success btn-sm rounded-pill py-0 updateLink" style="font-size: 10px;" data-bs-placement="top" title= "update product qty" data-bs-toggle="modal" data-bs-target="#updateProductModal"><i class="bi bi-file-earmark-check"></i></a>
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
  console.log("PFMS Data:", pfms);
  let html = "";
  for (const row of pfms) {
    let colorStyle =
      row.Qty < row.minQty ? "style='color:red; font-weight: bold;'" : "";
    html += `<tr data-id="${row.pfmID}">
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

/// This applies listeners to each table and monitors for a click
const setupEditEventListener = (elementId, table) => {
  document.getElementById(elementId).addEventListener("click", (e) => {
    if (e.target.closest("a.editLink")) {
      e.preventDefault();
      let rowElement = e.target.closest("tr");
      let id = rowElement ? rowElement.getAttribute("data-id") : null;

      console.log("Edit Extracted ID:", id);
      console.log("Edit Extraced Table:", table);

      if (id && id.trim() !== "") {
        fetchAndFillForm(id.trim(), table);
      } else {
        console.error("ERROR: `data-id` is missing or incorrect!");
      }
    }
    if (e.target.closest("a.updateLink")) {
      e.preventDefault();
      let rowElement = e.target.closest("tr");
      let id = rowElement ? rowElement.getAttribute("data-id") : null;

      console.log("Update Extracted ID:", id);
      console.log("Update Extraced Table:", table);

      if (id && id.trim() !== "") {
        fetchAndFillUpdateForm(id.trim(), table);
      } else {
        console.error("ERROR: `data-id` is missing or incorrrect!");
      }
    }
  });
};

// Apply the function to both tables
setupEditEventListener("products", "products");
setupEditEventListener("materials", "materials");
setupEditEventListener("pfms", "pfms");

const fetchAndFillUpdateForm = async (id, table) => {
  console.log("fetching product: ", id, table);

  let url = `../src/classes/inventoryActions.php?update${
    table.charAt(0).toUpperCase() + table.slice(1)
  }=1&id=${id}&table=${table}`;
  console.log("fetch URL: ", url);

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

    const fieldMappings = {
      products: {
        productID: "h_productID",
        partName: "pPartName",
        partQty: "pStock",
      },
      materials: {},
      pfms: {},
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
  } catch (error) {}
};

//fills update modal for with queried data
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
        productID: "hiddenProductID",
        partName: "partName",
        minQty: "minQty",
        boxesPerSkid: "boxSkid",
        partsPerBox: "partBox",
        partWeight: "partWeight",
        customer: "customer",
        productionType: "partType",
        displayOrder: "displayOrder",
      },
      materials: {
        matPartNumber: "h_matPartNumber",
        matName: "matName",
        productID: "productID",
        minLbs: "minLbs",
        matCustomer: "mCustomer",
        displayOrder: "mDisplayOrder",
      },
      pfms: {
        pfmID: "h_pfmID",
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

updateProductForm.addEventListener("submit", async (e) => {
  console.log("update product button has been clicked!");

  //prevent form from submitting data to DB
  e.defaultPrevented();

  const formData = new FormData(updateProductForm);

  //check to make sure the required input field are not empty.
  if (!updateProductForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    updateProductForm.classList.add("was-validated");
    return false;
  }
  const productData = {
    action: "updateProduct",
    products: {
      productID: formData.get("h_productID"),
      partyQty: formData.get("pStock"),
      pAmount: formData.get("pAmount"),
    },
  };

  console.log("Raw data output: ", productData);
});

editProductForm.addEventListener("submit", async (e) => {
  console.log("submit edit Product button was clicked!");
  //prevent form from submitting data to DB
  e.preventDefault();
  //console.log("Edit Product submit button has been clicked!");
  const formData = new FormData(editProductForm);

  //check to make sure the input fields are not empty
  if (!editProductForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    editProductForm.classList.add("was-validated");
    return false;
  }

  const productData = {
    action: "editProduct",
    products: {
      productID: formData.get("productID"),
      partName: formData.get("p_Part"),
      minQty: formData.get("p_minQty"),
      boxesPerSkid: formData.get("p_boxSkid"),
      partsPerBox: formData.get("p_partBox"),
      partWeight: formData.get("p_partWeight"),
      customer: formData.get("p_customer"),
      displayOrder: formData.get("p_displayOrder"),
      productionType: formData.get("p_partType"),
    },
  };

  console.log("Raw data output: ", productData);

  const data = await fetch("../src/Classes/inventoryActions.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(productData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;
    editProductForm.reset();
    editProductForm.classList.remove("was-validated");
    editProductModal.hide();

    /* //calling fetchLast4Weeks inside main.js
    setTimeout(() => {
      //console.log("Refreshing last 4 weeks data...");
      window.fetchProductsMaterialPFM();
    }, 300); */
  } catch (error) {
    console.error("Failed to submit form: ", error);
  }
});

editMaterialForm.addEventListener("submit", async (e) => {
  console.log("submit edit Material button was clicked!");
  //prevent form from submitting data to DB
  e.preventDefault();
  //console.log("Edit Product submit button has been clicked!");
  const formData = new FormData(editMaterialForm);

  //check to make sure the input fields are not empty
  if (!editMaterialForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    editMaterialForm.classList.add("was-validated");
    return false;
  }

  const materialData = {
    action: "editMaterial",
    materials: {
      matPartNumber: formData.get("m_matPartNumber"),
      matName: formData.get("m_material"),
      productID: formData.get("m_productID"),
      minLbs: formData.get("m_minLbs"),
      matCustomer: formData.get("m_customer"),
      displayOrder: formData.get("m_displayOrder"),
    },
  };

  console.log("Raw data output: ", materialData);

  const data = await fetch("../src/Classes/inventoryActions.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(materialData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;
    editMaterialForm.reset();
    editMaterialForm.classList.remove("was-validated");
    editMaterialModal.hide();
  } catch (error) {
    console.error("Failed to submit form: ", error);
  }
});

editPFMForm.addEventListener("submit", async (e) => {
  console.log("submit edit pfm button clicked");

  e.preventDefault();
  const formData = new FormData(editPFMForm);

  //check to make sure the input fields are not empty
  if (!editPFMForm.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
    editPFMForm.classList.add("was-validated");
    return false;
  }

  const pfmData = {
    action: "editPFM",
    pfm: {
      pfmID: formData.get("p_pfmID"),
      partNumber: formData.get("pf_Number"),
      partName: formData.get("pf_Name"),
      productID: formData.get("pf_productID"),
      minQty: formData.get("pf_minQty"),
      customer: formData.get("pf_customer"),
      displayOrder: formData.get("pf_displayOrder"),
    },
  };

  console.log("Raw data output: ", pfmData);

  const data = await fetch("../src/Classes/inventoryActions.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(pfmData),
  });

  try {
    const response = await data.text();
    showAlert.innerHTML = response;
    editPFMForm.reset();
    editPFMForm.classList.remove("was-validated");
    editPFMModal.hide();
  } catch (error) {
    console.error("Failed to submit form: ", error);
  }
});

updateProductForm.addEventListener("submit", async (e) => {});
