<?php
session_start(); error_reporting(0); include('database.php');
$sessionValid = !empty($_SESSION['detsuid']);
$activePage = 'report'; $pageTitle = 'Report'; $showSearch = false;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Report — Expenditure</title>
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="js/auth.js"></script>
</head><body>
<?php include('_nav.php'); ?>

<div class="page-header-row">
  <div><p class="page-label">Generate</p><h1 class="page-title">Report</h1></div>
</div>

<!-- Filter Card -->
<div class="rpt-card">
  <form id="reportForm">
    <div class="filter-grid">
      <div class="form-group">
        <label for="reportType">Report Type</label>
        <select class="form-control" id="reportType" name="reportType" required>
          <option value="" disabled selected>Select type...</option>
          <option value="expense">Expense Report</option>
          <option value="income">Income Report</option>
        </select>
      </div>
      <div class="form-group">
        <label for="startDate">Start Date</label>
        <input type="date" class="form-control" id="startDate" name="startDate" required>
      </div>
      <div class="form-group">
        <label for="endDate">End Date</label>
        <input type="date" class="form-control" id="endDate" name="endDate" required>
      </div>
      <div class="form-group d-flex align-items-end">
        <button type="submit" class="btn btn-primary btn-block" id="generateBtn">
          <span id="btnText"><i class="fas fa-chart-bar"></i> Generate</span>
          <span id="btnSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Results -->
<div id="report-results" style="display:none;">
  <div class="rpt-summary-row">
    <div class="rpt-summary-chip">
      <span class="chip-label">Records</span>
      <span class="chip-val" id="total-records">0</span>
    </div>
    <div class="rpt-summary-chip">
      <span class="chip-label">Total Amount</span>
      <span class="chip-val" id="total-amount">₹0.00</span>
    </div>
    <div class="rpt-summary-chip rpt-range">
      <span class="chip-label">Date Range</span>
      <span class="chip-val" id="date-range" style="font-size:15px">—</span>
    </div>
    <div style="margin-left:auto; display:flex; align-items:center;">
      <h3 class="rpt-title" id="report-title">Results</h3>
    </div>
  </div>

  <div class="rpt-card">
    <div class="table-responsive">
      <table class="table">
        <thead id="report-thead"></thead>
        <tbody id="report-tbody"></tbody>
      </table>
    </div>
  </div>
</div>

  </div></section>

<style>
.page-header-row { margin-bottom: 24px; }
.page-label { font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--accent-cyan); margin: 0 0 4px; }
.page-title { font-family: var(--font-display); font-size: 28px; font-weight: 800; color: var(--text-primary); margin: 0; letter-spacing: -0.02em; }
.rpt-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 28px; margin-bottom: 20px; }
.filter-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; }
.rpt-summary-row { display: flex; gap: 14px; margin-bottom: 16px; align-items: center; flex-wrap: wrap; }
.rpt-summary-chip { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 14px 20px; display: flex; flex-direction: column; gap: 4px; min-width: 140px; }
.chip-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-dim); }
.chip-val { font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--text-primary); }
.rpt-title { font-family: var(--font-display); font-size: 20px; font-weight: 700; color: var(--text-primary); margin: 0; }
@media(max-width:860px){ .filter-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:540px){ .filter-grid { grid-template-columns: 1fr; } }
</style>

<script>
function checkAuth() {
  var h = localStorage.getItem('access_token'), s = <?= $sessionValid?'true':'false' ?>;
  if (!h && !s) { window.location.href = 'index.php'; return false; } return true;
}
$(document).ready(function() {
  if (!checkAuth()) return;
  var u = localStorage.getItem('user_data');
  if (u) { try { $('#user-name').text(JSON.parse(u).name||'User'); } catch(e){} }
  var today = new Date(), ago = new Date(today - 30*86400000);
  $('#endDate').val(today.toISOString().split('T')[0]);
  $('#startDate').val(ago.toISOString().split('T')[0]);

  $('#reportForm').on('submit', function(e) {
    e.preventDefault();
    var type=$('#reportType').val(), start=$('#startDate').val(), end=$('#endDate').val();
    if (!type||!start||!end) { alert('Please fill in all fields'); return; }
    $('#generateBtn').prop('disabled',true); $('#btnText').hide(); $('#btnSpinner').show();
    $.ajax({ url:'api/report.php', type:'GET', data:{type:type,start_date:start,end_date:end}, dataType:'json',
      success: function(r) {
        if (r.status==='success') displayReport(r);
        else alert(r.message||'Error generating report');
      },
      error: function(xhr) {
        if (xhr.status===401) { localStorage.removeItem('access_token'); window.location.href='index.php'; }
        else alert('Error generating report.');
      },
      complete: function() { $('#generateBtn').prop('disabled',false); $('#btnText').show(); $('#btnSpinner').hide(); }
    });
  });
});
function displayReport(r) {
  var titles = { expense:'Expense Report', income:'Income Report' };
  $('#report-title').text(titles[r.report_type]||'Report');
  $('#date-range').text(r.date_range.start + ' → ' + r.date_range.end);
  $('#total-records').text(r.summary.total_records||0);
  var amt = r.summary.total_amount||r.summary.total_pending||r.summary.total_received||0;
  $('#total-amount').text('₹' + Number(amt).toLocaleString('en-IN',{minimumFractionDigits:2}));
  var thead = '<tr><th>#</th><th>Date</th><th>Category</th><th>Amount</th><th>Description</th></tr>';
  var tbody = r.data.length ? r.data.map(function(item,i){
    return '<tr><td>'+(i+1)+'</td><td>'+item.date+'</td><td>'+(item.category||'—')+'</td>' +
      '<td><strong style="color:var(--accent-'+(r.report_type==='income'?'green':'red')+')">₹'+Number(item.amount).toLocaleString('en-IN',{minimumFractionDigits:2})+'</strong></td>' +
      '<td>'+(item.description||'—')+'</td></tr>';
  }).join('') : '<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-dim)">No records found</td></tr>';
  $('#report-thead').html(thead); $('#report-tbody').html(tbody);
  $('#report-results').show();
}
let sidebar = document.querySelector(".sidebar"), sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  sidebarBtn.classList.replace(sidebar.classList.contains("active")?"bx-menu":"bx-menu-alt-right", sidebar.classList.contains("active")?"bx-menu-alt-right":"bx-menu");
}
document.getElementById('profile-options-toggle').addEventListener('click', () => document.getElementById('profile-options').classList.toggle('show'));
</script>
</body></html>
