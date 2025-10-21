<?php
session_start();

require_once 'config/Database.php'; // New: Database connection class
require_once 'controllers/ProductController.php';
require_once 'controllers/ReportController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/SalesController.php';
require_once 'controllers/CategoryController.php'; // New: Category controller

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_SESSION['user_id']) ? 'dashboard' : 'landing');
$id = isset($_GET['id']) ? $_GET['id'] : null;

$public_actions = ['login', 'register', 'landing'];

if (!isset($_SESSION['user_id']) && !in_array($action, $public_actions)) {
    // If a non-logged-in user tries to access a protected page, redirect to landing
    header('Location: index.php?action=landing');
    exit;
}

switch ($action) {
    case 'dashboard':
        $controller = new ProductController();
        $controller->dashboard();
        break;
    case 'products': // New route for the full product list
        $controller = new ProductController();
        $controller->index();
        break;
    case 'add':
        $controller = new ProductController();
        $controller->add();
        break;
    case 'edit':
        $controller = new ProductController();
        $controller->edit($id);
        break;
    case 'delete':
        $controller = new ProductController();
        $controller->delete($id);
        break;
    case 'history':
        $controller = new ProductController();
        $controller->history($id);
        break;
    case 'search_products': // New AJAX endpoint
        $controller = new ProductController();
        $controller->searchProducts();
        break;
    case 'export_products_csv':
        $controller = new ProductController();
        $controller->exportToCsv();
        break;
    case 'record_sale':
        $controller = new SalesController();
        $controller->index();
        break;
    case 'sales_list':
        $controller = new SalesController();
        $controller->list();
        break;
    case 'invoice':
        $controller = new SalesController();
        $controller->generateInvoice($id);
        break;
    case 'export_sales_csv':
        $controller = new SalesController();
        $controller->exportToCsv();
        break;
    case 'reports':
        $controller = new ReportController();
        $controller->index();
        break;
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    case 'landing':
        $controller = new UserController();
        $controller->landing();
        break;
    // New: Category management routes
    case 'categories':
        $controller = new CategoryController();
        $controller->index();
        break;
    case 'add_category':
        $controller = new CategoryController();
        $controller->add();
        break;
    case 'edit_category':
        $controller = new CategoryController();
        $controller->edit($id);
        break;
    case 'delete_category':
        $controller = new CategoryController();
        $controller->delete($id);
        break;
    default:
        if (isset($_SESSION['user_id'])) {
            $controller = new ProductController(); // Default to dashboard for logged-in users
            $controller->dashboard();
        } else {
            header('Location: index.php?action=landing'); // Redirect to landing if not logged in
            exit;
        }
        break;
}
?>