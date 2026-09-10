// Switch between Student and Admin roles
function switchRole(role) {
  const roleSelect = document.getElementById("role");
  const userLabel = document.getElementById("userLabel");
  const emailInput = document.getElementById("email");

  if (roleSelect) roleSelect.value = role;

  if (role === "admin") {
    if (userLabel) userLabel.innerText = "Admin Email or Code";
    if (emailInput) emailInput.placeholder = "admin@nsbm.ac.lk";
  } else {
    if (userLabel) userLabel.innerText = "Student Email or ID";
    if (emailInput) emailInput.placeholder = "student@students.nsbm.ac.lk";
  }
}

// Client-side JavaScript Validation for Login
function validateLoginForm() {
  const email = document.getElementById("email") ? document.getElementById("email").value.trim() : "";
  const password = document.getElementById("password") ? document.getElementById("password").value.trim() : "";

  if (email === "") {
    alert("Please enter your email address or ID.");
    document.getElementById("email").focus();
    return false;
  }
  if (password === "") {
    alert("Please enter your password.");
    document.getElementById("password").focus();
    return false;
  }
  return true;
}

// Show / Hide Password
function togglePassword() {
  const passwordInput = document.getElementById("password");
  if (passwordInput) {
    passwordInput.type =
      passwordInput.type === "password" ? "text" : "password";
  }
}

