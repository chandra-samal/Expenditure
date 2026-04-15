<?php
// Shared sidebar + nav include
// Required: $activePage (string), $pageTitle (string)
// Optional: $navUser (string, PHP-rendered username), $showSearch (bool)
if (!isset($showSearch)) $showSearch = false;
if (!isset($navUser)) $navUser = '';
?>
<div class="sidebar">
  <div class="logo-details">
    <i class='bx bxs-wallet-alt'></i>
    <span class="logo_name">Expenditure</span>
  </div>
  <ul class="nav-links">
    <li><a href="home.php" <?= $activePage==='dashboard'?'class="active"':'' ?>><i class='bx bx-grid-alt'></i><span class="links_name">Dashboard</span></a></li>
    <li><a href="add-expenses.php" <?= $activePage==='expenses'?'class="active"':'' ?>><i class='bx bx-minus-circle'></i><span class="links_name">Expenses</span></a></li>
    <li><a href="add-income.php" <?= $activePage==='income'?'class="active"':'' ?>><i class='bx bx-plus-circle'></i><span class="links_name">Income</span></a></li>
    <li><a href="manage-transaction.php" <?= $activePage==='transactions'?'class="active"':'' ?>><i class='bx bx-transfer-alt'></i><span class="links_name">Transactions</span></a></li>
    <li><a href="analytics.php" <?= $activePage==='analytics'?'class="active"':'' ?>><i class='bx bx-bar-chart-alt-2'></i><span class="links_name">Analytics</span></a></li>
    <li><a href="report.php" <?= $activePage==='report'?'class="active"':'' ?>><i class="bx bx-file-blank"></i><span class="links_name">Report</span></a></li>
    <li><a href="user_profile.php" <?= $activePage==='settings'?'class="active"':'' ?>><i class='bx bx-cog'></i><span class="links_name">Settings</span></a></li>
    <li class="log_out"><a href="logout.php"><i class='bx bx-log-out'></i><span class="links_name">Log out</span></a></li>
  </ul>
</div>

<section class="home-section">
  <nav>
    <div class="sidebar-button">
      <i class='bx bx-menu sidebarBtn'></i>
      <span class="dashboard"><?= htmlspecialchars($pageTitle) ?></span>
    </div>
    <?php if ($showSearch): ?>
    <div class="search-box">
      <input type="text" id="search-input" placeholder="Search...">
      <i class='bx bx-search'></i>
    </div>
    <?php endif; ?>
    <div class="profile-details">
      <img src="images/maex.png" alt="">
      <?php if ($navUser): ?>
        <span class="admin_name"><?= htmlspecialchars($navUser) ?></span>
      <?php else: ?>
        <span class="admin_name" id="user-name">User</span>
      <?php endif; ?>
      <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
      <ul class="profile-options" id='profile-options'>
        <li><a href="user_profile.php"><i class="fas fa-user-circle"></i> User Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>
  </nav>
  <div class="home-content">
