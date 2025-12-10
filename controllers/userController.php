<?php
// User Controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/validation.php';

class UserController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update user profile
     */
    public function updateProfile($id, $name, $email) {
        // Check if email is already taken by another user
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            return ['success' => false, 'error' => 'Email already in use'];
        }

        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            // Update session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to update profile'];
    }

    /**
     * Change password
     */
    public function changePassword($id, $old_password, $new_password, $confirm_password) {
        // Get user
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return ['success' => false, 'error' => 'User not found'];
        }

        // Verify old password
        if (!password_verify($old_password, $result['password'])) {
            return ['success' => false, 'error' => 'Incorrect current password'];
        }

        // Validate new password
        if (!validatePassword($new_password)) {
            return ['success' => false, 'error' => 'New password must be at least 6 characters'];
        }

        if ($new_password !== $confirm_password) {
            return ['success' => false, 'error' => 'New passwords do not match'];
        }

        // Hash and update
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to change password'];
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics($user_id) {
        $incomeQuery = "SELECT SUM(amount) as total FROM income WHERE user_id = ?";
        $expenseQuery = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ?";

        $stmt = $this->conn->prepare($incomeQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $income = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        $stmt = $this->conn->prepare($expenseQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $expense = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'savings' => $income - $expense
        ];
    }

    /**
     * Get all users (admin function)
     */
    public function getAllUsers($limit = 50, $offset = 0) {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, role, created_at FROM users LIMIT ? OFFSET ?"
        );
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
