<?php
// Clase personalizada para reportes DNS en PDF (FPDF requerido)
if (!class_exists('FPDF')) {
    require_once __DIR__ . '/lib/fpdf/fpdf.php';
}

class PDF_DNS extends FPDF {
    public $generatedAt = '';
    protected $widths = array();
    protected $aligns = array();
    protected $headers = array();

    function SetWidths($w){ $this->widths = $w; }
    function SetAligns($a){ $this->aligns = $a; }
    function SetTableHeader($h){ $this->headers = $h; }

    function Header() {
        // Logo
        $logoPathPng = __DIR__ . '/logo.png';
        $logoPathJpg = __DIR__ . '/logo.jpg';
        if (file_exists($logoPathPng))      $this->Image($logoPathPng,10,6,30);
        elseif (file_exists($logoPathJpg))  $this->Image($logoPathJpg,10,6,30);

        // Título
        $this->SetFont('Arial','B',14);
        $this->SetXY(45,10);
        $this->Cell(0,8,utf8_decode('Reporte DNS de dominios .cl'),0,1,'L');

        // Fecha/hora a la derecha
        $this->SetFont('Arial','',10);
        $this->SetXY(-95,10);
        $ts = $this->generatedAt ?: date('Y-m-d H:i:s');
        $this->Cell(85,8,'Generado: '.$ts,0,0,'R');

        // Espacio
        $this->Ln(15);

        // Encabezado de la tabla (en todas las páginas)
        if (!empty($this->headers) && !empty($this->widths)) {
            $this->SetFont('Arial','B',9);
            $this->SetFillColor(230,230,230);
            $this->SetTextColor(0);
            for ($i=0;$i<count($this->headers);$i++){
                $w = isset($this->widths[$i]) ? $this->widths[$i] : 30;
                $this->Cell($w,7,utf8_decode($this->headers[$i]),1,0,'C',true);
            }
            $this->Ln();
        }
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // Número de líneas que ocupará un texto en MultiCell (de FPDF FAQ)
    function NbLines($w, $txt){
        $cw = &$this->CurrentFont['cw'];
        if($w==0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n") $nb--;
        $sep = -1; $i = 0; $j = 0; $l = 0; $nl = 1;
        while($i<$nb){
            $c = $s[$i];
            if($c=="\n"){ $i++; $sep=-1; $j=$i; $l=0; $nl++; continue; }
            if($c==' ') $sep=$i;
            $l += isset($cw[$c]) ? $cw[$c] : 0;
            if($l>$wmax){
                if($sep==-1){ if($i==$j) $i++; }
                else $i = $sep + 1;
                $sep = -1; $j = $i; $l = 0; $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    // Salto de página si no cabe la altura h
    function CheckPageBreak($h){
        if($this->GetY() + $h > $this->PageBreakTrigger){
            $this->AddPage($this->CurOrientation);
        }
    }

    // Dibuja una fila manteniendo alineación vertical por altura máxima
    function Row($data){
        $nb = 0;
        for($i=0;$i<count($data);$i++){
            $txt = str_replace("\r",'', $data[$i]);
            $w = isset($this->widths[$i]) ? $this->widths[$i] : 30;
            $nb = max($nb, $this->NbLines($w, utf8_decode($txt)));
        }
        $h = 5 * $nb;

        // Salto de página si hace falta
        $this->CheckPageBreak($h);

        // Celdas
        for($i=0;$i<count($data);$i++){
            $w = isset($this->widths[$i]) ? $this->widths[$i] : 30;
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $txt = utf8_decode($data[$i]);
            // Marco + contenido
            $this->MultiCell($w,5,$txt,1,$a);
            // Poner a la derecha de la celda
            $this->SetXY($x+$w, $y);
        }
        // Bajar a la siguiente línea
        $this->Ln($h);
    }
}