<?php
// Income Controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/validation.php';

class IncomeController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Add income
     */
    public function addIncome($user_id, $category_id, $amount, $source, $description, $date) {
        // Validation
        if (!validateAmount($amount)) {
            return ['success' => false, 'error' => 'Invalid amount'];
        }
        if (!validateDate($date)) {
            return ['success' => false, 'error' => 'Invalid date format'];
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO income (user_id, category_id, amount, source, description, date) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iidsss", $user_id, $category_id, $amount, $source, $description, $date);

        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => 'Failed to add income'];
    }

    /**
     * Get income by ID
     */
    public function getIncomeById($id, $user_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM income WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update income
     */
    public function updateIncome($id, $user_id, $category_id, $amount, $source, $description, $date) {
        if (!validateAmount($amount)) {
            return ['success' => false, 'error' => 'Invalid amount'];
        }

        $stmt = $this->conn->prepare(
            "UPDATE income SET category_id = ?, amount = ?, source = ?, description = ?, date = ? 
             WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("idsssii", $category_id, $amount, $source, $description, $date, $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to update income'];
    }

    /**
     * Delete income
     */
    public function deleteIncome($id, $user_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM income WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $id, $user_id);

        if ($stmt->execute()) {
            return ['success' => true];
        }
        return ['success' => false, 'error' => 'Failed to delete income'];
    }

    /**
     * Get all income for user with filters
     */
    public function getIncome($user_id, $filters = []) {
        $query = "SELECT i.*, ic.name as category_name FROM income i 
                  LEFT JOIN income_categories ic ON i.category_id = ic.id 
                  WHERE i.user_id = ?";
        $params = [$user_id];
        $types = "i";

        if (!empty($filters['start_date'])) {
            $query .= " AND i.date >= ?";
            $params[] = $filters['start_date'];
            $types .= "s";
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND i.date <= ?";
            $params[] = $filters['end_date'];
            $types .= "s";
        }

        if (!empty($filters['category_id'])) {
            $query .= " AND i.category_id = ?";
            $params[] = $filters['category_id'];
            $types .= "i";
        }

        $query .= " ORDER BY i.date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total income for user
     */
    public function getTotalIncome($user_id, $start_date = null, $end_date = null) {
        $query = "SELECT SUM(amount) as total FROM income WHERE user_id = ?";
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
     * Get income by category for user
     */
    public function getIncomeByCategoryForChart($user_id, $start_date = null, $end_date = null) {
        $query = "SELECT ic.name, SUM(i.amount) as amount FROM income i 
                  LEFT JOIN income_categories ic ON i.category_id = ic.id 
                  WHERE i.user_id = ?";
        $params = [$user_id];
        $types = "i";

        if ($start_date) {
            $query .= " AND i.date >= ?";
            $params[] = $start_date;
            $types .= "s";
        }

        if ($end_date) {
            $query .= " AND i.date <= ?";
            $params[] = $end_date;
            $types .= "s";
        }

        $query .= " GROUP BY ic.id, ic.name ORDER BY amount DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
