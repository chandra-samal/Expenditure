<?php
session_start(); error_reporting(0); include('database.php');
$sessionValid = !empty($_SESSION['detsuid']);
$activePage = 'transactions'; $pageTitle = 'Transactions'; $showSearch = true;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Transactions — Expenditure</title>
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script src="js/auth.js"></script>
</head><body>
<?php include('_nav.php'); ?>

<!-- Totals bar -->
<div class="totals-bar">
  <div class="total-chip income"><span class="chip-label">Income</span><span class="chip-val" id="total-income">₹0.00</span></div>
  <div class="total-chip expense"><span class="chip-label">Expense</span><span class="chip-val" id="total-expense">₹0.00</span></div>
  <div class="total-chip balance"><span class="chip-label">Net Balance</span><span class="chip-val" id="net-balance">₹0.00</span></div>
</div>

<!-- Toolbar -->
<div class="toolbar-row">
  <div class="toolbar-left">
    <select class="ctrl-select" id="type-filter">
      <option value="all">All Types</option>
      <option value="expense">Expenses</option>
      <option value="income">Income</option>
    </select>
    <label class="ctrl-label">Show
      <select class="ctrl-select" id="select-entries">
        <option value="10">10</option><option value="25">25</option>
        <option value="50">50</option><option value="100">100</option>
      </select>
      entries
    </label>
  </div>
  <div class="toolbar-right">
    <a href="api/export-csv.php?type=all" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Export CSV</a>
    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#import-csv-modal"><i class="fas fa-upload"></i> Import</button>
  </div>
</div>

<!-- Table -->
<div class="table-card">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr><th>#</th><th>Type</th><th>Category</th><th>Amount</th><th>Description</th><th>Date</th><th>Action</th></tr>
      </thead>
      <tbody id="transactions-tbody">
        <tr><td colspan="7" class="loading-cell"><i class="fas fa-spinner fa-spin"></i> Loading transactions...</td></tr>
      </tbody>
    </table>
  </div>
  <div class="table-footer">
    <nav><ul class="pagination justify-content-end mb-0" id="pagination"></ul></nav>
  </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="import-csv-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form id="import-csv-form" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title">Import CSV</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info"><strong>Format:</strong> Date, Particulars, Expense, Income, Category</div>
        <div class="form-group">
          <label for="csv-file">Select CSV File</label>
          <input type="file" class="form-control" id="csv-file" name="csv-file" accept=".csv" required style="padding:8px">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Import</button>
      </div>
    </form>
  </div></div>
</div>

  </div></section>

<style>
.totals-bar { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
.total-chip { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 14px 20px; flex: 1; min-width: 160px; display: flex; flex-direction: column; gap: 4px; }
.total-chip.income  { border-color: rgba(0,208,132,0.25); }
.total-chip.expense { border-color: rgba(255,77,109,0.25); }
.total-chip.balance { border-color: rgba(61,126,255,0.25); }
.chip-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-dim); }
.chip-val { font-family: var(--font-display); font-size: 22px; font-weight: 700; }
.total-chip.income  .chip-val { color: var(--accent-green); }
.total-chip.expense .chip-val { color: var(--accent-red); }
.total-chip.balance .chip-val { color: var(--accent-blue); }

.toolbar-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.toolbar-left, .toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.ctrl-select { background: var(--bg-elevated); border: 1px solid var(--border-accent); color: var(--text-primary); border-radius: 6px; padding: 6px 10px; font-family: var(--font-body); font-size: 13px; outline: none; cursor: pointer; }
.ctrl-label { color: var(--text-secondary); font-size: 13px; display: flex; align-items: center; gap: 6px; margin: 0; }

.table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); overflow: hidden; }
.loading-cell { text-align: center; padding: 48px !important; color: var(--text-dim); }
.table-footer { padding: 16px 20px; border-top: 1px solid var(--border); }

/* Override Bootstrap striped for dark theme */
.table-striped tbody tr:nth-of-type(odd) { background-color: rgba(255,255,255,0.02); }
.table-bordered { border: none !important; }
.table-bordered td, .table-bordered th { border-color: var(--border) !important; }
</style>

<script>
var currentPage = 1, currentLimit = 10, currentType = 'all';
function checkAuth() {
  var hasToken = localStorage.getItem('access_token');
  var hasSession = <?= $sessionValid ? 'true' : 'false' ?>;
  if (!hasToken && !hasSession) { window.location.href = 'index.php'; return false; }
  return true;
}
function loadTransactions() {
  if (!checkAuth()) return;
  $.ajax({ url: 'api/transactions.php', type: 'GET',
    data: { page: currentPage, limit: currentLimit, type: currentType }, dataType: 'json',
    success: function(r) {
      if (r.status === 'success') renderTransactions(r.data.transactions, r.data.pagination, r.data.totals);
      else $('#transactions-tbody').html('<tr><td colspan="7" class="text-center text-danger">' + r.message + '</td></tr>');
    },
    error: function(xhr) {
      if (xhr.status === 401) { localStorage.removeItem('access_token'); window.location.href = 'index.php'; }
      else $('#transactions-tbody').html('<tr><td colspan="7" class="loading-cell">Error loading transactions</td></tr>');
    }
  });
}
function renderTransactions(transactions, pagination, totals) {
  if (!transactions.length) {
    $('#transactions-tbody').html('<tr><td colspan="7" class="loading-cell">No transactions found</td></tr>');
    updateTotals(totals); return;
  }
  var html = '', start = (pagination.current_page - 1) * pagination.limit + 1;
  transactions.forEach(function(item, i) {
    var isIncome = item.type === 'Income';
    var badge = isIncome
      ? '<span class="badge" style="background:rgba(0,208,132,0.15);color:#33dda1;border:1px solid rgba(0,208,132,0.3)">Income</span>'
      : '<span class="badge" style="background:rgba(255,77,109,0.15);color:#ff7fa0;border:1px solid rgba(255,77,109,0.3)">Expense</span>';
    html += '<tr><td>' + (start+i) + '</td><td>' + badge + '</td><td>' + (item.category||'-') + '</td>' +
      '<td><strong style="color:' + (isIncome?'var(--accent-green)':'var(--accent-red)') + '">₹' + Number(item.amount).toLocaleString('en-IN',{minimumFractionDigits:2}) + '</strong></td>' +
      '<td>' + (item.description||'—') + '</td><td>' + item.date + '</td>' +
      '<td><button class="btn btn-sm btn-outline-danger delete-btn" data-id="' + item.id + '" data-type="' + item.type + '"><i class="fas fa-trash-alt"></i></button></td></tr>';
  });
  $('#transactions-tbody').html(html);
  updateTotals(totals);
  var pages = '';
  pages += '<li class="page-item '+(pagination.current_page<=1?'disabled':'')+'"><a class="page-link" href="#" data-page="'+(pagination.current_page-1)+'">&#8249;</a></li>';
  for (var i=1; i<=pagination.total_pages; i++) pages += '<li class="page-item '+(pagination.current_page===i?'active':'')+'"><a class="page-link" href="#" data-page="'+i+'">'+i+'</a></li>';
  pages += '<li class="page-item '+(pagination.current_page>=pagination.total_pages?'disabled':'')+'"><a class="page-link" href="#" data-page="'+(pagination.current_page+1)+'">&#8250;</a></li>';
  $('#pagination').html(pages);
}
function updateTotals(t) {
  var d = t || { total_income:0, total_expense:0, net_balance:0 };
  var fmt = function(v) { return '₹' + Number(v).toLocaleString('en-IN',{minimumFractionDigits:2}); };
  $('#total-income').text(fmt(d.total_income)); $('#total-expense').text(fmt(d.total_expense)); $('#net-balance').text(fmt(d.net_balance));
}
$(document).ready(function() {
  var u = localStorage.getItem('user_data');
  if (u) { try { $('#user-name').text(JSON.parse(u).name||'User'); } catch(e){} }
  loadTransactions();
  $('#select-entries').on('change', function() { currentLimit = parseInt($(this).val()); currentPage = 1; loadTransactions(); });
  $('#type-filter').on('change', function() { currentType = $(this).val(); currentPage = 1; loadTransactions(); });
  $(document).on('click', '.page-link', function(e) {
    e.preventDefault(); var p = parseInt($(this).data('page')); if (p>0) { currentPage=p; loadTransactions(); }
  });
  $(document).on('click', '.delete-btn', function() {
    var id = $(this).data('id'), type = $(this).data('type');
    if (confirm('Delete this ' + type + '?')) {
      $.ajax({ url: 'api/delete-transaction.php', type: 'POST', data: { id:id, type:type }, dataType: 'json',
        success: function(r) { if (r.status==='success') loadTransactions(); else alert(r.message); }
      });
    }
  });
  $('#search-input').on('keyup', function() {
    var v = $(this).val().toLowerCase();
    $('#transactions-tbody tr').filter(function() { $(this).toggle($(this).text().toLowerCase().includes(v)); });
  });
  $('#import-csv-form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url: 'api/import-csv.php', type: 'POST', data: new FormData(this), processData:false, contentType:false, dataType:'json',
      success: function(r) { if (r.status==='success') { $('#import-csv-modal').modal('hide'); loadTransactions(); } else alert(r.message); }
    });
  });
});
let sidebar = document.querySelector(".sidebar"), sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  sidebarBtn.classList.replace(sidebar.classList.contains("active")?"bx-menu":"bx-menu-alt-right", sidebar.classList.contains("active")?"bx-menu-alt-right":"bx-menu");
}
document.getElementById('profile-options-toggle').addEventListener('click', () => document.getElementById('profile-options').classList.toggle('show'));
</script>
</body></html>
