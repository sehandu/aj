// JavaScript validation and form submission handling
function register(event) {
  const form = document.getElementById("registerForm");

  // Get input field values
  const fullName = form.fullName.value.trim();
  const email = form.email.value.trim();
  const password = form.password.value;
  const confirmPassword = form.confirmPassword.value;

  // JavaScript Validation
  if (!fullName) {
    alert("Please enter your full name.");
    if (event) event.preventDefault();
    return false;
  }

  if (!email) {
    alert("Please enter your email address.");
    if (event) event.preventDefault();
    return false;
  }

  if (!password) {
    alert("Please enter a password.");
    if (event) event.preventDefault();
    return false;
  }

  if (password !== confirmPassword) {
    alert("Passwords do not match.");
    if (event) event.preventDefault();
    return false;
  }

  if (password.length < 6) {
    alert("Password must be at least 6 characters long.");
    if (event) event.preventDefault();
    return false;
  }

  return true;
}

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registerForm");
  if (form) {
    form.addEventListener("submit", function (event) {
      if (!register(event)) {
        event.preventDefault();
      }
    });
  }
});
