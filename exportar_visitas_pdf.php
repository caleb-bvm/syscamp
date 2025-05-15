<?php
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

// Captura de filtros
$institucion = $_GET['institucion'] ?? '';
$grado       = $_GET['grado'] ?? '';
$turno       = $_GET['turno'] ?? '';
$fecha       = $_GET['fecha'] ?? '';
$username    = $_GET['username'] ?? '';

// Condiciones dinámicas
$condiciones = [];
if (!empty($institucion)) $condiciones[] = "i.nombre_institucion ILIKE '%$institucion%'";
if (!empty($grado))       $condiciones[] = "r.grado ILIKE '%$grado%'";
if (!empty($turno))       $condiciones[] = "r.turno ILIKE '%$turno%'";
if (!empty($fecha))       $condiciones[] = "r.fecha = '$fecha'";
if (!empty($username))    $condiciones[] = "per.username ILIKE '%$username%'";

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta
$query = "
    SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha, 
           p.categoria, p.pregunta, rd.respuesta, rd.comentario,
           per.username
    FROM respuestas r
    JOIN respuestas_detalladas rd ON r.cod_respuesta = rd.respuestas_cod_respuesta
    JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
    JOIN institucion i ON r.id_institucion = i.id_institucion
    JOIN persona per ON id_persona = id_persona
    $where
    ORDER BY r.fecha DESC, p.categoria, p.cod_pregunta
";
$resultado = pg_query($conexion, $query);

if (!$resultado) {
    die("Error al ejecutar la consulta: " . pg_last_error($conexion));
}

// Crear PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetMargins(5, 10, 5);
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('📋 Reporte de Visitas a Instituciones'), 0, 1, 'C');
$pdf->Ln(3);

// Encabezado
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(60, 120, 180);
$pdf->SetTextColor(255);

$headers = ['Usuario', 'Institución', 'Grado', 'Turno', 'Fecha', 'Categoría', 'Pregunta', 'Respuesta', 'Comentario'];
$widths  = [25, 40, 20, 20, 25, 30, 60, 25, 40];

foreach ($headers as $i => $col) {
    $pdf->Cell($widths[$i], 7, utf8_decode($col), 1, 0, 'C', true);
}
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0);
$line_height = 5;

while ($fila = pg_fetch_assoc($resultado)) {
    $pdf->Cell($widths[0], $line_height, utf8_decode($fila['username']), 1);
    $pdf->Cell($widths[1], $line_height, utf8_decode($fila['nombre_institucion']), 1);
    $pdf->Cell($widths[2], $line_height, utf8_decode($fila['grado']), 1);
    $pdf->Cell($widths[3], $line_height, utf8_decode($fila['turno']), 1);
    $pdf->Cell($widths[4], $line_height, utf8_decode($fila['fecha']), 1);
    $pdf->Cell($widths[5], $line_height, utf8_decode($fila['categoria']), 1);
    $pdf->Cell($widths[6], $line_height, utf8_decode(substr($fila['pregunta'], 0, 40)) . '...', 1);
    $pdf->Cell($widths[7], $line_height, utf8_decode($fila['respuesta']), 1);
    $pdf->Cell($widths[8], $line_height, utf8_decode(substr($fila['comentario'], 0, 40)) . '...', 1);
    $pdf->Ln();
}

$pdf->Output('D', 'reporte_visitas.pdf');
pg_close($conexion);
?>
