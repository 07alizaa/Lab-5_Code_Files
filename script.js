 document.getElementById('loginForm').addEventListener('submit', function (event) {
      let username = document.getElementById('username').value;
      let password = document.getElementById('password').value;

      if (username === '' || password === '') {
        alert("Please fill in both fields.");
        event.preventDefault(); // stop form submission
      } else {
        alert("Login successful!");
      }
    });