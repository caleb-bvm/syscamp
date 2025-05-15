<?php
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

// Captura de filtros
$filtro_departamento = $_GET['filtro_departamento'] ?? '';
$filtro_municipio = $_GET['filtro_municipio'] ?? '';
$filtro_zona = $_GET['filtro_zona'] ?? '';
$filtro_sector = $_GET['filtro_sector'] ?? '';

// Condiciones para el WHERE
$condiciones = [];
if (!empty($filtro_departamento)) $condiciones[] = "i.id_departamento = $filtro_departamento";
if (!empty($filtro_municipio)) $condiciones[] = "i.id_municipio = $filtro_municipio";
if (!empty($filtro_zona)) $condiciones[] = "i.zona_institucion = '$filtro_zona'";
if (!empty($filtro_sector)) $condiciones[] = "i.sector_institucion = '$filtro_sector'";
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta
$query = "SELECT
    i.codigo_de_infraestructura,
    i.nombre_institucion,
    dep.nombre_departamento,
    mun.nombre_municipio,
    i.zona_institucion,
    i.sector_institucion
FROM instituciones i
INNER JOIN departamento dep ON i.id_departamento = dep.id_departamento
INNER JOIN municipio mun ON i.id_municipio = mun.id_municipio
$where
ORDER BY i.codigo_de_infraestructura ASC";
$resultado = pg_query($conexion, $query);

if (!$resultado) {
    die("Error al ejecutar la consulta: " . pg_last_error($conexion));
}

// Crear PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetMargins(5, 10, 5); // Reducir márgenes laterales
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Reporte General de Instituciones Educativas'), 0, 1, 'C');
$pdf->Ln(3); // Reducir espacio después del título

// Encabezados
$pdf->SetFont('Arial', 'B', 10); // Reducir tamaño de fuente del encabezado
$pdf->SetFillColor(70, 130, 180); // azul más suave
$pdf->SetTextColor(255);

$widths = [25, 65, 40, 40, 25, 25]; // Reducir anchos de columna ligeramente
$headers = ['Código', 'Nombre', 'Departamento', 'Municipio', 'Zona', 'Sector'];

foreach ($headers as $i => $col) {
    $pdf->Cell($widths[$i], 8, utf8_decode($col), 1, 0, 'C', true); // Reducir altura del encabezado
}
$pdf->Ln();

// Contenido
$pdf->SetFont('Arial', '', 9); // Reducir tamaño de fuente del contenido
$pdf->SetTextColor(0);
$line_height = 5; // Reducir altura de línea del contenido

while ($fila = pg_fetch_assoc($resultado)) {
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Calcular altura de la celda más alta (Nombre con MultiCell)
    $pdf->SetXY($x + $widths[0], $y);
    $pdf->MultiCell($widths[1], $line_height, utf8_decode($fila['nombre_institucion']), 0);
    $altura_nombre = $pdf->GetY() - $y;

    // Calcular la altura total de la fila
    $altura_fila = max($line_height, $altura_nombre);

    // Volver a la posición inicial
    $pdf->SetXY($x, $y);
    $pdf->Cell($widths[0], $altura_fila, utf8_decode($fila['codigo_de_infraestructura']), 1);
    $pdf->SetXY($x + $widths[0], $y);
    $pdf->MultiCell($widths[1], $line_height, utf8_decode($fila['nombre_institucion']), 1);

    // Ubicar celdas restantes alineadas a la altura
    $pdf->SetXY($x + $widths[0] + $widths[1], $y);
    $pdf->Cell($widths[2], $altura_fila, utf8_decode($fila['nombre_departamento']), 1);
    $pdf->Cell($widths[3], $altura_fila, utf8_decode($fila['nombre_municipio']), 1);
    $pdf->Cell($widths[4], $altura_fila, utf8_decode($fila['zona_institucion']), 1);
    $pdf->Cell($widths[5], $altura_fila, utf8_decode($fila['sector_institucion']), 1);

    $pdf->Ln($altura_fila);

    // Verificar si la siguiente fila se saldrá de la página y agregar una nueva si es necesario
    if ($pdf->GetY() + $altura_fila > $pdf->GetPageHeight() - $pdf->GetY()) { // Considerar un margen inferior
        $pdf->AddPage();
        // Repetir encabezados en la nueva página si lo deseas
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

// Generar PDF
$pdf->Output('D', 'reporte_instituciones.pdf');

// Cerrar la conexión a la base de datos
pg_close($conexion);
?>