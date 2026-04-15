<?php
session_start();
error_reporting(0);
include('database.php');
$sessionValid = !empty($_SESSION['detsuid']);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Expenditure</title>
  <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <script src="js/auth.js"></script>
</head>
<body>

<div class="sidebar">
  <div class="logo-details">
    <i class='bx bxs-wallet-alt'></i>
    <span class="logo_name">Expenditure</span>
  </div>
  <ul class="nav-links">
    <li><a href="#" class="active"><i class='bx bx-grid-alt'></i><span class="links_name">Dashboard</span></a></li>
    <li><a href="add-expenses.php"><i class='bx bx-minus-circle'></i><span class="links_name">Expenses</span></a></li>
    <li><a href="add-income.php"><i class='bx bx-plus-circle'></i><span class="links_name">Income</span></a></li>
    <li><a href="manage-transaction.php"><i class='bx bx-transfer-alt'></i><span class="links_name">Transactions</span></a></li>
    <li><a href="analytics.php"><i class='bx bx-bar-chart-alt-2'></i><span class="links_name">Analytics</span></a></li>
    <li><a href="report.php"><i class="bx bx-file-blank"></i><span class="links_name">Report</span></a></li>
    <li><a href="user_profile.php"><i class='bx bx-cog'></i><span class="links_name">Settings</span></a></li>
    <li class="log_out"><a href="logout.php"><i class='bx bx-log-out'></i><span class="links_name">Log out</span></a></li>
  </ul>
</div>

<section class="home-section">
  <nav>
    <div class="sidebar-button">
      <i class='bx bx-menu sidebarBtn'></i>
      <span class="dashboard">Dashboard</span>
    </div>
    <div class="search-box">
      <input type="text" id="search-input" placeholder="Search transactions...">
      <i class='bx bx-search'></i>
    </div>
    <div class="profile-details">
      <img src="images/maex.png" alt="">
      <span class="admin_name" id="user-name">Loading...</span>
      <i class='bx bx-chevron-down' id='profile-options-toggle'></i>
      <ul class="profile-options" id='profile-options'>
        <li><a href="user_profile.php"><i class="fas fa-user-circle"></i> User Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="home-content">
    <!-- Stat Cards -->
    <div class="overview-boxes">
      <div class="box">
        <div class="right-side">
          <div class="box-topic">Today's Expense</div>
          <div class="number" id="today-expense">₹0</div>
          <div class="indicator"><i class='bx bx-trending-up'></i><span class="text">vs yesterday</span></div>
        </div>
        <i class='fas fa-receipt cart'></i>
      </div>
      <div class="box">
        <div class="right-side">
          <div class="box-topic">Yesterday</div>
          <div class="number" id="yesterday-expense">₹0</div>
          <div class="indicator"><i class='bx bx-trending-up'></i><span class="text">previous day</span></div>
        </div>
        <i class="fas fa-wallet cart two"></i>
      </div>
      <div class="box">
        <div class="right-side">
          <div class="box-topic">Last 30 Days</div>
          <div class="number" id="monthly-expense">₹0</div>
          <div class="indicator"><i class='bx bx-calendar'></i><span class="text">monthly spend</span></div>
        </div>
        <i class='fas fa-calendar-alt cart three'></i>
      </div>
      <div class="box">
        <div class="right-side">
          <div class="box-topic">Total Expense</div>
          <div class="number" id="total-expense">₹0</div>
          <div class="indicator"><i class='bx bx-line-chart'></i><span class="text">all time</span></div>
        </div>
        <i class='fas fa-piggy-bank cart four'></i>
      </div>
    </div>

    <!-- Chart + Table Grid -->
    <div class="dashboard-grid">
      <section class="dashboard-panel">
        <div class="panel-header">
          <div>
            <p class="section-label">Expense Trend</p>
            <h2 class="section-title">Monthly Spending</h2>
          </div>
          <span class="badge-light">Last 30 days</span>
        </div>
        <div class="chart-panel">
          <canvas id="myChart"></canvas>
        </div>
      </section>

      <section class="dashboard-panel stats-panel">
        <div class="panel-header">
          <div>
            <p class="section-label">Breakdown</p>
            <h2 class="section-title">By Category</h2>
          </div>
        </div>
        <div class="table-panel">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr><th>%</th><th>Category</th><th>Amount</th></tr>
              </thead>
              <tbody id="expense-table-body"></tbody>
              <tfoot>
                <tr><td></td><td><strong>Total</strong></td><td>₹<span id="category-total">0</span></td></tr>
              </tfoot>
            </table>
          </div>
        </div>
      </section>
    </div>
  </div>
</section>

<button id="add-button" title="Add Expense"><i class="fas fa-plus"></i></button>

<script>
var chart;

function checkAuthAndRedirect() {
  var hasToken = localStorage.getItem('access_token');
  var hasSession = <?php echo $sessionValid ? 'true' : 'false'; ?>;
  if (!hasToken && !hasSession) { window.location.href = 'index.php'; return false; }
  return true;
}

function loadDashboardData() {
  if (!checkAuthAndRedirect()) return;
  $.ajax({
    url: 'api/dashboard.php', type: 'GET', dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        var data = response.data;
        $('#user-name').text(data.user.name || 'User');
        $('#today-expense').text('₹' + (data.today_expense || 0));
        $('#yesterday-expense').text('₹' + (data.yesterday_expense || 0));
        $('#monthly-expense').text('₹' + (data.monthly_expense || 0));
        $('#total-expense').text('₹' + (data.total_expense || 0));
        updateChart(data.chart.labels, data.chart.data);
        updateCategoryTable(data.categories);
      } else {
        if (response.message && response.message.includes('Unauthorized')) {
          localStorage.removeItem('access_token');
          window.location.href = 'index.php';
        }
      }
    },
    error: function(xhr) {
      if (xhr.status === 401) { localStorage.removeItem('access_token'); window.location.href = 'index.php'; }
      else { $('#user-name').text('Error loading data.'); }
    }
  });
}

function updateChart(labels, data) {
  var ctx = document.getElementById('myChart').getContext('2d');
  if (chart) chart.destroy();

  var gradient = ctx.createLinearGradient(0, 0, 0, 360);
  gradient.addColorStop(0, 'rgba(0, 229, 255, 0.35)');
  gradient.addColorStop(1, 'rgba(0, 229, 255, 0.01)');

  chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Expenses',
        data: data,
        backgroundColor: gradient,
        borderColor: '#00e5ff',
        borderWidth: 1.5,
        borderRadius: 6,
        borderSkipped: false,
        hoverBackgroundColor: 'rgba(0,229,255,0.5)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { intersect: false, mode: 'index' },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(10,18,33,0.95)',
          borderColor: 'rgba(255,255,255,0.1)',
          borderWidth: 1,
          titleColor: '#8aa4cc',
          bodyColor: '#e8f1ff',
          padding: 12,
          cornerRadius: 10,
          callbacks: {
            label: function(ctx) { return '  ₹' + ctx.parsed.y.toLocaleString(); }
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
          ticks: { color: '#4d6585', font: { size: 11 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
          ticks: { color: '#4d6585', font: { size: 11 }, callback: function(v) { return '₹' + v; } }
        }
      }
    }
  });
}

function updateCategoryTable(categories) {
  var total = categories.reduce(function(acc, curr) { return acc + curr.total_expense; }, 0);
  var colors = ['#00e5ff','#3d7eff','#00d084','#f5a623','#ff4d6d','#7b5ea7','#00b8d9','#ff7c43','#a8edea','#fed6e3'];
  var rows = categories.map(function(item, i) {
    var pct = total > 0 ? ((item.total_expense / total) * 100).toFixed(1) : 0;
    var color = colors[i % colors.length];
    return '<tr><td><span class="badge" style="background:' + color + '22;color:' + color + ';border:1px solid ' + color + '44">' + pct + '%</span></td><td style="color:var(--text-primary)">' + item.category + '</td><td>₹' + item.total_expense.toFixed(2) + '</td></tr>';
  }).join('');
  $('#expense-table-body').html(rows);
  $('#category-total').text(total.toFixed(2));
}

$(document).ready(function() {
  loadDashboardData();
  setInterval(loadDashboardData, 30000);
  $('#search-input').on('keyup', function() {
    var value = $(this).val().toLowerCase();
    $('table tbody tr').filter(function() { $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1); });
  });
});

let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  if (sidebar.classList.contains("active")) sidebarBtn.classList.replace("bx-menu","bx-menu-alt-right");
  else sidebarBtn.classList.replace("bx-menu-alt-right","bx-menu");
};
document.getElementById('profile-options-toggle').addEventListener('click', () => {
  document.getElementById('profile-options').classList.toggle('show');
});
document.getElementById('add-button').addEventListener('click', () => {
  document.getElementById('add-button').style.transform = 'scale(0) rotate(180deg)';
  setTimeout(() => { window.location.href = "add-expenses.php"; }, 200);
});
</script>
</body>
</html>
