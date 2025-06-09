//Fetch last 4 weeks of production logs Ajax request
window.fetchProductsMaterialPFM = async function () {
  //console.log("fetchLast4Weeks() is being called");
  const data = await fetch(
    "../src/classes/inventoryActions.php?getInventory=1",
    {
      method: "GET",
    }
  );

  const response = await data.text();

  //console.log("Fetched Data:", response); // Debugging output
  document.getElementById("weeks").innerHTML = response;
};

fetchProductsMaterialPFM();
