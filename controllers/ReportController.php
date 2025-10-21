<?php
require_once 'models/Product.php';
require_once 'models/Sale.php';
require_once 'config/Database.php';

class ReportController {
    private $db;
    private $product;
    private $sale;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
        $this->sale = new Sale($this->db);
    }

    public function index() {
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

        // Data for charts
        $products_by_category_data = $this->sale->getSalesCountByCategory($start_date, $end_date);
        $value_by_category_data = $this->sale->getSalesValueByCategory($start_date, $end_date);

        // Prepare data for Chart.js
        $category_labels = [];
        $category_product_counts = [];
        foreach ($products_by_category_data as $item) {
            $category_labels[] = $item['name'];
            $category_product_counts[] = $item['sales_count'];
        }

        $value_labels = [];
        $category_values = [];
        foreach ($value_by_category_data as $item) {
            $value_labels[] = $item['name'];
            $category_values[] = $item['total_value'] ?? 0;
        }

        // Pass all data to the view
        require_once 'views/view_reports.php';
    }
}
?>