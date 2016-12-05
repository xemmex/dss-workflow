<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    var $fecha_inicial;
    var $fecha_final;
    var $fecha;
    var $hora;
    var $usuario;
    var $categoria;

     
    // Cabecera de página
    function data($fecha,$hora,$usuario,$fecha_inicial,$fecha_final,$categoria){
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->usuario = $usuario;
        $this->fecha_inicial = $fecha_inicial;
        $this->fecha_final = $fecha_final;
        $this->categoria = $categoria;
    }

    function Header()
    {
        // Logo
        $this->Image(base_url().'public/img/dss-icon.png',10,8,12);
        // Arial bold 15
        $this->SetFont('Arial','B',10);
        // Movernos a la derecha
        $this->Cell(17);
        // Título
        $this->SetFont('Arial','B',10);
        $this->Cell(58,10,utf8_decode('Sistema de Soporte a Decisiones'),0,0,'C');
        $this->Cell(90);
        $this->SetFont('Arial','',10);
        $this->Cell(33,5,utf8_decode('Fecha: '.$this->fecha),0,2,'C');
        $this->Cell(33,5,utf8_decode('Hora: '.$this->hora),0,2,'C');
        $this->Cell(27,5,utf8_decode('Usuario: '.$this->usuario),0,1,'C');
        $this->Cell(34,5,utf8_decode('Desde: '.$this->fecha_inicial),0,2,'C');
        $this->Cell(33,5,utf8_decode('Hasta: '.$this->fecha_final),0,1,'C');
        
        $this->Cell(33,5,utf8_decode('Filtrado por: '),0,1,'C');
        $this->Cell(50,5,utf8_decode('Categoria: '.$this->categoria),0,1);

        $this->Cell(80);
        $this->SetFont('Arial','BU',12);
        $this->Cell(40,10,utf8_decode('Reporte de Categoría'),0,0,'C');
        // Salto de línea
        $this->Ln(10);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'C');
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->data($fecha,$hora,$usuario,$fecha_inicial,$fecha_final,$categoria);
$pdf->SetMargins(4, 10,1);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','B',12);
$pdf->Cell('11','10',utf8_decode('Nro'),1,0,'C');
$pdf->Cell('55','10',utf8_decode('Tipo Flujo de Trabajo'),1,0,'C');
$pdf->Cell('30','10',utf8_decode('Usuario'),1,0,'C');
$pdf->Cell('60','10',utf8_decode('Nombre'),1,0,'C');
$pdf->Cell('46','10',utf8_decode('Fecha'),1,1,'C');
$pdf->SetFont('Times','',12);
$max_tam = 30;
foreach ($data as $value) {
    $nombre = $value['titulo'];
    $workflow = $value['nombre'];
    $tam1 = strlen($nombre);
    $tam2 = strlen($workflow);
    $ln1 = intval($tam1/$max_tam)+1;
    $ln2 = intval($tam2/$max_tam)+1;
    if ($ln1>=$ln2){
        $cell_height = 10*$ln1;
    }
    else{
        $cell_height = 10*$ln2;
    }
    $date = new DateTime($value['fecha_inicio']);
    $fecha = date_format($date,'d-m-Y h:i:s A');
    $pdf->Cell('11',$cell_height,utf8_decode($value['id_instancia']),1,0,'C');
    $current_y = $pdf->GetY();
    $current_x = $pdf->GetX();
    $cell_width = 55;
    $pdf->MultiCell($cell_width,'10',utf8_decode($workflow),'T','C');
    $pdf->SetXY($current_x + $cell_width, $current_y);
    $pdf->Cell('30',$cell_height,utf8_decode($value['id_usuario']),1,0,'C');
    $current_y = $pdf->GetY();
    $current_x = $pdf->GetX();
    $cell_width = 60;
    $pdf->MultiCell($cell_width,'10',utf8_decode($nombre),'T','C');
    $pdf->SetXY($current_x + $cell_width, $current_y);
    $pdf->Cell('46',$cell_height,utf8_decode($fecha),1,1,'C'); 
}
$pdf->Cell('11','10','','T',0,'C');
$pdf->Cell('55','10','','T',0,'C');
$pdf->Cell('30','10','','T',0,'C');
$pdf->Cell('60','10','','T',0,'C');
$pdf->Cell('46','10','','T',1,'C');
$pdf->SetFont('Arial','B',12);
$cant = count($data);
$pdf->Cell(50,5,utf8_decode('Total Flujos de trabajo: '.$cant),0,2,'C');
$pdf->Output();


?>