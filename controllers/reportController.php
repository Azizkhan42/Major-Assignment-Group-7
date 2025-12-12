<?php
// Report Controller
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

class ReportController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Get monthly report
     */
    public function getMonthlyReport($user_id, $year, $month) {
        $start_date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $end_date = date('Y-m-t', strtotime($start_date));

        $income_query = "SELECT SUM(amount) as total FROM income WHERE user_id = ? AND date BETWEEN ? AND ?";
        $expense_query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND date BETWEEN ? AND ?";

        $stmt = $this->conn->prepare($income_query);
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        $stmt->execute();
        $income = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        $stmt = $this->conn->prepare($expense_query);
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        $stmt->execute();
        $expense = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        return [
            'period' => date('F Y', strtotime($start_date)),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_income' => $income,
            'total_expense' => $expense,
            'net_savings' => $income - $expense
        ];
    }

    /**
     * Get yearly report
     */
    public function getYearlyReport($user_id, $year) {
        $start_date = "$year-01-01";
        $end_date = "$year-12-31";

        $income_query = "SELECT SUM(amount) as total FROM income WHERE user_id = ? AND date BETWEEN ? AND ?";
        $expense_query = "SELECT SUM(amount) as total FROM expenses WHERE user_id = ? AND date BETWEEN ? AND ?";

        $stmt = $this->conn->prepare($income_query);
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        $stmt->execute();
        $income = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        $stmt = $this->conn->prepare($expense_query);
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        $stmt->execute();
        $expense = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

        return [
            'period' => $year,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_income' => $income,
            'total_expense' => $expense,
            'net_savings' => $income - $expense
        ];
    }

    /**
     * Get monthly breakdown by category
     */
    public function getMonthlyBreakdown($user_id, $year, $month) {
        $start_date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
        $end_date = date('Y-m-t', strtotime($start_date));

        $income_query = "SELECT ic.name, SUM(i.amount) as amount FROM income i
                         LEFT JOIN income_categories ic ON i.category_id = ic.id
                         WHERE i.user_id = ? AND i.date BETWEEN ? AND ?
                         GROUP BY ic.id, ic.name";

        $expense_query = "SELECT ec.name, SUM(e.amount) as amount FROM expenses e
                          LEFT JOIN expense_categories ec ON e.category_id = ec.id
                          WHERE e.user_id = ? AND e.date BETWEEN ? AND ?
                          GROUP BY ec.id, ec.name";

        $stmt = $this->conn->prepare($income_query);
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        $stmt->execute();
        $income_breakdown = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt = $this->conn->prepare($expense_query);
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        $stmt->execute();
        $expense_breakdown = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'income' => $income_breakdown,
            'expenses' => $expense_breakdown
        ];
    }

    /**
     * Get transaction details for report
     */
    public function getTransactionDetails($user_id, $start_date, $end_date, $type = null) {
        if ($type === 'income') {
            $query = "SELECT i.id, i.amount, i.source as description, ic.name as category, i.date, 'income' as type
                      FROM income i
                      LEFT JOIN income_categories ic ON i.category_id = ic.id
                      WHERE i.user_id = ? AND i.date BETWEEN ? AND ?
                      ORDER BY i.date DESC";
        } elseif ($type === 'expense') {
            $query = "SELECT e.id, e.amount, e.description, ec.name as category, e.date, 'expense' as type
                      FROM expenses e
                      LEFT JOIN expense_categories ec ON e.category_id = ec.id
                      WHERE e.user_id = ? AND e.date BETWEEN ? AND ?
                      ORDER BY e.date DESC";
        } else {
            $query = "SELECT id, amount, description, category, date, type FROM (
                        SELECT i.id, i.amount, i.source as description, ic.name as category, i.date, 'income' as type
                        FROM income i
                        LEFT JOIN income_categories ic ON i.category_id = ic.id
                        WHERE i.user_id = ? AND i.date BETWEEN ? AND ?
                        UNION ALL
                        SELECT e.id, e.amount, e.description, ec.name as category, e.date, 'expense' as type
                        FROM expenses e
                        LEFT JOIN expense_categories ec ON e.category_id = ec.id
                        WHERE e.user_id = ? AND e.date BETWEEN ? AND ?
                      ) as combined_transactions
                      ORDER BY date DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isssss", $user_id, $start_date, $end_date, $user_id, $start_date, $end_date);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
