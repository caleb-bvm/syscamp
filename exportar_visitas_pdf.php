<?php
// 1. Incluir la librería FPDF
require('fpdf186/fpdf.php');

// 2. Incluir la conexión a la base de datos
include_once('configuracion/conexion.php');

// 3. Capturar los filtros desde la URL
$categoria = $_GET['categoria'] ?? '';
$grado     = $_GET['grado'] ?? '';
$turno     = $_GET['turno'] ?? '';
$fecha     = $_GET['fecha'] ?? '';

// 4. Construir condiciones dinámicamente
$condiciones = [];
if (!empty($categoria)) {
    $condiciones[] = "p.categoria ILIKE '%$categoria%'";
}
if (!empty($grado)) {
    $condiciones[] = "r.grado ILIKE '%$grado%'";
}
if (!empty($turno)) {
    $condiciones[] = "r.turno ILIKE '%$turno%'";
}
if (!empty($fecha)) {
    $condiciones[] = "r.fecha = '$fecha'";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// 5. Consulta SQL
$query = "
    SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha, 
           p.categoria, p.pregunta, rd.respuesta, rd.comentario
    FROM respuestas r
    JOIN respuestas_detalladas rd ON r.cod_respuesta = rd.respuestas_cod_respuesta
    JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
    JOIN institucion i ON r.id_institucion = i.id_institucion
    $where
    ORDER BY r.fecha DESC, p.categoria, p.cod_pregunta
";
$resultado = pg_query($conexion, $query);

// 6. Crear PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// 7. Título
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Reporte General de Visitas a Instituciones Educativas'), 0, 1, 'C');
$pdf->Ln(5);

// 8. Encabezados
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 10, 'Institución', 1);
$pdf->Cell(20, 10, 'Grado', 1);
$pdf->Cell(20, 10, 'Turno', 1);
$pdf->Cell(25, 10, 'Fecha', 1);
$pdf->Cell(30, 10, 'Categoría', 1);
$pdf->Cell(60, 10, 'Pregunta', 1);
$pdf->Cell(30, 10, 'Respuesta', 1);
$pdf->Cell(50, 10, 'Comentario', 1);
$pdf->Ln();

// 9. Datos
$pdf->SetFont('Arial', '', 9);
while ($fila = pg_fetch_assoc($resultado)) {
    $pdf->Cell(40, 10, utf8_decode($fila['nombre_institucion']), 1);
    $pdf->Cell(20, 10, utf8_decode($fila['grado']), 1);
    $pdf->Cell(20, 10, utf8_decode($fila['turno']), 1);
    $pdf->Cell(25, 10, $fila['fecha'], 1);
    $pdf->Cell(30, 10, utf8_decode($fila['categoria']), 1);

    // Controlar alturas con MultiCell
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Guardar texto
    $pregunta  = utf8_decode($fila['pregunta']);
    $respuesta = utf8_decode($fila['respuesta']);
    $comentario = utf8_decode($fila['comentario']);

    // Dibujar la MultiCell de Pregunta y calcular altura
    $pdf->MultiCell(60, 10, $pregunta, 1, 'L');
    $altura = $pdf->GetY() - $y;

    // Volver a posición para celdas siguientes
    $pdf->SetXY($x + 60, $y);
    $pdf->MultiCell(30, $altura, $respuesta, 1);
    $pdf->SetXY($x + 90, $y);
    $pdf->MultiCell(50, $altura, $comentario, 1);

    // Ajustar Y para siguiente fila
    $pdf->SetY($y + $altura);
}

// 10. Exportar PDF
$pdf->Output('reporte_visitas.pdf', 'D'); // Forzar descarga
?>
