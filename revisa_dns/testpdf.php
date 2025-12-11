<?php
// Forzar carga de FPDF
define('FPDF_FONTPATH', __DIR__ . '/lib/fpdf/font/');
require __DIR__ . '/lib/fpdf/fpdf.php';

class PDF_DNS extends FPDF {
    public $generatedAt = '';

    function Header() {
        // Logo (si existe)
        $logoPathPng = __DIR__ . '/logo.png';
        $logoPathJpg = __DIR__ . '/logo.jpg';
        if (file_exists($logoPathPng)) {
            $this->Image($logoPathPng,10,6,30);
        } elseif (file_exists($logoPathJpg)) {
            $this->Image($logoPathJpg,10,6,30);
        }

        // Título
        $this->SetFont('Arial','B',14);
        $this->SetXY(45,10);
        $this->Cell(0,8,utf8_decode('Reporte DNS de dominios .cl'),0,1,'L');

        // Fecha/hora a la derecha
        $this->SetFont('Arial','',10);
        $this->SetXY(-95,10);
        $ts = $this->generatedAt ?: date('Y-m-d H:i:s');
        $this->Cell(85,8,'Generado: '.$ts,0,0,'R');

        $this->Ln(15);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Crear PDF
$pdf = new PDF_DNS('L','mm','A4');
$pdf->generatedAt = date('Y-m-d H:i:s');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Texto de prueba
for ($i=1; $i<=50; $i++) {
    $pdf->Cell(0,10,"Linea de prueba $i",0,1);
}

// Salida al navegador
$pdf->Output('I', 'testpdf.pdf');