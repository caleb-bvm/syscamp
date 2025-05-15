<?php
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

// Capturar filtros
$filtro_rol = $_GET['filtro_rol'] ?? '';
$filtro_distrito = $_GET['filtro_distrito'] ?? '';
$filtro_departamento = $_GET['filtro_departamento'] ?? '';

// Construir condiciones
$condiciones = [];
if (!empty($filtro_rol)) $condiciones[] = "p.id_rol = $filtro_rol";
if (!empty($filtro_distrito)) $condiciones[] = "p.id_distrito_reside = $filtro_distrito";
if (!empty($filtro_departamento)) $condiciones[] = "p.id_departamento_labora = $filtro_departamento";
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta
$query = "
SELECT p.codigo_persona, p.nombre_persona, p.apellido_persona, p.correo_persona,
        r.nombre_rol, d.nombre_distrito, dept.nombre_departamento
FROM persona p
JOIN rol r ON p.id_rol = r.id_rol
JOIN distrito d ON p.id_distrito_reside = d.id_distrito
JOIN departamento dept ON p.id_departamento_labora = dept.id_departamento
$where
ORDER BY p.codigo_persona ASC
";
$resultado = pg_query($conexion, $query);
if (!$resultado) {
    die("Error en la consulta: " . pg_last_error($conexion));
}

// Crear PDF
$pdf = new FPDF('L', 'mm', 'A4');  // Horizontal para más espacio
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Reporte General de Personas'), 0, 1, 'C');
$pdf->Ln(3);

// Encabezados
$headers = ['Código', 'Nombres', 'Apellidos', 'Correo', 'Rol', 'Distrito', 'Departamento'];
$widths = [25, 40, 40, 70, 30, 30, 30];  // Ajusta según espacio

// Función para imprimir encabezados (reutilizable)
function imprimirEncabezados($pdf, $headers, $widths) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(70, 130, 180);
    $pdf->SetTextColor(255);
    $current_x = $pdf->GetX();
    foreach ($headers as $i => $col) {
        $pdf->Cell($widths[$i], 8, utf8_decode($col), 1, 0, 'C', true);
        $current_x += $widths[$i];
        $pdf->SetX($current_x);
    }
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetTextColor(0);
}

imprimirEncabezados($pdf, $headers, $widths);

$line_height = 6;

while ($fila = pg_fetch_assoc($resultado)) {
    $y_inicio_fila = $pdf->GetY();
    $altura_fila = $line_height;
    $current_x = $pdf->GetX();
    $celdas = [];

    // Calcular altura para celdas multilínea y almacenar el texto
    $pdf->SetXY($current_x + $widths[0], $y_inicio_fila);
    $pdf->MultiCell($widths[1], $line_height, $celdas['nombre'] = utf8_decode($fila['nombre_persona']), 0);
    $altura_fila = max($altura_fila, $pdf->GetY() - $y_inicio_fila);

    $pdf->SetXY($current_x + $widths[0] + $widths[1], $y_inicio_fila);
    $pdf->MultiCell($widths[2], $line_height, $celdas['apellido'] = utf8_decode($fila['apellido_persona']), 0);
    $altura_fila = max($altura_fila, $pdf->GetY() - $y_inicio_fila);

    $pdf->SetXY($current_x + $widths[0] + $widths[1] + $widths[2], $y_inicio_fila);
    $pdf->MultiCell($widths[3], $line_height, $celdas['correo'] = utf8_decode($fila['correo_persona']), 0);
    $altura_fila = max($altura_fila, $pdf->GetY() - $y_inicio_fila);

    $celdas['codigo'] = utf8_decode($fila['codigo_persona']);
    $celdas['rol'] = utf8_decode($fila['nombre_rol']);
    $celdas['distrito'] = utf8_decode($fila['nombre_distrito']);
    $celdas['departamento'] = utf8_decode($fila['nombre_departamento']);

    // Reiniciar X e Y al inicio de la fila
    $pdf->SetXY($current_x, $y_inicio_fila);

    // Dibujar todas las celdas con la altura calculada
    $pdf->Cell($widths[0], $altura_fila, $celdas['codigo'], 1);
    $pdf->Cell($widths[1], $altura_fila, $celdas['nombre'], 1);
    $pdf->Cell($widths[2], $altura_fila, $celdas['apellido'], 1);
    $pdf->Cell($widths[3], $altura_fila, $celdas['correo'], 1);
    $pdf->Cell($widths[4], $altura_fila, $celdas['rol'], 1);
    $pdf->Cell($widths[5], $altura_fila, $celdas['distrito'], 1);
    $pdf->Cell($widths[6], $altura_fila, $celdas['departamento'], 1);

    // Mover a la siguiente fila
    $pdf->Ln($altura_fila);

    // Salto de página
    if ($pdf->GetY() + $line_height > $pdf->GetPageHeight() - 15) {
        $pdf->AddPage();
        imprimirEncabezados($pdf, $headers, $widths);
    }
}

// Output
$pdf->Output('D', 'reporte_personas.pdf');
pg_close($conexion);
?>