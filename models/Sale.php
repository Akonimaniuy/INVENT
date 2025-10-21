<?php
class Sale {
    private $conn;
    private $table = 'sales';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getSales($user_id, $search_term = '', $sort_column = 'sale_date', $sort_order = 'DESC', $limit = 10, $offset = 0, $start_date = '', $end_date = '') {
        $query = 'SELECT
                    s.id, s.quantity_sold, s.price_per_item, s.total_price, s.sale_date,
                    p.name as product_name,
                    u.full_name as user_name
                  FROM ' . $this->table . ' s
                  LEFT JOIN products p ON s.product_id = p.id
                  LEFT JOIN users u ON s.user_id = u.id
                  ';

        $params = [$user_id];
        $where_clauses = [];
        $where_clauses[] = 's.user_id = ?';

        if (!empty($search_term)) {
            // Search by product name or user name
            $where_clauses[] = '(p.name LIKE ? OR u.full_name LIKE ?)';
            $search_param = "%{$search_term}%";
            $params[] = $search_param;
            $params[] = $search_param;
        }

        if (!empty($start_date)) {
            $where_clauses[] = 's.sale_date >= ?';
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $where_clauses[] = 's.sale_date <= ?';
            $params[] = $end_date . ' 23:59:59'; // Include the whole day
        }
        if (!empty($where_clauses)) {
            $query .= ' WHERE ' . implode(' AND ', $where_clauses);
        }

        // Note: Column names for ORDER BY cannot be bound as parameters. They are validated in the controller.
        $query .= ' ORDER BY ' . $sort_column . ' ' . $sort_order . ' LIMIT ? OFFSET ?';
        $stmt = $this->conn->prepare($query);

        // Bind WHERE params
        $i = 1;
        foreach ($params as $param) {
            $stmt->bindValue($i++, $param); // Data type is inferred
        }

        // Bind LIMIT/OFFSET params as integers
        $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    public function countSales($user_id, $search_term = '', $start_date = '', $end_date = '') {
        $query = 'SELECT COUNT(s.id) as total 
                  FROM ' . $this->table . ' s
                  LEFT JOIN products p ON s.product_id = p.id
                  LEFT JOIN users u ON s.user_id = u.id';
        $params = [$user_id];
        $where_clauses = [];
        $where_clauses[] = 's.user_id = ?';

        if (!empty($search_term)) {
            $where_clauses[] = '(p.name LIKE ? OR u.full_name LIKE ?)';
            $params[] = "%{$search_term}%";
            $params[] = "%{$search_term}%";
        }
        if (!empty($start_date)) {
            $where_clauses[] = 's.sale_date >= ?';
            $params[] = $start_date;
        }
        if (!empty($end_date)) {
            $where_clauses[] = 's.sale_date <= ?';
            $params[] = $end_date . ' 23:59:59';
        }
        if (!empty($where_clauses)) {
            $query .= ' WHERE ' . implode(' AND ', $where_clauses);
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Get the most recent sales transactions.
     * @param int $limit The number of recent sales to fetch.
     * @return array
     */
    public function getRecentSales($user_id, $limit = 5) {
        $query = 'SELECT
                    s.quantity_sold, s.total_price, s.sale_date,
                    p.name as product_name,
                    u.full_name as user_name
                  FROM ' . $this->table . ' s
                  LEFT JOIN products p ON s.product_id = p.id
                  LEFT JOIN users u ON s.user_id = u.id
                  WHERE s.user_id = :user_id
                  ORDER BY s.sale_date DESC
                  LIMIT :limit';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get the top-selling products by total quantity sold.
     * @param int $limit The number of top products to fetch.
     * @return array
     */
    public function getTopSellingProducts($user_id, $limit = 5) {
        $query = 'SELECT p.name as product_name, p.image as product_image, SUM(s.quantity_sold) as total_quantity_sold
                  FROM ' . $this->table . ' s
                  JOIN products p ON s.product_id = p.id
                  WHERE s.user_id = :user_id
                  GROUP BY s.product_id, p.name, p.image
                  ORDER BY total_quantity_sold DESC
                  LIMIT :limit';
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get sales count per category for reports
    public function getSalesCountByCategory($user_id, $start_date = '', $end_date = '') {
        $query = 'SELECT c.name, COUNT(s.id) as sales_count 
                  FROM categories c 
                  JOIN products p ON c.id = p.category_id
                  JOIN sales s ON p.id = s.product_id';
        
        $params = [];
        $where_clauses = ['s.user_id = ?'];
        $params[] = $user_id;
        if (!empty($start_date)) { $where_clauses[] = 's.sale_date >= ?'; $params[] = $start_date; }
        if (!empty($end_date)) { $where_clauses[] = 's.sale_date <= ?'; $params[] = $end_date . ' 23:59:59'; }
        if (!empty($where_clauses)) { $query .= ' WHERE ' . implode(' AND ', $where_clauses); }

        $query .= ' GROUP BY c.id, c.name ORDER BY sales_count DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total sales value per category for reports
    public function getSalesValueByCategory($user_id, $start_date = '', $end_date = '') {
        $query = 'SELECT c.name, SUM(s.total_price) as total_value 
                  FROM categories c 
                  JOIN products p ON c.id = p.category_id
                  JOIN sales s ON p.id = s.product_id';

        $params = [];
        $where_clauses = ['s.user_id = ?'];
        $params[] = $user_id;
        if (!empty($start_date)) { $where_clauses[] = 's.sale_date >= ?'; $params[] = $start_date; }
        if (!empty($end_date)) { $where_clauses[] = 's.sale_date <= ?'; $params[] = $end_date . ' 23:59:59'; }
        if (!empty($where_clauses)) { $query .= ' WHERE ' . implode(' AND ', $where_clauses); }

        $query .= ' GROUP BY c.id, c.name ORDER BY total_value DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSaleById($sale_id, $user_id) {
        $query = 'SELECT
                    s.id, s.quantity_sold, s.price_per_item, s.total_price, s.sale_date,
                    p.name as product_name,
                    u.full_name as user_name
                  FROM ' . $this->table . ' s
                  LEFT JOIN products p ON s.product_id = p.id
                  LEFT JOIN users u ON s.user_id = u.id
                  WHERE s.id = ? AND s.user_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$sale_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>