import { postData } from "/js/inventoryApiClient.js";

const BASE_URL = "/api/dispatcher.php";

document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("login_form");
  if (loginForm) {
    loginForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(loginForm);
      const data = {
        action: "login",
        username: formData.get("username"),
        password: formData.get("password"),
      };

      console.log("Login data:", data);

      try {
        const result = await postData(data);

        if (!result || result.error) {
          console.error("Error during login:", result?.error);
          alert(result?.error || "Login failed");
          return;
        } else {
          // On success, redirect to inventory page
          window.location.href = "Inventory_1.php";
        }
      } catch (error) {
        console.error("Error during login:", error);
        alert("An error occurred. Please try again.");
      }
    });
  }
});
