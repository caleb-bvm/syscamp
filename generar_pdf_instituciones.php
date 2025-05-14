<?php
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

// Captura de filtros
$filtro_departamento = $_GET['filtro_departamento'] ?? '';
$filtro_municipio = $_GET['filtro_municipio'] ?? '';
$filtro_zona = $_GET['filtro_zona'] ?? '';
$filtro_sector = $_GET['filtro_sector'] ?? '';

// Condiciones
$condiciones = [];
if (!empty($filtro_departamento)) {
    $condiciones[] = "id_departamento = $filtro_departamento";
}
if (!empty($filtro_municipio)) {
    $condiciones[] = "id_municipio = $filtro_municipio";
}
if (!empty($filtro_zona)) {
    $condiciones[] = "zona_institucion = '$filtro_zona'";
}
if (!empty($filtro_sector)) {
    $condiciones[] = "sector_institucion = '$filtro_sector'";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

$query = "SELECT * FROM instituciones $where ORDER BY codigo_de_infraestructura ASC";
$resultado = pg_query($conexion, $query);

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Reporte General de Instituciones', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);

// Encabezados
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(30, 10, 'Código', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Departamento', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Municipio', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Distrito', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Zona', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Sector', 1, 1, 'C', true);

// Datos
while ($fila = pg_fetch_assoc($resultado)) {
    $pdf->Cell(30, 8, $fila['codigo_de_infraestructura'], 1);
    $pdf->Cell(60, 8, $fila['nombre_institucion'], 1);
    $pdf->Cell(40, 8, $fila['nombre_departamento'], 1);
    $pdf->Cell(40, 8, $fila['nombre_municipio'], 1);
    $pdf->Cell(40, 8, $fila['nombre_distrito'], 1);
    $pdf->Cell(30, 8, $fila['zona_institucion'], 1);
    $pdf->Cell(30, 8, $fila['sector_institucion'], 1);
    $pdf->Ln();
}

$pdf->Output('reporte_instituciones.pdf', 'D');
