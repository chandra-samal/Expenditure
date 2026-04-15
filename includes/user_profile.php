<?php
session_start(); error_reporting(0); include('database.php');
if (empty($_SESSION['detsuid'])) { header('location:logout.php'); exit; }

$userid = $_SESSION['detsuid'];
$result = mysqli_query($db, "SELECT * FROM users WHERE id = $userid");
$row = mysqli_fetch_assoc($result);
$first_name = $row['name']; $email = $row['email']; $phone = $row['phone'];
$created = date('d M Y', strtotime($row['created_at']));

// Handle password change
$pw_errors = []; $pw_success = '';
if (isset($_POST['submit'])) {
    $old_pw = $_POST['old_password'] ?? '';
    $new_pw = $_POST['new_password'] ?? '';
    $confirm_pw = $_POST['confirm_password'] ?? '';
    if (empty($old_pw)) $pw_errors[] = "Please enter your old password";
    if (empty($new_pw)) $pw_errors[] = "Please enter a new password";
    elseif (strlen($new_pw) < 8) $pw_errors[] = "New password must be at least 8 characters";
    if ($new_pw !== $confirm_pw) $pw_errors[] = "Passwords do not match";
    if (empty($pw_errors)) {
        $res2 = mysqli_query($db, "SELECT password FROM users WHERE id = $userid");
        $pwRow = mysqli_fetch_assoc($res2);
        if (!password_verify($old_pw, $pwRow['password'])) { $pw_errors[] = "Old password is incorrect"; }
        else {
            $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
            mysqli_query($db, "UPDATE users SET password='$hashed' WHERE id=$userid");
            $pw_success = "Password updated successfully!";
        }
    }
}

$navUser = $first_name;
$activePage = 'settings'; $pageTitle = 'Settings'; $showSearch = false;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Settings — Expenditure</title>
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head><body>
<?php include('_nav.php'); ?>

<div class="profile-layout">
  <!-- Sidebar card -->
  <div class="profile-sidebar">
    <div class="avatar-wrap">
      <div class="avatar-circle">
        <span><?= strtoupper(substr($first_name, 0, 1)) ?></span>
      </div>
    </div>
    <h3 class="profile-name"><?= htmlspecialchars($first_name) ?></h3>
    <p class="profile-email"><?= htmlspecialchars($email) ?></p>
    <p class="profile-joined"><i class='bx bx-calendar'></i> Joined <?= $created ?></p>

    <div class="profile-tabs">
      <button class="ptab active" data-tab="account"><i class='bx bx-user'></i> Account</button>
      <button class="ptab" data-tab="password"><i class='bx bx-lock-alt'></i> Password</button>
    </div>
  </div>

  <!-- Content -->
  <div class="profile-content">
    <!-- Account tab -->
    <div class="tab-panel active" id="tab-account">
      <div class="section-head">
        <p class="page-label">Personal Info</p>
        <h2 class="section-h2">Account Settings</h2>
      </div>
      <form method="POST" action="update_user.php">
        <div class="profile-form-grid">
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($first_name) ?>" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" required>
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($phone) ?>">
          </div>
          <div class="form-group">
            <label>Registered Date</label>
            <input type="text" class="form-control" value="<?= $created ?>" readonly style="opacity:.6">
          </div>
        </div>
        <div class="form-actions-row">
          <button type="submit" class="btn btn-primary" name="update_user"><i class='bx bx-check'></i> Save Changes</button>
          <a href="user_profile.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>

    <!-- Password tab -->
    <div class="tab-panel" id="tab-password">
      <div class="section-head">
        <p class="page-label">Security</p>
        <h2 class="section-h2">Change Password</h2>
      </div>
      <?php if (!empty($pw_errors)): ?>
        <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $pw_errors)) ?></div>
      <?php endif; ?>
      <?php if ($pw_success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($pw_success) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="profile-form-grid">
          <div class="form-group" style="grid-column:1/-1">
            <label>Current Password</label>
            <input type="password" class="form-control" name="old_password" placeholder="••••••••" required>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input type="password" class="form-control" name="new_password" placeholder="Min. 8 characters" required>
          </div>
          <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" class="form-control" name="confirm_password" placeholder="Repeat new password" required>
          </div>
        </div>
        <div class="form-actions-row">
          <button type="submit" class="btn btn-primary" name="submit"><i class='bx bx-lock-alt'></i> Update Password</button>
          <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

  </div></section>

<style>
.profile-layout { display: grid; grid-template-columns: 260px 1fr; gap: 24px; align-items: start; }

.profile-sidebar { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 28px 20px; text-align: center; position: sticky; top: calc(var(--nav-h) + 28px); }
.avatar-wrap { margin-bottom: 16px; }
.avatar-circle { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue)); display: flex; align-items: center; justify-content: center; margin: 0 auto; font-family: var(--font-display); font-size: 32px; font-weight: 800; color: #04080f; box-shadow: 0 8px 24px rgba(0,229,255,0.3); }
.profile-name { font-family: var(--font-display); font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px; }
.profile-email { font-size: 13px; color: var(--text-dim); margin: 0 0 8px; word-break: break-all; }
.profile-joined { font-size: 12px; color: var(--text-dim); display: flex; align-items: center; justify-content: center; gap: 5px; margin-bottom: 24px; }
.profile-joined i { font-size: 14px; }
.profile-tabs { display: flex; flex-direction: column; gap: 4px; }
.ptab { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: var(--radius-sm); background: transparent; border: none; color: var(--text-secondary); font-family: var(--font-body); font-size: 14px; font-weight: 500; cursor: pointer; transition: background 0.15s, color 0.15s; text-align: left; }
.ptab i { font-size: 18px; }
.ptab:hover { background: var(--bg-elevated); color: var(--text-primary); }
.ptab.active { background: rgba(0,229,255,0.1); color: var(--accent-cyan); }

.profile-content { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 32px; }
.tab-panel { display: none; }
.tab-panel.active { display: block; }
.section-head { margin-bottom: 28px; }
.page-label { font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--accent-cyan); margin: 0 0 4px; }
.section-h2 { font-family: var(--font-display); font-size: 22px; font-weight: 700; color: var(--text-primary); margin: 0; }
.profile-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px; }
.form-actions-row { display: flex; gap: 12px; align-items: center; }

@media(max-width:860px){ .profile-layout { grid-template-columns: 1fr; } .profile-sidebar { position: static; } }
@media(max-width:540px){ .profile-form-grid { grid-template-columns: 1fr; } }
</style>

<script>
let sidebar = document.querySelector(".sidebar"), sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  sidebarBtn.classList.replace(sidebar.classList.contains("active")?"bx-menu":"bx-menu-alt-right", sidebar.classList.contains("active")?"bx-menu-alt-right":"bx-menu");
}
document.getElementById('profile-options-toggle').addEventListener('click', () => document.getElementById('profile-options').classList.toggle('show'));

document.querySelectorAll('.ptab').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.ptab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    this.classList.add('active');
    document.getElementById('tab-' + this.dataset.tab).classList.add('active');
  });
});
<?php if (!empty($pw_errors) || $pw_success): ?>
document.querySelector('[data-tab="password"]').click();
<?php endif; ?>
</script>
</body></html>
