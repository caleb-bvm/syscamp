<?php
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

// Capturar filtros desde la URL
$filtro_codigo = $_GET['filtro_codigo'] ?? '';
$filtro_categoria = $_GET['filtro_categoria'] ?? '';

// Construir condiciones dinámicamente
$condiciones = [];
if (!empty($filtro_codigo)) $condiciones[] = "cod_pregunta = '$filtro_codigo'";
if (!empty($filtro_categoria)) $condiciones[] = "categoria = '$filtro_categoria'";
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL
$query = "SELECT cod_pregunta, pregunta, categoria FROM preguntas $where ORDER BY cod_pregunta ASC";
$resultado = pg_query($conexion, $query);
if (!$resultado) {
    die("Error en la consulta: " . pg_last_error($conexion));
}

// Crear PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('📄 Reporte de Preguntas'), 0, 1, 'C');
$pdf->Ln(3);

// Encabezados
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(70, 130, 180);
$pdf->SetTextColor(255);
$headers = ['Código', 'Pregunta', 'Categoría'];
$widths = [30, 110, 50];

// Imprimir encabezados
foreach ($headers as $i => $col) {
    $pdf->Cell($widths[$i], 8, utf8_decode($col), 1, 0, 'C', true);
}
$pdf->Ln();

// Datos
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);
$line_height = 5;

while ($fila = pg_fetch_assoc($resultado)) {
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Calcular altura de la celda de pregunta
    $pdf->SetXY($x + $widths[0], $y);
    $pdf->MultiCell($widths[1], $line_height, utf8_decode($fila['pregunta']), 0);
    $altura_pregunta = $pdf->GetY() - $y;
    $altura_fila = max($line_height, $altura_pregunta);

    // Código
    $pdf->SetXY($x, $y);
    $pdf->Cell($widths[0], $altura_fila, utf8_decode($fila['cod_pregunta']), 1);

    // Pregunta (MultiCell ya fue hecha, solo necesitamos mover la posición Y para la siguiente celda)
    $pdf->SetXY($x + $widths[0], $y);
    $pdf->MultiCell($widths[1], $line_height, utf8_decode($fila['pregunta']), 1);

    // Categoría
    $pdf->SetXY($x + $widths[0] + $widths[1], $y);
    $pdf->Cell($widths[2], $altura_fila, utf8_decode($fila['categoria']), 1);

    $pdf->Ln($altura_fila);

    // Verificar salto de página
    if ($pdf->GetY() + $altura_fila > $pdf->GetPageHeight() - 15) {
        $pdf->AddPage();

        // Repetir encabezados
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(70, 130, 180);
        $pdf->SetTextColor(255);
        foreach ($headers as $i => $col) {
            $pdf->Cell($widths[$i], 8, utf8_decode($col), 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0);
    }
}

// Salida del PDF
$pdf->Output('D', 'reporte_preguntas.pdf');
pg_close($conexion);
?>