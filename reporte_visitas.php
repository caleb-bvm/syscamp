<?php
// Asegúrate de que no haya espacios en blanco ni líneas vacías ANTES de esta línea

// Verificar si se solicitó la generación del PDF
if (isset($_GET['generar_pdf']) && $_GET['generar_pdf'] == 1) {
    require('fpdf186/fpdf.php');
    include_once('configuracion/conexion.php');

    // Capturar filtros desde la URL
    $categoria = $_GET['categoria'] ?? '';
    $grado     = $_GET['grado'] ?? '';
    $turno     = $_GET['turno'] ?? '';
    $fecha     = $_GET['fecha'] ?? '';
    $nueva_columna = $_GET['nueva_columna'] ?? ''; // Capturar el nuevo filtro

    // Construir condiciones dinámicamente
    $condiciones = [];
    if (!empty($categoria)) $condiciones[] = "p.categoria ILIKE '%" . pg_escape_string($conexion, $categoria) . "%'";
    if (!empty($grado))     $condiciones[] = "r.grado ILIKE '%" . pg_escape_string($conexion, $grado) . "%'";
    if (!empty($turno))     $condiciones[] = "r.turno ILIKE '%" . pg_escape_string($conexion, $turno) . "%'";
    if (!empty($fecha))     $condiciones[] = "r.fecha = '" . pg_escape_string($conexion, $fecha) . "'";
    if (!empty($nueva_columna)) $condiciones[] = "alguna_tabla.nombre_de_la_nueva_columna ILIKE '%" . pg_escape_string($conexion, $nueva_columna) . "%'"; // Ajusta la tabla y columna

    $where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

    // Consulta SQL con filtros
    $query = "
        SELECT r.cod_respuesta, i.nombre_institucion, r.grado, r.turno, r.fecha,
               p.categoria, p.pregunta, rd.respuesta, rd.comentario,
               alguna_tabla.nombre_de_la_nueva_columna AS nueva_columna_reporte -- Agrega la nueva columna
        FROM respuestas r
        JOIN respuestas_detalladas rd ON r.cod_respuesta = rd.respuestas_cod_respuesta
        JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
        JOIN institucion i ON r.id_institucion = i.id_institucion
        LEFT JOIN alguna_tabla ON r.alguna_fk = alguna_tabla.alguna_pk -- Ajusta las relaciones
        $where
        ORDER BY r.fecha DESC, p.categoria, p.cod_pregunta
    ";
    $resultado = pg_query($conexion, $query);
    if (!$resultado) {
        die("Error en la consulta: " . pg_last_error($conexion));
    }

    // Crear PDF
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Título
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode('Reporte de Visitas'), 0, 1, 'C');
    $pdf->Ln(3);

    // Encabezados
    $headers = ['Institución', 'Grado', 'Turno', 'Fecha', 'Categoría', 'Pregunta', 'Respuesta', 'Comentario', 'Nueva Columna']; // Agrega el nuevo encabezado
    $widths = [40, 20, 20, 25, 30, 60, 30, 50, 40]; // Ajusta el ancho

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

        $pdf->SetXY($current_x + $widths[0] + $widths[1] + $widths[2] + $widths[3] + $widths[4] + $widths[5] + $widths[6] + $widths[7], $y_inicio_fila);
        $pdf->MultiCell($widths[8], $line_height, utf8_decode($fila['nueva_columna_reporte']), 0, 'L'); // Agrega la nueva columna al PDF
        $altura_nueva_columna = $pdf->GetY() - $y_inicio_fila;
        $altura_fila = max($altura_fila, $altura_nueva_columna);

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
        $pdf->Cell($widths[8], $altura_fila, '', 1); // Agrega el borde para la nueva columna

        $pdf->Ln($altura_fila);

        if ($pdf->GetY() + $line_height > $pdf->GetPageHeight() - 15) {
            $pdf->AddPage();
            imprimirEncabezados($pdf, $headers, $widths);
        }
    }

    // Output del PDF (descarga directa) SOLO si se solicitó la generación
    $pdf->Output('D', 'reporte_visitas.pdf');
    pg_close($conexion);
    exit(); // Detener la ejecución del script después de generar el PDF
} 
// Asegúrate de que no haya espacios en blanco ni líneas vacías DESPUÉS de esta línea
?>