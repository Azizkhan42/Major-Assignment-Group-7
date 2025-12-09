<?php
// Session Management Functions

/**
 * Start session safely
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Redirect if not logged in
 */
function requireLogin() {
    startSession();
    if (!isLoggedIn()) {
        header("Location: /Personal-budget-dashboard/index.php?msg=login_required");
        exit();
    }
}

/**
 * Redirect if not admin
 */
function requireAdmin() {
    startSession();
    if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
        header("Location: /Personal-budget-dashboard/index.php?msg=unauthorized");
        exit();
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    startSession();
    return $_SESSION['user_role'] ?? null;
}

/**
 * Logout user
 */
function logoutUser() {
    startSession();
    session_destroy();
    unset($_SESSION);
}

/**
 * Set user session
 */
function setUserSession($user_id, $user_email, $user_name, $user_role) {
    startSession();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $user_email;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['user_role'] = $user_role;
}
?>
