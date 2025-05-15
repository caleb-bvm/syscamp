<?php
// Asegúrate de que no haya espacios en blanco ni líneas vacías ANTES de esta línea
require('fpdf186/fpdf.php');
include_once('configuracion/conexion.php');

<<<<<<< HEAD
// Capturar filtros desde la URL (igual que en el reporte web)
$categoria = $_GET['categoria'] ?? '';
$grado     = $_GET['grado'] ?? '';
$turno     = $_GET['turno'] ?? '';
$fecha     = $_GET['fecha'] ?? '';

// Construir condiciones dinámicamente
$condiciones = [];
if (!empty($categoria)) $condiciones[] = "p.categoria ILIKE '%" . pg_escape_string($conexion, $categoria) . "%'";
if (!empty($grado))     $condiciones[] = "r.grado ILIKE '%" . pg_escape_string($conexion, $grado) . "%'";
if (!empty($turno))     $condiciones[] = "r.turno ILIKE '%" . pg_escape_string($conexion, $turno) . "%'";
if (!empty($fecha))     $condiciones[] = "r.fecha = '" . pg_escape_string($conexion, $fecha) . "'";

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL con filtros (igual que en el reporte web)
$query = "
    SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha,
           p.categoria, p.pregunta, rd.respuesta, rd.comentario
=======
// Capturar filtros desde la URL
$institucion = $_GET['institucion'] ?? '';
$grado       = $_GET['grado'] ?? '';
$turno       = $_GET['turno'] ?? '';
$fecha       = $_GET['fecha'] ?? '';
$username    = $_GET['username'] ?? '';

// Construir condiciones dinámicamente
$condiciones = [];
if (!empty($institucion)) $condiciones[] = "i.nombre_institucion ILIKE '%$institucion%'";
if (!empty($grado))       $condiciones[] = "r.grado ILIKE '%$grado%'";
if (!empty($turno))       $condiciones[] = "r.turno ILIKE '%$turno%'";
if (!empty($fecha))       $condiciones[] = "r.fecha = '$fecha'";
if (!empty($username))    $condiciones[] = "per.username ILIKE '%$username%'";

$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

// Consulta SQL
$query = "
    SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha, 
           p.categoria, p.pregunta, rd.respuesta, rd.comentario,
           per.username
>>>>>>> 47cab5de72399b9a84a981bb8955c06c49cde0fd
    FROM respuestas r
    JOIN respuestas_detalladas rd ON r.cod_respuesta = rd.respuestas_cod_respuesta
    JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
    JOIN institucion i ON r.id_institucion = i.id_institucion
    JOIN persona per ON id_persona =id_persona
    $where
    ORDER BY r.fecha DESC, p.categoria, p.cod_pregunta
";
$resultado = pg_query($conexion, $query);
if (!$resultado) {
    die("Error en la consulta: " . pg_last_error($conexion));
}

<<<<<<< HEAD
// Crear PDF
$pdf = new FPDF('L', 'mm', 'A4');   // Horizontal para más espacio
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Reporte de Visitas'), 0, 1, 'C');
$pdf->Ln(3);

// Encabezados
$headers = ['Institución', 'Grado', 'Turno', 'Fecha', 'Categoría', 'Pregunta', 'Respuesta', 'Comentario'];
$widths = [40, 20, 20, 25, 30, 60, 30, 50]; // Ajusta según espacio

// Función para imprimir encabezados
function imprimirEncabezados($pdf, $headers, $widths) {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(70, 130, 180);
    $pdf->SetTextColor(255);
    $current_x = $pdf->GetX();
    foreach ($headers as $i => $col) {
        $pdf->Cell($widths[$i], 7, utf8_decode($col), 1, 0, 'C', true);
        $current_x += $widths[$i];
        $pdf->SetX($current_x);
    }
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
}

imprimirEncabezados($pdf, $headers, $widths);

$line_height = 6;

while ($fila = pg_fetch_assoc($resultado)) {
    $y_inicio_fila = $pdf->GetY();
    $altura_fila = $line_height;
    $current_x = $pdf->GetX();

    // Calcular altura para celdas multilínea
    $pdf->SetXY($current_x, $y_inicio_fila);
    $pdf->MultiCell($widths[0], $line_height, utf8_decode($fila['nombre_institucion']), 0, 'L');
    $altura_institucion = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_institucion);

    $pdf->SetXY($current_x + $widths[0], $y_inicio_fila);
    $pdf->MultiCell($widths[1], $line_height, utf8_decode($fila['grado']), 0, 'C');
    $altura_grado = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_grado);

    $pdf->SetXY($current_x + $widths[0] + $widths[1], $y_inicio_fila);
    $pdf->MultiCell($widths[2], $line_height, utf8_decode($fila['turno']), 0, 'C');
    $altura_turno = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_turno);

    $pdf->SetXY($current_x + $widths[0] + $widths[1] + $widths[2], $y_inicio_fila);
    $pdf->MultiCell($widths[3], $line_height, utf8_decode($fila['fecha']), 0, 'C');
    $altura_fecha = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_fecha);

    $pdf->SetXY($current_x + $widths[0] + $widths[1] + $widths[2] + $widths[3], $y_inicio_fila);
    $pdf->MultiCell($widths[4], $line_height, utf8_decode($fila['categoria']), 0, 'L');
    $altura_categoria = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_categoria);

    $pdf->SetXY($current_x + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4], $y_inicio_fila);
    $pdf->MultiCell($widths[5], $line_height, utf8_decode($fila['pregunta']), 0, 'L');
    $altura_pregunta = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_pregunta);

    $pdf->SetXY($current_x + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4] + $widths[5], $y_inicio_fila);
    $pdf->MultiCell($widths[6], $line_height, utf8_decode($fila['respuesta']), 0, 'L');
    $altura_respuesta = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_respuesta);

    $pdf->SetXY($current_x + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4] + $widths[5] + $widths[6], $y_inicio_fila);
    $pdf->MultiCell($widths[7], $line_height, utf8_decode($fila['comentario']), 0, 'L');
    $altura_comentario = $pdf->GetY() - $y_inicio_fila;
    $altura_fila = max($altura_fila, $altura_comentario);

    // Dibujar los bordes de la fila con la altura máxima calculada
    $pdf->SetXY($current_x, $y_inicio_fila);
    $pdf->Cell($widths[0], $altura_fila, '', 1);
    $pdf->Cell($widths[1], $altura_fila, '', 1);
    $pdf->Cell($widths[2], $altura_fila, '', 1);
    $pdf->Cell($widths[3], $altura_fila, '', 1);
    $pdf->Cell($widths[4], $altura_fila, '', 1);
    $pdf->Cell($widths[5], $altura_fila, '', 1);
    $pdf->Cell($widths[6], $altura_fila, '', 1);
    $pdf->Cell($widths[7], $altura_fila, '', 1);

    // Mover a la siguiente fila
    $pdf->Ln($altura_fila);

    // Salto de página automático
    if ($pdf->GetY() + $line_height > $pdf->GetPageHeight() - 15) {
        $pdf->AddPage();
        imprimirEncabezados($pdf, $headers, $widths);
    }
}

// Asegúrate de que no haya espacios en blanco ni líneas vacías DESPUÉS de esta línea
// Output del PDF (descarga directa)
$pdf->Output('D', 'reporte_visitas.pdf');
pg_close($conexion);
// Asegúrate de que no haya espacios en blanco ni líneas vacías DESPUÉS de esta línea
?>
=======
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>📋 Reporte de Visitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>📋 Reporte de Visitas</h2>

    <!-- Formulario de filtros -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="institucion" value="<?= htmlspecialchars($institucion) ?>" class="form-control" placeholder="Institución">
        </div>
        <div class="col-md-2">
            <input type="text" name="grado" value="<?= htmlspecialchars($grado) ?>" class="form-control" placeholder="Grado">
        </div>
        <div class="col-md-2">
            <input type="text" name="turno" value="<?= htmlspecialchars($turno) ?>" class="form-control" placeholder="Turno">
        </div>
        <div class="col-md-2">
            <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" class="form-control" placeholder="Username">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </form>

    <div class="mb-3">
        <a href="exportar_visitas_pdf.php?<?= http_build_query($_GET) ?>" class="btn btn-danger">📄 Exportar PDF</a>
    </div>

    <!-- Tabla de resultados -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
            <tr>
                <th>Username</th>
                <th>Institución</th>
                <th>Grado</th>
                <th>Turno</th>
                <th>Fecha</th>
                <th>Categoría</th>
                <th>Pregunta</th>
                <th>Respuesta</th>
                <th>Comentario</th>
            </tr>
            </thead>
            <tbody>
            <?php if (pg_num_rows($resultado) > 0): ?>
                <?php while ($fila = pg_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['username']) ?></td>
                        <td><?= htmlspecialchars($fila['nombre_institucion']) ?></td>
                        <td><?= htmlspecialchars($fila['grado']) ?></td>
                        <td><?= htmlspecialchars($fila['turno']) ?></td>
                        <td><?= htmlspecialchars($fila['fecha']) ?></td>
                        <td><?= htmlspecialchars($fila['categoria']) ?></td>
                        <td><?= htmlspecialchars($fila['pregunta']) ?></td>
                        <td><?= htmlspecialchars($fila['respuesta']) ?></td>
                        <td><?= htmlspecialchars($fila['comentario']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center">No se encontraron resultados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<?php include_once('footer.php'); ?>
>>>>>>> 47cab5de72399b9a84a981bb8955c06c49cde0fd
