<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'ims'; // Make sure this matches your database name
    private $username = 'root'; // Your database username
    private $password = ''; // Your database password
    private $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo 'Connection error: ' . $exception->getMessage();
            // In a production environment, you might log this error instead of echoing
            exit();
        }

        return $this->conn;
    }
}
?>