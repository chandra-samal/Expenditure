<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expenditure — Create Account</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');
    :root { --bg:#04080f; --card:#0d1628; --elev:#101e30; --cyan:#00e5ff; --blue:#3d7eff; --green:#00d084; --border:rgba(255,255,255,0.08); --border2:rgba(255,255,255,0.12); --txt:#e8f1ff; --txt2:#8aa4cc; --txt3:#4d6585; }
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--txt);min-height:100vh;display:flex;-webkit-font-smoothing:antialiased;}

    .auth-visual{flex:1;position:relative;overflow:hidden;display:none;}
    @media(min-width:900px){.auth-visual{display:flex;}}
    .auth-visual img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;filter:brightness(0.3) saturate(0.6);}
    .auth-visual::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(0,229,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(0,229,255,0.04) 1px,transparent 1px);background-size:48px 48px;z-index:1;}
    .auth-visual-overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(0,229,255,0.06),rgba(61,126,255,0.04));z-index:1;}
    .auth-brand{position:absolute;top:48px;left:48px;z-index:2;display:flex;align-items:center;gap:12px;}
    .auth-brand-icon{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--cyan),var(--blue));display:flex;align-items:center;justify-content:center;font-size:19px;color:#04080f;box-shadow:0 8px 24px rgba(0,229,255,0.35);}
    .auth-brand-name{font-family:'Syne',sans-serif;font-size:19px;font-weight:700;color:var(--txt);}
    .auth-visual-content{position:absolute;bottom:0;left:0;right:0;padding:52px;z-index:2;}
    .auth-tagline{font-family:'Syne',sans-serif;font-size:34px;font-weight:800;line-height:1.15;color:var(--txt);margin-bottom:12px;}
    .auth-tagline span{color:var(--green);}
    .auth-sub{font-size:15px;color:var(--txt2);max-width:320px;line-height:1.7;}

    .auth-form-panel{width:100%;max-width:460px;display:flex;flex-direction:column;justify-content:center;padding:36px 40px;background:var(--card);overflow-y:auto;}
    .auth-form-logo{display:flex;align-items:center;gap:10px;margin-bottom:36px;}
    .auth-form-logo-icon{width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,var(--cyan),var(--blue));display:flex;align-items:center;justify-content:center;font-size:15px;color:#04080f;box-shadow:0 6px 18px rgba(0,229,255,0.3);}
    .auth-form-logo-name{font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--txt);}
    .auth-heading{font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:var(--txt);margin-bottom:4px;letter-spacing:-0.02em;}
    .auth-subheading{font-size:14px;color:var(--txt2);margin-bottom:28px;}

    #error-msg{font-size:13px;color:#ff7fa0;background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.25);border-radius:8px;padding:10px 14px;margin-bottom:14px;display:none;text-align:center;}
    #success-msg{font-size:13px;color:#5adaaa;background:rgba(0,208,132,0.1);border:1px solid rgba(0,208,132,0.25);border-radius:8px;padding:10px 14px;margin-bottom:14px;display:none;text-align:center;}
    #error-msg:not(:empty),#success-msg:not(:empty){display:block;}

    .form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    .field-group{margin-bottom:14px;}
    .field-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--txt2);margin-bottom:6px;display:block;}
    .field-wrap{position:relative;}
    .field-input{width:100%;background:var(--elev);border:1.5px solid rgba(255,255,255,0.09);border-radius:8px;color:var(--txt);font-family:'DM Sans',sans-serif;font-size:14px;padding:11px 40px 11px 14px;outline:none;transition:border-color 0.2s,box-shadow 0.2s;}
    .field-input::placeholder{color:var(--txt3);}
    .field-input:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(0,229,255,0.12);background:rgba(20,32,53,0.9);}
    .field-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:17px;color:var(--txt3);cursor:pointer;transition:color 0.2s;}
    .field-icon:hover{color:var(--cyan);}

    .terms-row{display:flex;align-items:center;gap:8px;margin-bottom:22px;font-size:13px;color:var(--txt2);}
    .terms-row input{width:15px;height:15px;accent-color:var(--green);cursor:pointer;}
    .terms-row a{color:var(--cyan);text-decoration:none;}

    .btn-signup{width:100%;padding:13px;border-radius:8px;border:none;background:linear-gradient(135deg,var(--green),#009b5e);color:#fff;font-family:'Syne',sans-serif;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity 0.2s,transform 0.2s;box-shadow:0 8px 24px rgba(0,208,132,0.3);margin-bottom:20px;}
    .btn-signup:hover{opacity:0.9;transform:translateY(-1px);box-shadow:0 12px 30px rgba(0,208,132,0.4);}
    .btn-signup:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
    .spinner-border{width:17px;height:17px;border-width:2.5px;}
    .auth-footer{text-align:center;font-size:13.5px;color:var(--txt2);}
    .auth-footer a{color:var(--cyan);font-weight:600;text-decoration:none;}
    .auth-footer a:hover{text-decoration:underline;}
    @media(max-width:440px){.auth-form-panel{padding:28px 20px;} .form-row-2{grid-template-columns:1fr;}}
  </style>
</head>
<body>
<!-- Left visual -->
<div class="auth-visual">
  <img src="images/draw1.webp" alt="">
  <div class="auth-visual-overlay"></div>
  <div class="auth-brand">
    <div class="auth-brand-icon"><i class='bx bxs-wallet-alt'></i></div>
    <span class="auth-brand-name">Expenditure</span>
  </div>
  <div class="auth-visual-content">
    <div class="auth-tagline">Start tracking your<br><span>finances</span> today.</div>
    <p class="auth-sub">Create your free account and take control of every rupee you earn and spend.</p>
  </div>
</div>

<!-- Right form -->
<div class="auth-form-panel">
  <div class="auth-form-logo">
    <div class="auth-form-logo-icon"><i class='bx bxs-wallet-alt'></i></div>
    <span class="auth-form-logo-name">Expenditure</span>
  </div>
  <h1 class="auth-heading">Create account</h1>
  <p class="auth-subheading">It's free and takes less than a minute</p>

  <p id="error-msg"></p>
  <p id="success-msg"></p>

  <form id="signupForm">
    <div class="form-row-2">
      <div class="field-group">
        <label class="field-label">Full Name</label>
        <div class="field-wrap">
          <input type="text" class="field-input" name="name" id="name" placeholder="Your name" required>
          <i class="bx bx-user field-icon" style="pointer-events:none"></i>
        </div>
      </div>
      <div class="field-group">
        <label class="field-label">Phone</label>
        <div class="field-wrap">
          <input type="text" class="field-input" name="phone" id="phone" placeholder="+91 00000 00000" required>
          <i class="bx bx-phone field-icon" style="pointer-events:none"></i>
        </div>
      </div>
    </div>

    <div class="field-group">
      <label class="field-label">Email Address</label>
      <div class="field-wrap">
        <input type="email" class="field-input" name="email" id="email" placeholder="you@example.com" required>
        <i class="bx bx-envelope field-icon" style="pointer-events:none"></i>
      </div>
    </div>

    <div class="form-row-2">
      <div class="field-group">
        <label class="field-label">Password</label>
        <div class="field-wrap">
          <input type="password" class="field-input" name="password" id="password" placeholder="Min. 8 characters" required>
          <i class="bx bx-hide field-icon show-hide"></i>
        </div>
      </div>
      <div class="field-group">
        <label class="field-label">Confirm Password</label>
        <div class="field-wrap">
          <input type="password" class="field-input" name="confirm_password" id="confirm_password" placeholder="Repeat password" required>
          <i class="bx bx-hide field-icon show-hide"></i>
        </div>
      </div>
    </div>

    <div class="terms-row">
      <input type="checkbox" id="terms" required>
      <label for="terms">I agree to the <a href="#!">Terms of Service</a></label>
    </div>

    <button type="submit" id="signupBtn" class="btn-signup">
      <span id="signupText">Create Account</span>
      <span id="signupSpinner" class="spinner-border" style="display:none;"></span>
    </button>
    <div class="auth-footer">Already have an account? <a href="index.php">Sign in</a></div>
  </form>
</div>

<script>
document.querySelectorAll('.show-hide').forEach(function(icon) {
  icon.addEventListener('click', function() {
    var input = this.parentElement.querySelector('input');
    if (input.type === 'password') { input.type = 'text'; this.classList.replace('bx-hide','bx-show'); }
    else { input.type = 'password'; this.classList.replace('bx-show','bx-hide'); }
  });
});

document.getElementById('signupForm').addEventListener('submit', function(e) {
  e.preventDefault();
  var errorMsg = document.getElementById('error-msg'), successMsg = document.getElementById('success-msg');
  var btn = document.getElementById('signupBtn'), text = document.getElementById('signupText'), spin = document.getElementById('signupSpinner');
  errorMsg.textContent = ''; successMsg.textContent = '';
  btn.disabled = true; text.style.display = 'none'; spin.style.display = 'inline-block';

  fetch('api/signup.php', {
    method: 'POST', headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      name: document.getElementById('name').value,
      email: document.getElementById('email').value,
      phone: document.getElementById('phone').value,
      password: document.getElementById('password').value,
      confirm_password: document.getElementById('confirm_password').value
    })
  }).then(r => r.json()).then(data => {
    if (data.status === 'success') {
      successMsg.textContent = data.message;
      setTimeout(() => window.location.href = 'index.php', 1500);
    } else {
      errorMsg.textContent = data.message || 'An error occurred';
      btn.disabled = false; text.style.display = 'inline'; spin.style.display = 'none';
    }
  }).catch(() => {
    errorMsg.textContent = 'A network error occurred. Please try again.';
    btn.disabled = false; text.style.display = 'inline'; spin.style.display = 'none';
  });
});
</script>
</body>
</html>
