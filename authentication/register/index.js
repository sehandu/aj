// JavaScript validation and form submission handling
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('registerForm');

  form.addEventListener('submit', function (e) {
    // Prevent default HTML form submission
    e.preventDefault();

    // Get input field values
    const fullName = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // JavaScript Validation
    if (!fullName) {
      alert('Please enter your full name.');
      return;
    }

    if (!email) {
      alert('Please enter your email address.');
      return;
    }

    if (!password) {
      alert('Please enter a password.');
      return;
    }

    if (password !== confirmPassword) {
      alert('Passwords do not match.');
      return;
    }

    if (password.length < 6) {
      alert('Password must be at least 6 characters long.');
      return;
    }

    // Prepare form data for asynchronous submission
    const formData = new FormData(form);

    // Send data to PHP script using fetch API
    fetch('index.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        // Display result using JS alert
        alert(data.message);

        // Reset form and redirect on successful registration
        if (data.status === 'success') {
          form.reset();
          window.location.href = '../login/login.php?msg=' + encodeURIComponent(data.message);
        }
      })
      .catch(error => {
        alert('An error occurred during submission.');
        console.error('Error:', error);
      });
  });
});
