<?php
session_start(); error_reporting(0); include('database.php');
if (empty($_SESSION['detsuid'])) { header('location:logout.php'); exit; }
$uid = $_SESSION['detsuid'];
$ret = mysqli_query($db, "select name from users where id='$uid'");
$row = mysqli_fetch_array($ret);
$navUser = $row['name'];
$activePage = 'analytics'; $pageTitle = 'Analytics'; $showSearch = false;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Analytics — Expenditure</title>
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head><body>
<?php include('_nav.php'); ?>

<div class="analytics-header">
  <p class="page-label">Spending Overview</p>
  <h1 class="page-title">Analytics</h1>
</div>

<div class="analytics-grid">
  <!-- Pie chart -->
  <div class="analytics-panel chart-panel-wrap">
    <div class="panel-header">
      <div><p class="section-label">By Category</p><h2 class="section-title">Expense Breakdown</h2></div>
    </div>
    <div class="pie-wrap">
      <canvas id="myChart"></canvas>
      <div id="no-data" style="display:none;" class="no-data-msg"><i class="fas fa-chart-pie"></i><p>No expense data yet.</p></div>
    </div>
  </div>

  <!-- Legend / category list -->
  <div class="analytics-panel legend-panel">
    <div class="panel-header">
      <div><p class="section-label">Detail</p><h2 class="section-title">Categories</h2></div>
    </div>
    <div class="legend-body" id="legend-list">
      <div class="legend-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
    </div>
  </div>
</div>

  </div></section>

<style>
.analytics-header { margin-bottom: 24px; }
.page-label { font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--accent-cyan); margin: 0 0 4px; }
.page-title { font-family: var(--font-display); font-size: 28px; font-weight: 800; color: var(--text-primary); margin: 0; letter-spacing: -0.02em; }

.analytics-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
.analytics-panel { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); overflow: hidden; transition: border-color 0.2s; }
.analytics-panel:hover { border-color: var(--border-accent); }

.pie-wrap { padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 380px; position: relative; }
.pie-wrap canvas { max-height: 360px; max-width: 360px; }

.no-data-msg { display: flex; flex-direction: column; align-items: center; gap: 12px; color: var(--text-dim); }
.no-data-msg i { font-size: 48px; opacity: 0.3; }
.no-data-msg p { font-size: 14px; }

.legend-body { padding: 16px; max-height: 440px; overflow-y: auto; }
.legend-loading { padding: 32px; text-align: center; color: var(--text-dim); font-size: 13px; }

.legend-item { display: flex; align-items: center; gap: 12px; padding: 12px 8px; border-bottom: 1px solid var(--border); transition: background 0.15s; border-radius: var(--radius-sm); }
.legend-item:last-child { border-bottom: none; }
.legend-item:hover { background: var(--bg-elevated); }
.legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.legend-name { flex: 1; font-size: 13.5px; color: var(--text-secondary); }
.legend-pct { font-family: var(--font-display); font-size: 14px; font-weight: 700; color: var(--text-primary); }
.legend-amount { font-size: 12px; color: var(--text-dim); margin-top: 2px; }

@media(max-width:900px){ .analytics-grid { grid-template-columns: 1fr; } }
</style>

<script>
const COLORS = ['#00e5ff','#3d7eff','#00d084','#f5a623','#ff4d6d','#7b5ea7','#00b8d9','#ff7c43','#a8edea','#fed6e3'];

fetch('pie-data.php')
  .then(r => r.json())
  .then(data => {
    if (!data || data.length === 0) {
      document.getElementById('myChart').style.display = 'none';
      document.getElementById('no-data').style.display = 'flex';
      document.getElementById('legend-list').innerHTML = '<div class="legend-loading">No data</div>';
      return;
    }
    const labels = data.map(d => d.category);
    const values = data.map(d => parseFloat(d.total_expense));
    const total  = values.reduce((a,b) => a+b, 0);
    const pcts   = values.map(v => ((v/total)*100).toFixed(1));

    // Build pie chart
    const ctx = document.getElementById('myChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{ data: values, backgroundColor: COLORS.slice(0, data.length), borderWidth: 2, borderColor: 'rgba(10,18,33,0.8)', hoverOffset: 8 }]
      },
      options: {
        responsive: true, maintainAspectRatio: true, cutout: '60%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(10,18,33,0.95)', borderColor: 'rgba(255,255,255,0.1)', borderWidth: 1,
            titleColor: '#8aa4cc', bodyColor: '#e8f1ff', padding: 12, cornerRadius: 10,
            callbacks: {
              label: function(ctx) {
                return '  ₹' + Number(ctx.parsed).toLocaleString('en-IN',{minimumFractionDigits:2}) + ' (' + pcts[ctx.dataIndex] + '%)';
              }
            }
          }
        }
      }
    });

    // Build legend
    const legend = document.getElementById('legend-list');
    legend.innerHTML = data.map((item, i) => `
      <div class="legend-item">
        <div class="legend-dot" style="background:${COLORS[i % COLORS.length]}"></div>
        <div style="flex:1; min-width:0;">
          <div class="legend-name">${item.category}</div>
          <div class="legend-amount">₹${Number(item.total_expense).toLocaleString('en-IN',{minimumFractionDigits:2})}</div>
        </div>
        <div class="legend-pct">${pcts[i]}%</div>
      </div>`).join('');
  })
  .catch(() => {
    document.getElementById('legend-list').innerHTML = '<div class="legend-loading" style="color:var(--accent-red)">Error loading data</div>';
  });

let sidebar = document.querySelector(".sidebar"), sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  sidebarBtn.classList.replace(sidebar.classList.contains("active")?"bx-menu":"bx-menu-alt-right", sidebar.classList.contains("active")?"bx-menu-alt-right":"bx-menu");
}
document.getElementById('profile-options-toggle').addEventListener('click', () => document.getElementById('profile-options').classList.toggle('show'));
</script>
</body></html>
