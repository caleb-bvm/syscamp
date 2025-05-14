<?php
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

$query = "SELECT cod_pregunta, pregunta, categoria FROM preguntas ORDER BY cod_pregunta ASC";
$resultado = pg_query($conexion, $query);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Reporte de Preguntas',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,10,'Código',1);
$pdf->Cell(120,10,'Pregunta',1);
$pdf->Cell(50,10,'Categoría',1);
$pdf->Ln();

$pdf->SetFont('Arial','',9);
while ($fila = pg_fetch_assoc($resultado)) {
    $pdf->Cell(20,10,$fila['cod_pregunta'],1);
    $pdf->Cell(120,10,utf8_decode(substr($fila['pregunta'], 0, 60)),1); // truncar si es muy larga
    $pdf->Cell(50,10,utf8_decode($fila['categoria']),1);
    $pdf->Ln();
}

$pdf->Output('reporte_preguntas.pdf', 'D');
