<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($full_name, $email, $password) {
        $query = 'INSERT INTO ' . $this->table . ' (full_name, email, password) VALUES (?, ?, ?)';
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(1, $full_name);
        $stmt->bindParam(2, $email);
        $stmt->bindParam(3, $password_hash);
        return $stmt->execute();
    }

    public function login($email, $password) {
        $query = 'SELECT id, full_name, email, password FROM ' . $this->table . ' WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function findByEmail($email) {
        $query = 'SELECT id FROM ' . $this->table . ' WHERE email = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>