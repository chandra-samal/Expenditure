<?php
$db = new mysqli('localhost', 'root', '', 'expenditure', 3306);
if ($db->connect_error) {
    echo 'connect error: ' . $db->connect_error . PHP_EOL;
    exit(1);
}
$userid = 68;
$expenseRes = $db->query('SELECT COALESCE(SUM(ExpenseCost),0) AS total_expense FROM tblexpense WHERE UserId=' . $userid);
$incomeRes = $db->query('SELECT COALESCE(SUM(IncomeAmount),0) AS total_income FROM tblincome WHERE UserId=' . $userid);
if ($expenseRes) {
    $row = $expenseRes->fetch_assoc();
    echo 'expense=' . $row['total_expense'] . PHP_EOL;
}
if ($incomeRes) {
    $row = $incomeRes->fetch_assoc();
    echo 'income=' . $row['total_income'] . PHP_EOL;
}
?>