<?php
require_once 'models/Product.php';
require_once 'models/Sale.php';
require_once 'config/Database.php';
require_once 'models/InvoiceGenerator.php';

class SalesController {
    private $db;
    private $product;
    private $sale;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
        $this->sale = new Sale($this->db);
    }

    public function generateInvoice($sale_id) {
        $saleDetails = $this->sale->getSaleById($sale_id);

        if (!$saleDetails) {
            $_SESSION['error'] = 'Invoice not found.';
            header('Location: index.php?action=sales_list');
            exit;
        }

        $pdf = new InvoiceGenerator();
        $pdf->generate($saleDetails);
    }

    public function exportToCsv() {
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

        // Fetch all matching records (no pagination)
        $stmt = $this->sale->getSales($search_term, 'sale_date', 'DESC', 1000000, 0, $start_date, $end_date);
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "sales_history_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add CSV header
        fputcsv($output, ['Sale ID', 'Product Name', 'Quantity Sold', 'Price/Item', 'Total Price', 'Sold By', 'Sale Date']);

        // Add data rows
        foreach ($sales as $sale) {
            fputcsv($output, [$sale['id'], $sale['product_name'], $sale['quantity_sold'], $sale['price_per_item'], $sale['total_price'], $sale['user_name'], $sale['sale_date']]);
        }

        fclose($output);
        exit;
    }

    public function list() {
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
        // Get date range, default to empty strings
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

        // Whitelist allowed sort columns
        $sort_whitelist = ['product_name', 'total_price', 'quantity_sold', 'sale_date'];
        $sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $sort_whitelist) ? $_GET['sort'] : 'sale_date';

        // Whitelist allowed sort orders
        $sort_order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

        // Pagination logic
        $sales_per_page = 15;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) {
            $current_page = 1;
        }
        $total_sales = $this->sale->countSales($search_term, $start_date, $end_date);
        $total_pages = ceil($total_sales / $sales_per_page);
        $offset = ($current_page - 1) * $sales_per_page;

        $stmt = $this->sale->getSales($search_term, $sort_column, $sort_order, $sales_per_page, $offset, $start_date, $end_date);
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // The variables will be available in the view
        require_once 'views/sales_list.php';
    }

    public function index() {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 0;
            $user_id = $_SESSION['user_id'];

            if (empty($product_id) || !is_numeric($product_id)) {
                $errors[] = 'Please select a valid product.';
            }
            if (!is_numeric($quantity) || $quantity <= 0) {
                $errors[] = 'Please enter a valid quantity greater than zero.';
            }

            if (empty($errors)) {
                if ($this->product->recordSale($product_id, $quantity, $user_id)) {
                    $_SESSION['message'] = "Sale of {$quantity} item(s) recorded successfully!";
                }
                // If recordSale fails, it sets an error in the session.
                header('Location: index.php?action=record_sale');
                exit;
            }
        }

        $products = $this->product->getProducts('', 'name', 'ASC', 1000, 0)->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/record_sale.php';
    }
}
?>