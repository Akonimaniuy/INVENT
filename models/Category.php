<?php
class Category {
    private $conn;
    private $table = 'categories';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all categories for a specific user
    public function read($user_id) {
        $query = 'SELECT id, name FROM ' . $this->table . ' WHERE user_id = ? ORDER BY name';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt;
    }

    // Read single category for a specific user
    public function readOne($id, $user_id) {
        $query = 'SELECT id, name FROM ' . $this->table . ' WHERE id = ? AND user_id = ? LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id, $user_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    // Check if a category name exists for a specific user (and optionally ignore a specific ID)
    public function nameExists($name, $user_id, $ignore_id = null) {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE name = ? AND user_id = ?';
        $params = [$name, $user_id];
        if ($ignore_id !== null) {
            $query .= ' AND id != ?';
            $params[] = $ignore_id;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    // Create category for a specific user
    public function create($name, $user_id) {
        $query = 'INSERT INTO ' . $this->table . ' (name, user_id) VALUES (?, ?)';
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $name = htmlspecialchars(strip_tags($name));

        if ($stmt->execute([$name, $user_id])) {
            return true;
        }
        return false;
    }

    // Update category for a specific user
    public function update($id, $name, $user_id) {
        $query = 'UPDATE ' . $this->table . ' SET name = ? WHERE id = ? AND user_id = ?';
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $name = htmlspecialchars(strip_tags($name));

        if ($stmt->execute([$name, $id, $user_id])) {
            return true;
        }
        return false;
    }

    // Delete category for a specific user
    public function delete($id, $user_id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = ? AND user_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id, $user_id]);
        return $stmt->rowCount() > 0;
    }
}
?>