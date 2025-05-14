<?php
// 1. Incluir la librería FPDF
require('fpdf186/fpdf.php'); // Ajusta la ruta si colocaste fpdf.php en otro lugar (ej: require('fpdf/fpdf.php');)

// 2. Incluir la conexión a la base de datos
include_once('configuracion/conexion.php');

// 3. Capturar los filtros (si los hay)
$filtro_rol = $_GET['filtro_rol'] ?? '';
$filtro_distrito = $_GET['filtro_distrito'] ?? '';
$filtro_departamento = $_GET['filtro_departamento'] ?? '';

// 4. Construir la consulta SQL (igual que en reporte_personas.php)
$condiciones = [];
if (!empty($filtro_rol)) {
    $condiciones[] = "p.id_rol = $filtro_rol";
}
if (!empty($filtro_distrito)) {
    $condiciones[] = "p.id_distrito_reside = $filtro_distrito";
}
if (!empty($filtro_departamento)) {
    $condiciones[] = "p.id_departamento_labora = $filtro_departamento";
}
$where = count($condiciones) > 0 ? "WHERE " . implode(" AND ", $condiciones) : "";

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

// 5. Crear un nuevo objeto FPDF
$pdf = new FPDF();
$pdf->AddPage();

// 6. Establecer la fuente
$pdf->SetFont('Arial','B',12); // Fuente para los encabezados

// 7. Añadir el título
$pdf->Cell(0,10,'Reporte General de Personas',0,1,'C');
$pdf->Ln(5); // Salto de línea

// 8. Establecer la fuente para los datos
$pdf->SetFont('Arial','',10);

// 9. Añadir los encabezados de la tabla
$pdf->Cell(20,10,'Código',1);
$pdf->Cell(40,10,'Nombres',1);
$pdf->Cell(40,10,'Apellidos',1);
$pdf->Cell(50,10,'Correo',1);
$pdf->Cell(25,10,'Rol',1);
$pdf->Cell(30,10,'Distrito',1);
$pdf->Cell(35,10,'Departamento',1);
$pdf->Ln();

// 10. Recorrer los resultados y añadir los datos a la tabla
while ($fila = pg_fetch_assoc($resultado)) {
    $pdf->Cell(20,10,$fila['codigo_persona'],1);
    $pdf->Cell(40,10,$fila['nombre_persona'],1);
    $pdf->Cell(40,10,$fila['apellido_persona'],1);
    $pdf->Cell(50,10,$fila['correo_persona'],1);
    $pdf->Cell(25,10,$fila['nombre_rol'],1);
    $pdf->Cell(30,10,$fila['nombre_distrito'],1);
    $pdf->Cell(35,10,$fila['nombre_departamento'],1);
    $pdf->Ln();
}

// 11. Generar y enviar el PDF al navegador
$pdf->Output('reporte_personas.pdf','D'); // 'D' fuerza la descarga
?>