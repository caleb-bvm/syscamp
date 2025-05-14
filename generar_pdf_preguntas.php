<?php
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

// Capturar filtros desde la URL
$filtro_codigo = $_GET['filtro_codigo'] ?? '';
$filtro_categoria = $_GET['filtro_categoria'] ?? '';

// Construir condiciones dinámicamente
$condiciones = [];
if (!empty($filtro_codigo)) {
    $condiciones[] = "cod_pregunta = '$filtro_codigo'";
}
if (!empty($filtro_categoria)) {
    $condiciones[] = "categoria = '$filtro_categoria'";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL con filtros
$query = "SELECT cod_pregunta, pregunta, categoria FROM preguntas $where ORDER BY cod_pregunta ASC";
$resultado = pg_query($conexion, $query);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, '📄 Reporte de Preguntas', 0, 1, 'C');
$pdf->Ln(5);

// Encabezados
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(30, 10, 'Código', 1);
$pdf->Cell(110, 10, 'Pregunta', 1);
$pdf->Cell(50, 10, 'Categoría', 1);
$pdf->Ln();

// Datos
$pdf->SetFont('Arial', '', 10);
while ($fila = pg_fetch_assoc($resultado)) {
    $pdf->Cell(30, 10, $fila['cod_pregunta'], 1);
    $pdf->Cell(110, 10, $fila['pregunta'], 1);
    $pdf->Cell(50, 10, $fila['categoria'], 1);
    $pdf->Ln();
}

$pdf->Output('reporte_preguntas.pdf', 'D'); // 'D' = forzar descarga
?>
