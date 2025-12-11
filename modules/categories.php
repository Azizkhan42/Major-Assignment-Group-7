<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../controllers/categoryController.php';

requireLogin();

$user_id = getCurrentUserId();
$category_controller = new CategoryController();

// Initialize action variable
$action = $_GET['action'] ?? '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_expense_category') {
        $result = $category_controller->addExpenseCategory(
            $user_id,
            $_POST['name'],
            $_POST['description'] ?? ''
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/categories.php?msg=added");
            exit();
        }
    } elseif ($action === 'add_income_category') {
        $result = $category_controller->addIncomeCategory(
            $user_id,
            $_POST['name'],
            $_POST['description'] ?? ''
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/categories.php?msg=added");
            exit();
        }
    } elseif ($action === 'edit_expense_category') {
        $result = $category_controller->updateExpenseCategory(
            $_POST['id'],
            $user_id,
            $_POST['name'],
            $_POST['description'] ?? ''
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/categories.php?msg=updated");
            exit();
        }
    } elseif ($action === 'edit_income_category') {
        $result = $category_controller->updateIncomeCategory(
            $_POST['id'],
            $user_id,
            $_POST['name'],
            $_POST['description'] ?? ''
        );
        if ($result['success']) {
            header("Location: /Personal-budget-dashboard/modules/categories.php?msg=updated");
            exit();
        }
    }
}

// Handle delete
if (($action === 'delete_expense_category') && isset($_GET['id'])) {
    $result = $category_controller->deleteExpenseCategory($_GET['id'], $user_id);
    if ($result['success']) {
        header("Location: /Personal-budget-dashboard/modules/categories.php?msg=deleted");
        exit();
    }
}

if (($action === 'delete_income_category') && isset($_GET['id'])) {
    $result = $category_controller->deleteIncomeCategory($_GET['id'], $user_id);
    if ($result['success']) {
        header("Location: /Personal-budget-dashboard/modules/categories.php?msg=deleted");
        exit();
    }
}

// Get categories
$expense_categories = $category_controller->getExpenseCategories($user_id);
$income_categories = $category_controller->getIncomeCategories($user_id);

$edit_expense_cat = null;
$edit_income_cat = null;

if (($action === 'edit_expense_category') && isset($_GET['id'])) {
    $edit_expense_cat = $category_controller->getExpenseCategoryById($_GET['id'], $user_id);
}

if (($action === 'edit_income_category') && isset($_GET['id'])) {
    $edit_income_cat = $category_controller->getIncomeCategoryById($_GET['id'], $user_id);
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
                    <i class="fas fa-tags"></i> Categories
                </h1>
                <p class="text-muted">Manage your expense and income categories</p>
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
                    <button class="nav-link active" id="expense-cat-tab" data-bs-toggle="tab" data-bs-target="#expenseCatTab" type="button">
                        <i class="fas fa-minus-circle"></i> Expense Categories
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="income-cat-tab" data-bs-toggle="tab" data-bs-target="#incomeCatTab" type="button">
                        <i class="fas fa-plus-circle"></i> Income Categories
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Expense Categories Tab -->
                <div class="tab-pane fade show active" id="expenseCatTab">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-plus"></i> <?php echo ($action === 'edit_expense_category') ? 'Edit Category' : 'Add Expense Category'; ?></h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="<?php echo ($action === 'edit_expense_category') ? 'edit_expense_category' : 'add_expense_category'; ?>">
                                        <?php if ($edit_expense_cat): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_expense_cat['id']; ?>">
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label for="expense_cat_name" class="form-label">Category Name</label>
                                            <input type="text" class="form-control" id="expense_cat_name" name="name" required 
                                                   value="<?php echo $edit_expense_cat ? htmlspecialchars($edit_expense_cat['name']) : ''; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="expense_cat_desc" class="form-label">Description</label>
                                            <textarea class="form-control" id="expense_cat_desc" name="description" rows="3"><?php echo $edit_expense_cat ? htmlspecialchars($edit_expense_cat['description'] ?? '') : ''; ?></textarea>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> <?php echo ($action === 'edit_expense_category') ? 'Update Category' : 'Add Category'; ?>
                                            </button>
                                            <?php if ($action === 'edit_expense_category'): ?>
                                                <a href="/Personal-budget-dashboard/modules/categories.php" class="btn btn-secondary">
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
                                    <h5 class="mb-0"><i class="fas fa-list"></i> Expense Categories</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($expense_categories)): ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-4">No expense categories</td>
                                                    </tr>
                                                <?php else:
                                                    foreach ($expense_categories as $cat):
                                                ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars(substr($cat['description'] ?? '', 0, 50)); ?></td>
                                                        <td>
                                                            <a href="/Personal-budget-dashboard/modules/categories.php?action=edit_expense_category&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="/Personal-budget-dashboard/modules/categories.php?action=delete_expense_category&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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

                <!-- Income Categories Tab -->
                <div class="tab-pane fade" id="incomeCatTab">
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-plus"></i> <?php echo ($action === 'edit_income_category') ? 'Edit Category' : 'Add Income Category'; ?></h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="<?php echo ($action === 'edit_income_category') ? 'edit_income_category' : 'add_income_category'; ?>">
                                        <?php if ($edit_income_cat): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_income_cat['id']; ?>">
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label for="income_cat_name" class="form-label">Category Name</label>
                                            <input type="text" class="form-control" id="income_cat_name" name="name" required 
                                                   value="<?php echo $edit_income_cat ? htmlspecialchars($edit_income_cat['name']) : ''; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="income_cat_desc" class="form-label">Description</label>
                                            <textarea class="form-control" id="income_cat_desc" name="description" rows="3"><?php echo $edit_income_cat ? htmlspecialchars($edit_income_cat['description'] ?? '') : ''; ?></textarea>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> <?php echo ($action === 'edit_income_category') ? 'Update Category' : 'Add Category'; ?>
                                            </button>
                                            <?php if ($action === 'edit_income_category'): ?>
                                                <a href="/Personal-budget-dashboard/modules/categories.php" class="btn btn-secondary">
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
                                    <h5 class="mb-0"><i class="fas fa-list"></i> Income Categories</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Description</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($income_categories)): ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-4">No income categories</td>
                                                    </tr>
                                                <?php else:
                                                    foreach ($income_categories as $cat):
                                                ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars(substr($cat['description'] ?? '', 0, 50)); ?></td>
                                                        <td>
                                                            <a href="/Personal-budget-dashboard/modules/categories.php?action=edit_income_category&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-info">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="/Personal-budget-dashboard/modules/categories.php?action=delete_income_category&id=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
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
