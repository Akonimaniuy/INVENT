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
        $user_id = $_SESSION['user_id'];
        $stmt = $this->category->read($user_id);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/index.php';
    }

    public function add() {
        $errors = [];
        $user_id = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $errors[] = 'Category name is required.';
            } elseif ($this->category->nameExists($name, $user_id)) {
                $errors[] = 'A category with this name already exists.';
            }

            if (empty($errors)) {
                if ($this->category->create($name, $user_id)) {
                    $_SESSION['message'] = 'Category added successfully!';
                    header('Location: index.php?action=categories');
                    exit;
                } else {
                    $errors[] = 'Failed to add category due to a server error.';
                }
            }
        }
        require_once 'views/add.php';
    }

    public function edit($id) {
        $errors = [];
        $user_id = $_SESSION['user_id'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                $errors[] = 'Category name is required.';
            } elseif ($this->category->nameExists($name, $user_id, $id)) {
                $errors[] = 'Another category with this name already exists.';
            }

            if (empty($errors)) {
                if ($this->category->update($id, $name, $user_id)) {
                    $_SESSION['message'] = 'Category updated successfully!';
                    header('Location: index.php?action=categories');
                    exit;
                } else {
                    $errors[] = 'Failed to update category due to a server error.';
                }
            }
        } else {
            // Fetch category details for the form
            $category = $this->category->readOne($id, $user_id);
            if (!$category) {
                $_SESSION['error'] = 'Category not found.';
                header('Location: index.php?action=categories');
                exit;
            }
        }
        require_once 'views/edit.php';
    }

    public function delete($id) {
        $user_id = $_SESSION['user_id'];
        if ($this->category->delete($id, $user_id)) {
            $_SESSION['message'] = 'Category deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete category.';
        }
        header('Location: index.php?action=categories');
        exit;
    }
}
?>