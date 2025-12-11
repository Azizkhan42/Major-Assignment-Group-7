<?php
// Category Controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

class CategoryController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Add expense category
     */
    public function addExpenseCategory($user_id, $name, $description = '') {
        $stmt = $this->conn->prepare(
            "INSERT INTO expense_categories (user_id, name, description) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $user_id, $name, $description);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to add category'];
    }

    /**
     * Add income category
     */
    public function addIncomeCategory($user_id, $name, $description = '') {
        $stmt = $this->conn->prepare(
            "INSERT INTO income_categories (user_id, name, description) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $user_id, $name, $description);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to add category'];
    }

    /**
     * Get all expense categories for user
     */
    public function getExpenseCategories($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM expense_categories WHERE user_id = ? ORDER BY name ASC"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get all income categories for user
     */
    public function getIncomeCategories($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM income_categories WHERE user_id = ? ORDER BY name ASC"
        );
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update expense category
     */
    public function updateExpenseCategory($id, $user_id, $name, $description = '') {
        $stmt = $this->conn->prepare(
            "UPDATE expense_categories SET name = ?, description = ? WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ssii", $name, $description, $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to update category'];
    }

    /**
     * Update income category
     */
    public function updateIncomeCategory($id, $user_id, $name, $description = '') {
        $stmt = $this->conn->prepare(
            "UPDATE income_categories SET name = ?, description = ? WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ssii", $name, $description, $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to update category'];
    }

    /**
     * Delete expense category
     */
    public function deleteExpenseCategory($id, $user_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM expense_categories WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to delete category'];
    }

    /**
     * Delete income category
     */
    public function deleteIncomeCategory($id, $user_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM income_categories WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to delete category'];
    }

    /**
     * Get category by ID (expense)
     */
    public function getExpenseCategoryById($id, $user_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM expense_categories WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get category by ID (income)
     */
    public function getIncomeCategoryById($id, $user_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM income_categories WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
