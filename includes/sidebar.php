<?php
require_once __DIR__ . '/../helpers/session.php';
startSession();
?>
<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <button class="btn-close btn-close-white sidebar-toggle d-md-none" aria-label="Close" onclick="toggleSidebar()"></button>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link" href="/Personal-budget-dashboard/modules/dashboard.php">
                <i class="fas fa-home"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/Personal-budget-dashboard/modules/transactions.php">
                <i class="fas fa-exchange-alt"></i>
                <span class="nav-text">Transactions</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/Personal-budget-dashboard/modules/categories.php">
                <i class="fas fa-tags"></i>
                <span class="nav-text">Categories</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/Personal-budget-dashboard/modules/reports.php">
                <i class="fas fa-chart-bar"></i>
                <span class="nav-text">Reports</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/Personal-budget-dashboard/modules/profile.php">
                <i class="fas fa-user"></i>
                <span class="nav-text">Profile</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <p class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
            <p class="user-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'user@email.com'); ?></p>
        </div>
        <a href="/Personal-budget-dashboard/logout.php" class="btn btn-danger btn-sm w-100">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

<!-- Sidebar Toggle Button (Mobile) -->
<button class="btn btn-primary sidebar-toggle-btn d-md-none" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}
</script>
