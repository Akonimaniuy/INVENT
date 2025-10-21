<?php
require_once 'models/Product.php';
require_once 'models/Category.php';
require_once 'config/database.php';
require_once 'config/Database.php';
require_once 'models/Sale.php';
require_once 'views/ImageUploader.php';

class ProductController {
    private $db;
    private $product;
    private $category;
    private $sale;
    private $imageUploader;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
        $this->category = new Category($this->db);
        $this->sale = new Sale($this->db);
        $this->imageUploader = new ImageUploader();
    }

    public function dashboard() {
        $user_id = $_SESSION['user_id'];
        $stats = $this->product->getDashboardStats($user_id);
        $low_stock_products = $this->product->getLowStockProducts($user_id);
        $recent_sales = $this->sale->getRecentSales($user_id, 5);
        $top_selling_products = $this->sale->getTopSellingProducts($user_id, 5);
        // These variables will be available in the dashboard view
        require_once 'views/dashboard.php';
    }

    public function index() {
        $user_id = $_SESSION['user_id'];
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
        $view_mode = isset($_GET['view']) && $_GET['view'] === 'categorized' ? 'categorized' : 'list';

        // Whitelist allowed sort columns to prevent SQL injection
        $sort_whitelist = ['name', 'price', 'quantity', 'date_added', 'date_updated', 'category_name'];
        $default_sort = ($view_mode === 'categorized') ? 'category_name' : 'date_updated';
        $sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $sort_whitelist) ? $_GET['sort'] : $default_sort;

        // Whitelist allowed sort orders
        $default_order = ($view_mode === 'categorized') ? 'ASC' : 'DESC';
        $sort_order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : $default_order;

        if ($view_mode === 'list') {
            // Pagination logic for list view
            $products_per_page = 10;
            $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($current_page < 1) {
                $current_page = 1;
            }
            $total_products = $this->product->countProducts($user_id, $search_term);
            $total_pages = ceil($total_products / $products_per_page);
            $offset = ($current_page - 1) * $products_per_page;

            $stmt = $this->product->getProducts($user_id, $search_term, $sort_column, $sort_order, $products_per_page, $offset);
        } else {
            // For categorized view, fetch all products, no pagination
            $stmt = $this->product->getProducts($user_id, $search_term, $sort_column, $sort_order, 1000, 0);
        }

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // The variables will be available in the view
        require_once 'views/view_products.php';
    }

    public function add() {
        $user_id = $_SESSION['user_id'];
        $category_stmt = $this->category->read($user_id);
        $categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $price = $_POST['price'] ?? '';
            $quantity = $_POST['quantity'] ?? '';

            if (empty($name)) $errors[] = 'Product name is required.';
            if (!is_numeric($price) || $price < 0) $errors[] = 'Price must be a non-negative number.';
            if (!is_numeric($quantity) || floor($quantity) != $quantity || $quantity < 0) $errors[] = 'Quantity must be a non-negative integer.';

            $image_name = $this->imageUploader->upload($_FILES['image']) ?: '';

            // Check for upload errors set in the session by the uploader
            if (isset($_SESSION['error'])) {
                $errors[] = $_SESSION['error'];
                unset($_SESSION['error']);
            }

            if (empty($errors)) {
                $this->product->addProduct($user_id, $name, $_POST['description'], $price, $quantity, $_POST['category_id'], $image_name);
                $_SESSION['message'] = 'Product added successfully!';
                header('Location: index.php');
                exit;
            }
        }
        require_once 'views/add_product.php';
    }

    public function edit($id) {
        $user_id = $_SESSION['user_id'];
        $category_stmt = $this->category->read($user_id);
        $categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
        $product = $this->product->getProductById($id, $user_id);

        if (!$product) {
            $_SESSION['error'] = 'Product not found.';
            header('Location: index.php');
            exit;
        }
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $image_name = $product['image']; // Keep old image by default
            $name = trim($_POST['name'] ?? '');
            $price = $_POST['price'] ?? '';
            $quantity = $_POST['quantity'] ?? '';

            if (empty($name)) $errors[] = 'Product name is required.';
            if (!is_numeric($price) || $price < 0) $errors[] = 'Price must be a non-negative number.';
            if (!is_numeric($quantity) || floor($quantity) != $quantity || $quantity < 0) $errors[] = 'Quantity must be a non-negative integer.';

            // Check for a new file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $new_image = $this->imageUploader->upload($_FILES['image']);
                if ($new_image) {
                    $this->imageUploader->delete($image_name); // Delete old image
                    $image_name = $new_image; // Set new image name
                }
            }

            // Check for upload errors set in the session by the uploader
            if (isset($_SESSION['error'])) {
                $errors[] = $_SESSION['error'];
                unset($_SESSION['error']);
            }

            if (empty($errors)) {
                $this->product->updateProduct($id, $user_id, $name, $_POST['description'], $price, $quantity, $_POST['category_id'], $image_name);
                $_SESSION['message'] = 'Product updated successfully!';
                header('Location: index.php');
                exit;
            }
        }
        require_once 'views/edit_product.php';
    }

    public function delete($id) {
        $user_id = $_SESSION['user_id'];
        $product = $this->product->getProductById($id, $user_id);
        $this->imageUploader->delete($product['image'] ?? null);
        $this->product->deleteProduct($id, $user_id);
        header('Location: index.php');
    }

    public function history($id) {
        $user_id = $_SESSION['user_id'];
        $product = $this->product->getProductById($id, $user_id);
        if (!$product) {
            $_SESSION['error'] = 'Product not found.';
            header('Location: index.php');
            exit;
        }
        $history = $this->product->getMovementHistory($id);

        // Pass data to the view
        require_once 'views/product_history.php';
    }

    public function searchProducts() {
        $user_id = $_SESSION['user_id'];
        header('Content-Type: application/json');
        $searchTerm = isset($_GET['term']) ? trim($_GET['term']) : '';
        if (empty($searchTerm)) {
            echo json_encode([]);
            exit;
        }
        $products = $this->product->getProducts($user_id, $searchTerm, 'name', 'ASC', 10, 0)->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
        exit;
    }

    public function exportToCsv() {
        $user_id = $_SESSION['user_id'];
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

        // Whitelist allowed sort columns
        $sort_whitelist = ['name', 'price', 'quantity', 'date_added', 'date_updated', 'category_name'];
        $sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $sort_whitelist) ? $_GET['sort'] : 'date_updated';

        // Whitelist allowed sort orders
        $sort_order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'DESC';

        // Fetch all matching records (no pagination)
        $stmt = $this->product->getProducts($user_id, $search_term, $sort_column, $sort_order, 1000000, 0);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "product_list_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add CSV header
        fputcsv($output, ['ID', 'Name', 'Category', 'Description', 'Price', 'Quantity', 'Date Added', 'Last Updated']);

        // Add data rows
        foreach ($products as $product) {
            fputcsv($output, [$product['id'], $product['name'], $product['category_name'] ?? 'N/A', $product['description'], $product['price'], $product['quantity'], $product['date_added'], $product['date_updated']]);
        }

        fclose($output);
        exit;
    }
}
?>