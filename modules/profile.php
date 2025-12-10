<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../controllers/userController.php';

requireLogin();

$user_id = getCurrentUserId();
$user_controller = new UserController();
$user = $user_controller->getUserById($user_id);
$stats = $user_controller->getUserStatistics($user_id);

$error_msg = '';
$success_msg = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $result = $user_controller->updateProfile(
            $user_id,
            $_POST['name'],
            $_POST['email']
        );
        if ($result['success']) {
            $user = $user_controller->getUserById($user_id);
            $success_msg = 'Profile updated successfully!';
        } else {
            $error_msg = $result['error'];
        }
    } elseif ($action === 'change_password') {
        $result = $user_controller->changePassword(
            $user_id,
            $_POST['old_password'],
            $_POST['new_password'],
            $_POST['confirm_password']
        );
        if ($result['success']) {
            $success_msg = 'Password changed successfully!';
        } else {
            $error_msg = $result['error'];
        }
    }
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
                    <i class="fas fa-user"></i> Profile
                </h1>
                <p class="text-muted">Manage your account settings</p>
            </div>

            <!-- Messages -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Statistics -->
                <div class="col-lg-4 mb-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Your Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="stat-item mb-3">
                                <span class="stat-label">Total Income (All Time)</span>
                                <h4 class="stat-value text-success"><?php echo formatCurrency($stats['total_income']); ?></h4>
                            </div>
                            <div class="stat-item mb-3">
                                <span class="stat-label">Total Expenses (All Time)</span>
                                <h4 class="stat-value text-danger"><?php echo formatCurrency($stats['total_expense']); ?></h4>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Net Savings (All Time)</span>
                                <h4 class="stat-value text-info"><?php echo formatCurrency($stats['savings']); ?></h4>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Account Info</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Role:</strong> <span class="badge bg-primary"><?php echo ucfirst($_SESSION['user_role']); ?></span>
                            </p>
                            <p class="mb-0">
                                <strong>Member Since:</strong> <?php echo date('M d, Y'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Profile Settings -->
                <div class="col-lg-8">
                    <!-- Update Profile -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-edit"></i> Update Profile</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="update_profile">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required 
                                               value="<?php echo htmlspecialchars($user['name']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required 
                                               value="<?php echo htmlspecialchars($user['email']); ?>">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-lock"></i> Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="change_password">

                                <div class="mb-3">
                                    <label for="old_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="old_password" name="old_password" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                        <small class="form-text text-muted">Minimum 6 characters</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
