<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login or show a pop-up message
    echo "<script>
        alert('Please log in or register to proceed to checkout.');
        window.location.href = 'login.php';
    </script>";
    exit();
}

// User is logged in, generate PDF invoice
require('fpdf/fpdf.php'); // Include the FPDF library or your chosen PDF library

class PDF extends FPDF
{
    // Custom header (optional)
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Cake Zone Invoice', 0, 1, 'C');
        $this->Ln(10);
    }
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Thank you for shopping with us!', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Add invoice details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Invoice Date: ' . date('Y-m-d'), 0, 1);
$pdf->Cell(0, 10, 'Customer ID: ' . $_SESSION['user_id'], 0, 1);
$pdf->Ln(10);

$pdf->SetFillColor(200, 220, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(80, 10, 'Product Name', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Price (Each)', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Subtotal', 1, 1, 'C', true);

$total = 0;

$pdf->SetFont('Arial', '', 12);

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
        $subtotal = $item['product_price'] * $quantity;
        $total += $subtotal;

        $pdf->Cell(80, 10, $item['product_name'], 1);
        $pdf->Cell(40, 10, '$' . number_format($item['product_price'], 2), 1, 0, 'C');
        $pdf->Cell(30, 10, $quantity, 1, 0, 'C');
        $pdf->Cell(40, 10, '$' . number_format($subtotal, 2), 1, 1, 'C');
    }
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 10, 'Total', 1, 0, 'R');
$pdf->Cell(40, 10, '$' . number_format($total, 2), 1, 1, 'C');


// Output the PDF to the browser or save to a file
$pdf->Output('D', 'Invoice.pdf'); // Download the file
exit();
?>