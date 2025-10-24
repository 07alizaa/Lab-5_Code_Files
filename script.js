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
  // Load server-generated CAPTCHA question from captcha.php and inject into form.
  // This keeps index.html unchanged while enabling server-side CAPTCHA stored in $_SESSION.
  (function loadCaptcha() {
    // Request fresh captcha (no-cache)
    fetch('captcha.php', { cache: 'no-store' })
      .then(function (res) {
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      })
      .then(function (data) {
        if (!data || !data.question) return;

        // Try to find an existing placeholder span (#captcha-question)
        var qSpan = document.getElementById('captcha-question');
        var captchaInput = document.getElementById('captcha');

        if (qSpan) {
          qSpan.textContent = data.question;
        } else {
          // Create label + span for question
          var label = document.createElement('label');
          label.setAttribute('for', 'captcha');
          label.id = 'captcha-label';
          label.textContent = 'CAPTCHA: ';

          qSpan = document.createElement('span');
          qSpan.id = 'captcha-question';
          qSpan.textContent = data.question;
          label.appendChild(qSpan);

          // If captcha input doesn't exist, create it
          if (!captchaInput) {
            captchaInput = document.createElement('input');
            captchaInput.type = 'text';
            captchaInput.id = 'captcha';
            captchaInput.name = 'captcha';
            captchaInput.required = true;
            captchaInput.placeholder = 'Answer';
          }

          // Insert into DOM: try to place after password field if present
          var passwordField = document.getElementById('password');
          if (passwordField && passwordField.parentNode) {
            // insert label and input after password field
            var ref = passwordField.nextSibling;
            passwordField.parentNode.insertBefore(label, ref);
            passwordField.parentNode.insertBefore(captchaInput, label.nextSibling);
          } else {
            // fallback: append at end of the form
            form.appendChild(label);
            form.appendChild(captchaInput);
          }
        }
      })
      .catch(function (err) {
        // Do not block the page if captcha fails; server will still reject when required
        console.warn('Failed to load captcha:', err);
      });
  })();
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

// Autofill username from cookie if present (index.html may be the frontend; this provides a fallback)
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