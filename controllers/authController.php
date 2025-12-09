<?php
// Authentication Controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/validation.php';

class AuthController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * User Registration
     */
    public function register($name, $email, $password, $confirm_password) {
        $errors = [];

        // Validation
        if (empty($name)) $errors[] = "Name is required";
        if (!validateEmail($email)) $errors[] = "Invalid email format";
        if (!validatePassword($password)) $errors[] = "Password must be at least 6 characters";
        if ($password !== $confirm_password) $errors[] = "Passwords do not match";

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check if email already exists
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return ['success' => false, 'errors' => ['Email already registered']];
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $role = 'user';
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $user_id = $this->conn->insert_id;
            
            // Create default categories for new user
            $this->createDefaultCategories($user_id);

            return ['success' => true, 'message' => 'registered'];
        }

        return ['success' => false, 'errors' => ['Registration failed. Please try again.']];
    }

    /**
     * User Login
     */
    public function login($email, $password) {
        $errors = [];

        // Validation
        if (!validateEmail($email)) $errors[] = "Invalid email format";
        if (empty($password)) $errors[] = "Password is required";

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Get user
        $stmt = $this->conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'errors' => ['invalid_credentials']];
        }

        $user = $result->fetch_assoc();

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'errors' => ['invalid_credentials']];
        }

        // Set session
        setUserSession($user['id'], $user['email'], $user['name'], $user['role']);

        return ['success' => true, 'message' => 'login_success'];
    }

    /**
     * Create default categories for new user
     */
    private function createDefaultCategories($user_id) {
        $expenseCategories = ['Food', 'Rent', 'Entertainment', 'Transportation', 'Utilities', 'Healthcare'];
        $incomeCategories = ['Salary', 'Freelance', 'Investment'];

        foreach ($expenseCategories as $category) {
            $stmt = $this->conn->prepare("INSERT INTO expense_categories (user_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $category);
            $stmt->execute();
        }

        foreach ($incomeCategories as $category) {
            $stmt = $this->conn->prepare("INSERT INTO income_categories (user_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $category);
            $stmt->execute();
        }
    }
}
?>
