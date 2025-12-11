<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../controllers/incomeController.php';
require_once __DIR__ . '/../controllers/expenseController.php';
require_once __DIR__ . '/../controllers/userController.php';

requireLogin();

$user_id = getCurrentUserId();
$income_controller = new IncomeController();
$expense_controller = new ExpenseController();
$user_controller = new UserController();

// Get date range for charts + recent transactions (current month)
$date_range = getDateRange('month');
$start_date = $date_range['start'];
$end_date = $date_range['end'];

// REAL all-time totals
$total_income = $income_controller->getTotalIncome($user_id);
$total_expenses = $expense_controller->getTotalExpenses($user_id);
$monthly_savings = $total_income - $total_expenses;

// All-time balance (already correct)
$stats = $user_controller->getUserStatistics($user_id);

// Charts (monthly)
$expense_by_category = $expense_controller->getExpensesByCategoryForChart($user_id, $start_date, $end_date);
$income_by_category = $income_controller->getIncomeByCategoryForChart($user_id, $start_date, $end_date);

// Recent transactions (monthly)
$recent_income = $income_controller->getIncome($user_id, ['start_date' => $start_date, 'end_date' => $end_date]);
$recent_expenses = $expense_controller->getExpenses($user_id, ['start_date' => $start_date, 'end_date' => $end_date]);

// Prepare chart data
$expense_categories = [];
$expense_amounts = [];
$expense_colors = getCategoryChartColors();
foreach ($expense_by_category as $index => $item) {
    $expense_categories[] = $item['name'] ?? 'Uncategorized';
    $expense_amounts[] = (float)$item['amount'] ?? 0;
}

$income_categories = [];
$income_amounts = [];
foreach ($income_by_category as $index => $item) {
    $income_categories[] = $item['name'] ?? 'Uncategorized';
    $income_amounts[] = (float)$item['amount'] ?? 0;
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="main-content">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="content-area">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <h1 class="page-title">
                    <i class="fas fa-chart-line"></i> Dashboard
                </h1>
                <p class="text-muted">well come back agian , <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>

            <!-- Flash Message -->
            <?php 
            $msg = getFlashMessage();
            if ($msg):
                $msgCode = $_GET['msg'] ?? '';
                $msgType = getMessageType($msgCode);
            ?>
                <div class="alert alert-<?php echo $msgType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="summary-card income-card">
                        <div class="card-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="card-content">
                            <h6 class="card-title">Total incomes</h6>
                            <h3 class="card-value"><?php echo formatCurrency($total_income); ?></h3>
                            <p class="card-subtitle">All time</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="summary-card expense-card">
                        <div class="card-icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div class="card-content">
                            <h6 class="card-title">Total expenses</h6>
                            <h3 class="card-value"><?php echo formatCurrency($total_expenses); ?></h3>
                            <p class="card-subtitle">All time</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="summary-card savings-card">
                        <div class="card-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="card-content">
                            <h6 class="card-title">Net Savings</h6>
                            <h3 class="card-value"><?php echo formatCurrency($monthly_savings); ?></h3>
                            <p class="card-subtitle">All time</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="summary-card total-card">
                        <div class="card-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="card-content">
                            <h6 class="card-title">Total Balance</h6>
                            <h3 class="card-value"><?php echo formatCurrency($stats['savings']); ?></h3>
                            <p class="card-subtitle">All time</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-pie-chart"></i> Expenses by Category (This Month)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="expenseChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-pie-chart"></i> Income by Category (This Month)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="incomeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-list"></i> Recent Transactions (This Month)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($recent_expenses as &$exp) {
                                            $exp['type'] = 'expense';
                                        }
                                        foreach ($recent_income as &$inc) {
                                            $inc['type'] = 'income';
                                        }

                                        $transactions = array_merge($recent_expenses, $recent_income);
                                        usort($transactions, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                                        $transactions = array_slice($transactions, 0, 8);

                                        if (empty($transactions)):
                                        ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No transactions yet
                                                </td>
                                            </tr>
                                        <?php else:
                                            foreach ($transactions as $trans):
                                        ?>
                                            <tr>
                                                <td><small><?php echo formatDate($trans['date']); ?></small></td>
                                                <td><?php echo htmlspecialchars(substr($trans['description'] ?? $trans['source'] ?? '', 0, 30)); ?></td>
                                                <td><small><?php echo htmlspecialchars($trans['category_name'] ?? 'N/A'); ?></small></td>
                                                <td><strong><?php echo formatCurrency($trans['amount']); ?></strong></td>
                                                <td>
                                                    <?php if ($trans['type'] === 'income'): ?>
                                                        <span class="badge bg-success">Income</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Expense</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="/Personal-budget-dashboard/modules/transactions.php?action=add_income" class="btn btn-outline-success btn-lg">
                                    <i class="fas fa-plus-circle"></i> Add Income
                                </a>
                                <a href="/Personal-budget-dashboard/modules/transactions.php?action=add_expense" class="btn btn-outline-danger btn-lg">
                                    <i class="fas fa-minus-circle"></i> Add Expense
                                </a>
                                <a href="/Personal-budget-dashboard/modules/reports.php" class="btn btn-outline-info btn-lg">
                                    <i class="fas fa-file-pdf"></i> View Reports
                                </a>
                                <a href="/Personal-budget-dashboard/modules/categories.php" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-tags"></i> Manage Categories
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Scripts -->
<script>
// Expense Chart
const expenseCtx = document.getElementById('expenseChart');
if (expenseCtx && <?php echo json_encode($expense_amounts); ?>.length > 0) {
    new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($expense_categories); ?>,
            datasets: [{
                data: <?php echo json_encode($expense_amounts); ?>,
                backgroundColor: <?php echo json_encode(array_slice($expense_colors, 0, count($expense_amounts))); ?>,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
} else {
    expenseCtx.parentElement.innerHTML = '<div class="text-center text-muted py-4">No expense data available</div>';
}

// Income Chart
const incomeCtx = document.getElementById('incomeChart');
if (incomeCtx && <?php echo json_encode($income_amounts); ?>.length > 0) {
    new Chart(incomeCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($income_categories); ?>,
            datasets: [{
                data: <?php echo json_encode($income_amounts); ?>,
                backgroundColor: <?php echo json_encode(array_slice($expense_colors, 0, count($income_amounts))); ?>,
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
} else {
    incomeCtx.parentElement.innerHTML = '<div class="text-center text-muted py-4">No income data available</div>';
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
