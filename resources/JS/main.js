document.addEventListener("DOMContentLoaded", function () {
  fetch("../src/classes/fetch_data.php")
    .then((response) => response.json())
    .then((data) => {
      let selects = ["mat1Name", "mat2Name", "mat3Name", "mat4Name"];

      selects.forEach((selectId) => {
        let select = document.getElementById(selectId);
        select.innerHTML = ""; // Clear existing options

        data.forEach((item) => {
          let option = document.createElement("option");
          option.value = item.id;
          option.textContent = item.name;
          select.appendChild(option);
        });
      });
    })
    .catch((error) => console.error("Error fetching data:", error));
});
