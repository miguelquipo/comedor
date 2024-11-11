<?php
require 'fpdf/fpdf.php';

// Obtener los parámetros de la URL
$cedula = $_GET['cedula'];
$codigo_emp = $_GET['codigo_emp'];

// Datos ficticios para el ejemplo (puedes adaptarlos)
$fechaActual = date('Y-m-d');
$horaActual = date('H:i:s');
$nombreCompleto = "Nombre de prueba";

// Generar PDF
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Comprobante de Registro', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Fecha: $fechaActual", 0, 1);
$pdf->Cell(0, 10, "Hora: $horaActual", 0, 1);
$pdf->Cell(0, 10, "Empleado: $nombreCompleto", 0, 1);
$pdf->Cell(0, 10, "Cédula: $cedula", 0, 1);

$pdf->Output('D', "Comprobante_$cedula.pdf");
?>
