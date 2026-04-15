<?php
session_start(); error_reporting(0); include('database.php');
if (empty($_SESSION['detsuid'])) { header('location:logout.php'); exit; }
$uid = $_SESSION['detsuid'];
$ret = mysqli_query($db, "select name from users where id='$uid'");
$row = mysqli_fetch_array($ret);
$navUser = $row['name'];
$activePage = 'income'; $pageTitle = 'Income'; $showSearch = false;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Add Income — Expenditure</title>
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

<div class="page-wrap">
  <div class="page-header">
    <div>
      <p class="page-label">Track Earnings</p>
      <h1 class="page-title">Add Income</h1>
    </div>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-category-modal">
      <i class="fas fa-plus-circle"></i> New Category
    </button>
  </div>

  <div class="content-card">
    <p class="card-sub">Record your income sources and keep track of earnings.</p>
    <form id="incomeForm">
      <div class="form-grid-2">
        <div class="form-group">
          <label for="incomeDate">Date of Income</label>
          <input class="form-control" type="date" id="incomeDate" name="incomeDate" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-group">
          <label for="category">Category</label>
          <select class="form-control" id="category" name="category" required>
            <option value="" selected disabled>Choose Category</option>
          </select>
        </div>
        <div class="form-group">
          <label for="incomeAmount">Amount (₹)</label>
          <input class="form-control" type="number" id="incomeAmount" name="incomeAmount" required placeholder="0.00" step="0.01" min="0">
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          <input class="form-control" type="text" id="description" name="description" placeholder="Optional notes">
        </div>
      </div>
      <div class="form-actions">
        <div id="success-message" class="alert alert-success" style="display:none;">Income added successfully.</div>
        <div id="error-message" class="alert alert-danger" style="display:none;"></div>
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Income</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="add-category-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form id="add-category-form">
      <div class="modal-header">
        <h5 class="modal-title">New Income Category</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="category-name">Category Name</label>
          <input type="text" class="form-control" id="category-name" name="category-name" placeholder="e.g. Salary, Freelance..." required>
          <input type="hidden" name="mode" value="income">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Add Category</button>
      </div>
    </form>
  </div></div>
</div>

  </div></section>

<style>
.page-wrap { max-width: 760px; margin: 0 auto; padding: 0 4px; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.page-label { font-size: 11px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase; color: var(--accent-green); margin: 0 0 4px; }
.page-title { font-family: var(--font-display); font-size: 28px; font-weight: 800; color: var(--text-primary); margin: 0; letter-spacing: -0.02em; }
.content-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-xl); padding: 32px; }
.card-sub { color: var(--text-dim); font-size: 13.5px; margin-bottom: 28px; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-actions { display: flex; align-items: center; gap: 16px; margin-top: 28px; flex-wrap: wrap; }
.form-actions .alert { margin: 0; flex: 1; font-size: 13.5px; }
@media(max-width:640px){ .form-grid-2 { grid-template-columns: 1fr; } }
</style>

<script>
let sidebar = document.querySelector(".sidebar");
let sidebarBtn = document.querySelector(".sidebarBtn");
sidebarBtn.onclick = function() {
  sidebar.classList.toggle("active");
  sidebarBtn.classList.replace(sidebar.classList.contains("active") ? "bx-menu" : "bx-menu-alt-right",
                               sidebar.classList.contains("active") ? "bx-menu-alt-right" : "bx-menu");
}
document.getElementById('profile-options-toggle').addEventListener('click', () => {
  document.getElementById('profile-options').classList.toggle('show');
});
$(document).ready(function() {
  loadCategories();
  function loadCategories() {
    $.ajax({ url: 'api/get-categories.php', type: 'GET', data: { mode: 'income' }, dataType: 'json',
      success: function(r) {
        if (r.status === 'success') {
          var opts = '<option value="" selected disabled>Choose Category</option>';
          $.each(r.data, function(i, c) { opts += '<option value="' + c.categoryid + '">' + c.categoryname + '</option>'; });
          $('#category').html(opts);
        }
      }
    });
  }
  $('#incomeForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url: 'api/add-income.php', type: 'POST', data: $(this).serialize(), dataType: 'json',
      success: function(r) {
        if (r.status === 'success') { $('#success-message').show(); setTimeout(() => window.location.href='manage-transaction.php', 1200); }
        else { $('#error-message').text(r.message).show(); }
      }, error: function() { $('#error-message').text('An error occurred.').show(); }
    });
  });
  $('#add-category-form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url: 'api/add-category.php', type: 'POST', data: $(this).serialize(), dataType: 'json',
      success: function(r) {
        if (r.status === 'success') { $('#add-category-modal').modal('hide'); $('#add-category-form')[0].reset(); loadCategories(); }
        else { alert(r.message); }
      }
    });
  });
});
</script>
</body></html>
