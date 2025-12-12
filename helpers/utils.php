<?php
// Utility Helper Functions

/**
 * Format currency
 */
function formatCurrency($amount) {
    return 'PKR ' . number_format($amount, 2, '.', ',');
}

/**
 * Format date
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Get chart colors for categories
 */
function getCategoryChartColors() {
    return [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A',
        '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2',
        '#F8B88B', '#A9DFBF'
    ];
}

/**
 * Generate user initials for avatar
 */
function getUserInitials($name) {
    $initials = strtoupper(implode('', array_map(function($n) { 
        return isset($n[0]) ? $n[0] : ''; 
    }, explode(' ', trim($name)))));
    return substr($initials, 0, 2) ?: 'U';
}

/**
 * Calculate date range
 */
function getDateRange($period) {
    $today = date('Y-m-d');
    $startDate = $today;
    
    if ($period === 'week') {
        $startDate = date('Y-m-d', strtotime('last Monday'));
    } elseif ($period === 'month') {
        $startDate = date('Y-m-01');
    } elseif ($period === 'year') {
        $startDate = date('Y-01-01');
    }
    
    return ['start' => $startDate, 'end' => $today];
}

/**
 * Redirect to page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Get message from query parameter
 */
function getFlashMessage() {
    if (isset($_GET['msg'])) {
        $msg = htmlspecialchars($_GET['msg']);
        $messages = [
            'registered' => 'Registration successful! Please login.',
            'login_success' => 'Logged in successfully!',
            'logout_success' => 'Logged out successfully!',
            'login_required' => 'Please login to continue.',
            'unauthorized' => 'Unauthorized access.',
            'added' => 'Record added successfully!',
            'updated' => 'Record updated successfully!',
            'deleted' => 'Record deleted successfully!',
            'error' => 'An error occurred. Please try again.',
            'invalid_credentials' => 'Invalid email or password.',
            'email_exists' => 'Email already registered.',
            'invalid_amount' => 'Amount must be greater than 0.',
            'invalid_date' => 'Invalid date format.',
        ];
        return $messages[$msg] ?? 'Operation completed.';
    }
    return '';
}

/**
 * Get message type (success/error/warning)
 */
function getMessageType($msgCode) {
    $successMessages = ['registered', 'login_success', 'logout_success', 'added', 'updated', 'deleted'];
    $errorMessages = ['invalid_credentials', 'email_exists', 'invalid_amount', 'invalid_date', 'error', 'unauthorized'];
    $warningMessages = ['login_required'];
    
    if (in_array($msgCode, $successMessages)) {
        return 'success';
    } elseif (in_array($msgCode, $errorMessages)) {
        return 'danger';
    } elseif (in_array($msgCode, $warningMessages)) {
        return 'warning';
    }
    return 'info';
}
?>
