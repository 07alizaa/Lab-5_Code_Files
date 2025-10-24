// Client-side validation for the login form.
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  if (!form) return;

  form.addEventListener('submit', function (event) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const captcha = document.getElementById('captcha').value.trim();

    if (username === '' || password === '' || captcha === '') {
      alert('Please fill in all fields.');
      event.preventDefault();
      return;
    }

    // Basic CAPTCHA check (only client-side; server validates authoritative)
    if (!/^[0-9]+$/.test(captcha)) {
      alert('Please enter a numeric answer for CAPTCHA.');
      event.preventDefault();
      return;
    }
  });
});

// Helper to read cookie by name (used if needed by client-side logic)
function readCookie(name) {
  const cookies = document.cookie.split(';');
  for (let c of cookies) {
    const [k, v] = c.trim().split('=');
    if (k === name) return decodeURIComponent(v);
  }
  return null;
}

// Autofill username from cookie if present (index.php already pre-fills via PHP but keep this for index.html fallback)
document.addEventListener('DOMContentLoaded', function () {
  const unameField = document.getElementById('username');
  if (!unameField) return;
  try {
    const remembered = readCookie('remember_username');
    if (remembered && unameField.value.trim() === '') {
      unameField.value = remembered;
    }
  } catch (e) {
    // ignore cookie read errors
  }
});