<?php
require_once 'models/Category.php';
require_once 'config/Database.php';

class CategoryController {
    private $db;
    private $category;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->category = new Category($this->db);
    }

    public function index() {
        $stmt = $this->category->read();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/index.php';
    }

    public function add() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $errors[] = 'Category name is required.';
            } elseif ($this->category->nameExists($name)) {
                $errors[] = 'A category with this name already exists.';
            }

            if (empty($errors)) {
                $this->category->name = $name;
                if ($this->category->create()) {
                    $_SESSION['message'] = 'Category added successfully!';
                    header('Location: index.php?action=categories');
                    exit;
                } else {
                    $errors[] = 'Failed to add category due to a server error.';
                }
            }
        }
        require_once 'views/add.php'; // Corrected path
    }

    public function edit($id) {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $errors[] = 'Category name is required.';
            } elseif ($this->category->nameExists($name, $id)) {
                $errors[] = 'Another category with this name already exists.';
            }

            if (empty($errors)) {
                $this->category->id = $id;
                $this->category->name = $name;
                if ($this->category->update()) {
                    $_SESSION['message'] = 'Category updated successfully!';
                    header('Location: index.php?action=categories');
                    exit;
                } else {
                    $errors[] = 'Failed to update category due to a server error.';
                }
            }
        } else {
            // Fetch category details for the form
            $this->category->id = $id;
            if (!$this->category->readOne()) {
                $_SESSION['error'] = 'Category not found.';
                header('Location: index.php?action=categories');
                exit;
            }
        }
        require_once 'views/edit.php'; // Corrected path
    }

    public function delete($id) {
        $this->category->id = $id;
        if ($this->category->delete()) {
            $_SESSION['message'] = 'Category deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete category.';
        }
        header('Location: index.php?action=categories');
        exit;
    }
}
?>