<?php
$fpdfPath = __DIR__ . '/../lib/fpdf.php';
if (!file_exists($fpdfPath)) {
    die('<b>Error:</b> FPDF library not found. Please download it from fpdf.org and place it in the /lib directory.');
}
require_once $fpdfPath;

class InvoiceGenerator extends FPDF {
    
    // Page header
    function Header() {
        // Logo or Company Name
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 10, 'INVOICE', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Inventory Management System', 0, 1, 'C');
        $this->Ln(10);
    }

    // Page footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Invoice content
    public function generate(array $saleDetails) {
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Arial', '', 12);

        // Invoice Details
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, 'Invoice ID:');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $saleDetails['id'], 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, 'Date:');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, date('M d, Y H:i', strtotime($saleDetails['sale_date'])), 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(40, 10, 'Sold To:');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $saleDetails['user_name'], 0, 1);

        $this->Ln(10);

        // Table Header
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(230, 230, 230);
        $this->Cell(100, 10, 'Product', 1, 0, 'L', true);
        $this->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
        $this->Cell(30, 10, 'Price/Item', 1, 0, 'R', true);
        $this->Cell(30, 10, 'Total', 1, 1, 'R', true);

        // Table Row
        $this->SetFont('Arial', '', 12);
        $this->Cell(100, 10, $saleDetails['product_name'], 1);
        $this->Cell(30, 10, $saleDetails['quantity_sold'], 1, 0, 'C');
        $this->Cell(30, 10, '$' . number_format($saleDetails['price_per_item'], 2), 1, 0, 'R');
        $this->Cell(30, 10, '$' . number_format($saleDetails['total_price'], 2), 1, 1, 'R');

        $this->Ln(10);

        // Grand Total
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(130, 10, '', 0);
        $this->Cell(30, 10, 'Grand Total', 1, 0, 'R', true);
        $this->Cell(30, 10, '$' . number_format($saleDetails['total_price'], 2), 1, 1, 'R', true);

        $this->Output('I', 'Invoice-' . $saleDetails['id'] . '.pdf');
    }
}
?>
