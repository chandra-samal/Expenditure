<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once('../database.php');
include_once('../auth_helper.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userid = requireAuthentication();
    
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $type = isset($_GET['type']) ? strtolower($_GET['type']) : 'all';
    $offset = ($page - 1) * $limit;
    
    $limit = min(max($limit, 1), 100);
    $page = max($page, 1);
    
    if ($type === 'expense') {
        $query = "
            SELECT ID, 'Expense' as Type, Category, ExpenseCost as Amount, Description, ExpenseDate as TransactionDate 
            FROM tblexpense WHERE userid='$userid'
            ORDER BY TransactionDate DESC
            LIMIT $limit OFFSET $offset
        ";
        $countQuery = "SELECT COUNT(*) as total FROM tblexpense WHERE userid='$userid'";
        $sumQuery = "SELECT COALESCE(SUM(ExpenseCost), 0) AS total_expense, 0 AS total_income FROM tblexpense WHERE userid='$userid'";
    } elseif ($type === 'income') {
        $query = "
            SELECT ID, 'Income' as Type, Category, IncomeAmount as Amount, Description, IncomeDate as TransactionDate 
            FROM tblincome WHERE userid='$userid'
            ORDER BY TransactionDate DESC
            LIMIT $limit OFFSET $offset
        ";
        $countQuery = "SELECT COUNT(*) as total FROM tblincome WHERE userid='$userid'";
        $sumQuery = "SELECT 0 AS total_expense, COALESCE(SUM(IncomeAmount), 0) AS total_income FROM tblincome WHERE userid='$userid'";
    } else {
        $query = "
            SELECT ID, 'Expense' as Type, Category, ExpenseCost as Amount, Description, ExpenseDate as TransactionDate 
            FROM tblexpense WHERE userid='$userid'
            UNION ALL
            SELECT ID, 'Income' as Type, Category, IncomeAmount as Amount, Description, IncomeDate as TransactionDate 
            FROM tblincome WHERE userid='$userid'
            ORDER BY TransactionDate DESC
            LIMIT $limit OFFSET $offset
        ";
        $countQuery = "
            SELECT COUNT(*) as total FROM (
                SELECT ID FROM tblexpense WHERE userid='$userid'
                UNION ALL
                SELECT ID FROM tblincome WHERE userid='$userid'
            ) as combined_table
        ";
        $sumQuery = "SELECT COALESCE(SUM(expense_sum), 0) AS total_expense, COALESCE(SUM(income_sum), 0) AS total_income FROM (
            SELECT ExpenseCost AS expense_sum, 0 AS income_sum FROM tblexpense WHERE userid='$userid'
            UNION ALL
            SELECT 0 AS expense_sum, IncomeAmount AS income_sum FROM tblincome WHERE userid='$userid'
        ) AS totals";
    }
    
    $result = mysqli_query($db, $query);
    $transactions = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = [
                'id' => (int)$row['ID'],
                'type' => $row['Type'],
                'category' => $row['Category'],
                'amount' => (float)$row['Amount'],
                'description' => $row['Description'],
                'date' => $row['TransactionDate']
            ];
        }
    }
    
    $countResult = mysqli_query($db, $countQuery);
    $countData = mysqli_fetch_assoc($countResult);
    $total = (int)$countData['total'];
    $total_pages = ceil($total / $limit);
    
    $sumResult = mysqli_query($db, $sumQuery);
    $sumData = mysqli_fetch_assoc($sumResult);
    $totalExpense = isset($sumData['total_expense']) ? (float)$sumData['total_expense'] : 0;
    $totalIncome = isset($sumData['total_income']) ? (float)$sumData['total_income'] : 0;
    $netBalance = $totalIncome - $totalExpense;
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'transactions' => $transactions,
            'totals' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_balance' => $netBalance
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total,
                'limit' => $limit
            ]
        ]
    ]);
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
