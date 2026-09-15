function validateLoginForm(event) {
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();

  if (email === "") {
    alert("Please enter your email address or ID.");
    if (event) event.preventDefault();
    return false;
  }
  if (password === "") {
    alert("Please enter your password.");
    if (event) event.preventDefault();
    return false;
  }
  return true;
}

