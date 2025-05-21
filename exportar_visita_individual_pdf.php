<?php
require('fpdf186/fpdf.php'); // Asegúrate que la ruta a fpdf.php es correcta
include_once('configuracion/conexion.php'); // Tu archivo de conexión a la BD

$cod_respuesta = $_GET['cod_respuesta'] ?? null;

if (!$cod_respuesta) {
    die("ID de respuesta no proporcionado para generar el PDF.");
}

// 1. Consulta para obtener los datos generales del formulario, incluyendo la ruta de la imagen de RESPUESTAS
$query_formulario_general = "
    SELECT
        r.cod_respuesta,
        r.ruta_imagen_adjunta, -- ¡AHORA OBTENEMOS LA RUTA DE LA IMAGEN DIRECTAMENTE DE LA TABLA RESPUESTAS!
        i.nombre_institucion,
        i.codigo_de_infraestructura,
        r.grado,
        r.turno,
        r.fecha,
        r.director,
        per.username,
        dpt.nombre_departamento,
        mun.nombre_municipio,
        dis.nombre_distrito
    FROM
        respuestas r
    JOIN
        institucion i ON r.id_institucion = i.id_institucion
    JOIN
        persona per ON r.codigo_persona = per.id_persona
    JOIN
        distrito dis ON i.id_distrito = dis.id_distrito
    JOIN
        municipio mun ON dis.id_municipio = mun.id_municipio
    JOIN
        departamento dpt ON mun.id_departamento = dpt.id_departamento
    WHERE
        r.cod_respuesta = $cod_respuesta
";
$resultado_formulario_general = pg_query($conexion, $query_formulario_general);
$datos_formulario = pg_fetch_assoc($resultado_formulario_general);

if (!$datos_formulario) {
    die("No se encontró el formulario con el ID proporcionado.");
}

// 2. Consulta para obtener las preguntas y respuestas detalladas
$query_detalles = "
    SELECT p.categoria, p.pregunta, rd.respuesta, rd.comentario
    FROM respuestas_detalladas rd
    JOIN preguntas p ON rd.cod_pregunta = p.cod_pregunta
    WHERE rd.respuestas_cod_respuesta = $cod_respuesta
    ORDER BY p.categoria, p.cod_pregunta
";
$resultado_detalles = pg_query($conexion, $query_detalles);

if (!$resultado_detalles) {
    die("Error al ejecutar la consulta de detalles: " . pg_last_error($conexion));
}

// Crear PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// --- Encabezado con título a la izquierda, logo del MINED ---
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(100, 10, utf8_decode('Reporte Individual de Visita'), 0, 0, 'L'); // Título a la izquierda
$pdf->Image('mined_black.png', 170, 10, 30); // Logo del MINED a la derecha (x, y, width)
$pdf->Ln(20); // Salto de línea para dar espacio después del encabezado
// --- Fin del encabezado ---


// Información general del formulario (justificada)
$pdf->SetFont('Arial', '', 10);
$line_height = 7;
$label_width = 50; // Ancho para las etiquetas como "Usuario:"
$value_width = 130; // Ancho para los valores como el nombre de usuario

$pdf->Cell($label_width, $line_height, utf8_decode('Usuario:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['username']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Institución:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['nombre_institucion']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Código de Infraestructura:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['codigo_de_infraestructura']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Departamento:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['nombre_departamento']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Municipio:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['nombre_municipio']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Distrito:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['nombre_distrito']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Grado:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['grado']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Turno:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['turno']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Fecha de Acompañamiento:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['fecha']), 0, 1, 'L');

$pdf->Cell($label_width, $line_height, utf8_decode('Nombre del Director:'), 0, 0, 'L');
$pdf->Cell($value_width, $line_height, utf8_decode($datos_formulario['director']), 0, 1, 'L');

$pdf->Ln(10); // Espacio antes de la tabla de detalles

// Encabezado de la tabla de detalles
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(60, 120, 180);
$pdf->SetTextColor(255);

$headers_detalles = ['Categoría', 'Pregunta', 'Respuesta', 'Comentario'];
$widths_detalles  = [35, 80, 25, 50];

foreach ($headers_detalles as $i => $col) {
    $pdf->Cell($widths_detalles[$i], 7, utf8_decode($col), 1, 0, 'C', true);
}
$pdf->Ln();

// Contenido de la tabla de detalles
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0);
$line_height_detalles = 5;

// Almacenar las filas de detalle en un array para procesarlas después de la tabla
$detalles_para_tabla = [];
while ($fila_detalle = pg_fetch_assoc($resultado_detalles)) {
    $detalles_para_tabla[] = $fila_detalle;
}

foreach ($detalles_para_tabla as $fila_detalle) {
    // Calcular la altura necesaria para la fila si se usa MultiCell
    // Aunque aquí se mantiene el substr para truncar.
    $pdf->Cell($widths_detalles[0], $line_height_detalles, utf8_decode($fila_detalle['categoria']), 1);
    $pdf->Cell($widths_detalles[1], $line_height_detalles, utf8_decode(substr($fila_detalle['pregunta'], 0, 45)) . '...', 1);
    $pdf->Cell($widths_detalles[2], $line_height_detalles, utf8_decode($fila_detalle['respuesta']), 1);
    $pdf->Cell($widths_detalles[3], $line_height_detalles, utf8_decode(substr($fila_detalle['comentario'], 0, 30)) . '...', 1);
    $pdf->Ln();
}

// --- Mover la lógica para mostrar la imagen de evidencia al final ---
// Asegúrate de añadir una nueva página si el contenido de la tabla no deja espacio
$pdf->AddPage(); // Esto asegura que la imagen siempre estará en una nueva página (la última si todo lo demás ya se puso)

if (!empty($datos_formulario['ruta_imagen_adjunta']) && file_exists($datos_formulario['ruta_imagen_adjunta'])) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode('Imagen de Evidencia de Visita'), 0, 1, 'C');
    $pdf->Ln(2); // Pequeño espacio

    // Obtener las dimensiones de la imagen para ajustarla al PDF
    list($ancho_original, $alto_original) = getimagesize($datos_formulario['ruta_imagen_adjunta']);

    // Definir un ancho máximo deseado para la imagen en el PDF
    $ancho_max_pdf = 100; // Por ejemplo, 100 mm

    // Calcular la altura proporcional para mantener la relación de aspecto
    $alto_calculado = ($ancho_max_pdf / $ancho_original) * $alto_original;

    // Centrar la imagen (calcula la posición X para que esté centrada)
    $x_imagen_evidencia = ($pdf->GetPageWidth() - $ancho_max_pdf) / 2;
    $y_imagen_evidencia = $pdf->GetY(); // Obtener la posición Y actual

    $pdf->Image(
        $datos_formulario['ruta_imagen_adjunta'],
        $x_imagen_evidencia,
        $y_imagen_evidencia,
        $ancho_max_pdf,
        $alto_calculado
    );
    $pdf->Ln($alto_calculado + 5); // Avanzar la posición Y en el PDF después de la imagen
} else {
    // Si no hay imagen, puedes agregar un mensaje o simplemente no hacer nada
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, utf8_decode('No se adjuntó imagen de evidencia para esta visita.'), 0, 1, 'C');
    $pdf->Ln(5);
}
// --- Fin de la lógica de la imagen de evidencia ---

$pdf->Output('D', 'reporte_visita_' . $cod_respuesta . '.pdf');
pg_close($conexion);
?>