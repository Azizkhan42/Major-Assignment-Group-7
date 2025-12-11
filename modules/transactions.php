<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../controllers/incomeController.php';
require_once __DIR__ . '/../controllers/expenseController.php';
require_once __DIR__ . '/../controllers/categoryController.php';

requireLogin();

$user_id = getCurrentUserId();
$income_controller = new IncomeController();
$expense_controller = new ExpenseController();
$category_controller = new CategoryController();

$action = $_GET['action'] ?? 'view';
$type = $_GET['type'] ?? 'income';
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['action'] ?? '';
    
    if ($post_action === 'add_income') {
        $result = $income_controller->addIncome(
            $user_id,
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['source'] ?? '',
            $_POST['description'] ?? '',
            $_POST['date']
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/transactions.php?msg=added");
            exit();
        }
    } elseif ($post_action === 'add_expense') {
        $result = $expense_controller->addExpense(
            $user_id,
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['description'] ?? '',
            $_POST['date']
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/transactions.php?msg=added");
            exit();
        }
    } elseif ($post_action === 'edit_income') {
        $result = $income_controller->updateIncome(
            $_POST['id'],
            $user_id,
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['source'] ?? '',
            $_POST['description'] ?? '',
            $_POST['date']
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/transactions.php?msg=updated");
            exit();
        }
    } elseif ($post_action === 'edit_expense') {
        $result = $expense_controller->updateExpense(
            $_POST['id'],
            $user_id,
            $_POST['category_id'],
            $_POST['amount'],
            $_POST['description'] ?? '',
            $_POST['date']
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/transactions.php?msg=updated");
            exit();
        }
    }
}

// Handle delete
if (($action === 'delete_income') && isset($_GET['id'])) {
    $result = $income_controller->deleteIncome($_GET['id'], $user_id);
    if ($result['success']) {
        header("Location: /Personal-budget-dashboard/modules/transactions.php?msg=deleted");
        exit();
    }
}

if (($action === 'delete_expense') && isset($_GET['id'])) {
    $result = $expense_controller->deleteExpense($_GET['id'], $user_id);
    if ($result['success']) {
        header("Location: /Personal-budget-dashboard/modules/transactions.php?msg=deleted");
        exit();
    }
}

// Get transactions
$income_transactions = $income_controller->getIncome($user_id);
$expense_transactions = $expense_controller->getExpenses($user_id);

// Get categories
$income_categories = $category_controller->getIncomeCategories($user_id);
$expense_categories = $category_controller->getExpenseCategories($user_id);

$edit_income = null;
$edit_expense = null;

if ($action === 'edit_income' && isset($_GET['id'])) {
    $edit_income = $income_controller->getIncomeById($_GET['id'], $user_id);
}

if ($action === 'edit_expense' && isset($_GET['id'])) {
    $edit_expense = $expense_controller->getExpenseById($_GET['id'], $user_id);
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
                    <i class="fas fa-exchange-alt"></i> Transactions
                </h1>
                <p class="text-muted">Manage your income and expenses</p>
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

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $type === 'income' ? 'active' : ''; ?>" id="income-tab" data-bs-toggle="tab" data-bs-target="#incomeTab" type="button">
                        <i class="fas fa-plus-circle"></i> Income
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo ($type === 'expense' || $type === '') ? 'active' : ''; ?>" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expenseTab" type="button">
                        <i class="fas fa-minus-circle"></i> Expenses
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Income Tab -->
                <div class="tab-pane fade <?php echo $type === 'income' ? 'show active' : ''; ?>" id="incomeTab">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-plus"></i> <?php echo $action === 'edit_income' ? 'Edit Income' : 'Add Income'; ?></h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" id="incomeForm">
                                        <input type="hidden" name="action" value="<?php echo $action === 'edit_income' ? 'edit_income' : 'add_income'; ?>">
                                        <?php if ($edit_income): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_income['id']; ?>">
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label for="income_category" class="form-label">Category</label>
                                            <select class="form-select" id="income_category" name="category_id" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($income_categories as $cat): ?>
                                                    <option value="<?php echo $cat['id']; ?>" <?php echo $edit_income && $edit_income['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cat['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="income_amount" class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">PKR</span>
                                                <input type="number" class="form-control" id="income_amount" name="amount" step="0.01" required 
                                                       value="<?php echo $edit_income ? $edit_income['amount'] : ''; ?>">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="income_source" class="form-label">Source</label>
                                            <input type="text" class="form-control" id="income_source" name="source" 
                                                   value="<?php echo $edit_income ? htmlspecialchars($edit_income['source']) : ''; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="income_date" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="income_date" name="date" required 
                                                   value="<?php echo $edit_income ? $edit_income['date'] : date('Y-m-d'); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="income_description" class="form-label">Description</label>
                                            <textarea class="form-control" id="income_description" name="description" rows="3"><?php echo $edit_income ? htmlspecialchars($edit_income['description']) : ''; ?></textarea>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save"></i> <?php echo $action === 'edit_income' ? 'Update Income' : 'Add Income'; ?>
                                            </button>
                                            <?php if ($action === 'edit_income'): ?>
                                                <a href="/Personal-budget-dashboard/modules/transactions.php" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-list"></i> Income List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Source</th>
                                                    <th>Amount</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($income_transactions)): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-4">No income yet</td>
                                                    </tr>
                                                <?php else:
                                                    foreach ($income_transactions as $inc):
                                                ?>
                                                    <tr>
                                                        <td><?php echo formatDate($inc['date']); ?></td>
                                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($inc['category_name']); ?></span></td>
                                                        <td><?php echo htmlspecialchars(substr($inc['source'] ?? '', 0, 40)); ?></td>
                                                        <td><strong><?php echo formatCurrency($inc['amount']); ?></strong></td>
                                                        <td>
                                                            <a href="/Personal-budget-dashboard/modules/transactions.php?action=edit_income&id=<?php echo $inc['id']; ?>" class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="/Personal-budget-dashboard/modules/transactions.php?action=delete_income&id=<?php echo $inc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expenses Tab -->
                <div class="tab-pane fade <?php echo ($type === 'expense' || $type === '') ? 'show active' : ''; ?>" id="expenseTab">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-plus"></i> <?php echo $action === 'edit_expense' ? 'Edit Expense' : 'Add Expense'; ?></h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="" id="expenseForm">
                                        <input type="hidden" name="action" value="<?php echo $action === 'edit_expense' ? 'edit_expense' : 'add_expense'; ?>">
                                        <?php if ($edit_expense): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_expense['id']; ?>">
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label for="expense_category" class="form-label">Category</label>
                                            <select class="form-select" id="expense_category" name="category_id" required>
                                                <option value="">Select Category</option>
                                                <?php foreach ($expense_categories as $cat): ?>
                                                    <option value="<?php echo $cat['id']; ?>" <?php echo $edit_expense && $edit_expense['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($cat['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="expense_amount" class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">PKR</span>
                                                <input type="number" class="form-control" id="expense_amount" name="amount" step="0.01" required 
                                                       value="<?php echo $edit_expense ? $edit_expense['amount'] : ''; ?>">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="expense_date" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="expense_date" name="date" required 
                                                   value="<?php echo $edit_expense ? $edit_expense['date'] : date('Y-m-d'); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="expense_description" class="form-label">Description</label>
                                            <textarea class="form-control" id="expense_description" name="description" rows="3"><?php echo $edit_expense ? htmlspecialchars($edit_expense['description']) : ''; ?></textarea>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> <?php echo $action === 'edit_expense' ? 'Update Expense' : 'Add Expense'; ?>
                                            </button>
                                            <?php if ($action === 'edit_expense'): ?>
                                                <a href="/Personal-budget-dashboard/modules/transactions.php?type=expense" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-list"></i> Expenses List</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($expense_transactions)): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-4">No expenses yet</td>
                                                    </tr>
                                                <?php else:
                                                    foreach ($expense_transactions as $exp):
                                                ?>
                                                    <tr>
                                                        <td><?php echo formatDate($exp['date']); ?></td>
                                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($exp['category_name']); ?></span></td>
                                                        <td><?php echo htmlspecialchars(substr($exp['description'] ?? '', 0, 40)); ?></td>
                                                        <td><strong><?php echo formatCurrency($exp['amount']); ?></strong></td>
                                                        <td>
                                                            <a href="/Personal-budget-dashboard/modules/transactions.php?action=edit_expense&id=<?php echo $exp['id']; ?>&type=expense" class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="/Personal-budget-dashboard/modules/transactions.php?action=delete_expense&id=<?php echo $exp['id']; ?>&type=expense" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
