function validateForm() {
  const title = document.getElementById("eventTitle").value;
  const description = document.getElementById("description").value;
  const category = document.getElementById("category").value;
  const date = document.getElementById("event_date").value;
  const errorMsg = document.getElementById("errorMessage");

  if (title === "" || description === "" || category === "" || date === "") {
    if (errorMsg) {
      errorMsg.textContent = "Please fill out all required fields.";
    }
    return false;
  }
  return true;
}

function clearForm() {
  document.getElementById("eventForm").reset();
  const errorMsg = document.getElementById("errorMessage");
  if (errorMsg) {
    errorMsg.textContent = "";
  }
}
