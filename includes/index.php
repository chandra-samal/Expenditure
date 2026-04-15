<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expenditure — Login</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="js/auth.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
      --bg:     #04080f;
      --panel:  #0a1221;
      --card:   #0d1628;
      --elev:   #101e30;
      --cyan:   #00e5ff;
      --blue:   #3d7eff;
      --border: rgba(255,255,255,0.08);
      --txt:    #e8f1ff;
      --txt2:   #8aa4cc;
      --txt3:   #4d6585;
    }

    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--txt);
      min-height: 100vh;
      display: flex;
      -webkit-font-smoothing: antialiased;
    }

    /* Left panel */
    .auth-visual {
      flex: 1;
      position: relative;
      overflow: hidden;
      display: none;
    }
    @media (min-width: 900px) { .auth-visual { display: flex; } }

    .auth-visual img {
      position: absolute; inset: 0;
      width: 100%; height: 100%;
      object-fit: cover;
      filter: brightness(0.35) saturate(0.7);
    }

    .auth-visual-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(135deg, rgba(0,229,255,0.08) 0%, rgba(61,126,255,0.05) 100%);
    }

    /* Grid pattern */
    .auth-visual::before {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(0,229,255,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,229,255,0.04) 1px, transparent 1px);
      background-size: 48px 48px;
      z-index: 1;
    }

    .auth-visual-content {
      position: relative;
      z-index: 2;
      padding: 52px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      height: 100%;
      width: 100%;
    }

    .auth-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      position: absolute;
      top: 52px;
      left: 52px;
    }
    .auth-brand-icon {
      width: 42px; height: 42px;
      border-radius: 10px;
      background: linear-gradient(135deg, var(--cyan), var(--blue));
      display: flex; align-items: center; justify-content: center;
      font-size: 20px;
      color: #04080f;
      box-shadow: 0 8px 24px rgba(0,229,255,0.35);
    }
    .auth-brand-name {
      font-family: 'Syne', sans-serif;
      font-size: 20px;
      font-weight: 700;
      color: var(--txt);
    }

    .auth-tagline {
      font-family: 'Syne', sans-serif;
      font-size: 36px;
      font-weight: 800;
      line-height: 1.15;
      color: var(--txt);
      margin-bottom: 16px;
    }
    .auth-tagline span { color: var(--cyan); }
    .auth-sub {
      font-size: 15px;
      color: var(--txt2);
      max-width: 340px;
      line-height: 1.7;
    }

    /* Floating stats */
    .auth-stat-cards {
      display: flex;
      gap: 14px;
      margin-bottom: 40px;
    }
    .auth-stat {
      background: rgba(13,22,40,0.85);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 14px;
      padding: 14px 18px;
      backdrop-filter: blur(8px);
    }
    .auth-stat-value { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 700; color: var(--cyan); }
    .auth-stat-label { font-size: 11px; color: var(--txt3); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }

    /* Right panel (form) */
    .auth-form-panel {
      width: 100%;
      max-width: 440px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 40px 40px;
      background: var(--card);
      position: relative;
    }

    .auth-form-logo {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 44px;
    }
    .auth-form-logo-icon {
      width: 36px; height: 36px; border-radius: 8px;
      background: linear-gradient(135deg, var(--cyan), var(--blue));
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: #04080f;
      box-shadow: 0 6px 18px rgba(0,229,255,0.3);
    }
    .auth-form-logo-name { font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 700; color: var(--txt); }

    .auth-heading { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; color: var(--txt); margin-bottom: 6px; letter-spacing: -0.02em; }
    .auth-subheading { font-size: 14px; color: var(--txt2); margin-bottom: 36px; }

    /* Messages */
    #error-msg { font-size: 13px; color: #ff7fa0; background: rgba(255,77,109,0.1); border: 1px solid rgba(255,77,109,0.25); border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; display: none; text-align: center; }
    #success-msg { font-size: 13px; color: #5adaaa; background: rgba(0,208,132,0.1); border: 1px solid rgba(0,208,132,0.25); border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; display: none; text-align: center; }
    #error-msg:not(:empty), #success-msg:not(:empty) { display: block; }

    /* Form field */
    .field-group { margin-bottom: 18px; }
    .field-label { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--txt2); margin-bottom: 8px; display: block; }
    .field-wrap { position: relative; }
    .field-input {
      width: 100%;
      background: var(--elev);
      border: 1.5px solid rgba(255,255,255,0.09);
      border-radius: 10px;
      color: var(--txt);
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      padding: 13px 42px 13px 16px;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .field-input::placeholder { color: var(--txt3); }
    .field-input:focus {
      border-color: var(--cyan);
      box-shadow: 0 0 0 3px rgba(0,229,255,0.12);
      background: rgba(20,32,53,0.9);
    }
    .field-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 18px; color: var(--txt3); cursor: pointer; transition: color 0.2s; }
    .field-icon:hover { color: var(--cyan); }

    /* Remember + forgot */
    .auth-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
    .remember-label { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--txt2); cursor: pointer; }
    .remember-label input[type="checkbox"] {
      width: 16px; height: 16px;
      accent-color: var(--cyan);
      cursor: pointer;
    }
    .forgot-link { font-size: 13px; color: var(--cyan); text-decoration: none; opacity: 0.8; transition: opacity 0.2s; }
    .forgot-link:hover { opacity: 1; color: var(--cyan); }

    /* Submit button */
    .btn-login {
      width: 100%;
      padding: 14px;
      border-radius: 10px;
      border: none;
      background: linear-gradient(135deg, var(--cyan) 0%, var(--blue) 100%);
      color: #04080f;
      font-family: 'Syne', sans-serif;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: opacity 0.2s, transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 8px 24px rgba(0,229,255,0.3);
      margin-bottom: 24px;
    }
    .btn-login:hover { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 12px 30px rgba(0,229,255,0.4); }
    .btn-login:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .spinner-border { width: 18px; height: 18px; border-width: 2.5px; }

    /* Sign up link */
    .auth-footer { text-align: center; font-size: 13.5px; color: var(--txt2); }
    .auth-footer a { color: var(--cyan); font-weight: 600; text-decoration: none; }
    .auth-footer a:hover { text-decoration: underline; }

    /* Decorative glow */
    .auth-form-panel::before {
      content: '';
      position: absolute;
      top: -60px; right: -60px;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(0,229,255,0.06) 0%, transparent 70%);
      pointer-events: none;
    }

    @media (max-width: 440px) {
      .auth-form-panel { padding: 30px 24px; }
    }
  </style>
</head>
<body>

<!-- Left visual panel -->
<div class="auth-visual">
  <img src="images/Wavy_Tech-28_Single-10.jpg" alt="">
  <div class="auth-visual-overlay"></div>
  <div class="auth-brand">
    <div class="auth-brand-icon"><i class='bx bxs-wallet-alt'></i></div>
    <span class="auth-brand-name">Expenditure</span>
  </div>
  <div class="auth-visual-content">
    <div class="auth-stat-cards">
      <div class="auth-stat">
        <div class="auth-stat-value">₹0</div>
        <div class="auth-stat-label">Tracked Today</div>
      </div>
      <div class="auth-stat">
        <div class="auth-stat-value">100%</div>
        <div class="auth-stat-label">Secure</div>
      </div>
    </div>
    <div class="auth-tagline">Track every<br><span>rupee</span> you spend.</div>
    <p class="auth-sub">Smart expense tracking to help you understand where your money goes.</p>
  </div>
</div>

<!-- Right form panel -->
<div class="auth-form-panel">
  <div class="auth-form-logo">
    <div class="auth-form-logo-icon"><i class='bx bxs-wallet-alt'></i></div>
    <span class="auth-form-logo-name">Expenditure</span>
  </div>

  <h1 class="auth-heading">Welcome back</h1>
  <p class="auth-subheading">Sign in to your account</p>

  <p id="error-msg"></p>
  <p id="success-msg"></p>

  <form id="loginForm">
    <div class="field-group">
      <label class="field-label" for="email">Email address</label>
      <div class="field-wrap">
        <input type="email" id="email" name="email" class="field-input" placeholder="you@example.com" required>
        <i class="bx bx-envelope field-icon" style="pointer-events:none"></i>
      </div>
    </div>

    <div class="field-group">
      <label class="field-label" for="password">Password</label>
      <div class="field-wrap">
        <input type="password" id="password" name="password" class="field-input" placeholder="••••••••" required>
        <i class="bx bx-hide field-icon show-hide"></i>
      </div>
    </div>

    <div class="auth-row">
      <label class="remember-label">
        <input type="checkbox" id="rememberMe">
        Remember me
      </label>
      <a href="#!" class="forgot-link">Forgot password?</a>
    </div>

    <button type="submit" id="loginBtn" class="btn-login">
      <span id="loginText">Sign in</span>
      <span id="loginSpinner" class="spinner-border" role="status" style="display:none;"></span>
    </button>

    <div class="auth-footer">
      Don't have an account? <a href="signup.php">Create account</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  if (typeof AuthManager !== 'undefined' && AuthManager.isAuthenticated()) {
    window.location.href = 'home.php';
  }
});

document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
  var email = document.getElementById('email').value;
  var password = document.getElementById('password').value;
  var errorMsg = document.getElementById('error-msg');
  var successMsg = document.getElementById('success-msg');
  var loginBtn = document.getElementById('loginBtn');
  var loginText = document.getElementById('loginText');
  var loginSpinner = document.getElementById('loginSpinner');

  errorMsg.textContent = ''; successMsg.textContent = '';
  loginBtn.disabled = true;
  loginText.style.display = 'none';
  loginSpinner.style.display = 'inline-block';

  $.ajax({
    url: 'api/login.php', type: 'POST', contentType: 'application/json',
    data: JSON.stringify({ email: email, password: password }),
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        localStorage.setItem('access_token', response.access_token);
        localStorage.setItem('user_data', JSON.stringify(response.user));
        successMsg.textContent = 'Login successful! Redirecting...';
        setTimeout(function() { window.location.href = 'home.php'; }, 500);
      } else {
        errorMsg.textContent = response.message || 'Invalid credentials';
        loginBtn.disabled = false; loginText.style.display = 'inline'; loginSpinner.style.display = 'none';
      }
    },
    error: function(xhr) {
      var message = 'An error occurred. Please try again.';
      try { var r = JSON.parse(xhr.responseText); message = r.message || message; } catch(e) {}
      errorMsg.textContent = message;
      loginBtn.disabled = false; loginText.style.display = 'inline'; loginSpinner.style.display = 'none';
    }
  });
});

// Password toggle
document.querySelector('.show-hide').addEventListener('click', function() {
  var input = document.getElementById('password');
  if (input.type === 'password') {
    input.type = 'text';
    this.classList.replace('bx-hide', 'bx-show');
  } else {
    input.type = 'password';
    this.classList.replace('bx-show', 'bx-hide');
  }
});
</script>
</body>
</html>
