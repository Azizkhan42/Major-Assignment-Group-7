<?php
// Expense Controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/validation.php';

class ExpenseController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Add expense
     */
    public function addExpense($user_id, $category_id, $amount, $description, $date) {
        // Validation
        if (!validateAmount($amount)) {
            return ['success' => false, 'error' => 'Invalid amount'];
        }
        if (!validateDate($date)) {
            return ['success' => false, 'error' => 'Invalid date format'];
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO expenses (user_id, category_id, amount, description, date) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iidss", $user_id, $category_id, $amount, $description, $date);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to add expense'];
    }

    /**
     * Get expense by ID
     */
    public function getExpenseById($id, $user_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM expenses WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update expense
     */
    public function updateExpense($id, $user_id, $category_id, $amount, $description, $date) {
        if (!validateAmount($amount)) {
            return ['success' => false, 'error' => 'Invalid amount'];
        }

        $stmt = $this->conn->prepare(
            "UPDATE expenses SET category_id = ?, amount = ?, description = ?, date = ? 
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("idssii", $category_id, $amount, $description, $date, $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to update expense'];
    }

    /**
     * Delete expense
     */
    public function deleteExpense($id, $user_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM expenses WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to delete expense'];
    }

    /**
     * Get all expenses for user with filters
     */
    public function getExpenses($user_id, $filters = []) {
        $query = "SELECT e.*, ec.name as category_name FROM expenses e 
                  LEFT JOIN expense_categories ec ON e.category_id = ec.id 
                  WHERE e.user_id = ?";
        $params = [$user_id];
        $types = "i";

        if (!empty($filters['start_date'])) {
            $query .= " AND e.date >= ?";
            $params[] = $filters['start_date'];
            $types .= "s";
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND e.date <= ?";
            $params[] = $filters['end_date'];
            $types .= "s";
        }

        if (!empty($filters['category_id'])) {
            $query .= " AND e.category_id = ?";
            $params[] = $filters['category_id'];
            $types .= "i";
        }

        $query .= " ORDER BY e.date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total expenses for user
     */
    public function getTotalExpenses($user_id, $start_date = null, $end_date = null) {
        $query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ?";
        $params = [$user_id];
        $types = "i";

        if ($start_date) {
            $query .= " AND date >= ?";
            $params[] = $start_date;
            $types .= "s";
        }

        if ($end_date) {
            $query .= " AND date <= ?";
            $params[] = $end_date;
            $types .= "s";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    /**
     * Get expenses by category for chart
     */
    public function getExpensesByCategoryForChart($user_id, $start_date = null, $end_date = null) {
        $query = "SELECT ec.name, SUM(e.amount) as amount FROM expenses e 
                  LEFT JOIN expense_categories ec ON e.category_id = ec.id 
                  WHERE e.user_id = ?";
        $params = [$user_id];
        $types = "i";

        if ($start_date) {
            $query .= " AND e.date >= ?";
            $params[] = $start_date;
            $types .= "s";
        }

        if ($end_date) {
            $query .= " AND e.date <= ?";
            $params[] = $end_date;
            $types .= "s";
        }

        $query .= " GROUP BY ec.id, ec.name ORDER BY amount DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get recent transactions (both income and expenses)
     */
    public function getRecentTransactions($user_id, $limit = 5) {
        // Get recent expenses
        $query = "SELECT id, user_id, amount, description, date, 'expense' as type, category_id FROM expenses 
                  WHERE user_id = ? 
                  UNION ALL 
                  SELECT id, user_id, amount, source as description, date, 'income' as type, category_id FROM income 
                  WHERE user_id = ? 
                  ORDER BY date DESC 
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $user_id, $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
