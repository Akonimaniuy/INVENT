<?php
class Product {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProducts($search_term = '', $sort_column = 'date_added', $sort_order = 'DESC', $limit = 10, $offset = 0) {
        $query = 'SELECT
                    p.id, p.name, p.description, p.price, p.quantity, p.image, p.date_added, p.date_updated,
                    c.name as category_name
                  FROM ' . $this->table . ' p
                  LEFT JOIN categories c ON p.category_id = c.id
                  ';

        $where_clauses = [];
        $params = ['limit' => $limit, 'offset' => $offset];

        if (!empty($search_term)) {
            $where_clauses[] = '(p.name LIKE :search_term OR p.description LIKE :search_term)';
            $params['search_term'] = "%{$search_term}%";
        }

        if (!empty($where_clauses)) {
            $query .= ' WHERE ' . implode(' AND ', $where_clauses);
        }

        // Note: Column names for ORDER BY cannot be bound as parameters. They are validated in the controller.
        $order_by_column = ($sort_column === 'category_name') ? 'category_name' : 'p.' . $sort_column;
        $query .= ' ORDER BY ' . $order_by_column . ' ' . $sort_order . ' LIMIT :limit OFFSET :offset';
        $stmt = $this->conn->prepare($query);

        // Bind all parameters
        $stmt->bindValue(':limit', $params['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $params['offset'], PDO::PARAM_INT);
        if (isset($params['search_term'])) {
            $stmt->bindValue(':search_term', $params['search_term']);
        }

        $stmt->execute();
        return $stmt;
    }

    public function countProducts($search_term = '') {
        $query = 'SELECT COUNT(id) as total FROM ' . $this->table;
        $params = [];
        if (!empty($search_term)) {
            $query .= ' WHERE name LIKE ? OR description LIKE ?';
            $params[] = "%{$search_term}%";
            $params[] = "%{$search_term}%";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getProductById($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addProduct($name, $description, $price, $quantity, $category_id, $image) {
        $this->conn->beginTransaction();
        try {
            $query = 'INSERT INTO ' . $this->table . ' (name, description, price, quantity, category_id, image) VALUES (?, ?, ?, ?, ?, ?)';
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name, $description, $price, $quantity, $category_id, $image]);
            $product_id = $this->conn->lastInsertId();

            $this->logMovement($product_id, 'initial_stock', $quantity, $quantity);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function updateProduct($id, $name, $description, $price, $quantity, $category_id, $image) {
        $this->conn->beginTransaction();
        try {
            // Get current quantity before update
            $stmt_get = $this->conn->prepare('SELECT quantity FROM ' . $this->table . ' WHERE id = ?');
            $stmt_get->execute([$id]);
            $old_quantity = $stmt_get->fetchColumn();

            // Update the product
            $query = 'UPDATE ' . $this->table . ' SET name = ?, description = ?, price = ?, quantity = ?, category_id = ?, image = ? WHERE id = ?';
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name, $description, $price, $quantity, $category_id, $image, $id]);

            // Log movement if quantity changed
            $quantity_change = $quantity - $old_quantity;
            if ($quantity_change != 0) {
                $movement_type = $quantity_change > 0 ? 'manual_add' : 'manual_remove';
                $this->logMovement($id, $movement_type, $quantity_change, $quantity);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function deleteProduct($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    public function recordSale($product_id, $quantity_sold, $user_id) {
        $this->conn->beginTransaction();
        try {
            // 1. Get product info and lock the row for update to prevent race conditions
            $stmt_get = $this->conn->prepare('SELECT quantity, price FROM ' . $this->table . ' WHERE id = ? FOR UPDATE');
            $stmt_get->execute([$product_id]);
            $product = $stmt_get->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Product not found.");
            }

            // 2. Check for sufficient stock
            if ($product['quantity'] < $quantity_sold) {
                throw new Exception("Not enough stock. Only " . $product['quantity'] . " items available.");
            }

            // 3. Update product quantity
            $new_quantity = $product['quantity'] - $quantity_sold;
            $stmt_update = $this->conn->prepare('UPDATE ' . $this->table . ' SET quantity = ? WHERE id = ?');
            $stmt_update->execute([$new_quantity, $product_id]);

            // 4. Log the sale
            $total_price = $product['price'] * $quantity_sold;
            $stmt_sale = $this->conn->prepare('INSERT INTO sales (product_id, user_id, quantity_sold, price_per_item, total_price) VALUES (?, ?, ?, ?, ?)');
            $stmt_sale->execute([$product_id, $user_id, $quantity_sold, $product['price'], $total_price]);

            // 5. Log the movement
            $this->logMovement($product_id, 'sale', -$quantity_sold, $new_quantity);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            // Pass the specific error message to the controller
            $_SESSION['error'] = $e->getMessage();
            return false;
        }
    }

    // Get inventory statistics for the dashboard
    public function getDashboardStats() {
        $stats = [];

        // 1. Total number of products (unique items)
        $query1 = 'SELECT COUNT(id) as total_products FROM ' . $this->table;
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->execute();
        $stats['total_products'] = $stmt1->fetch(PDO::FETCH_ASSOC)['total_products'];

        // 2. Total stock value
        $query2 = 'SELECT SUM(price * quantity) as total_value FROM ' . $this->table;
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $stats['total_value'] = $stmt2->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;

        return $stats;
    }

    // Get products with low stock
    public function getLowStockProducts($threshold = 10) {
        $query = 'SELECT id, name, quantity, image FROM ' . $this->table . ' WHERE quantity < ? AND quantity > 0 ORDER BY quantity ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get product count per category for reports
    public function getProductsCountByCategory() {
        $query = 'SELECT c.name, COUNT(p.id) as product_count 
                  FROM categories c 
                  LEFT JOIN products p ON c.id = p.category_id 
                  GROUP BY c.id, c.name 
                  ORDER BY product_count DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total stock value per category for reports
    public function getStockValueByCategory() {
        $query = 'SELECT c.name, SUM(p.price * p.quantity) as total_value 
                  FROM categories c 
                  LEFT JOIN products p ON c.id = p.category_id 
                  GROUP BY c.id, c.name 
                  ORDER BY total_value DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Log a stock movement
    private function logMovement($product_id, $movement_type, $quantity_change, $new_quantity) {
        $query = 'INSERT INTO product_movements (product_id, movement_type, quantity_change, new_quantity) VALUES (?, ?, ?, ?)';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$product_id, $movement_type, $quantity_change, $new_quantity]);
    }

    // Get movement history for a product
    public function getMovementHistory($product_id) {
        $query = 'SELECT * FROM product_movements WHERE product_id = ? ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
